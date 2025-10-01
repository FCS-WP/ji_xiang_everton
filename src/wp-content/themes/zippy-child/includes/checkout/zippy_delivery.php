<?php

add_filter('woocommerce_package_rates', 'customize_shipping_rates_based_on_order_mode', 999);

function customize_shipping_rates_based_on_order_mode($rates)
{
  if (empty($rates)) {
    return $rates;
  }

  $rules = get_minimum_rule_by_order_mode();
  $cart_subtotal = floatval(get_subtotal_cart());

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
        $fee_delivery = floatval(WC()->session->get('shipping_fee')) ?? 0;
        $rates[$rate_key]->label = __('Shipping Fee', 'your-text-domain');
        $rates[$rate_key]->cost = $fee_delivery;
        $rates[$rate_key]->taxes = 0;
      }
    }
  }

  return $rates;

  // 2. Delivery: remove free shipping if under minimum
  if (is_delivery()) {
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

add_filter('woocommerce_cart_shipping_method_full_label', 'custom_shipping_label_with_distance', 10, 2);

function custom_shipping_label_with_distance($label, $method)
{
  $total_distance = WC()->session->get('total_distance');
  if ($total_distance) {
    $distance_in_meters = round($total_distance / 1000, 2);
    $label .= ' - ' . $distance_in_meters . 'km';
  }

  return $label;
}

// Set minimum order amount
add_action('woocommerce_checkout_process', 'set_minimum_order_amount', 10);
add_action('woocommerce_before_cart', 'set_minimum_order_notice', 10);

function set_minimum_order_amount()
{

  if (!is_delivery()) return;

  $minimum = get_minimum_rule_by_order_mode();
  $minimum_order = $minimum['minimum_total_to_order'];

  if (WC()->cart && WC()->cart->total < $minimum_order) {
    wc_add_notice(
      sprintf(
        'You must have an order with a minimum of %s to proceed to checkout.',
        wc_price($minimum_order)
      ),
      'error'
    );
  }
}

function set_minimum_order_notice()
{

  if (!is_delivery()) return;

  $minimum = get_minimum_rule_by_order_mode();
  $minimum_order = $minimum['minimum_total_to_order'];

  if (WC()->cart && WC()->cart->total < $minimum_order) {
    wc_print_notice(
      sprintf(
        'Your current order total is %s — you must have a minimum of %s to checkout.',
        wc_price(WC()->cart->total),
        wc_price($minimum_order)
      ),
      'error'
    );
  }
}
