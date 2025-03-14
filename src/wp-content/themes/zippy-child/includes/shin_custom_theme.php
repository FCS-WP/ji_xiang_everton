<?php

// Add short description and price after product title in loop
add_action('woocommerce_after_shop_loop_item_title', 'custom_product_short_description_and_price', 15);

function custom_product_short_description_and_price() {
    global $product;
    $date = new DateTime();
    
    $product_id = $product->get_id();
    $start_date = get_post_meta($product_id, '_start_date_available', true);
    $end_date = get_post_meta($product_id, '_end_date_available', true);

    if(empty($start_date)){
        $start_date = $date->format("Y-m-d");     
    }

    $formatted_date_begin = date("d M y", strtotime($start_date));

    if(empty($end_date)){
        $formatted_date_end = ' to future';
    }else{
        $formatted_date_end = ' to '. date("d M y", strtotime($end_date));  
    }

    

    $available_date_text = 'Available for order<br>from '. $formatted_date_begin . $formatted_date_end;
    
    echo '<div class="product-text-available">' . $available_date_text . '</div>';

    // Display short description
    if ($product->get_short_description()) {
        echo '<div class="product-short-description">' . wp_trim_words($product->get_short_description(), 20) . '</div>';
    }

    // Display product price
    echo '<div class="product-price">' . $product->get_price_html() . '</div>';
    
    // Display add to cart
    
    if($_SESSION['status_popup'] != true){
        echo '<div class="cta_add_to_cart"><a class="action-popup-btn" productID="' . $product_id . '" href="#order-popup-' . $product_id . '">Add</a></div>';
    }else{
        echo do_shortcode('[quickview_button]');
    }
    echo do_shortcode('[lightbox id="order-popup-' . $product_id . '" width="550px" padding="15px" ][block id="delivery-takeaway"][/lightbox]');
}


function handle_user_registration() {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        
        if (isset($_POST['firstname'], $_POST['lastname'], $_POST['email'], $_POST['password'], $_POST['confirm'], $_POST['input_latitude_1'], $_POST['input_longitude_1'])) {
            
            $firstname = sanitize_text_field($_POST['firstname']);
            $lastname = sanitize_text_field($_POST['lastname']);
            $email = sanitize_email($_POST['email']);
            $telephone = sanitize_text_field($_POST['telephone']);
            $birthday = sanitize_text_field($_POST['birthday']);
            $gender = sanitize_text_field($_POST['gender']);
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm'];
            $postcode = sanitize_text_field($_POST['postcode']);
            $address_1 = sanitize_text_field($_POST['address_1']);
            $input_latitude_1 = sanitize_text_field($_POST['input_latitude_1']);
            $input_longitude_1 = sanitize_text_field($_POST['input_longitude_1']);
            $newsletter = isset($_POST['newsletter']) ? 1 : 0;
            $agree = isset($_POST['agree']) ? 1 : 0;
            
            if (empty($firstname) || empty($lastname) || empty($email) || empty($password) || empty($confirm_password)) {
                echo "<script>alert('Please fill in all required information.');</script>";
                return;
            }

            if (!is_email($email)) {
                echo "<script>alert('Invalid email.');</script>";
                return;
            }

            if ($password !== $confirm_password) {
                echo "<script>alert('Confirmation password does not match.');</script>";
                return;
            }

            
            if (email_exists($email)) {
                echo "<script>alert('This email has been registered.');</script>";
                return;
            }

            
            $user_id = wp_create_user($email, $password, $email);

            

            update_user_meta($user_id, 'billing_first_name', $firstname);
            update_user_meta($user_id, 'billing_last_name', $lastname);
            update_user_meta($user_id, 'billing_phone', $telephone);
            update_user_meta($user_id, 'birthday', $birthday);
            update_user_meta($user_id, 'gender', $gender);
            update_user_meta($user_id, 'postcode', $postcode);
            update_user_meta($user_id, 'billing_address_1', $address_1);
            update_user_meta($user_id, 'input_latitude_1', $input_latitude_1);
            update_user_meta($user_id, 'input_longitude_1', $input_longitude_1);
            update_user_meta($user_id, 'billing_email', $email);

            
            wp_set_current_user($user_id);
            wp_set_auth_cookie($user_id);

            echo "<script>alert('Register successfully');</script>";
            return;
        }
    }
}
add_action('init', 'handle_user_registration');


function add_custom_billing_fields_to_woocommerce($fields) {
    $fields['billing']['fields']['birthday'] = array(
        'label'       => 'Date of birth',
        'description' => 'Customer date of birth.',
        'type'        => 'date',
        'show'        => true
    );

    $fields['billing']['fields']['gender'] = array(
        'label'       => 'Gender',
        'description' => 'Customer gender.',
        'type'        => 'select',
        'options'     => array(
            'male'   => 'Male',
            'female' => 'Female',
            'other'  => 'Other'
        ),
        'show'        => true
    );

    $fields['billing']['fields']['input_latitude_1'] = array(
        'label'       => 'Latitude',
        'description' => 'Latitude of billing address.',
        'type'        => 'text',
        'show'        => true
    );

    $fields['billing']['fields']['input_longitude_1'] = array(
        'label'       => 'Longitude',
        'description' => 'Longitude of billing address.',
        'type'        => 'text',
        'show'        => true
    );

    return $fields;
}
add_filter('woocommerce_customer_meta_fields', 'add_custom_billing_fields_to_woocommerce');

function save_custom_fields_on_edit_account($user_id) {
    if (isset($_POST['birthday'])) {
        update_user_meta($user_id, 'birthday', sanitize_text_field($_POST['birthday']));
    }

    if (isset($_POST['gender'])) {
        update_user_meta($user_id, 'gender', sanitize_text_field($_POST['gender']));
    }

    if (isset($_POST['postcode'])) {
        update_user_meta($user_id, 'postcode', sanitize_text_field($_POST['postcode']));
    }

    if (isset($_POST['input_latitude_1'])) {
        update_user_meta($user_id, 'input_latitude_1', sanitize_text_field($_POST['input_latitude_1']));
    }

    if (isset($_POST['input_longitude_1'])) {
        update_user_meta($user_id, 'input_longitude_1', sanitize_text_field($_POST['input_longitude_1']));
    }
}
add_action('woocommerce_save_account_details', 'save_custom_fields_on_edit_account');


function remove_checkout_fields($fields) {
    unset($fields['billing']['billing_company']);  
    unset($fields['billing']['billing_address_2']); 
    unset($fields['billing']['billing_city']);      
    unset($fields['billing']['billing_state']);   
    unset( $fields['order'] ); 

    return $fields;
}
add_filter('woocommerce_checkout_fields', 'remove_checkout_fields');

function remove_checkout_coupon_form() {
    remove_action('woocommerce_checkout_order_review', 'woocommerce_order_review', 10);

}
add_action('wp', 'remove_checkout_coupon_form');

function flatsome_custom_quickview_button($atts) {
    global $product;

    if (!$product) return '';

    $product_id = $product->get_id();

    $button = '<div class="cta_add_to_cart"><a href="#" class="quick-view" 
                  data-prod="' . esc_attr($product_id) . '" 
                  data-toggle="quick-view">
                  Add
               </a></div>';

    return $button;
}
add_shortcode('quickview_button', 'flatsome_custom_quickview_button');
