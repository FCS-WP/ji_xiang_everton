<?php
add_filter('woocommerce_get_item_data', 'akk_display_selected_in_cart', 10, 2);
function akk_display_selected_in_cart($item_data, $cart_item)
{
    $product = $cart_item['data'];

    if (isset($cart_item['akk_selected']) && is_composite_product($product)) {
        foreach ($cart_item['akk_selected'] as $product_id => $qty) {
            if ($qty <= 0) continue;
            $name = get_the_title($product_id);
            $item_data[] = array(
                'name'  => esc_html($name),
                'value' =>  ' x ' . intval($qty[0])
            );
        }
    } else {
        foreach ($cart_item['akk_selected'] as $product_id => $qty) {
            if ($qty <= 0) continue;
            $name = get_the_title($product_id);
            $item_data[] = array(
                'name'  => esc_html($name) . ' (' . get_pricing_price(wc_get_product($product_id), true) . ')',
                'value' =>  ' x ' . intval($qty[0])
            );
        }
    }
    return $item_data;
}
