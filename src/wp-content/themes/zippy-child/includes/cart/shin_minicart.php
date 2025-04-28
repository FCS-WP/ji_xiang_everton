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
