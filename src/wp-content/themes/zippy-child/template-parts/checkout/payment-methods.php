<div class="quickcheckout-heading"><i class="fa fa-truck"></i> Payment Method</div>
<div class="quickcheckout-content">
  <p>Please select the preferred payment method to use on this order.</p>
  <?php
  if (WC()->cart->needs_payment()) {
    $available_gateways = WC()->payment_gateways()->get_available_payment_gateways();
    WC()->payment_gateways()->set_current_gateway($available_gateways);
  } else {
    $available_gateways = array();
  }

  wc_get_template(
    'checkout/payment.php',
    array(
      'checkout'           => WC()->checkout(),
      'available_gateways' => $available_gateways,
      'order_button_text'  => apply_filters('woocommerce_order_button_text', __('Place order', 'woocommerce')),
    )
  );
  ?>
