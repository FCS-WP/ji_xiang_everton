<?php
add_action('woocommerce_checkout_create_order_line_item', 'add_selected_sub_products_to_order_item', 10, 4);
function add_selected_sub_products_to_order_item($item, $cart_item_key, $values, $order)
{
    if (isset($values['akk_selected'])) {
        $item->add_meta_data('akk_selected', $values['akk_selected'], true);
    }
}

add_action('woocommerce_after_order_itemmeta', 'display_sub_products_in_admin_order', 10, 3);
function display_sub_products_in_admin_order($item_id, $item, $product)
{
    $akk_selected = $item->get_meta('akk_selected');
    if (!$akk_selected || !is_array($akk_selected)) return;

    echo '<ul>';
    foreach ($akk_selected as $product_id => $qty) {
        $sub_product = wc_get_product($product_id);
        if (!$sub_product || $qty <= 0) continue;
        echo '<li> <a href="' . esc_url(admin_url('post.php?post=' . $sub_product->get_id() . '&action=edit')) . '" target="_blank">' . esc_html($sub_product->get_name()) . ' (' . wc_price($sub_product->get_price()) .  ')' . ' × ' . intval($qty) . ' </a></li>';
    }
    echo '</ul>';
}
add_action('woocommerce_add_order_item_meta', 'save_packing_instructions_to_order', 10, 3);
function save_packing_instructions_to_order($item_id, $values, $cart_item_key)
{
    if (!empty($values['packing_instructions'])) {
        wc_add_order_item_meta($item_id, 'Packing Instructions', $values['packing_instructions']);
    }
}
