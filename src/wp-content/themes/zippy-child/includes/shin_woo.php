<?php

add_action( 'woocommerce_widget_shopping_cart_buttons', function(){
    // Removing Buttons
    remove_action( 'woocommerce_widget_shopping_cart_buttons', 'woocommerce_widget_shopping_cart_proceed_to_checkout', 20 );

    // Adding customized Buttons
    add_action( 'woocommerce_widget_shopping_cart_buttons', 'custom_widget_shopping_cart_proceed_to_checkout', 20 );
}, 1 );


// Custom Checkout button
function custom_widget_shopping_cart_proceed_to_checkout() {
    
    $subtotal = WC()->cart->get_subtotal();
    $total_delivery = 0;
    echo do_shortcode('[script_js_minicart]');
    if($subtotal < $total_delivery){
        return;
    }else{
        $original_link = wc_get_checkout_url();
        $custom_link = home_url( '/checkout' ); // HERE replacing checkout link
        echo '<a href="' . esc_url( $custom_link ) . '" class="button checkout wc-forward">' . esc_html__( 'Checkout', 'woocommerce' ) . '</a>';
    }
}


function script_js_minicart(){
    $total_quantity = WC()->cart->get_cart_contents_count();
    ?>
    <script>
    "use strict";
    $ = jQuery;

    $(document).ready(function($) {
        let priceText = $('.woocommerce-mini-cart__total .woocommerce-Price-amount bdi').text();
        let subTotalPriceValue = parseFloat(priceText.replace(/[^0-9.]/g, ''));
        let dataDelivery = $('#minimunOrder').attr('dataDelivery');
        let dataFreeship = $('#freeDelivery').attr('dataFreeship');
        let elementMinimunOrder = $('#minimunOrder');
        let elementFreeship = $('#freeDelivery');
        let elementDeliveryNeedMore = $('#deliveryNeedMore');
        let elementFreeshipNeedMore = $('#freeshipNeedMore');
        let elementTotal_quanity_cart = $('#total_quanity_cart');
        
        let widthPercentageDelivery = (subTotalPriceValue / dataDelivery) * 100;
        widthPercentageDelivery = Math.min(widthPercentageDelivery, 100);

        let widthPercentageFreeship = (subTotalPriceValue / dataFreeship) * 100;
        widthPercentageFreeship = Math.min(widthPercentageFreeship, 100);

        elementMinimunOrder.css('width', widthPercentageDelivery + '%');
        elementFreeship.css('width', widthPercentageFreeship + '%');

        if((dataDelivery - subTotalPriceValue) <= 0){
            elementDeliveryNeedMore.text('0');
        }else{
            elementDeliveryNeedMore.text((dataDelivery - subTotalPriceValue).toFixed(2));
        }

        if((dataFreeship - subTotalPriceValue) <= 0){
            elementFreeshipNeedMore.text('0');
        }else{
            elementFreeshipNeedMore.text((dataFreeship - subTotalPriceValue).toFixed(2));
        }

        elementTotal_quanity_cart.text(<?php echo $total_quantity; ?>);
        
    }); 
    </script>
    <?php
}
add_shortcode('script_js_minicart','script_js_minicart');

