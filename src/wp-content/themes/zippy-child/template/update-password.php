<?php

if (!is_user_logged_in()) {
    return;
}
?>
<form method="post">
    <h3><?php esc_html_e('Change Password', 'woocommerce'); ?></h3>

    <p>
        <label for="password_current"><?php esc_html_e('Current Password', 'woocommerce'); ?> <span class="required">*</span></label>
        <input type="password" name="password_current" id="password_current" required>
    </p>

    <p>
        <label for="password_1"><?php esc_html_e('New Password', 'woocommerce'); ?> <span class="required">*</span></label>
        <input type="password" name="password_1" id="password_1" required>
    </p>

    <p>
        <label for="password_2"><?php esc_html_e('Confirm New Password', 'woocommerce'); ?> <span class="required">*</span></label>
        <input type="password" name="password_2" id="password_2" required>
    </p>

    <input type="hidden" name="action" value="save_password">
    <button type="submit" class="woocommerce-button button" name="save_password"><?php esc_html_e('Save Password', 'woocommerce'); ?></button>
</form>
<?php

// Handle password update request
add_action('template_redirect', 'handle_custom_password_update');

function handle_custom_password_update() {
    if (isset($_POST['action']) && $_POST['action'] === 'save_password') {
        if (!is_user_logged_in()) {
            return;
        }

        $current_user = wp_get_current_user();
        $current_password = $_POST['password_current'];
        $new_password = $_POST['password_1'];
        $confirm_password = $_POST['password_2'];

        // Verify current password
        if (!wp_check_password($current_password, $current_user->user_pass, $current_user->ID)) {
            wc_add_notice(__('Your current password is incorrect.', 'woocommerce'), 'error');
            return;
        }

        // Validate new passwords match
        if ($new_password !== $confirm_password) {
            wc_add_notice(__('New passwords do not match.', 'woocommerce'), 'error');
            return;
        }

        // Update the password
        wp_set_password($new_password, $current_user->ID);
        wc_add_notice(__('Your password has been updated successfully.', 'woocommerce'));

        // Redirect after successful update
        wp_safe_redirect(wc_get_page_permalink('myaccount'));
        exit;
    }
}