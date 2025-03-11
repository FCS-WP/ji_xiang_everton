<?php

// Add short description and price after product title in loop
add_action('woocommerce_after_shop_loop_item_title', 'custom_product_short_description_and_price', 15);

function custom_product_short_description_and_price() {
    global $product;
    
    $product_id = $product->get_id();
    
    // Display short description
    if ($product->get_short_description()) {
        echo '<div class="product-short-description">' . wp_trim_words($product->get_short_description(), 20) . '</div>';
    }
    
    // Display product price
    echo '<div class="product-price">' . $product->get_price_html() . '</div>';
    
    // Display add to cart
    echo '<div class="cta_add_to_cart"><a class="action-popup-btn" href="#order-popup-' . $product_id . '">Add</a></div>';
    echo do_shortcode('[lightbox id="order-popup-' . $product_id . '" width="550px" padding="15px" ][block id="delivery-takeaway"][/lightbox]');
}