<?php


// Add short description and price after product title in loop
add_action('woocommerce_after_shop_loop_item_title', 'custom_product_short_description_and_price', 15);

function custom_product_short_description_and_price()
{
  if (is_admin()) return;
  global $product;

  $product_id = $product->get_id();

  $price = get_minimum_price_for_combo($product);

  $product_short_des = str_replace('${price}', $price, $product->get_short_description());

  // Display short description
  if ($product->get_short_description()) {
    echo '<div class="product-short-description">' . wp_trim_words($product_short_des, 20) . '</div>';
  }

  // Display product price
  echo '<div class="product-price">' . $product->get_price_html() . '</div>';

  // Display add to cart
  if ($product->is_virtual()) {
    echo '<div class="cta_add_to_cart"><a class="whatsapp_product_btn" target="_blank" href="' . build_whatsapp_link($product) . '">Contact for Sale</a></div>';
  } else {
    if (empty(WC()->session->get('status_popup'))) {
      echo '<div class="cta_add_to_cart"><a class="lightbox-zippy-btn" data-product_id="' . $product_id . '" href="#lightbox-zippy-form" >Add</a></div>';
    } else {
      echo do_shortcode('[quickview_button id=' . $product_id . ']');
    }
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
    'value'             => get_post_meta(get_the_ID(), '_custom_minimum_order_qty', true) ?: 1,
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
  $min_qty = $product->get_stock_quantity() < get_post_meta($product->get_id(), '_custom_minimum_order_qty', true) ? $product->get_stock_quantity() : get_post_meta($product->get_id(), '_custom_minimum_order_qty', true);
  if ($min_qty && !$product->is_virtual()) {
    echo '<p class="custom-min-qty" style="color: red; font-weight: bold;">' . sprintf(__('Minimum order quantity: %d', 'woocommerce'), $min_qty) . '</p>';
  }
}, 25);

add_filter('woocommerce_quantity_input_args', function ($args, $product) {
  $min_qty = $product->get_stock_quantity() < get_post_meta($product->get_id(), '_custom_minimum_order_qty', true) ? $product->get_stock_quantity() : get_post_meta($product->get_id(), '_custom_minimum_order_qty', true);


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
  if (
    isset($args['min_value'], $args['max_value']) &&
    $args['min_value'] == $args['max_value']
  ) {
    $required_qty           = $args['min_value'];
    $args['max_value']   = $required_qty + 1;
    $args['input_value'] = $required_qty;
    $args['readonly']    = true;
  }
  return $args;
}, 10, 2);


function get_minimum_price_for_combo($product)
{
  // Check have the combo or not 
  $product_combo = get_field('product_combo', $product->get_id());

  if (!is_array($product_combo)) return $product->get_price_html();

  $sub_product_obj = $product_combo[0];

  $sub_product_id = $sub_product_obj["product"]->ID ?? null;

  $sub_product = wc_get_product($sub_product_id);

  // if (!is_object($sub_product)) return $product->get_price_html();

  return $sub_product->get_price_html();
}
