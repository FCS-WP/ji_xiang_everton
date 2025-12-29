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

  echo '<div class="custom-lightbox-summary">';
  $price = get_minimum_price_for_combo($product);

  $description = str_replace('${price}', $price, $product->get_description());
  echo '<div class="custom-description">';
  echo wpautop($description);
  echo '</div>';
  echo '<div class="custom-price">';
  echo '<p style="font-size: 14px; font-weight:700; margin-bottom: 10px; color: #c0392b">' . "If you have any packing preferences, do leave the packing instructions below." . '</p>';
  echo $product->get_price_html();

  echo '</div>';

  echo '</div>';
}
