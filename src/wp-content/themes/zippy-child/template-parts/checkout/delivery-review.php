<div id="method_shipping">
  <div class="quickcheckout-heading"><i class="fa fa-truck"></i> Delivery Method</div>
  <div class="quickcheckout-content">
    <input type="hidden" name="billing_method_shipping" id="billing_method_shipping" value="<?php echo esc_attr(WC()->session->get('order_mode') ?? ''); ?>">

    <div class="select_method_shipping">
      <table class="shipping__table shipping__table--multiple">
        <tbody>
          <?php foreach (WC()->cart->get_coupons() as $code => $coupon) : ?>
            <tr class="cart-discount coupon-<?php echo esc_attr(sanitize_title($code)); ?>">
              <th><?php wc_cart_totals_coupon_label($coupon); ?></th>
              <td data-title="<?php echo esc_attr(wc_cart_totals_coupon_label($coupon, false)); ?>"><?php wc_cart_totals_coupon_html($coupon); ?></td>
            </tr>
          <?php endforeach; ?>

          <?php if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()) : ?>

            <?php do_action('woocommerce_cart_totals_before_shipping'); ?>

            <?php wc_cart_totals_shipping_html(); ?>

            <?php do_action('woocommerce_cart_totals_after_shipping'); ?>

          <?php elseif (WC()->cart->needs_shipping() && 'yes' === get_option('woocommerce_enable_shipping_calc')) : ?>

            <tr class="shipping">
              <th><?php esc_html_e('Shipping', 'woocommerce'); ?></th>
              <td data-title="<?php esc_attr_e('Shipping', 'woocommerce'); ?>"><?php woocommerce_shipping_calculator(); ?></td>
            </tr>

          <?php endif; ?>
        </tbody>

      </table>


    </div>
  </div>
  <div class="quickcheckout-heading"><i class="fa fa-truck"></i> Order Information</div>
  <p class="title_cutlery">Do you need cutlery?</p>
  <div class="row_order_information">
    <label class="switch">
      <input type="checkbox" id="switchInput">
      <span class="slider round" id="switchButton"></span>
    </label>
    <input type="hidden" name="billing_cutlery" id="billing_cutlery" value="NO">

    <p id="labelSwitch">No, thanks.</p>
  </div>
  <!-- Delivery Information -->
  <?php get_template_part('template-parts/checkout/order-delivery-info', ''); ?>

  <div class="quickcheckout-heading"><i class="fa fa-truck"></i> Payment Method</div>
  <div class="quickcheckout-content">
    <p>Please select the preferred payment method to use on this order.</p>
  </div>
</div>
