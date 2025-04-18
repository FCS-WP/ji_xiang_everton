<?php
add_filter('woocommerce_checkout_fields', 'autofill_from_session');

function autofill_from_session($fields)
{
    if (is_user_logged_in() && isset($_SESSION['delivery_address']) && !empty($_SESSION['delivery_address'])) {
        $delivery_address = sanitize_text_field($_SESSION['delivery_address']);

        if (empty($fields['billing']['billing_address_1']['value'])) {
            $fields['billing']['billing_address_1']['value'] = $delivery_address;
        }

        if (empty($fields['shipping']['shipping_address_1']['value'])) {
            $fields['shipping']['shipping_address_1']['value'] = $delivery_address;
        }
    }

    return $fields;
}