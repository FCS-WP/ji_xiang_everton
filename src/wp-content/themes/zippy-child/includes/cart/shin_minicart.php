<?php
function custom_woocommerce_mini_cart()
{
?>

  <div class="widget_shopping_cart_content">
    <?php woocommerce_mini_cart(); ?>
  </div>
  <?php do_action('flatsome_cart_sidebar'); ?>

  </div>
<?php
}
add_shortcode('mini_cart_sidebar', 'custom_woocommerce_mini_cart');

add_filter('woocommerce_quantity_input_min', 'modify_minimum_order', 10, 2);

function modify_minimum_order($min, $_product)
{
  // echo $_product;
  $quantity = $_product->get_stock_quantity() < get_post_meta($_product->get_id(), '_custom_minimum_order_qty', true) ? $_product->get_stock_quantity() : get_post_meta($_product->get_id(), '_custom_minimum_order_qty', true);

  return $quantity;
}
