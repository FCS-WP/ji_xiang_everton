<?php

/**
 * My Addresses
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/my-address.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.3.0
 */

defined('ABSPATH') || exit;

$customer_id = get_current_user_id();
$address_type = 'billing';

$billing_data = [
    'billing_first_name'   => get_user_meta($customer_id, 'billing_first_name', true),
    'billing_last_name'    => get_user_meta($customer_id, 'billing_last_name', true),
    'billing_address_1'    => get_user_meta($customer_id, 'billing_address_1', true),
    'billing_city'         => get_user_meta($customer_id, 'billing_city', true),
    'billing_postcode'     => get_user_meta($customer_id, 'billing_postcode', true),
    'input_latitude_1'     => get_user_meta($customer_id, 'input_latitude_1', true),
    'input_longitude_1'    => get_user_meta($customer_id, 'input_longitude_1', true),
];

if (isset($_POST['update_billing_address']) && wp_verify_nonce($_POST['billing_address_nonce'], 'update_billing_address')) {
    foreach ($billing_data as $key => $value) {
        if (isset($_POST[$key])) {
            update_user_meta($customer_id, $key, sanitize_text_field($_POST[$key]));
        }
    }

    echo '<p class="woocommerce-message">Billing address updated successfully.</p>';
}
?>

<?php if (! wc_ship_to_billing_address_only() && wc_shipping_enabled()) : ?>
    <div class="custom-form-address">
        <div class="custom-woo-address">
            <form method="POST">
                <?php wp_nonce_field('update_billing_address', 'billing_address_nonce'); ?>

                <label for="billing_first_name">First Name:</label>
                <input type="text" name="billing_first_name" id="billing_first_name" value="<?php echo esc_attr($billing_data['billing_first_name']); ?>" required>

                <label for="billing_last_name">Last Name:</label>
                <input type="text" name="billing_last_name" id="billing_last_name" value="<?php echo esc_attr($billing_data['billing_last_name']); ?>" required>

                <label for="billing_postcode">Postal Code:</label>
                <input type="text" name="billing_postcode" id="billing_postcode" value="<?php echo esc_attr($billing_data['billing_postcode']); ?>" required>

                <label for="billing_address_1">Address:</label>
                <input type="text" name="billing_address_1" id="billing_address_1" value="<?php echo esc_attr($billing_data['billing_address_1']); ?>" required>

                <input type="hidden" name="input_latitude_1" id="input_latitude_1" value="<?php echo esc_attr($billing_data['input_latitude_1']); ?>">
                <input type="hidden" name="input_longitude_1" id="input_longitude_1" value="<?php echo esc_attr($billing_data['input_longitude_1']); ?>">

                <button type="submit" name="update_billing_address" class="woocommerce-Button button">Update your address</button>
            </form>

        </div>
    </div>
<?php endif; ?>