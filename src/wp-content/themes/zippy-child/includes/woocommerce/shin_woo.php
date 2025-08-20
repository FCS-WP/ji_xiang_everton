<?php

//Save custom field billing
function custom_save_checkout_fields($order_id)
{
  $fields = [
    'billing_outlet',
    'billing_date',
    'billing_time',
    'billing_outlet_address',
    'billing_method_shipping',
  ];

  foreach ($fields as $field) {
    if (!empty($_POST[$field])) {
      update_post_meta($order_id, '_' . $field, sanitize_text_field($_POST[$field]));
    }
  }
}

add_action('woocommerce_checkout_update_order_meta', 'custom_save_checkout_fields');

//Display Admin
function custom_display_order_meta($order)
{
  $productID = $order->get_id();
  echo '<h4>' . __('Shipping Details', 'woocommerce') . '</h4>';
  echo '<p><strong>Method Shipping:</strong> ' . get_post_meta($productID, '_billing_method_shipping', true) . '</p>';
  echo '<p><strong>Outlet Name:</strong> ' . get_post_meta($productID, '_billing_outlet', true) . '</p>';
  echo '<p><strong>Outlet Address:</strong> ' . get_post_meta($productID, '_billing_outlet_address', true) . '</p>';
  echo '<p><strong>Date:</strong> ' . get_post_meta($productID, '_billing_date', true) . '</p>';
  echo '<p><strong>Time:</strong> ' . get_post_meta($productID, '_billing_time', true) . '</p>';
}
add_action('woocommerce_admin_order_data_after_shipping_address', 'custom_display_order_meta', 10, 1);


// add_action('woocommerce_checkout_process', 'validate_required_session_data');

add_filter('woocommerce_cart_needs_shipping', 'custom_force_cart_not_need_shipping');
function custom_force_cart_not_need_shipping($needs_shipping)
{
  if (is_admin()) return;
  if (is_takeaway()) {
    return false;
  }
  return $needs_shipping;
}

/**
 * Validate required session data before proceeding with checkout or other actions
 */
function validate_required_session_data()
{
  $required_sessions = [
    'outlet_name',
    'date',
    'time',
  ];

  if (is_delivery()) {
    $required_sessions[] = 'delivery_address';
  }

  foreach ($required_sessions as $session_key) {
    $value = WC()->session->get($session_key);
    if (empty($value)) {
      wc_add_notice(__('Missing required information: ' . $session_key, 'woocommerce'), 'error');
    }
  }

  $time_data = WC()->session->get('time');
  if (is_array($time_data)) {
    if (empty($time_data['from']) || empty($time_data['to'])) {
      wc_add_notice(__('Missing required delivery time information.', 'woocommerce'), 'error');
    }
  }
}
