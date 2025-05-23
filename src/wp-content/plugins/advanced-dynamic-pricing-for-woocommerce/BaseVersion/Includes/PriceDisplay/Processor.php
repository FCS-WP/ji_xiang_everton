<?php

namespace ADP\BaseVersion\Includes\PriceDisplay;

use ADP\BaseVersion\Includes\Cache\CacheHelper;
use ADP\BaseVersion\Includes\CartProcessor\ToPricingCartItemAdapter\ToPricingCartItemAdapter;
use ADP\BaseVersion\Includes\Compatibility\Container\SomewhereWarmBundlesCmp;
use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\Core\Cart\Cart;
use ADP\BaseVersion\Includes\Core\Cart\CartItem\CartItemConverter;
use ADP\BaseVersion\Includes\Core\Cart\CartItem\Type\Base\CartItemAttributeEnum;
use ADP\BaseVersion\Includes\Core\Cart\CartItem\Type\Basic\BasicCartItem;
use ADP\BaseVersion\Includes\Core\Cart\CartItem\Type\Container\ContainerCartItem;
use ADP\BaseVersion\Includes\Core\CartCalculator;
use ADP\BaseVersion\Includes\Core\ICartCalculator;
use ADP\BaseVersion\Includes\Debug\ProductCalculatorListener;
use ADP\BaseVersion\Includes\PriceDisplay\WcProductProcessor\IWcProductProcessor;
use ADP\BaseVersion\Includes\PriceDisplay\WcProductProcessor\WcProductProcessorHelper;
use ADP\BaseVersion\Includes\ProductExtensions\ProductExtension;
use ADP\BaseVersion\Includes\WC\DataStores\ProductVariationDataStoreCpt;
use ADP\BaseVersion\Includes\WC\WcCartItemFacade;
use ADP\Factory;
use Exception;
use ReflectionClass;
use ReflectionException;
use WC_Product;
use WC_Product_Grouped;
use WC_Product_Variable;

defined('ABSPATH') or exit;

class Processor implements IWcProductProcessor
{
    const ERR_PRODUCT_WITH_NO_PRICE = 101;
    const ERR_TMP_ITEM_MISSING = 102;
    const ERR_PRODUCT_DOES_NOT_EXISTS = 103;
    const ERR_CART_DOES_NOT_EXISTS = 104;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var ICartCalculator
     */
    protected $calc;

    /**
     * @var Cart
     */
    protected $cart;

    /**
     * @var ProductCalculatorListener
     */
    protected $listener;

    /**
     * @var CartItemConverter
     */
    protected $cartItemConverter;

    /**
     * @param Context|ICartCalculator|null $context
     * @param ICartCalculator|null $calc
     */
    public function __construct($contextOrCalc = null, $deprecated = null)
    {
        $this->context  = adp_context();
        $this->listener = new ProductCalculatorListener();
        $calc           = $contextOrCalc instanceof ICartCalculator ? $contextOrCalc : $deprecated;

        if ($calc instanceof ICartCalculator) {
            $this->calc = $calc;
        } else {
            $this->calc = Factory::callStaticMethod("Core_CartCalculator", 'make', $this->listener);
            /** @see CartCalculator::make() */
        }

        $this->cartItemConverter = new CartItemConverter();
    }

    public function withContext(Context $context)
    {
        $this->context = $context;
    }

    /**
     * @param Cart $cart
     */
    public function withCart(Cart $cart)
    {
        $items = $cart->getItems();
        foreach ($items as $index => $cartItem) {
            $items[$index] = clone $cartItem;
        }
        $cart->setItems($items);

        $this->cart = $cart;
    }

    protected function isCartExists()
    {
        return isset($this->cart);
    }

