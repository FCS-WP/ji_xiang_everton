<?php

function custom_add_register_endpoint() {
    add_rewrite_endpoint( 'register', EP_PAGES );
}
add_action( 'init', 'custom_add_register_endpoint' );

function custom_flush_rewrite_rules() {
    custom_add_register_endpoint();
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'custom_flush_rewrite_rules' );

add_action('woocommerce_register_post', 'validate_custom_registration_fields', 10, 3);
function validate_custom_registration_fields($username, $email, $errors) {
    if ($_POST['password'] !== $_POST['confirm_password']) {
        $errors->add('password_mismatch', __('Passwords do not match', 'woocommerce'));
    }

    $required_fields = ['first_name', 'last_name', 'billing_phone', 'billing_address_1', 'billing_postcode'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $errors->add($field . '_missing', sprintf(__('%s is required', 'woocommerce'), ucfirst(str_replace('_', ' ', $field))));
        }
    }
}

add_action('woocommerce_created_customer', 'save_custom_registration_fields');
function save_custom_registration_fields($customer_id) {
    if (!empty($_POST['first_name'])) {
        update_user_meta($customer_id, 'first_name', sanitize_text_field($_POST['first_name']));
        update_user_meta($customer_id, 'billing_first_name', sanitize_text_field($_POST['first_name']));
    }

    if (!empty($_POST['last_name'])) {
        update_user_meta($customer_id, 'last_name', sanitize_text_field($_POST['last_name']));
        update_user_meta($customer_id, 'billing_last_name', sanitize_text_field($_POST['last_name']));
    }

    if (!empty($_POST['billing_phone'])) {
        update_user_meta($customer_id, 'billing_phone', sanitize_text_field($_POST['billing_phone']));
    }

    if (!empty($_POST['billing_address_1'])) {
        update_user_meta($customer_id, 'billing_address_1', sanitize_text_field($_POST['billing_address_1']));
    }

    if (!empty($_POST['billing_postcode'])) {
        update_user_meta($customer_id, 'billing_postcode', sanitize_text_field($_POST['billing_postcode']));
    }
}