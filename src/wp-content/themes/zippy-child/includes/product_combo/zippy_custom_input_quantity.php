<?php
function render_flatsome_quantity_input($product, $stock_level = null, $min_qty = 0, $group_id = null)
{
    $min_qty = (isset($min_qty) && is_numeric($min_qty) && $min_qty >= 0) ? (int)$min_qty : 0;
    $max_qty = $stock_level !== null ? (int)$stock_level : $product->get_max_purchase_quantity();
    $max_attr = $max_qty > 0 ? 'max="' . esc_attr($max_qty) . '"' : '';
    $min_attr   = 'min="' . esc_attr($min_qty) . '"';
    $value_attr = 'value="' . esc_attr($min_qty) . '"';
    $price = get_pricing_price($product);

    $data_group = $group_id !== null ? ' data-group="' . esc_attr($group_id) . '"' : '';

    return '<div class="ux-quantity quantity buttons_added">' .
        '<input type="button" value="-" class="ux-quantity__button ux-quantity__button--minus button minus is-form">' .
        '<input
            type="number"
            id="quantity_' . esc_attr($product->get_id()) . '"
            class="input-text qty text akk-sub-product-qty"
            name="akk_sub_products[' . esc_attr($product->get_id()) . ']"
            ' . $value_attr . '
            aria-label="' . esc_attr($product->get_name()) . ' quantity"
            size="4"
            ' . $min_attr . '
            ' . $max_attr . '
            step="1"
            placeholder=""
            inputmode="numeric"
            autocomplete="off"
            data-price="' . esc_attr($price) . '"
            data-min="' . esc_attr($min_qty) . '"' .
            $data_group .
        '>' .
        '<input type="button" value="+" class="ux-quantity__button ux-quantity__button--plus button plus is-form">' .
        '</div>';
}

