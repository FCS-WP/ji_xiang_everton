<?php

function remove_checkout_fields($fields)
{
  unset($fields['billing']['billing_company']);
  unset($fields['billing']['billing_city']);
  unset($fields['billing']['billing_postcode']);
  unset($fields['billing']['billing_country']);

  return $fields;
}
add_filter('woocommerce_checkout_fields', 'remove_checkout_fields');

remove_action('woocommerce_checkout_order_review', 'woocommerce_order_review', 20);
