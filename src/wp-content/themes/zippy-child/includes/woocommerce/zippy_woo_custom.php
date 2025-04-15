<?php 
add_filter('woocommerce_checkout_fields', 'autofill_address_line_1_from_session');

function autofill_address_line_1_from_session($fields) {
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

function check_input_value_js() {
    if (is_checkout() && is_user_logged_in() && isset($_SESSION['delivery_address']) && !empty($_SESSION['delivery_address'])) {
        ?>
        <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function() {
                var inputValue = document.getElementById('billing_address_1').value;
                console.log('Billing Address 1 value: ', inputValue);

                if (inputValue === "") {
                    var address = "<?php echo esc_js($_SESSION['delivery_address']); ?>";
                    document.getElementById('billing_address_1').value = address; 
                }
            });
        </script>
        <?php
    }
}

