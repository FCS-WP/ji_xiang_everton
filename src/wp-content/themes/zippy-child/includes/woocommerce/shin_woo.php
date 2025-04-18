<?php




//Edit checkout page
add_action('woocommerce_after_order_notes', 'custom_hidden_fields_from_session');
function custom_hidden_fields_from_session($checkout) {
    ?>
    <input type="hidden" name="billing_cutlery" id="billing_cutlery" value="NO">
    <input type="hidden" name="billing_outlet" id="billing_outlet" value="<?php echo esc_attr($_SESSION['outlet_name'] ?? ''); ?>">
    <input type="hidden" name="billing_outlet_address" id="billing_outlet_address" value="<?php echo esc_attr($_SESSION['outlet_address'] ?? ''); ?>">
    <input type="hidden" name="billing_date" id="billing_date" value="<?php echo esc_attr($_SESSION['date'] ?? ''); ?>">
    <input type="hidden" name="billing_time" id="billing_time" value="<?php echo esc_attr('From ' . ($_SESSION['time']['from'] ?? '') . ' To ' . ($_SESSION['time']['to'] ?? '')); ?>">
    <input type="hidden" name="billing_method_shipping" id="billing_method_shipping" value="<?php echo esc_attr($_SESSION['order_mode'] ?? ''); ?>">
    <input type="hidden" name="billing_shipping_fee" id="billing_shipping_fee" value="<?php echo esc_attr($_SESSION['shipping_fee'] ?? ''); ?>">
    <?php
}

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


add_action('woocommerce_checkout_process', 'validate_required_session_data');

add_filter( 'woocommerce_cart_needs_shipping', 'custom_force_cart_not_need_shipping' );
function custom_force_cart_not_need_shipping( $needs_shipping ) {
	if ( isset($_SESSION['order_mode']) && $_SESSION['order_mode'] === 'takeaway' ) {
		return false;
	}
	return $needs_shipping;
}

function validate_required_session_data() {
    $required_sessions = [
        'outlet_name',
        'date',
        'time'
    ];

    if (isset($_SESSION['order_mode']) && $_SESSION['order_mode'] === 'delivery') {
        $required_sessions[] = 'delivery_address';
    }

    foreach ($required_sessions as $session_key) {
        if (!isset($_SESSION[$session_key]) || empty($_SESSION[$session_key])) {
            wc_add_notice(__('Missing required information: ' . $session_key, 'woocommerce'), 'error');
        }
    }

    if (
        isset($_SESSION['time']) &&
        (!isset($_SESSION['time']['from']) || !isset($_SESSION['time']['to']) || empty($_SESSION['time']['from']) || empty($_SESSION['time']['to']))
    ) {
        wc_add_notice(__('Missing required delivery time information.', 'woocommerce'), 'error');
    }
}