function rule_minimun_checkout_on_cart_page(){
    $subtotal = WC()->cart->get_subtotal();
    $total_delivery = 0;
    if($subtotal < $total_delivery){
        remove_action( 'woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20 );    
    }else{
        return;
    }
    
}
add_action('woocommerce_after_calculate_totals','rule_minimun_checkout_on_cart_page');

function rule_minimun_checkout_all_site() {
    $subtotal = WC()->cart->get_subtotal();
    $total_delivery = 0;
    if (is_page('checkout') && ($subtotal < $total_delivery)) {
        wp_redirect(home_url());
        exit;
    }else{
        return;
    }
}
add_action('template_redirect', 'rule_minimun_checkout_all_site');

function add_custom_product_fields() {
    add_meta_box(
        'product_availability_dates',
        'Product Availability',
        'custom_product_fields_callback',
        'product',
        'normal',
        'high'
    );
}

function custom_product_fields_callback($post) {
    $start_date = get_post_meta($post->ID, '_start_date_available', true);
    $end_date = get_post_meta($post->ID, '_end_date_available', true);
    ?>
    <p>
        <label for="start_date_available">Start Date Available:</label>
        <input type="date" id="start_date_available" name="start_date_available" value="<?php echo esc_attr($start_date); ?>" />
    </p>
    <p>
        <label for="end_date_available">End Date Available:</label>
        <input type="date" id="end_date_available" name="end_date_available" value="<?php echo esc_attr($end_date); ?>" />
    </p>
    <?php
}

add_action('add_meta_boxes', 'add_custom_product_fields');

function save_custom_product_fields($post_id) {
    if (isset($_POST['start_date_available'])) {
        update_post_meta($post_id, '_start_date_available', sanitize_text_field($_POST['start_date_available']));
    }
    if (isset($_POST['end_date_available'])) {
        update_post_meta($post_id, '_end_date_available', sanitize_text_field($_POST['end_date_available']));
    }
}

add_action('save_post_product', 'save_custom_product_fields');

//Edit checkout page
function custom_add_checkout_fields($fields) {
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
function custom_save_checkout_fields($order_id) {
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

    foreach( $fields as $field){
        if (!empty($_POST[$field])) {
            update_post_meta($order_id, '_' . $field , sanitize_text_field($_POST[$field]));
        }
    }
}
add_action('woocommerce_checkout_update_order_meta', 'custom_save_checkout_fields');

add_filter('woocommerce_package_rates', 'custom_fixed_shipping_cost', 10, 2);

function custom_fixed_shipping_cost($rates, $package) {
    if (!isset($_SESSION["order_mode"])) {
        return $rates; 
    }

    $order_mode = $_SESSION["order_mode"];
    $shipping_fee = $_SESSION["shipping_fee"] ?? 0;

    foreach ($rates as $rate_id => $rate) {
        if ($order_mode === 'takeaway') {
            $rates[$rate_id]->label = 'Takeaway Fee';
            $rates[$rate_id]->cost = 0;
        } else {
            $rates[$rate_id]->label = 'Delivery Fee';
            $rates[$rate_id]->cost = $shipping_fee;
        }
    }

    return $rates;
}

//Display Admin
function custom_display_order_meta($order) {
    $productID = $order->get_id();
    $methodShipping = get_post_meta($productID, '_billing_method_shipping', true);
    echo '<h3>' . __('Shipping Details', 'woocommerce') . '</h3>';
    echo '<p><strong>Method Shipping:</strong> ' . get_post_meta($productID, '_billing_method_shipping', true) . '</p>';
    echo '<p><strong>Cutlery:</strong> ' . get_post_meta($productID, '_billing_cutlery', true) . '</p>';
    if($methodShipping == 'delivery'){
        echo '<p><strong>Delivery Address:</strong> ' . get_post_meta($productID, '_billing_delivery_address', true) . '</p>';
    }
    echo '<p><strong>Outlet Name:</strong> ' . get_post_meta($productID, '_billing_outlet', true) . '</p>';
    echo '<p><strong>Outlet Address:</strong> ' . get_post_meta($productID, '_billing_outlet_address', true) . '</p>';
    echo '<p><strong>Date:</strong> ' . get_post_meta($productID, '_billing_date', true) . '</p>';
    echo '<p><strong>Time:</strong> ' . get_post_meta($productID, '_billing_time', true) . '</p>';
}
add_action('woocommerce_admin_order_data_after_billing_address', 'custom_display_order_meta', 10, 1);

// Add custom field to the product edit page
add_action('woocommerce_product_options_general_product_data', function() {
    woocommerce_wp_text_input([
        'id'                => '_custom_minimum_order_qty',
        'label'             => __('Minimum Order Quantity', 'woocommerce'),
        'description'       => __('Enter the minimum quantity required to add this product to the cart.', 'woocommerce'),
        'type'              => 'number',
        'custom_attributes' => ['step' => '1', 'min' => '1'],
    ]);
});

// Save the custom field value
add_action('woocommerce_process_product_meta', function($post_id) {
    if (isset($_POST['_custom_minimum_order_qty'])) {
        update_post_meta($post_id, '_custom_minimum_order_qty', absint($_POST['_custom_minimum_order_qty']));
    }
});

// Display minimum order quantity on the product page
add_action('woocommerce_single_product_summary', function() {
    global $product;
    $min_qty = get_post_meta($product->get_id(), '_custom_minimum_order_qty', true);
    if ($min_qty) {
        echo '<p class="custom-min-qty" style="color: red; font-weight: bold;">' . sprintf(__('Minimum order quantity: %d', 'woocommerce'), $min_qty) . '</p>';
    }
}, 25);

add_action( 'wp', 'add_to_cart_from_session' );

function add_to_cart_from_session() {
    if (empty($_SESSION) || empty($_SESSION['product_id'])) {
        return;
    }

    if ( ! class_exists( 'WooCommerce' ) || ! WC()->cart ) {
        return;
    }

    if ( isset( $_SESSION['product_id'] ) ) {
        $product_id = intval( $_SESSION['product_id'] );
        $min_qty = get_post_meta($product_id, '_custom_minimum_order_qty', true);
        $quantity = ($min_qty && $min_qty > 0 ? $min_qty : 1);

        $product = wc_get_product( $product_id );
        if ( $product ) {
            WC()->cart->add_to_cart( $product_id, $quantity );
            // Delete session after add to cart
            unset( $_SESSION['product_id'] );
        }
    }
}

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
            $args['input_value'] = $required_qty; // Đặt giá trị mặc định của input
        }
    }

    return $args;
}, 10, 2);
