<?php
add_action('after_setup_theme', function () {
    remove_action('flatsome_single_product_lightbox_summary', 'woocommerce_template_single_price', 10);
    remove_action('flatsome_single_product_lightbox_summary', 'woocommerce_template_single_excerpt', 20);
    remove_action('flatsome_single_product_lightbox_summary', 'woocommerce_template_single_add_to_cart', 30);
    remove_action('flatsome_single_product_lightbox_product_gallery', 'woocommerce_show_product_sale_flash', 20);
    remove_action('flatsome_single_product_lightbox_summary', 'woocommerce_template_single_meta', 40);
});
add_action('flatsome_single_product_lightbox_summary', 'my_custom_lightbox_content', 50);
function my_custom_lightbox_content()
{
    global $product;

    if (! $product) return;

    echo '<div class="custom-lightbox-summary" style="margin-top: 20px;">';

    echo '<div class="custom-description" style="font-size: 14px; line-height: 1.6;">';
    echo wpautop($product->get_short_description());
    echo wpautop($product->get_description());
    echo '</div>';
    echo '<div class="custom-price" style="font-size: 18px; font-weight: bold; margin-bottom: 10px;">';
    echo wc_price($product->get_price());
    echo '</div>';

    echo '</div>';
}
