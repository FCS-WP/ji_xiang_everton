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
        'default'     => $_SESSION['selectOutlet'],
        'required'  => true,
        'priority'  => 999
    );

    $fields['billing']['billing_pickup_date'] = array(
        'type'      => 'hidden',
        'placeholder' => __('Enter pick-up date (e.g., 18 Mar 2025)', 'woocommerce'),
        'default'   => $_SESSION['selectDateTakeaway'],
        'required'  => true,
        'priority'  => 999
    );

    $fields['billing']['billing_pickup_time'] = array(
        'type'      => 'hidden',
        'placeholder' => __('Enter pick-up time (e.g., 11:00 AM)', 'woocommerce'),
        'default'   => $_SESSION['selectTakeAwayTime'],
        'required'  => true,
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
        'billing_pickup_date',
        'billing_pickup_time',
    ];

    foreach( $fields as $field){
        if (!empty($_POST[$field])) {
            update_post_meta($order_id, '_' . $field , sanitize_text_field($_POST[$field]));
        }
    }
}
add_action('woocommerce_checkout_update_order_meta', 'custom_save_checkout_fields');

//Display Admin
function custom_display_order_meta($order) {
    $productID = $order->get_id();
    echo '<h3>' . __('Takeaway Details', 'woocommerce') . '</h3>';
    echo '<p><strong>Cutlery:</strong> ' . get_post_meta($productID, '_billing_cutlery', true) . '</p>';
    echo '<p><strong>Outlet:</strong> ' . get_post_meta($productID, '_billing_outlet', true) . '</p>';
    echo '<p><strong>Pick-Up Date:</strong> ' . get_post_meta($productID, '_billing_pickup_date', true) . '</p>';
    echo '<p><strong>Pick-Up Time:</strong> ' . get_post_meta($productID, '_billing_pickup_time', true) . '</p>';
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

// Validate minimum order quantity when adding to cart
add_filter('woocommerce_add_to_cart_validation', function($passed, $product_id, $quantity) {
    $min_qty = get_post_meta($product_id, '_custom_minimum_order_qty', true);
    if ($min_qty && $quantity < $min_qty) {
        wc_add_notice(sprintf(__('You must order at least %d of this product.', 'woocommerce'), $min_qty), 'error');
        return false;
    }
    return $passed;
}, 10, 3);

// Display minimum order quantity on the product page
add_action('woocommerce_single_product_summary', function() {
    global $product;
    $min_qty = get_post_meta($product->get_id(), '_custom_minimum_order_qty', true);
    if ($min_qty) {
        echo '<p class="custom-min-qty" style="color: red; font-weight: bold;">' . sprintf(__('Minimum order quantity: %d', 'woocommerce'), $min_qty) . '</p>';
    }
}, 25);
