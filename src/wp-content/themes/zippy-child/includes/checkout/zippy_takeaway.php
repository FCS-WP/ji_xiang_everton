<?php


function remove_cart_session()
{
  // Empty cart
  WC()->cart->empty_cart();
  //Empty Session
  WC()->session->destroy_session();

  wp_send_json_success(['message' => 'Cart session removed']);
}

add_action('wp_ajax_remove_cart_session', 'remove_cart_session');
add_action('wp_ajax_nopriv_remove_cart_session', 'remove_cart_session');
