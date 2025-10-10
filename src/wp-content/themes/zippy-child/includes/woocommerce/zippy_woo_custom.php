<?php
add_filter('woocommerce_checkout_fields', 'autofill_from_session');

function autofill_from_session($fields)
{
    if (is_user_logged_in()) {
        $delivery_address = WC()->session->get('delivery_address');

        if (!empty($delivery_address)) {
            $delivery_address = sanitize_text_field($delivery_address);

            if (empty($fields['billing']['billing_address_1']['value'])) {
                $fields['billing']['billing_address_1']['value'] = $delivery_address;
            }

            if (empty($fields['shipping']['shipping_address_1']['value'])) {
                $fields['shipping']['shipping_address_1']['value'] = $delivery_address;
            }
        }
    }

    return $fields;
}
