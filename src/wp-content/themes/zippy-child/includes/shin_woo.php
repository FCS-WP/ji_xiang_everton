<?php
function set_minimum_quantity_10($args, $product) {
    $args['min_value'] = 10;
    return $args;
}
add_filter('woocommerce_quantity_input_args', 'set_minimum_quantity_10', 10, 2);

add_action( 'woocommerce_widget_shopping_cart_buttons', function(){
    // Removing Buttons
    remove_action( 'woocommerce_widget_shopping_cart_buttons', 'woocommerce_widget_shopping_cart_proceed_to_checkout', 20 );

    // Adding customized Buttons
    add_action( 'woocommerce_widget_shopping_cart_buttons', 'custom_widget_shopping_cart_proceed_to_checkout', 20 );
}, 1 );


// Custom Checkout button
function custom_widget_shopping_cart_proceed_to_checkout() {
    
    $subtotal = WC()->cart->get_subtotal();
    $total_delivery = 100;
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
    $total_delivery = 100;
    if($subtotal < $total_delivery){
        remove_action( 'woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20 );    
    }else{
        return;
    }
    
}
add_action('woocommerce_after_calculate_totals','rule_minimun_checkout_on_cart_page');

function rule_minimun_checkout_all_site() {
    $subtotal = WC()->cart->get_subtotal();
    $total_delivery = 100;
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


