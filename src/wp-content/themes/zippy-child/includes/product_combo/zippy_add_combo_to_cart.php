<?php
add_filter('woocommerce_get_item_data', 'akk_display_selected_in_cart', 10, 2);
function akk_display_selected_in_cart($item_data, $cart_item)
{
    $product     = $cart_item['data'];
    $parent_qty  = isset($cart_item['quantity']) ? intval($cart_item['quantity']) : 1;

    if (isset($cart_item['akk_selected']) && is_composite_product($product)) {
        foreach ($cart_item['akk_selected'] as $product_id => $qty) {
            if ($qty <= 0) continue;
            $name       = get_the_title($product_id);
            $total_qty  = intval($qty[0]) * $parent_qty;
            $item_data[] = array(
                'name'  => esc_html($name),
                'value' => ' x ' . $total_qty
            );
        }
    } else {
        if (isset($cart_item['akk_selected'])) {
            foreach ($cart_item['akk_selected'] as $product_id => $qty) {
                if ($qty <= 0) continue;
                $name       = get_the_title($product_id);
                $total_qty  = intval($qty[0]) * $parent_qty;
                $item_data[] = array(
                    'name'  => esc_html($name) . ' (' . get_pricing_price(wc_get_product($product_id), true) . ')',
                    'value' => ' x ' . $total_qty
                );
            }
        }
    }
    return $item_data;
}
