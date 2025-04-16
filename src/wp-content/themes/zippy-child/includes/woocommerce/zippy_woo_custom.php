<?php
add_filter('woocommerce_checkout_fields', 'autofill_from_session');

function autofill_from_session($fields)
{
    if (is_user_logged_in() && isset($_SESSION['delivery_address']) && !empty($_SESSION['delivery_address'])) {
        $delivery_address = sanitize_text_field($_SESSION['delivery_address']);

        if (empty($fields['billing']['billing_address_1']['value'])) {
            $fields['billing']['billing_address_1']['value'] = $delivery_address;
        }

        if (empty($fields['shipping']['shipping_address_1']['value'])) {
            $fields['shipping']['shipping_address_1']['value'] = $delivery_address;
        }
    }

    return $fields;
}

add_action('wp_footer', 'check_input_value_js');

function check_input_value_js()
{
    if (is_checkout() && is_user_logged_in() && isset($_SESSION['delivery_address']) && !empty($_SESSION['delivery_address'])) {
     
?>
       <script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        var billingField = document.getElementById('billing_address_1');
        var shippingField = document.getElementById('shipping_address_1');
        var postcodeField = document.getElementById('shipping_postcode');

        var address = "<?php echo esc_js($_SESSION['delivery_address']); ?>";

        if (billingField && billingField.value === "") {
            billingField.value = address;
        }

        if (shippingField && shippingField.value === "") {
            shippingField.value = address;
        }

        if (postcodeField) {
            var match = address.match(/(\d+)\s*$/);
            var postcode = match ? match[1] : '';
            
            postcodeField.value = postcode;
        }

        [shippingField, postcodeField].forEach(function(input) {
            if (input) {
                input.setAttribute('readonly', true);
                input.style.backgroundColor = '#f5f5f5';
                input.style.cursor = 'not-allowed';
            }
        });
    });
</script>

<?php
    }
}
add_filter('gettext', 'bbloomer_translate_shippingtodiffaddr', 9999, 3);

function bbloomer_translate_shippingtodiffaddr($translated, $untranslated, $domain)
{
    if (! is_admin() && 'woocommerce' === $domain) {
        switch ($untranslated) {
            case 'Ship to a different address?':
                $translated = 'Delivery Address ';
                break;
        }
    }
    return $translated;
}
