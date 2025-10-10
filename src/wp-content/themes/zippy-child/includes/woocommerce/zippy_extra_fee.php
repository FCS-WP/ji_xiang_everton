<?php

use Zippy_Booking\Utils\Zippy_Wc_Calculate_Helper;

add_action('woocommerce_cart_calculate_fees', 'add_extra_fee_with_gst');

function add_extra_fee_with_gst($cart)
{
  if (is_admin() && !defined('DOING_AJAX')) {
    return;
  }

  $extra_fee = WC()->session->get('extra_fee') ? WC()->session->get('extra_fee') : 0;
  if ($extra_fee <= 0) {
    return;
  }

  $tax_fee = Zippy_Wc_Calculate_Helper::get_tax($extra_fee);
  $net_fee = $extra_fee - $tax_fee;

  $fee = new WC_Order_Item_Fee();
  $fee->set_name("Delivery Extra Fee");

  $fee->set_total($net_fee);
  $fee->set_total_tax($tax_fee);
  $cart->fees_api()->add_fee((object) [
    'id'        => 'custom_delivery_fee',
    'name'      => "Extra Fee",
    'amount'    => $net_fee,
    'taxable'   => true,
    'tax'       => $tax_fee,
    'tax_class' => ''
  ]);
}
