<?php


add_action('woocommerce_widget_shopping_cart_buttons', function () {
  // Removing Buttons
  remove_action('woocommerce_widget_shopping_cart_buttons', 'woocommerce_widget_shopping_cart_proceed_to_checkout', 20);

  // Adding customized Buttons
  add_action('woocommerce_widget_shopping_cart_buttons', 'custom_widget_shopping_cart_proceed_to_checkout', 20);
}, 1);

function remove_checkout_fields($fields)
{
  unset($fields['billing']['billing_company']);
  unset($fields['billing']['billing_address_2']);
  unset($fields['billing']['billing_city']);
  unset($fields['billing']['billing_state']);
  unset($fields['billing']['billing_postcode']);
  unset($fields['billing']['billing_address_1']);
  unset($fields['billing']['billing_address_2']);
  unset($fields['billing']['billing_country']);
  unset($fields['order']);

  return $fields;
}
add_filter('woocommerce_checkout_fields', 'remove_checkout_fields');

function remove_checkout_coupon_form()
{
  remove_action('woocommerce_checkout_order_review', 'woocommerce_order_review', 10);
}

add_action('wp', 'remove_checkout_coupon_form');
