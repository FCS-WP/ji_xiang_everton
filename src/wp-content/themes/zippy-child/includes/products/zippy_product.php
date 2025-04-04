<?php


// Add short description and price after product title in loop
add_action('woocommerce_after_shop_loop_item_title', 'custom_product_short_description_and_price', 15);

function custom_product_short_description_and_price()
{
  global $product;
  $date = new DateTime();

  $product_id = $product->get_id();

  // Display short description
  if ($product->get_short_description()) {
    echo '<div class="product-short-description">' . wp_trim_words($product->get_short_description(), 20) . '</div>';
  }

  // Display product price
  echo '<div class="product-price">' . $product->get_price_html() . '</div>';

  // Display add to cart

  if (!isset($_SESSION['status_popup'])) {
    echo '<div class="cta_add_to_cart"><a class="lightbox-zippy-btn" data-product_id="' . $product_id . '" href="#lightbox-zippy-form" >Add</a></div>';
  } else {
    echo do_shortcode('[quickview_button]');
  }
}

function lightbox_zippy_form()
{
  echo do_shortcode('[lightbox id="lightbox-zippy-form" width="600px" padding="20px 0px"][zippy_form][/lightbox]');
}

add_shortcode('lightbox_zippy_form', 'lightbox_zippy_form');


function flatsome_custom_quickview_button($atts)
{
  global $product;

  if (!$product) return '';

  $product_id = $product->get_id();

  $button = '<div class="cta_add_to_cart"><a href="#" class="quick-view" 
                  data-prod="' . esc_attr($product_id) . '" 
                  data-toggle="quick-view">
                  Add
               </a></div>';

  return $button;
}
add_shortcode('quickview_button', 'flatsome_custom_quickview_button');
