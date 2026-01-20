<?php

/**
 * Checkout billing information form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-billing.php.
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
$billing_address_default = $checkout->get_value('billing_address_1');

$billing_address = $billing_address_default
	? $billing_address_default
	: WC()->session->get('delivery_address');
?>

<div class="woocommerce-billing-fields">
	<?php if (wc_ship_to_billing_address_only() && WC()->cart->needs_shipping()) : ?>
		<h3><?php esc_html_e('Billing &amp; Shipping', 'woocommerce'); ?></h3>
	<?php else : ?>
		<h3><?php esc_html_e('Billing details', 'woocommerce'); ?></h3>
	<?php endif; ?>

	<?php do_action('woocommerce_before_checkout_billing_form', $checkout); ?>

	<div class="woocommerce-billing-fields__field-wrapper">
		<?php
		$fields = $checkout->get_checkout_fields('billing');
		?>

		<p class="form-row form-row-first">
			<label for="billing_first_name">
				First name <abbr class="required" title="required">*</abbr>
			</label>
			<input type="text" class="input-text" name="billing_first_name" id="billing_first_name" required
				value="<?php echo esc_attr($checkout->get_value('billing_first_name')); ?>" />
		</p>

		<p class="form-row form-row-last">
			<label for="billing_last_name">
				Last name <abbr class="required" title="required">*</abbr>
			</label>
			<input type="text" class="input-text" name="billing_last_name" id="billing_last_name" required
				value="<?php echo esc_attr($checkout->get_value('billing_last_name')); ?>" />
		</p>

		<p class="form-row form-row-wide">
			<label for="billing_address_1">Street address <abbr class="required" title="required">*</abbr></label>
			<input type="text" class="input-text" name="billing_address_1" id="billing_address_1" required
				value="<?php echo esc_attr($billing_address); ?>" />
		</p>

		<p class="form-row form-row-first">
			<label for="billing_phone">Phone <abbr class="required" title="required">*</abbr></label>
			<input type="tel" class="input-text" name="billing_phone" id="billing_phone" required
				value="<?php echo esc_attr($checkout->get_value('billing_phone')); ?>" />
		</p>

		<p class="form-row form-row-last">
			<label for="billing_email">Email address <abbr class="required" title="required">*</abbr></label>
			<input type="email" class="input-text" name="billing_email" id="billing_email" required
				value="<?php echo esc_attr($checkout->get_value('billing_email')); ?>" />
		</p>
	</div>

	<?php do_action('woocommerce_after_checkout_billing_form', $checkout); ?>
</div>

<?php if (! is_user_logged_in() && $checkout->is_registration_enabled()) : ?>
	<div class="woocommerce-account-fields">
		<?php if (! $checkout->is_registration_required()) : ?>

			<p class="form-row form-row-wide create-account">
				<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
					<input class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" id="createaccount" <?php checked((true === $checkout->get_value('createaccount') || (true === apply_filters('woocommerce_create_account_default_checked', false))), true); ?> type="checkbox" name="createaccount" value="1" /> <span><?php esc_html_e('Create an account?', 'woocommerce'); ?></span>
				</label>
			</p>

		<?php endif; ?>

		<?php do_action('woocommerce_before_checkout_registration_form', $checkout); ?>

		<?php if ($checkout->get_checkout_fields('account')) : ?>

			<div class="create-account">
				<?php foreach ($checkout->get_checkout_fields('account') as $key => $field) : ?>
					<?php woocommerce_form_field($key, $field, $checkout->get_value($key)); ?>
				<?php endforeach; ?>
				<div class="clear"></div>
			</div>

		<?php endif; ?>

		<?php do_action('woocommerce_after_checkout_registration_form', $checkout); ?>
	</div>
<?php endif; ?>
