<?php
if (!is_user_logged_in()) {
    echo '<p>You need to log in to update your billing address.</p>';
    return;
}

$current_user_id = get_current_user_id();
$billing_first_name = get_user_meta($current_user_id, 'billing_first_name', true);
$billing_last_name = get_user_meta($current_user_id, 'billing_last_name', true);
$billing_address_1 = get_user_meta($current_user_id, 'billing_address_1', true);
$billing_city = get_user_meta($current_user_id, 'billing_city', true);
$billing_postcode = get_user_meta($current_user_id, 'billing_postcode', true);
$input_latitude_1 = get_user_meta($current_user_id, 'input_latitude_1', true);
$input_longitude_1 = get_user_meta($current_user_id, 'input_longitude_1', true);
?>

<form method="POST">
    <label>First Name:</label>
    <input type="text" name="billing_first_name" value="<?php echo esc_attr($billing_first_name); ?>" required>
    
    <label>Last Name:</label>
    <input type="text" name="billing_last_name" value="<?php echo esc_attr($billing_last_name); ?>" required>
    
    <div id="billing_postcode_field">
        <label>Postal Code:</label>
        <input id="input_postcode" type="text" name="billing_postcode" value="<?php echo esc_attr($billing_postcode); ?>" required>
    </div>

    <label>Address:</label>
    <input id="input_address_1" type="text" name="billing_address_1" value="<?php echo esc_attr($billing_address_1); ?>" required>

    <input id="input_latitude_1" name="input_latitude_1" type="hidden" class="form-control " value="<?php echo $input_latitude_1; ?>">
    <input id="input_longitude_1" name="input_longitude_1" type="hidden" class="form-control " value="<?php echo $input_longitude_1; ?>" >


    <button class="woocommerce-Button button" type="submit" name="update_billing_address">Update your address</button>
</form>

<?php
