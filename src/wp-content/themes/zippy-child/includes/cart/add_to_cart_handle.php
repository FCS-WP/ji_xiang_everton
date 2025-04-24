<?php



function get_minimum_rule_by_order_mode()
{

  $response = array(
    'minimum_total_to_order' => 0,
    'minimum_total_to_freeship' => 0
  );

  if (isset($_SESSION['order_mode']) && $_SESSION['order_mode'] == 'delivery') {
    $response['minimum_total_to_order'] = isset($_SESSION['minimum_order_to_delivery']) ? $_SESSION['minimum_order_to_delivery'] : 0;
    $response['minimum_total_to_freeship'] = isset($_SESSION['minimum_order_to_freeship']) ? $_SESSION['minimum_order_to_freeship'] : 0;
  }

  return $response;
}

add_action('wp', 'add_to_cart_from_session');

function add_to_cart_from_session()
{
  if (!isset($_SESSION['product_id']) || empty($_SESSION['product_id'])) {
    return;
  }

  if (! class_exists('WooCommerce') || ! WC()->cart) {
    return;
  }

  if (isset($_SESSION['product_id'])) {

    $product_id = intval($_SESSION['product_id']);

    $min_qty = get_post_meta($product_id, '_custom_minimum_order_qty', true);

    $quantity = ($min_qty && $min_qty > 0 ? $min_qty : 1);

    $product = wc_get_product($product_id);

    if ($product) {
      WC()->cart->add_to_cart($product_id, $quantity);
      // Delete session after add to cart
      unset($_SESSION['product_id']);
    }
  }
}
