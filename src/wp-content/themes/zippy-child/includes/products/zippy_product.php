<?php


// Add short description and price after product title in loop
add_action('woocommerce_after_shop_loop_item_title', 'custom_product_short_description_and_price', 15);

function custom_product_short_description_and_price()
{
  global $product;

  $product_id = $product->get_id();

  // Display short description
  if ($product->get_short_description()) {
    echo '<div class="product-short-description">' . wp_trim_words($product->get_short_description(), 20) . '</div>';
  }

  // Display product price
  echo '<div class="product-price">' . $product->get_price_html() . '</div>';

  // Display add to cart

  if (empty(WC()->session->get('status_popup'))) {
    echo '<div class="cta_add_to_cart"><a class="lightbox-zippy-btn" data-product_id="' . $product_id . '" href="#lightbox-zippy-form" >Add</a></div>';
  } else {
    echo do_shortcode('[quickview_button id='.$product_id.']');
  }
}



// Add custom field to the product edit page
add_action('woocommerce_product_options_general_product_data', function () {
  woocommerce_wp_text_input([
    'id'                => '_custom_minimum_order_qty',
    'label'             => __('Minimum Order Quantity', 'woocommerce'),
    'description'       => __('Enter the minimum quantity required to add this product to the cart.', 'woocommerce'),
    'type'              => 'number',
    'custom_attributes' => ['step' => '1', 'min' => '1'],
  ]);
});

// Save the custom field value
add_action('woocommerce_process_product_meta', function ($post_id) {
  if (isset($_POST['_custom_minimum_order_qty'])) {
    update_post_meta($post_id, '_custom_minimum_order_qty', absint($_POST['_custom_minimum_order_qty']));
  }
});

// Display minimum order quantity on the product page
add_action('woocommerce_single_product_summary', function () {
  global $product;
  $min_qty = get_post_meta($product->get_id(), '_custom_minimum_order_qty', true);
  if ($min_qty) {
    echo '<p class="custom-min-qty" style="color: red; font-weight: bold;">' . sprintf(__('Minimum order quantity: %d', 'woocommerce'), $min_qty) . '</p>';
  }
}, 25);

add_filter('woocommerce_quantity_input_args', function ($args, $product) {
  $min_qty = get_post_meta($product->get_id(), '_custom_minimum_order_qty', true);

  if ($min_qty) {
    $cart = WC()->cart->get_cart();
    $cart_qty = 0;

    foreach ($cart as $cart_item) {
      if ($cart_item['product_id'] == $product->get_id()) {
        $cart_qty += $cart_item['quantity'];
      }
    }

    $required_qty = max(1, $min_qty - $cart_qty);

    if ($cart_qty < $min_qty) {
      $args['min_value'] = $required_qty;
      $args['input_value'] = $required_qty;
    }
  }

  return $args;
}, 10, 2);
