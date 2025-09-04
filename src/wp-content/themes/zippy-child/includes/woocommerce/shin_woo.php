<?php

//Save custom field billing
function custom_save_checkout_fields($order_id)
{
  $order = new WC_Order($order_id);

  $fields = [
    BILLING_DATE,
    BILLING_TIME,
    BILLING_OUTLET_ADDRESS,
    BILLING_OUTLET,
    BILLING_METHOD,
  ];

  foreach ($fields as $field) {
    if (!empty($_POST[$field])) {
      $order->update_meta_data($field, sanitize_text_field($_POST[$field]), true);
    }
  }
  $order->save_meta_data();
}
add_action('woocommerce_checkout_update_order_meta', 'custom_save_checkout_fields');

//Display Admin
function custom_display_order_meta($order)
{
  $action = $_GET['action'];

  if ($action === "edit") {
    echo '<h4>' . __('Shipping Details', 'woocommerce') . '</h4>';

    if ($order->get_meta(BILLING_METHOD) == 'delivery') {
      $fields = [
        BILLING_METHOD => 'Method Shipping',
        BILLING_OUTLET => 'Outlet Name',
        BILLING_OUTLET_ADDRESS => 'Outlet Address',
        BILLING_DATE => 'Delivery Date',
        BILLING_TIME => 'Delivery Time',
      ];
    } else {
      $fields = [
        BILLING_METHOD => 'Method Shipping',
        BILLING_OUTLET => 'Outlet Name',
        BILLING_OUTLET_ADDRESS => 'Outlet Address',
        BILLING_DATE => 'Takeaway Date',
        BILLING_TIME => 'Takeaway Time',
      ];
    }

    foreach ($fields as $key => $field) {
      echo '<p><strong>' . $field . ':</strong> ' . ucfirst($order->get_meta($key)) . '</p>';
    };

    if ($order->get_meta('_billing_delivery_to')) {
       echo '<p><strong>Delivery to:</strong> ' . ucfirst($order->get_meta('_billing_delivery_to')) . '</p>';
       echo '<p><strong>Delivery postal:</strong> ' . ucfirst($order->get_meta('_billing_delivery_postal')) . '</p>';
       echo '<p><strong>Delivery distance:</strong> ' . ucfirst($order->get_meta('_billing_distance')) . '</p>';
    }
  } 

  if ($action == 'new') {
    echo '<div id="admin_create_order"></div>';
  }

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
// Modify the Label Total
add_filter('woocommerce_get_order_item_totals', 'reordering_order_item_totals', 10, 3);
function reordering_order_item_totals($total_rows, $order, $tax_display)
{
  $total_rows['order_total']['label'] = 'Total:';
  return $total_rows;
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


// add_filter('woocommerce_get_order_item_totals', 'custom_display_gst_total', 10, 3);
function custom_display_gst_total($total_rows, $order, $tax_display)
{
  $gst = $order->get_total_tax();

  // Add GST line before grand total
  $new_total = [];

  foreach ($total_rows as $key => $row) {
    if ('order_total' === $key) {
      // Insert GST row before total
      $new_total['gst_inclusive'] = [
        'label' => __('GST (Inclusive):', 'your-textdomain'),
        'value' => wc_price($gst, ['currency' => $order->get_currency()]),
      ];
    }
    $new_total[$key] = $row;
  }

  return $new_total;
}
