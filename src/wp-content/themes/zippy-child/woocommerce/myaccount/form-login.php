<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

do_action('woocommerce_before_customer_login_form');

// Lấy endpoint hiện tại
global $wp;
$current_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
?>

<div class="custom-login-container">
    <?php
    if (rtrim($current_path, '/') === '/my-account/register') {
        wc_get_template('myaccount/form-register-custom.php');
    } else {
        wc_get_template('myaccount/form-login-custom.php');
    }
    ?>
</div>

<?php do_action('woocommerce_after_customer_login_form'); ?>