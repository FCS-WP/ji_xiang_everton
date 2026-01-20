<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<?php wc_print_notices(); ?>

<div class="custom-woo-column">
    <h2 class="text-center" style="margin-bottom: 1.5rem;">Register</h2>
    <form method="post" class="woocommerce-form woocommerce-form-register register" <?php do_action( 'woocommerce_register_form_tag' ); ?>>

        <?php do_action( 'woocommerce_register_form_start' ); ?>

        <p class="woocommerce-form-row form-row form-row-first">
            <label for="first_name"><?php esc_html_e( 'First Name', 'woocommerce' ); ?> <span class="required">*</span></label>
            <input type="text" name="first_name" id="first_name" class="woocommerce-Input input-text"
                value="<?php echo isset($_POST['first_name']) ? esc_attr($_POST['first_name']) : ''; ?>" required />
        </p>

        <p class="woocommerce-form-row form-row form-row-last">
            <label for="last_name"><?php esc_html_e( 'Last Name', 'woocommerce' ); ?> <span class="required">*</span></label>
            <input type="text" name="last_name" id="last_name" class="woocommerce-Input input-text"
                value="<?php echo isset($_POST['last_name']) ? esc_attr($_POST['last_name']) : ''; ?>" required />
        </p>

        <div class="clear"></div>

        <p class="woocommerce-form-row form-row form-row-wide">
            <label for="reg_email"><?php esc_html_e( 'Email address', 'woocommerce' ); ?> <span class="required">*</span></label>
            <input type="email" name="email" id="reg_email" autocomplete="email" class="woocommerce-Input input-text"
                value="<?php echo isset($_POST['email']) ? esc_attr($_POST['email']) : ''; ?>" required />
        </p>

        <p class="woocommerce-form-row form-row form-row-wide">
            <label for="billing_phone"><?php esc_html_e( 'Phone', 'woocommerce' ); ?> <span class="required">*</span></label>
            <input type="tel" name="billing_phone" id="billing_phone" class="woocommerce-Input input-text"
                value="<?php echo isset($_POST['billing_phone']) ? esc_attr($_POST['billing_phone']) : ''; ?>" required />
        </p>

        <p class="woocommerce-form-row form-row form-row-wide">
            <label for="billing_postcode"><?php esc_html_e( 'Postal Code', 'woocommerce' ); ?> <span class="required">*</span></label>
            <input type="text" name="billing_postcode" id="billing_postcode" class="woocommerce-Input input-text"
                value="<?php echo isset($_POST['billing_postcode']) ? esc_attr($_POST['billing_postcode']) : ''; ?>" required />
        </p>

        <p class="woocommerce-form-row form-row form-row-wide">
            <label for="billing_address_1"><?php esc_html_e( 'Billing Address', 'woocommerce' ); ?> <span class="required">*</span></label>
            <input type="text" name="billing_address_1" id="billing_address_1" class="woocommerce-Input input-text"
                value="<?php echo isset($_POST['billing_address_1']) ? esc_attr($_POST['billing_address_1']) : ''; ?>" required />
        </p>

        <p class="woocommerce-form-row form-row form-row-wide">
            <label for="reg_password"><?php esc_html_e( 'Password', 'woocommerce' ); ?> <span class="required">*</span></label>
            <input type="password" name="password" id="reg_password" class="woocommerce-Input input-text" required />
        </p>

        <p class="woocommerce-form-row form-row form-row-wide">
            <label for="confirm_password"><?php esc_html_e( 'Confirm Password', 'woocommerce' ); ?> <span class="required">*</span></label>
            <input type="password" name="confirm_password" id="confirm_password" class="woocommerce-Input input-text" required />
        </p>

        <?php do_action( 'woocommerce_register_form' ); ?>

        <p class="woocommerce-form-row form-row">
            <?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
            <button type="submit" class="woocommerce-Button woocommerce-button button woocommerce-form-register__submit" name="register" value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>">
                <?php esc_html_e( 'Register', 'woocommerce' ); ?>
            </button>
        </p>

        <?php do_action( 'woocommerce_register_form_end' ); ?>

    </form>
</div>
