<?php
function render_flatsome_quantity_input($product, $stock_level = null, $min_qty = 0, $groups = null, $is_composite = false)
{
    if (!$product || !is_a($product, 'WC_Product')) {
        return '';
    }

    // Calculate limits
    $max_qty = $stock_level !== null
        ? (int) $stock_level
        : (int) $product->get_max_purchase_quantity();

    $group_id = get_product_group_id($product->get_id(), $groups);

    $min_qty = (is_numeric($min_qty) && $min_qty >= 0)
        ? (int) $min_qty
        : max(0, (int) $product->get_min_purchase_quantity());

    // Adjust for composite
    if ($is_composite) {
        if (empty($group_id)) {
            $max_qty = $min_qty; 
        } else {
            $max_qty = (int) get_product_group_max_quantity($product->get_id(), $groups);
        }
    }

    // Attributes
    $attrs = [
        'type'        => 'number',
        'id'          => 'quantity_' . $product->get_id(),
        'class'       => 'input-text qty text akk-sub-product-qty',
        'name'        => 'akk_sub_products[' . $product->get_id() . ']',
        'value'       => $min_qty,
        'aria-label'  => $product->get_name() . ' quantity',
        'size'        => 4,
        'min'         => $min_qty,
        'step'        => 1,
        'inputmode'   => 'numeric',
        'autocomplete' => 'off',
        'data-price'  => get_pricing_price_in_cart($product, 1),
        'data-min'    => $min_qty,
    ];

    if ($max_qty > 0) {
        $attrs['max'] = $max_qty;
    }
    if ($group_id) {
        $attrs['data-group'] = $group_id;
    }
    if ($is_composite && empty($group_id)) {
        $attrs['disabled'] = 'disabled';
    }

    // Build input field
    $attr_html = '';
    foreach ($attrs as $key => $val) {
        $attr_html .= sprintf(' %s="%s"', esc_attr($key), esc_attr($val));
    }

    return sprintf(
        '<div class="ux-quantity quantity buttons_added">
            <input type="button" value="-" class="ux-quantity__button ux-quantity__button--minus button minus is-form">
            <input %s aria-live="polite">
            <input type="button" value="+" class="ux-quantity__button ux-quantity__button--plus button plus is-form">
        </div>',
        trim($attr_html)
    );
}
