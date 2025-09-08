<?php
add_action('woocommerce_checkout_create_order_line_item', 'add_selected_sub_products_to_order_item', 10, 4);
function add_selected_sub_products_to_order_item($item, $cart_item_key, $values, $order)
{
    if (isset($values['akk_selected'])) {
        $item->add_meta_data('akk_selected', $values['akk_selected'], true);
    }
    if (!empty($values['packing_instructions'])) {
        $item->add_meta_data('packing_instructions', $values['packing_instructions'], true);
    }
}
add_filter('woocommerce_hidden_order_itemmeta', function ($hidden_meta) {
    $hidden_meta[] = 'packing_instructions';
    return $hidden_meta;
});
add_filter('woocommerce_order_item_get_formatted_meta_data', function ($formatted_meta, $item) {
    foreach ($formatted_meta as $key => $meta) {
        if ($meta->key === 'packing_instructions') {
            unset($formatted_meta[$key]);
        }
    }
    return $formatted_meta;
}, 10, 2);


add_action('woocommerce_after_order_itemmeta', 'display_sub_products_in_admin_order', 10, 3);
function display_sub_products_in_admin_order($item_id, $item, $product)
{
    $akk_selected = $item->get_meta('akk_selected');
    // pr($akk_selected);
    $packing = $item->get_meta('packing_instructions');
    if ($akk_selected && is_array($akk_selected)) {
        echo '<p><strong>Combo Products:</strong></p>';
        echo '<ul>';
        foreach ($akk_selected as $product_id => $qty) {
            $sub_product = wc_get_product($product_id);
            if(is_array($qty)) $qty = $qty[0];
            if (!$sub_product || $qty <= 0) continue;
            $price = empty($qty[1]) ? $sub_product->get_price() : $qty[1];
            echo '<li><a href="' . esc_url(admin_url('post.php?post=' . $sub_product->get_id() . '&action=edit')) . '" target="_blank">'
                . esc_html($sub_product->get_name()) . ' (' . wc_price($price) . ') × ' . intval($qty) . '</a></li>';
        }
        echo '</ul>';
    }
    if (!empty($packing)) {
        echo '<p><strong>Packing Instructions:</strong> ' . esc_html($packing) . '</p>';
    }
}


add_action('woocommerce_order_item_meta_end', 'show_combo_below_item_in_thankyou_page', 10, 4);
function show_combo_below_item_in_thankyou_page($item_id)
{
    $sub_products = wc_get_order_item_meta($item_id, 'akk_selected', true);
    $packing = wc_get_order_item_meta($item_id, 'packing_instructions', true);

    if (!empty($sub_products) && is_array($sub_products)) {
        echo '<div class="akk-sub-products" style="margin-top: 5px;font-size: 0.9em">';
        echo '<strong>Combo items:</strong>';
        echo '<ul style="margin: 0 0 5px 15px;">';
        foreach ($sub_products as $product_id => $qty) {
            $product = wc_get_product($product_id);
            if(is_array($qty)) $qty = $qty[0];

            if ($product) {
                echo '<li>' . esc_html($product->get_name()) . ' × ' . intval($qty) . '</li>';
            }
        }
        echo '</ul>';
        echo '</div>';
    }

    if (!empty($packing)) {
        echo '<div style="margin-top:5px;font-size: 0.9em;"><strong>Packing instructions:</strong> ' . esc_html($packing) . '</div>';
    }
}
