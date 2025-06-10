<?php
function get_sub_product_stock_level($main_product_id, $sub_product_id)
{
    $combo = get_field('product_combo', $main_product_id);
    if (!$combo || !is_array($combo)) return null;

    foreach ($combo as $row) {
        if (
            isset($row['product']) &&
            is_object($row['product']) &&
            $row['product']->ID == $sub_product_id
        ) {
            return intval($row['stock_level']);
        }
    }

    return null;
}
//
function update_sub_product_stock_level_in_combo($main_product_id, $sub_product_id, $new_stock_level)
{
    $combo = get_field('product_combo', $main_product_id);
    if (!$combo || !is_array($combo)) return;

    foreach ($combo as $index => $row) {
        if (
            isset($row['product']) &&
            is_object($row['product']) &&
            $row['product']->ID == $sub_product_id
        ) {
            $combo[$index]['stock_level'] = $new_stock_level;

            update_field('product_combo', $combo, $main_product_id);
            break;
        }
    }
}
add_action('woocommerce_order_status_processing', 'akk_reduce_sub_products_stock', 10, 1);
function akk_reduce_sub_products_stock($order_id)
{
    if (!$order_id) return;

    $order = wc_get_order($order_id);
    if (!$order) return;

    foreach ($order->get_items() as $item_id => $item) {
        $akk_selected = $item->get_meta('akk_selected');
        if (!$akk_selected || !is_array($akk_selected)) continue;

        $main_product_id = $item->get_product_id();

        foreach ($akk_selected as $product_id => $qty) {
            if ($qty <= 0) continue;

            $product = wc_get_product($product_id);
            if (!$product || !$product->managing_stock()) continue;

            $acf_stock_level = get_sub_product_stock_level($main_product_id, $product_id);

            if ($acf_stock_level === null) continue;

            $new_stock_level = max(0, $acf_stock_level - $qty);

            $stock_quantity = $product->get_stock_quantity();
            if ($stock_quantity !== null) {
                $product->set_stock_quantity(max(0, $stock_quantity - $qty));
                $product->save();
            }

            update_sub_product_stock_level_in_combo($main_product_id, $product_id, $new_stock_level);
        }
    }
}
// Restore stock
add_action('woocommerce_order_status_cancelled', 'akk_restore_sub_products_stock', 10, 1);
add_action('woocommerce_order_status_refunded', 'akk_restore_sub_products_stock', 10, 1);

function akk_restore_sub_products_stock($order_id)
{
    if (!$order_id) return;

    $order = wc_get_order($order_id);
    if (!$order) return;

    foreach ($order->get_items() as $item) {
        $akk_selected = $item->get_meta('akk_selected');
        if (!$akk_selected || !is_array($akk_selected)) continue;

        foreach ($akk_selected as $product_id => $qty) {
            if ($qty <= 0) continue;

            $product = wc_get_product($product_id);
            if (!$product || !$product->managing_stock()) continue;

            $stock_quantity = $product->get_stock_quantity();
            if ($stock_quantity !== null) {
                $product->set_stock_quantity($stock_quantity + $qty);
                $product->save();
            }
        }
    }
}
