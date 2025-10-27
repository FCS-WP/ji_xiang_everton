<?php

/**
 * Mini-cart
 *
 * Contains the markup for the mini-cart, used by the cart widget.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/mini-cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see              https://woocommerce.com/document/template-structure/
 * @package          WooCommerce\Templates
 * @version          9.3.0
 * @flatsome-version 3.19.6
 */

use Zippy_Booking\Src\Services\Zippy_Booking_Helper;

defined('ABSPATH') || exit;
if (is_admin()) return;
$total_quantity = WC()->cart->get_cart_contents_count();
$rules = get_minimum_rule_by_order_mode();
$total_order = floatval(get_subtotal_cart());
$minimum_order = floatval(get_option('minimum_order', true));
$conditions = array(
  'total_cart' => $total_order,
  'rules' => $rules,
);
do_action('woocommerce_before_mini_cart');

?>

<div class="widget_shopping_cart">

  <!-- // User Shipping Info Here  -->

  <!-- Cart Header  -->
  <div class="row_mini_cart_custom">
    <div class="title_mini_cart_custom">
      <h3>Your Cart</h3>
      <p><span id="total_quanity_cart"><?php echo $total_quantity; ?></span> items added</p>
    </div>
    <div c
      lass="icon_mini_cart_custom">
      <?php require_once THEME_DIR . '-child/assets/icons/edit-icon.php' ?>
    </div>
  </div>

  <!-- User Infor  -->
  <?php get_template_part('template-parts/cart/user-info', ''); ?>

  <!-- Condition free shipping and Mini order Items -->
  <?php if ($total_quantity > 0): ?>
    <?php get_template_part('template-parts/cart/conditions-info', '', $conditions); ?>
  <?php endif; ?>
  <!-- // Mini Cart Here  -->
  <div class="widget_shopping_cart_content">
    <?php if (! WC()->cart->is_empty()) : ?>

      <ul class="woocommerce-mini-cart cart_list product_list_widget <?php echo esc_attr($args['list_class']); ?>">
        <?php
        do_action('woocommerce_before_mini_cart_contents');

        $cart_items = Zippy_Booking_Helper::sort_cart_items_by_product_category(WC()->cart->get_cart());

        foreach ($cart_items as $cart_item_key => $cart_item) {
          $_product   = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
          $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);
          if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key)) {
            /**
             * This filter is documented in woocommerce/templates/cart/cart.php.
             *
             * @since 2.1.0
             */
            $product_name      = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key);
            $thumbnail         = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key);
            $product_price     = apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key);
            // $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
        ?>
            <li class="woocommerce-mini-cart-item <?php echo esc_attr(apply_filters('woocommerce_mini_cart_item_class', 'mini_cart_item', $cart_item, $cart_item_key)); ?>">
              <?php
              echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                'woocommerce_cart_item_remove_link',
                sprintf(
                  '<a href="%s" class="remove remove_from_cart_button" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s" data-success_message="%s">&times;</a>',
                  esc_url(wc_get_cart_remove_url($cart_item_key)),
                  /* translators: %s is the product name */
                  esc_attr(sprintf(__('Remove %s from cart', 'woocommerce'), wp_strip_all_tags($product_name))),
                  esc_attr($product_id),
                  esc_attr($cart_item_key),
                  esc_attr($_product->get_sku()),
                  /* translators: %s is the product name */
                  esc_attr(sprintf(__('&ldquo;%s&rdquo; has been removed from your cart', 'woocommerce'), wp_strip_all_tags($product_name)))
                ),
                $cart_item_key
              );
              ?>
              <?php if (empty($product_permalink)) : ?>
                <?php echo $thumbnail . '<strong>' . wp_kses_post($product_name) . '</strong>';
                ?>
              <?php else : ?>
                <a href="<?php echo esc_url($product_permalink); ?>">
                  <?php echo $thumbnail . '<strong>' . wp_kses_post($product_name) . '</strong>';
                  ?>
                </a>
              <?php endif; ?>
              <?php echo wc_get_formatted_cart_item_data($cart_item);
              ?>
              <?php echo apply_filters('woocommerce_widget_cart_item_quantity', '<span class="quantity">' . sprintf('%s &times; %s', $cart_item['quantity'], $product_price) . '</span>', $cart_item, $cart_item_key); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
              ?>
            </li>
        <?php
          }
        }

        do_action('woocommerce_mini_cart_contents');
        ?>
      </ul>

      <?php do_action('flatsome_after_mini_cart_contents'); ?>

      <div class="ux-mini-cart-footer">
        <?php do_action('flatsome_before_mini_cart_total'); ?>
        <!-- Tax -->

        <?php if (wc_tax_enabled() && ! WC()->cart->display_prices_including_tax()) : ?>
          <?php if ('itemized' === get_option('woocommerce_tax_total_display')) : ?>
            <?php foreach (WC()->cart->get_tax_totals() as $code => $tax) : // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited 
            ?>
              <p class="woocommerce-mini-cart__total tax <?php echo esc_attr(sanitize_title($code)); ?>">
                <strong><?php echo esc_html($tax->label); ?></strong>
                <span><?php echo wp_kses_post($tax->formatted_amount); ?></span>
              </p>
            <?php endforeach; ?>
          <?php else : ?>
            <p class="woocommerce-mini-cart__total tax">
              <strong><?php echo esc_html(WC()->countries->tax_or_vat()); ?></strong>
              <span><?php wc_cart_totals_taxes_total_html(); ?></span>
            </p>
          <?php endif; ?>
        <?php endif; ?>
        <!-- Fee -->
        <?php foreach (WC()->cart->get_fees() as $fee) : ?>
          <p class="woocommerce-mini-cart__total fee">
            <span><?php echo esc_html($fee->name); ?></span>
            <span><?php wc_cart_totals_fee_html($fee); ?></span>
          </p>
        <?php endforeach; ?>
        <!-- Subtotal -->

        <p class="woocommerce-mini-cart__total total">
          <?php do_action('woocommerce_widget_shopping_cart_total'); ?>
        </p>
      </div>

      <div class="woocommerce-mini-cart__buttons_wrapper">
        <?php do_action('woocommerce_widget_shopping_cart_before_buttons'); ?>
        <!-- Total -->

        <p class="woocommerce-mini-cart__total total">
          <strong><?php esc_html_e('Total', 'woocommerce'); ?></strong>
          <?php echo WC()->cart->get_cart_total(); ?>
        </p>
        <!-- Mini Cart button  -->
        <p class="woocommerce-mini-cart__buttons buttons">


          <?php echo ' <a href="' . esc_url(wc_get_cart_url()) . '" class="button wc-forward">View cart</a>'; ?>

          <?php do_action('woocommerce_proceed_to_checkout'); ?>

        </p>

        <?php do_action('woocommerce_widget_shopping_cart_after_buttons'); ?>
      </div>

    <?php else : ?>

      <div class="ux-mini-cart-empty flex flex-row-col text-center pt pb">
        <?php do_action('flatsome_before_mini_cart_empty_message'); ?>
        <p class="woocommerce-mini-cart__empty-message empty"><?php esc_html_e('No products in the cart.', 'woocommerce'); ?></p>
        <?php do_action('flatsome_after_mini_cart_empty_message'); ?>
      </div>

    <?php endif; ?>
  </div>
</div>

<?php do_action('woocommerce_after_mini_cart'); ?>