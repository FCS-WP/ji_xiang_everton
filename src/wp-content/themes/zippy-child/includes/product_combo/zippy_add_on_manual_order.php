<?php


add_action('woocommerce_before_order_object_save', 'handle_get_items_admin', 10, 2);

function handle_get_items_admin($order, $data_store)
{
  if (!is_admin()) return;

  foreach ($order->get_items() as $item_id => $item) {
    $product = $item->get_product();
    $perent_quantity = $item->get_quantity();
    $price =  get_pricing_price_in_cart($product, $perent_quantity);

    if (! $product) {
      continue;
    }

    $list_sub_products = get_field('product_combo', $product->get_id());

    if (empty($list_sub_products) || !is_array($list_sub_products)) {
      //process for normal

      $item->set_total($price);

      continue;
    }

    //Process for composite
    if (is_composite_product($product)) {

      $result = process_add_item_for_combosite($list_sub_products, $perent_quantity, $price);

      if (! empty($result)) {
        $item->delete_meta_data('akk_selected');

        $item->add_meta_data('akk_selected', $result['add_on'], true);
        $item->set_total($result['total']);
      }
    } else {
      $result = process_add_item_for_modifier($list_sub_products, $perent_quantity, $price);
      if (! empty($result)) {
        $item->delete_meta_data('akk_selected');

        $item->add_meta_data('akk_selected', $result['add_on'], true);
        $item->set_total($result['total']);
      }
    }
  }
}


function process_add_item_for_combosite($list_sub_products, $qty, $perent_price)
{

  $addons = [];
  $result = [];

  foreach ($list_sub_products as $sub_products) {
    if (empty($sub_products) || !is_array($sub_products)) {
      continue;
    }
    foreach ($sub_products as $sub_product_post) {
      $product_id  = is_object($sub_product_post) ? $sub_product_post->ID : $sub_product_post;

      $sub_product = wc_get_product($product_id);

      if (! $sub_product) {
        continue;
      }

      $min_qty     = isset($sub_products['minimum_quantity'])  && $sub_products['minimum_quantity'] > 0  ? intval($sub_products['minimum_quantity']) * $qty : $qty;

      $addons[$product_id] = [
        $min_qty,
        get_pricing_price_in_cart($sub_product, $min_qty)
      ];
    }
  }

  $result['add_on'] = $addons;
  $result['total'] = $perent_price;

  return $result;
}


function process_add_item_for_modifier($list_sub_products, $qty, $perent_price)
{

  $addons = [];
  $result = [];
  $total = $perent_price;

  foreach ($list_sub_products as $sub_products) {

    if (empty($sub_products) || !is_array($sub_products)) {
      continue;
    }

    foreach ($sub_products as $sub_product_post) {
      $product_id  = is_object($sub_product_post) ? $sub_product_post->ID : $sub_product_post;

      $sub_product = wc_get_product($product_id);

      if (! $sub_product) {
        continue;
      }

      $min_qty     = isset($sub_products['minimum_quantity'])  && $sub_products['minimum_quantity'] > 0  ? intval($sub_products['minimum_quantity']) * $qty : $qty;
      $total = floatval($total) +  $min_qty *  get_pricing_price_in_cart($sub_product, $min_qty);
      $addons[$product_id] = [
        $min_qty,
        get_pricing_price_in_cart($sub_product, $min_qty)
      ];
    }
  }

  $result['add_on'] = $addons;
  $result['total'] = $total;

  return $result;
}
