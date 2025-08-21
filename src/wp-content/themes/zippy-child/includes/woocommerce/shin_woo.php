<?php

//Save custom field billing
function custom_save_checkout_fields($order_id)
{
  $order = new WC_Order($order_id);

  $fields = [
    'billing_outlet',
    'billing_date',
    'billing_time',
    'billing_outlet_address',
    'billing_method_shipping',
  ];

  foreach ($fields as $field) {
    if (!empty($_POST[$field])) {
      $order->update_meta_data('_' . $field, sanitize_text_field($_POST[$field]), true);
    }
  }
  $order->save_meta_data();
}

add_action('woocommerce_checkout_update_order_meta', 'custom_save_checkout_fields');

//Display Admin
function custom_display_order_meta($order)
{

  $fields = [
    '_billing_method_shipping' => 'Method Shipping',
    '_billing_outlet' => 'Outlet Name',
    '_billing_outlet_address' => 'Outlet Address',
    '_billing_date' => 'Delivery Date',
    '_billing_time' => 'Delivery Time',
  ];

  echo '<h4>' . __('Shipping Details', 'woocommerce') . '</h4>';

  foreach ($fields as $key => $field) {
    echo '<p><strong>' . $field . ':</strong> ' . $order->get_meta($key) . '</p>';
  };

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
