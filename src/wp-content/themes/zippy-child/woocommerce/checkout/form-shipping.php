<?php

/**
 * Checkout shipping information form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-shipping.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 * @global WC_Checkout $checkout
 */

defined('ABSPATH') || exit;

$delivery_address = isset($_SESSION['shipping_address_1'])
	? $_SESSION['shipping_address_1']
	: $checkout->get_value('shipping_address_1');

// Tách postcode từ địa chỉ nếu có
if (!empty($delivery_address) && preg_match('/(\d+)\s*$/', $delivery_address, $matches)) {
	$extracted_postcode = $matches[1];
} else {
	$extracted_postcode = $checkout->get_value('shipping_postcode');
	if (empty($extracted_postcode)) {
		$extracted_postcode = '';
	}
}
?>
<div class="woocommerce-shipping-fields">
	<?php if (true === WC()->cart->needs_shipping_address()) : ?>

		<h3 id="ship-to-different-address">
			Delivery Address
		</h3>

		<div class="shipping_address">

			<?php do_action('woocommerce_before_checkout_shipping_form', $checkout); ?>
			<div class="woocommerce-shipping-fields__field-wrapper">
				<p class="form-row form-row-first">
					<label for="shipping_first_name">First name <abbr class="required" title="required">*</abbr></label>
					<input type="text" class="input-text" required  name="shipping_first_name" id="shipping_first_name" value="<?php echo esc_attr($checkout->get_value('shipping_first_name')); ?>" />
				</p>

				<p class="form-row form-row-last">
					<label for="shipping_last_name">Last name <abbr class="required" title="required">*</abbr></label>
					<input type="text" class="input-text" required  name="shipping_last_name" id="shipping_last_name" value="<?php echo esc_attr($checkout->get_value('shipping_last_name')); ?>" />
				</p>

				<p class="form-row form-row-wide">
					<label for="shipping_address_1">Street address <abbr class="required" title="required">*</abbr></label>
					<input type="text" readonly required  class="input-text" name="shipping_address_1" id="shipping_address_1" value="<?php echo $delivery_address; ?>" />
				</p>

				<p class="form-row form-row-wide">
					<label for="shipping_postcode">Postcode / ZIP <abbr class="required" title="required">*</abbr></label>
					<input type="text" readonly required class="input-text" name="shipping_postcode" id="shipping_postcode" value="<?php echo $extracted_postcode; ?>" />
				</p>
			</div>


			<?php do_action('woocommerce_after_checkout_shipping_form', $checkout); ?>

		</div>

	<?php endif; ?>
</div>
<div class="woocommerce-additional-fields">
	<?php do_action('woocommerce_before_order_notes', $checkout); ?>

	<?php if (apply_filters('woocommerce_enable_order_notes_field', 'yes' === get_option('woocommerce_enable_order_comments', 'yes'))) : ?>

		<?php if (! WC()->cart->needs_shipping() || wc_ship_to_billing_address_only()) : ?>

			<h3><?php esc_html_e('Additional information', 'woocommerce'); ?></h3>

		<?php endif; ?>

		<div class="woocommerce-additional-fields__field-wrapper">
			<?php foreach ($checkout->get_checkout_fields('order') as $key => $field) : ?>
				<?php woocommerce_form_field($key, $field, $checkout->get_value($key)); ?>
			<?php endforeach; ?>
		</div>

	<?php endif; ?>

	<?php do_action('woocommerce_after_order_notes', $checkout); ?>
</div>