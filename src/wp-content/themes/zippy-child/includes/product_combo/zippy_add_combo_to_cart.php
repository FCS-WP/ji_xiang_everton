<?php
add_filter('woocommerce_get_item_data', 'akk_display_selected_in_cart', 10, 2);
function akk_display_selected_in_cart($item_data, $cart_item)
{
    if (isset($cart_item['akk_selected'])) {
        foreach ($cart_item['akk_selected'] as $product_id => $qty) {
            if ($qty <= 0) continue;
            $name = get_the_title($product_id);
            $item_data[] = array(
                'name'  => esc_html($name) . ' (' . wc_price(wc_get_product($product_id)->get_price()) . ')',
                'value' =>  ' x ' . intval($qty)
            );
        }
    }
    return $item_data;
}