    /**
     * @param WC_Product|int $theProduct
     * @param float $qty
     * @param array $cartItemData
     *
     * @return ProcessedProductSimple|ProcessedVariableProduct|ProcessedGroupedProduct|ProcessedProductContainer|null
     */
    public function calculateProduct($theProduct, $qty = 1.0, $cartItemData = array())
    {
        if (is_numeric($theProduct)) {
            $product = CacheHelper::getWcProduct($theProduct);
        } elseif ($theProduct instanceof WC_Product) {
            $product = clone $theProduct;
        } else {
            $this->context->handleError(new Exception("Product does not exists",
                self::ERR_PRODUCT_DOES_NOT_EXISTS));

            return null;
        }

        if ($product instanceof WC_Product_Grouped) {
            /** @var $processed ProcessedGroupedProduct */
            $processed = Factory::get("PriceDisplay_ProcessedGroupedProduct", $product, $qty);
            $children = array_filter( array_map( 'wc_get_product', $product->get_children() ), 'wc_products_array_filter_visible_grouped' );

            foreach ($children as $childId) {
                $processedChild = $this->calculateProduct($childId, $qty, $cartItemData);

                if (is_null($processedChild)) {
                    continue;
                }

                $processed->useChild($processedChild);
            }
        } elseif ($product instanceof WC_Product_Variable) {
            /** @var $processed ProcessedVariableProduct */
            $processed = Factory::get("PriceDisplay_ProcessedVariableProduct", $this->context, $product, $qty);
            $children = $product->get_visible_children();

            foreach ($children as $childId) {
                $processedChild = $this->calculate($childId, $qty, $cartItemData, $product);

                if (is_null($processedChild)) {
                    continue;
                }

                $processed->useChild($processedChild);
            }
        } elseif ( WcProductProcessorHelper::isCalculatingPartOfContainerProduct($product) ) {
            $containerProduct = WcProductProcessorHelper::getBundleProductFromBundled($product);
            $processedParent = $this->calculate($containerProduct, $qty, $cartItemData);

            $processed = null;
            if ($processedParent instanceof ProcessedProductContainer) {
                foreach ($processedParent->getContainerItemsByPos() as $containerItem) {
                    if ($containerItem->getProduct()->get_id() === $product->get_id()) {
                        $processed = $containerItem;
                    }
                }
            }
        } else {
            $processed = $this->calculate($product, $qty, $cartItemData);
        }

        return $processed;
    }

