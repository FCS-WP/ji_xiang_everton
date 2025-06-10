<?php


add_action('woocommerce_before_add_to_cart_button', 'combo_display_sub_products_on_frontend');
function combo_display_sub_products_on_frontend()
{
    global $product;

    $list_sub_products = get_field('product_combo', $product->get_id());
    if (empty($list_sub_products)) return;
    echo '<div class="combo-warning">Please select at least 10 products in the combo.</div>';

    echo '<div class="product-combo" >';
    foreach ($list_sub_products as  $sub_products) {
        if (empty($sub_products) || !is_array($sub_products)) return;

        echo '<div class="akk-sub-products">';

        foreach ($sub_products as $sub_product_post) {
            $product_id = is_object($sub_product_post) ? $sub_product_post->ID : $sub_product_post;
            $sub_product = wc_get_product($product_id);
            $stock_level = $sub_products['stock_level'];
            if (!$sub_product) continue;

            $image_url = get_the_post_thumbnail_url($sub_product->get_id(), 'thumbnail');

            echo '<div class="akk-sub-product">';

            echo '<div class="sub-product-image">';
            echo '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($sub_product->get_name()) . '" width="60">';
            echo '<label>' . esc_html($sub_product->get_name()) . ' (' . wc_price($sub_product->get_price()) . ')</label><br>';
            echo '</div>';

            echo '<div class="sub-product-info">';

            echo render_flatsome_quantity_input($sub_product, $stock_level);

            echo '</div>';

            echo '</div>';
        }

        echo '</div>';
    }
    echo '</div>';

?>
    <script>
        jQuery(document).ready(function($) {
            const $productCombo = $('.product-combo');
            const $qtyInputs = $('.akk-sub-product-qty');
            const $addToCartBtn = $('.single_add_to_cart_button');
            const $comboDisplay = $('#akk-combo-price');
            const $warning = $('.akk-warning');


            function updateComboPrice() {
                let total = 0;
                $qtyInputs.each(function() {
                    const price = parseFloat($(this).data('price')) || 0;
                    const qty = parseFloat($(this).val()) || 0;
                    total += price * qty;
                });

                if ($addToCartBtn.length) {
                    $addToCartBtn.text('Add ' + total.toFixed(1)) + '$';
                }

                if ($comboDisplay.length) {
                    $comboDisplay.text(total);
                }

                let totalQty = 0;
                $qtyInputs.each(function() {
                    totalQty += parseInt($(this).val()) || 0;
                });
                if (totalQty < 10) {
                    $addToCartBtn.prop('disabled', true);
                    $warning.show();
                } else {
                    $addToCartBtn.prop('disabled', false);
                    $warning.hide();
                }
            }

            $qtyInputs.on('input change', updateComboPrice);

            updateComboPrice();

        });
    </script>
<?php
}


add_filter('woocommerce_add_cart_item_data', 'capture_selected_sub_products', 10, 2);
function capture_selected_sub_products($cart_item_data, $product_id)
{
    $list_sub_products = get_field('product_combo', $product_id);
    if (empty($list_sub_products)) return $cart_item_data;
    if (isset($_POST['akk_sub_products']) && is_array($_POST['akk_sub_products'])) {
        $selected = array_filter(array_map('intval', $_POST['akk_sub_products']));
        if (!empty($selected)) {
            $cart_item_data['akk_selected'] = $selected;
            $cart_item_data['unique_key'] = md5(uniqid());
        }
    }
    return $cart_item_data;
}


add_action('woocommerce_before_calculate_totals', 'calculate_combo_price');
function calculate_combo_price($cart)
{
    if (is_admin() && !defined('DOING_AJAX')) return;

    foreach ($cart->get_cart() as $item) {
        if (isset($item['akk_selected'])) {
            $total_price = 0;

            foreach ($item['akk_selected'] as $product_id => $qty) {
                $sub_product = wc_get_product($product_id);
                if ($sub_product && $qty > 0) {
                    $total_price += $sub_product->get_price() * $qty;
                }
            }

            $item['data']->set_price($total_price);
        }
    }
}
