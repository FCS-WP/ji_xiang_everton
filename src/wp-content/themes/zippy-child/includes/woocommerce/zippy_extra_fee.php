<?php
add_action('woocommerce_cart_calculate_fees', 'add_extra_fee_with_gst');


function add_extra_fee_with_gst($cart)
{
  if (is_admin() && !defined('DOING_AJAX')) {
    return;
  }

  $fee_name = "Extra Fee";
  $extra_fee = isset($_SESSION['extra_fee']) ? $_SESSION['extra_fee'] : 0;

  $cart->add_fee($fee_name, $extra_fee, true);
}