    /**
     * @param WC_Product|int $theProduct
     * @param float $qty
     * @param array $cartItemData
     * @param WC_Product|null $theParentProduct
     *
     * @return ProcessedProductSimple|ProcessedProductContainer|null
     */
    protected function calculate($theProduct, $qty = 1.0, $cartItemData = array(), $theParentProduct = null)
    {
        if ( ! $this->isCartExists()) {
            $this->context->handleError(new Exception("Cart does not exists", self::ERR_CART_DOES_NOT_EXISTS));

            return null;
        }

        if (is_numeric($theProduct)) {
            $prodID = $theProduct;
        } elseif ($theProduct instanceof WC_Product) {
            $prodID = $theProduct->get_id();
        } else {
            $prodID = null;
        }

        $variationAttributes = $theProduct instanceof \WC_Product_Variation ? $theProduct->get_variation_attributes() : array();

        if ($prodID && $processedProduct = CacheHelper::maybeGetProcessedProductToDisplay(
                $prodID,
                $variationAttributes,
                $qty,
                $cartItemData,
                $this->cart,
                $this->calc
            )) {
            return $processedProduct;
        }

        if (is_numeric($theParentProduct)) {
            $parent = CacheHelper::getWcProduct($theParentProduct);
        } elseif ($theParentProduct instanceof WC_Product) {
            $parent = clone $theParentProduct;
            CacheHelper::loadVariationsPostMeta($parent->get_id());
        } else {
            $parent = null;
        }

        if (is_numeric($theProduct)) {
            if ($parent && $parent->is_type('variable')) {

                // We do not need to get product type if the parent product is known
                $overrideProductTypeQuery = function () {
                    return 'variation';
                };

                $applyDataStore = function () use ($parent) {
                    $data_store = new ProductVariationDataStoreCpt();
                    if ( ! is_null($parent)) {
                        $data_store->addParent($parent);
                    }

                    return $data_store;
                };

                if ( $this->context->isReplaceProductVariationDataStore() ) {
                    add_filter('woocommerce_product-variation_data_store', $applyDataStore, 10);
                    add_filter('woocommerce_product_type_query', $overrideProductTypeQuery, 10);
                    $product = CacheHelper::getWcProduct($theProduct);
                    remove_filter('woocommerce_product_type_query', $overrideProductTypeQuery, 10);
                    remove_filter('woocommerce_product-variation_data_store', $applyDataStore, 10);
                } else {
                    $product = CacheHelper::getWcProduct($theProduct);
                }
            } else {
                $product = CacheHelper::getWcProduct($theProduct);
            }
        } elseif ($theProduct instanceof WC_Product) {
            $product = clone $theProduct;

            try {
                $reflection = new ReflectionClass($product);
                $property   = $reflection->getProperty('changes');
                $property->setAccessible(true);
                $changes = $product->get_changes();

                $changes = array_filter([
                    'attributes'            => $changes['attributes'] ?? null,
                    'adpCustomInitialPrice' => $changes['adpCustomInitialPrice'] ?? null
                ]);

                $property->setValue($product, $changes);
            } catch (ReflectionException $exception) {
                $property = null;
            }
        } else {
            $product = null;
        }

        if ( ! $product) {
            $this->context->handleError(new Exception("Product does not exists",
                self::ERR_PRODUCT_DOES_NOT_EXISTS));

            return null;
        }

        $productExt = new ProductExtension($this->context, $product);
        $productExt->withContext($this->context);

        if ($product->get_price('edit') === '') {
            $this->context->handleError(new Exception("Empty price", self::ERR_PRODUCT_WITH_NO_PRICE));

            return null;
        }

        $cartItemData = apply_filters('adp_calculate_product_price_data', $cartItemData, $product, $this->context);

        if ( $productExt->getCustomPrice() === null ) {
            $productExt->setCustomPrice(
                apply_filters("adp_product_get_price", null, $product, $variationAttributes, 1, array(), null)
            );
        }

        $currencySwitcher = $this->context->currencyController;

        /**
         * Why do we use '==' instead of '==='?
         * @see \ADP\BaseVersion\Includes\CurrencyController::isCurrencyChanged()
         */
        if ($this->cart->getCurrency() == $currencySwitcher->getDefaultCurrency()) {

            if ($productExt->getCustomPrice() !== null ) {
                $product->set_price($productExt->getCustomPrice());
            } else {
                $product->set_price($currencySwitcher->getDefaultCurrencyProductPrice($product));
            }

            $salePrice = $currencySwitcher->getDefaultCurrencyProductSalePrice($product);
            if ($salePrice !== null) {
                $product->set_sale_price($salePrice);
            }
            $product->set_regular_price($currencySwitcher->getDefaultCurrencyProductRegularPrice($product));
        } elseif ($this->cart->getCurrency() == $currencySwitcher->getCurrentCurrency()) {

            if ($productExt->getCustomPrice() !== null) {
                $product->set_price(
                    $currencySwitcher->getCurrentCurrencyProductPriceWithCustomPrice(
                        $product,
                        $productExt->getCustomPrice()
                    )
                );
            } else {
                $product->set_price($currencySwitcher->getCurrentCurrencyProductPrice($product));
            }

            $salePrice = $currencySwitcher->getCurrentCurrencyProductSalePrice($product);
            if ($salePrice !== null) {
                $product->set_sale_price($salePrice);
            }
            $product->set_regular_price($currencySwitcher->getCurrentCurrencyProductRegularPrice($product));
        }

        $cart = clone $this->cart;

        $item = (new ToPricingCartItemAdapter())->adaptWcProduct($product, $cartItemData);
        $item->setQty($qty);

        $item->addAttr(CartItemAttributeEnum::TEMPORARY());

        $cart->addToCart($item);
        $this->listener->startCartProcessProduct($product);
        $this->calc->processItem($cart, $item);
        $this->listener->finishCartProcessProduct($product);

        $tmpItems         = array();
        $qtyAlreadyInCart = floatval(0);
        foreach ($cart->getItems() as $loopCartItem) {
            if ($loopCartItem->getWcItem()->getKey() === $item->getWcItem()->getKey()) {
                if ($loopCartItem->hasAttr(CartItemAttributeEnum::TEMPORARY())) {
                    $tmpItems[] = $loopCartItem;
                }
            }

            if ($loopCartItem->getWcItem()->getProduct()->get_id() === $item->getWcItem()->getProduct()->get_id()) {
                $qtyAlreadyInCart += $loopCartItem->getQty();
            }
        }
        $tmpFreeItems = array();
        foreach ($cart->getFreeItems() as $loopCartItem) {
            if ($loopCartItem->hasAttr($loopCartItem::ATTR_TEMP)) {
                $tmpFreeItems[] = $loopCartItem;
            }
        }

        $tmpListOfFreeCartItemChoices = array();
        foreach ($cart->getListOfFreeCartItemChoices() as $freeCartItemChoices) {
            if($freeCartItemChoices->hasAttr($freeCartItemChoices::ATTR_TEMP)) {
                $tmpListOfFreeCartItemChoices[] = $freeCartItemChoices;
            }
        }

        $qtyAlreadyInCart = $qtyAlreadyInCart - array_sum(array_map(function ($item) {
                return $item->getQty();
            }, $tmpItems));

        if (count($tmpItems) === 0) {
            $this->context->handleError(new Exception("Temporary item is missing", self::ERR_TMP_ITEM_MISSING));

            return null;
        }

        $tmpItems         = apply_filters("adp_before_processed_product", $tmpItems, $this);

        $processedProduct = WcProductProcessorHelper::tmpItemsToProcessedProduct(
            $this->context,
            $product,
            $tmpItems,
            $tmpFreeItems,
            $tmpListOfFreeCartItemChoices
        );

        $processedProduct->setQtyAlreadyInCart($qtyAlreadyInCart);
        CacheHelper::addProcessedProductToDisplay($item->getWcItem(), $qty, $processedProduct, $this->cart, $this->calc);
        $this->listener->processedProduct($processedProduct);

        return $processedProduct;
    }

    /**
     * @return ProductCalculatorListener
     */
    public function getListener()
    {
        return $this->listener;
    }

    /**
     * @return Cart
     */
    public function getCart()
    {
        return $this->cart;
    }
}
