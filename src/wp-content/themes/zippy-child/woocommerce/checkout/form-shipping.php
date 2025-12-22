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
$delivery_address = !empty(WC()->session->get('delivery_address'))
	? WC()->session->get('delivery_address')
	: $checkout->get_value('shipping_address_1');
$date = WC()->session->get('date');
$time = WC()->session->get('time');

$shipping_city =  WC()->session->get('delivery_address');
if (!empty($delivery_address) && preg_match('/(\d+)\s*$/', $delivery_address, $matches)) {
	$extracted_postcode = $matches[1];
} else {
	$extracted_postcode = $checkout->get_value('shipping_postcode');
	if (empty($extracted_postcode)) {
		$extracted_postcode = '';
	}
}

$blk_no = WC()->session->get('blk_no') ?? '';
$road_name = WC()->session->get('road_name') ?? '';
$building = WC()->session->get('building') != 'NIL' ? WC()->session->get('building') : '';

$shipping_address_1 = implode(' ', [$blk_no, $road_name]);
$shipping_address_2 = implode(' ', [$building, "SINGAPORE", $extracted_postcode])

?>
<?php if (is_delivery()): ?>
	<div class="woocommerce-shipping-fields">
		<?php if (true === WC()->cart->needs_shipping_address()) : ?>

			<div id="ship-to-different-address" style="display: none;">
				<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
					<input
						id="ship-to-different-address-checkbox"
						class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox"
						type="checkbox"
						name="ship_to_different_address"
						value="1"
						checked
						style="display: none;" />
					<h3>Delivery Address</h3>
				</label>
			</div>
			<h3>Delivery Address</h3>
			<div class="shipping_address">

				<?php do_action('woocommerce_before_checkout_shipping_form', $checkout); ?>
				<div class="woocommerce-shipping-fields__field-wrapper">
					<p class="form-row form-row-first">
						<label for="shipping_first_name">First name <abbr class="required" title="required">*</abbr></label>
						<input type="text" class="input-text" required name="shipping_first_name" id="shipping_first_name" value="<?php echo esc_attr($checkout->get_value('shipping_first_name')); ?>" />
					</p>

					<p class="form-row form-row-last">
						<label for="shipping_last_name">Last name <abbr class="required" title="required">*</abbr></label>
						<input type="text" class="input-text" required name="shipping_last_name" id="shipping_last_name" value="<?php echo esc_attr($checkout->get_value('shipping_last_name')); ?>" />
					</p>

					<p class="form-row form-row-first">
						<label for="shipping_unit_number">Unit Number <abbr class="required" title="required">*</abbr></label>
						<input type="text" class="input-text" placeholder="e.g. #18-00" required name="shipping_unit_number" id="shipping_unit_number" value="" />
					</p>

					<p class="form-row form-row-wide">
						<label for="delivery_address">Street address <abbr class="required" title="required">*</abbr></label>
						<input type="text" name="delivery_address" id="delivery_address" class="input-text noborder" readonly required value="<?php echo esc_attr($delivery_address); ?>" />

					</p>

					<p class="form-row form-row-wide">
						<label for="shipping_postcode">Postcode / ZIP <abbr class="required" title="required">*</abbr></label>
						<input type="text" readonly required class="input-text noborder" name="shipping_postcode" id="shipping_postcode" value="<?php echo esc_attr($extracted_postcode); ?>" />
					</p>
					<p class="form-row form-row-wide address-field update_totals_on_change validate-required" id="shipping_country_field" data-priority="40">
						<label for="shipping_country" class="">Country / Region&nbsp;<abbr class="required" title="required">*</abbr></label>
						<span class="woocommerce-input-wrapper"><strong>Singapore</strong>
							<input type="hidden" name="shipping_country" id="shipping_country" value="SG" aria-required="true" autocomplete="country" class="country_to_state" readonly="readonly"></span>
					</p>

					<p class="form-row form-row-wide">
						<label>Date <abbr class="required" title="required">*</abbr></label>
						<span class="date"><?php echo $date; ?></span>
					</p>

					<p class="form-row form-row-wide">
						<label>Time <abbr class="required" title="required">*</abbr></label>
						<span class="time">
							<?php echo zippy_get_delivery_time(); ?>
						</span>
					</p>

					<!-- Hidden fields -->
					<!-- BLOCK + ROAD_NAME -->
					<input type="text" hidden class="input-text" name="shipping_address_1" id="shipping_address_1" required
						value="<?php echo esc_attr($shipping_address_1); ?>" />
					<!-- UNIT_NO + BUILDING -->
					<input type="text" readonly hidden class="input-text noborder" name="shipping_address_2" id="shipping_address_2" value="<?php echo esc_attr($shipping_address_2); ?>" />


				</div>

				<?php do_action('woocommerce_after_checkout_shipping_form', $checkout); ?>

			</div>

		<?php endif; ?>
	</div>
<?php endif; ?>
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
