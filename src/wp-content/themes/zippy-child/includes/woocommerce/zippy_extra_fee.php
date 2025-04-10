<?php
// add_action('woocommerce_cart_calculate_fees', 'add_extra_fee_after_tax', 999);

function add_extra_fee_after_tax($cart)
{
  if (is_admin() && !defined('DOING_AJAX')) {
    return;
  }

  $fee_name = __('Extra Fee ', 'your-textdomain');

  $extra_fee = 10;

  $tax_rate = get_tax_percent();

  $tax_amount = ($extra_fee * $tax_rate->tax_rate) / 100;

  // // Add fee after tax
  $cart->add_fee($fee_name, $extra_fee + $tax_amount, true);
}
