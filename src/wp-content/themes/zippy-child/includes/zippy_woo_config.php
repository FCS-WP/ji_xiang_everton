<?php


add_action('woocommerce_widget_shopping_cart_buttons', function () {
  // Removing Buttons
  remove_action('woocommerce_widget_shopping_cart_buttons', 'woocommerce_widget_shopping_cart_proceed_to_checkout', 20);

  // Adding customized Buttons
  add_action('woocommerce_widget_shopping_cart_buttons', 'custom_widget_shopping_cart_proceed_to_checkout', 20);
}, 1);
// Custom Checkout button
function custom_widget_shopping_cart_proceed_to_checkout()
{

  $subtotal = WC()->cart->get_subtotal();
  $rule = get_minimum_rule_by_order_mode();
  $original_link = wc_get_checkout_url();
  $custom_link = home_url('/checkout');
  // echo do_shortcode('[script_js_minicart]');
  if ($subtotal < $rule['minimun_total_to_order']) {
    echo '<p href="" class="button checkout wc-forward disabled-button-custom">Hit Minimum Order to Checkout</p>';
  } else {
    echo '<a href="' . esc_url($custom_link) . '" class="button checkout wc-forward button-checkout-minicart">Proceed to Checkout Page<br>Order for ' . format_date_DdMY($_SESSION['date']) . ' ' . $_SESSION['time']['from'] . '</a>';
  }
}
function remove_checkout_fields($fields)
{
  unset($fields['billing']['billing_company']);
  unset($fields['billing']['billing_address_2']);
  unset($fields['billing']['billing_city']);
  unset($fields['billing']['billing_state']);
  unset($fields['billing']['billing_postcode']);
  unset($fields['billing']['billing_address_1']);
  unset($fields['billing']['billing_address_2']);
  unset($fields['billing']['billing_country']);
  unset($fields['order']);

  return $fields;
}
// add_filter('woocommerce_checkout_fields', 'remove_checkout_fields');

function remove_checkout_coupon_form()
{
  remove_action('woocommerce_checkout_order_review', 'woocommerce_order_review', 10);
}

add_action('wp', 'remove_checkout_coupon_form');
