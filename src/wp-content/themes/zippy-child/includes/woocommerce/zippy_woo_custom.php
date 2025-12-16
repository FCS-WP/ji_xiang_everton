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

            if (empty($fields['shipping']['shipping_address_2']['value'])) {
                $fields['shipping']['shipping_address_2']['value'] = $delivery_address;
            }
        }
    }

    return $fields;
}

/**
 * Conditionally disables the New Order email if the order has no line items.
 *
 * @param bool     $enabled Whether the email is enabled (true by default).
 * @param WC_Order $order   The WC_Order object.
 * @return bool
 */

function disable_new_order_email_if_empty($enabled, $order)
{
    if (! is_a($order, 'WC_Order')) {
        return $enabled;
    }

    $product_items = $order->get_items('line_item');

    if (empty($product_items)) {
        return false;
    }

    return $enabled;
}

add_filter('woocommerce_email_enabled_new_order', 'disable_new_order_email_if_empty', 10, 2);
