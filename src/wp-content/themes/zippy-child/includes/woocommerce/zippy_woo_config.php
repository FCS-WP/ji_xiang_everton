<?php

function remove_checkout_fields($fields)
{
  unset($fields['billing']['billing_company']);
  unset($fields['billing']['billing_city']);
  unset($fields['billing']['billing_postcode']);
  unset($fields['billing']['billing_country']);
  unset($fields['billing']['billing_address_2']);
  unset($fields['shipping']['shipping_city']);
  unset($fields['shipping']['shipping_company']);

  return $fields;
}
add_filter('woocommerce_checkout_fields', 'remove_checkout_fields');
