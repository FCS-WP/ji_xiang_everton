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

  $fields['shipping']['shipping_unit_number'] = array(
    'label'       => __('Unit Number', 'woocommerce'),
    'placeholder' => __('e.g. #18-00', 'woocommerce'),
    'required'    => true,
    'class'       => array('form-row-wide'),
    'priority'    => 55,
  );

  return $fields;
}
add_filter('woocommerce_checkout_fields', 'remove_checkout_fields');
