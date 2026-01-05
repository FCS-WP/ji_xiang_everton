<?php

/**
 * Review order table
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/review-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 5.2.0
 */

defined('ABSPATH') || exit;
?>


<?php

use Zippy_Booking\Src\Services\Zippy_Booking_Helper;
use Zippy_Booking\Utils\Zippy_Wc_Calculate_Helper;

$priceShippingIncludeTax = Zippy_Wc_Calculate_Helper::get_total_price_including_tax(WC()->cart->get_shipping_total());
?>
<div id="order_review" class="woocommerce-checkout-review-order">
  <table class="shop_table cart_custom shop_table woocommerce-checkout-review-order-table" cellspacing="0">
    <thead>
      <tr>
        <th class="product-thumbnail">Image</th>
        <th class="product-name">Product Name</th>
        <th class="product-price_custom">Price</th>
        <th class="product-quantity">Quantity</th>
        <th class="product-subtotal_custom">Total</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $cart_subtotal = 0;
      $cart_items = Zippy_Booking_Helper::sort_cart_items_by_product_category(WC()->cart->get_cart());
      foreach ($cart_items as $cart_item_key => $cart_item) {
        $_product   = $cart_item['data'];
        $product_id = $cart_item['product_id'];

        if ($_product && $_product->exists() && $_product->get_price() > 0 && $cart_item['quantity'] > 0) {
          $product_permalink = get_permalink($product_id);
      ?>

          <tr>
            <td class="product-thumbnail">
              <?php echo $_product->get_image(); ?>
            </td>

            <td class="product-name">
              <a href="#">
                <?php echo $_product->get_name(); ?>
              </a>

              <?php
              $parent_qty = isset($cart_item['quantity']) ? intval($cart_item['quantity']) : 1;
              ?>

              <?php if ($_product->get_type() == 'composite'): ?>
                <?php if (isset($cart_item['akk_selected'])): ?>
                  <div class="akk-sub-products-list">
                    <?php foreach ($cart_item['akk_selected'] as $sub_product_id => $qty): ?>
                      <?php
                      if (!is_array($qty) || $qty[0] <= 0) continue;
                      $sub_product = wc_get_product($sub_product_id);
                      if (!$sub_product) continue;

                      $price = isset($qty[1]) ? $qty[1] : $sub_product->get_price();
                      $final_qty = intval($qty[0]) * $parent_qty;
                      ?>
                      <p class="akk-sub-product">
                        <?php echo esc_html($sub_product->get_name()) . ' x ' . $final_qty; ?>
                      </p>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>
              <?php else: ?>
                <?php if (isset($cart_item['akk_selected'])): ?>
                  <div class="akk-sub-products-list">
                    <?php foreach ($cart_item['akk_selected'] as $sub_product_id => $qty): ?>
                      <?php
                      if (!is_array($qty) || $qty[0] <= 0) continue;
                      $sub_product = wc_get_product($sub_product_id);
                      if (!$sub_product) continue;

                      $price = isset($qty[1]) ? $qty[1] : $sub_product->get_price();
                      $final_qty = intval($qty[0]) * $parent_qty;
                      ?>
                      <p class="akk-sub-product">
                        <?php echo esc_html($sub_product->get_name()) . ' (' . wc_price($price) . ') x ' . $final_qty; ?>
                      </p>
                    <?php endforeach; ?>
                  </div>

                  <?php if (!empty($cart_item['combo_extra_price'])): ?>
                    <p class="combo-extra-price">
                      Platter Plate: <?php echo wc_price($cart_item['combo_extra_price'] * $parent_qty); ?>
                    </p>
                  <?php endif; ?>

                <?php endif; ?>
              <?php endif; ?>

            </td>


            <td class="product-price_custom">
              <?php echo wc_price($_product->get_price()); ?>
            </td>

            <td class="product-quantity">
              x <?php echo esc_html($cart_item['quantity']); ?>
            </td>
            <td class="product-subtotal_custom">
              <?php echo wc_price($cart_item['line_total'] + $cart_item['line_tax']); ?>
            </td>
          </tr>

      <?php }
      }
      $rule = get_minimum_rule_by_order_mode();
      $fee_delivery = 0;
      $extra_fee = !empty(WC()->session->get('extra_fee')) ? WC()->session->get('extra_fee') : 0;

      if ($cart_subtotal < $rule["minimum_total_to_order"]) {
        if (is_delivery()) {
          $fee_delivery = WC()->session->get('shipping_fee');
        }
      }
      ?>
      <tr>
        <td colspan="4" class="text-right"><strong>Sub-total:</strong></td>
        <td><?php echo (WC()->cart->get_cart_subtotal()); ?></td>
      </tr>
      <?php if (is_delivery()): ?>
        <tr>
          <td colspan="4" class="text-right">
            <strong>Delivery Fee:</strong>
          </td>
          <td>
            <?php
            echo wc_price($priceShippingIncludeTax);
            ?>
          </td>
        </tr>
      <?php endif; ?>
      <?php if ($extra_fee != 0): ?>
        <tr>
          <td colspan="4" class="text-right"><strong>Delivery Extra Fee:</strong></td>
          <td><?php echo wc_price(WC()->session->get('extra_fee')); ?></td>
        </tr>
      <?php endif; ?>

      <tr>
        <td colspan="4" class="text-right"><strong>GST (INCLUSIVE):</strong></td>
        <?php
        $tax          = get_tax_percent();
        $sub_total    = (float) WC()->cart->subtotal;
        $shipping     = (float) $priceShippingIncludeTax;
        $fee          = (float) (WC()->session->get('extra_fee') ?? 0);
        $total        = (float) WC()->cart->get_total('edit');

        $tax_rate     = ! empty($tax->tax_rate) ? floatval($tax->tax_rate) : 0;

        // Calculate GST portions
        $subtotal_tax = get_tax_inclusive_amount($sub_total, $tax_rate);

        $shipping_tax = get_tax_inclusive_amount($shipping, $tax_rate);

        $fee_tax      = get_tax_inclusive_amount($fee, $tax_rate);

        $gst = $subtotal_tax + $shipping_tax + $fee_tax;

        $gst_display = wc_price($gst);

        ?>
        <td><?php echo $gst_display; ?></td>
      </tr>

      <!-- Coupon row -->

      <?php foreach (WC()->cart->get_coupons() as $code => $coupon) : ?>
        <tr class="coupon-<?php echo esc_attr(sanitize_title($code)); ?>">
          <td colspan="4" class="text-right"><strong><?php wc_cart_totals_coupon_label($coupon); ?> </strong></td>
          <td><?php wc_cart_totals_coupon_html($coupon); ?></td>
        </tr>
      <?php endforeach; ?>
      <tr>
        <td colspan="4" class="text-right"><strong>Total:</strong></td>
        <td><strong><?php echo wc_cart_totals_order_total_html() ?></strong></td>
      </tr>
    </tbody>
  </table>
</div>
