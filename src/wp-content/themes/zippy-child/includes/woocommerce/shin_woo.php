<?php

use Zippy_Booking\Utils\Zippy_Wc_Calculate_Helper;

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
      echo '<p><strong>Delivery to:</strong> ' . ucfirst($order->get_meta(BILLING_DELIVERY)) . '</p>';
      echo '<p><strong>Delivery distance:</strong> ' . metersToKilometers($order->get_meta(BILLING_DISTANCE)) . '</p>';
    }
  }

  if ($action == 'new') {
    echo '<div id="admin_create_order"></div>';
    echo '<style>#woocommerce-order-items{display:none !important}</style>';
  }
}
add_action('woocommerce_admin_order_data_after_shipping_address', 'custom_display_order_meta', 10, 1);



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

add_action('admin_enqueue_scripts', 'custom_admin_order_meta_scripts', 20);
function custom_admin_order_meta_scripts()
{
  wp_dequeue_script('woocommerce_admin_meta_boxes');
  wp_deregister_script('woocommerce_admin_meta_boxes');

  wp_register_script(
    'woocommerce_admin_meta_boxes',
    get_stylesheet_directory_uri() . '/includes/woocommerce/js/my-meta-boxes-order.js',
    ['jquery', 'jquery-ui-sortable'],
    '1.0',
    true
  );
  wp_enqueue_script('woocommerce_admin_meta_boxes');
}

add_action('woocommerce_before_order_object_save', 'handle_calculate_tax', 10, 1);
/**
 * Calculate tax for order
 * @param WC_Order $order
 * @param null $data_store
 */
function handle_calculate_tax($order, $data_store = null)
{
  $tax_product_total = calculate_product_tax($order);
  $shipping_tax      = calculate_shipping_tax($order);
  $fee_tax           = calculate_fee_tax($order);

  $tax_total = $tax_product_total + $shipping_tax + $fee_tax;

  $order->set_cart_tax($tax_total);
}

function calculate_product_tax($order)
{
  $tax_product_total = 0;
  foreach ($order->get_items() as $item) {
    $tax_product_total += $item->get_subtotal_tax();
  }
  return $tax_product_total;
}

function calculate_shipping_tax($order)
{
  $shipping_subtotal = 0;
  foreach ($order->get_items('shipping') as $item) {
    $shipping_subtotal += $item->get_total();
  }
  return Zippy_Wc_Calculate_Helper::get_tax_by_price_exclude_tax($shipping_subtotal);
}

function calculate_fee_tax($order)
{
  $fee_subtotal = 0;
  foreach ($order->get_items('fee') as $item) {
    $fee_subtotal += $item->get_total();
  }
  return Zippy_Wc_Calculate_Helper::get_tax_by_price_exclude_tax($fee_subtotal);
}

add_action('woocommerce_checkout_order_processed', 'custom_clear_cart_session', 10, 1);
function custom_clear_cart_session($order_id)
{
  if (! WC()->cart) return;
  WC()->cart->empty_cart();

  if (! WC()->session) return;
  WC()->session->destroy_session();
}
