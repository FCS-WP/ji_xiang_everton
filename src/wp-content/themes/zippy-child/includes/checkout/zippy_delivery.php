<?php

add_filter('woocommerce_package_rates', 'customize_shipping_rates_based_on_order_mode', 999);

function customize_shipping_rates_based_on_order_mode($rates)
{
  if (empty($rates)) {
    return $rates;
  }

  $rules = get_minimum_rule_by_order_mode();
  $cart_subtotal = floatval(get_subtotal_cart());

  $order_mode = $_SESSION['order_mode'];
  $minimum_for_free_shipping = floatval($rules['minimum_total_to_freeship']);

  // 1. Qualifies for free shipping
  if ($cart_subtotal >= $minimum_for_free_shipping) {

    foreach ($rates as $rate_key => $rate) {
      if ($rate->method_id === 'free_shipping') {
        $rates[$rate_key]->label = __('Free shipping', 'your-text-domain');
      } else {
        unset($rates[$rate_key]);
      }
    }
  } else {

    foreach ($rates as $rate_key => $rate) {
      if ($rate->method_id === 'free_shipping') {
        unset($rates[$rate_key]);
      } else {
        $rates[$rate_key]->label = __('Shipping Fee', 'your-text-domain');
      }
    }
  }

  return $rates;

  // 2. Delivery: remove free shipping if under minimum
  if ($order_mode === 'delivery') {
    return filter_shipping_methods($rates, [], ['free_shipping']);
  } else {
    // Pickup or others: only keep free shipping
    return filter_shipping_methods($rates, ['free_shipping']);
  }
}
function filter_shipping_methods($rates, $keep_methods = [], $remove_methods = [])
{
  foreach ($rates as $rate_key => $rate) {
    $method_id = $rate->method_id;
    if (!empty($keep_methods) && !in_array($method_id, $keep_methods)) {
      unset($rates[$rate_key]);
    }

    if (!empty($remove_methods) && in_array($method_id, $remove_methods)) {
      unset($rates[$rate_key]);
    }
  }

  return $rates;
}
