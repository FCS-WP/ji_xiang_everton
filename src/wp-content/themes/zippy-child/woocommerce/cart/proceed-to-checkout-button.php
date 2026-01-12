<?php

/**
 * Proceed to checkout button
 *
 * Contains the markup for the proceed to checkout button on the cart.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/proceed-to-checkout-button.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

$total_order = floatval(get_total_cart());
$minimum_order = floatval(zippy_get_minimum_order_by_user_delivery());
?>
<?php if ($total_order < $minimum_order && is_delivery()) : ?>
	<a disabled class="button checkout wc-forward disabled-button-custom">Hit Minimum Order to Checkout</a>
<?php else: ?>
	<?php
	$order_date = !empty(WC()->session->get('date')) ? WC()->session->get('date') : '';
	$order_time = zippy_get_delivery_time();
	?>
	<?php $order_text = sprintf('Proceed to Checkout Page<br>Order for %s </br> %s', format_date_DdMY($order_date),	$order_time);
	?>
	<a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="button checkout wc-forward button-checkout-minicart"><?php echo $order_text; ?></a>

<?php endif; ?>