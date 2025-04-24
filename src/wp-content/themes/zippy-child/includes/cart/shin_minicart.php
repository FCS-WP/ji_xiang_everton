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

function rule_minimun_checkout_on_cart_page()
{
  $subtotal = WC()->cart->get_subtotal();
  $rule = get_minimum_rule_by_order_mode();
  if ($subtotal < $rule['minimum_total_to_order']) {
    remove_action('woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20);
  } else {
    return;
  }
}
add_action('woocommerce_after_calculate_totals', 'rule_minimun_checkout_on_cart_page');

function rule_minimun_checkout_all_site()
{
  $subtotal = WC()->cart->get_subtotal();
  $rule = get_minimum_rule_by_order_mode();
  if (is_page('checkout') && ($subtotal < $rule['minimum_total_to_order'])) {
    wp_redirect(home_url());
    exit;
  } else {
    return;
  }
}
add_action('template_redirect', 'rule_minimun_checkout_all_site');
