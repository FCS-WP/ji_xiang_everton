<?php




//Edit checkout page
function custom_add_checkout_fields($fields)
{

  // 1. Cutlery (Text Input)
  $fields['billing']['billing_cutlery'] = array(
    'type'      => 'hidden',
    'placeholder' => __('Enter cutlery preference', 'woocommerce'),
    'default'     => "NO",
    'required'  => false,
    'priority'  => 999
  );

  $fields['billing']['billing_outlet'] = array(
    'type'      => 'hidden',
    'placeholder' => __('Enter outlet location', 'woocommerce'),
    'default'     => $_SESSION['outlet_name'],
    'required'  => true,
    'priority'  => 999
  );

  $fields['billing']['billing_outlet_address'] = array(
    'type'      => 'hidden',
    'placeholder' => __('Enter outlet location', 'woocommerce'),
    'default'     => $_SESSION['outlet_name'],
    'required'  => true,
    'priority'  => 999
  );

  $fields['billing']['billing_date'] = array(
    'type'      => 'hidden',
    'placeholder' => __('Enter pick-up date (e.g., 18 Mar 2025)', 'woocommerce'),
    'default'   => $_SESSION['date'],
    'required'  => true,
    'priority'  => 999
  );

  $fields['billing']['billing_time'] = array(
    'type'      => 'hidden',
    'placeholder' => __('Enter pick-up time (e.g., 11:00 AM)', 'woocommerce'),
    'default'   => 'From ' . $_SESSION['time']['from'] . ' To ' . $_SESSION['time']['to'],
    'required'  => true,
    'priority'  => 999
  );

  $fields['billing']['billing_method_shipping'] = array(
    'type'      => 'hidden',
    'placeholder' => __('Enter pick-up time (e.g., 11:00 AM)', 'woocommerce'),
    'default'   => $_SESSION['order_mode'],
    'required'  => true,
    'priority'  => 999
  );

  $fields['billing']['billing_shipping_fee'] = array(
    'type'      => 'hidden',
    'placeholder' => __('', 'woocommerce'),
    'default'   => $_SESSION['shipping_fee'],
    'required'  => false,
    'priority'  => 999
  );

  $fields['billing']['billing_delivery_address'] = array(
    'type'      => 'hidden',
    'placeholder' => __('', 'woocommerce'),
    'default'   => $_SESSION['delivery_address'],
    'required'  => false,
    'priority'  => 999
  );

  return $fields;
}
add_filter('woocommerce_checkout_fields', 'custom_add_checkout_fields');

//Save custom field billing
function custom_save_checkout_fields($order_id)
{
  $fields = [
    'billing_cutlery',
    'billing_outlet',
    'billing_date',
    'billing_time',
    'billing_outlet_address',
    'billing_method_shipping',
    'billing_shipping_fee',
    'billing_delivery_address',
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
  $methodShipping = get_post_meta($productID, '_billing_method_shipping', true);
  echo '<h4>' . __('Shipping Details', 'woocommerce') . '</h4>';
  echo '<p><strong>Method Shipping:</strong> ' . get_post_meta($productID, '_billing_method_shipping', true) . '</p>';
  echo '<p><strong>Cutlery:</strong> ' . get_post_meta($productID, '_billing_cutlery', true) . '</p>';
  if ($methodShipping == 'delivery') {
    echo '<p><strong>Delivery Address:</strong> ' . get_post_meta($productID, '_billing_delivery_address', true) . '</p>';
  }
  echo '<p><strong>Outlet Name:</strong> ' . get_post_meta($productID, '_billing_outlet', true) . '</p>';
  echo '<p><strong>Outlet Address:</strong> ' . get_post_meta($productID, '_billing_outlet_address', true) . '</p>';
  echo '<p><strong>Date:</strong> ' . get_post_meta($productID, '_billing_date', true) . '</p>';
  echo '<p><strong>Time:</strong> ' . get_post_meta($productID, '_billing_time', true) . '</p>';
}
add_action('woocommerce_admin_order_data_after_shipping_address', 'custom_display_order_meta', 10, 1);

// Add custom field to the product edit page
add_action('woocommerce_product_options_general_product_data', function () {
  woocommerce_wp_text_input([
    'id'                => '_custom_minimum_order_qty',
    'label'             => __('Minimum Order Quantity', 'woocommerce'),
    'description'       => __('Enter the minimum quantity required to add this product to the cart.', 'woocommerce'),
    'type'              => 'number',
    'custom_attributes' => ['step' => '1', 'min' => '1'],
  ]);
});

// Save the custom field value
add_action('woocommerce_process_product_meta', function ($post_id) {
  if (isset($_POST['_custom_minimum_order_qty'])) {
    update_post_meta($post_id, '_custom_minimum_order_qty', absint($_POST['_custom_minimum_order_qty']));
  }
});

// Display minimum order quantity on the product page
add_action('woocommerce_single_product_summary', function () {
  global $product;
  $min_qty = get_post_meta($product->get_id(), '_custom_minimum_order_qty', true);
  if ($min_qty) {
    echo '<p class="custom-min-qty" style="color: red; font-weight: bold;">' . sprintf(__('Minimum order quantity: %d', 'woocommerce'), $min_qty) . '</p>';
  }
}, 25);

add_filter('woocommerce_quantity_input_args', function ($args, $product) {
  $min_qty = get_post_meta($product->get_id(), '_custom_minimum_order_qty', true);

  if ($min_qty) {
    $cart = WC()->cart->get_cart();
    $cart_qty = 0;

    foreach ($cart as $cart_item) {
      if ($cart_item['product_id'] == $product->get_id()) {
        $cart_qty += $cart_item['quantity'];
      }
    }

    $required_qty = max(1, $min_qty - $cart_qty);

    if ($cart_qty < $min_qty) {
      $args['min_value'] = $required_qty;
      $args['input_value'] = $required_qty;
    }
  }

  return $args;
}, 10, 2);
