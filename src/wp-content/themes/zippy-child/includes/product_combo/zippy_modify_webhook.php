<?php

add_filter('woocommerce_webhook_payload', 'add_add_on_items_webhook', 10, 4);

function add_add_on_items_webhook($payload, $resource = '', $resource_id = 0, $webhook = null)
{
  if ($resource !== 'order') {
    return $payload;
  }

  $order = wc_get_order($payload['id']);
  if (!$order) {
    return $payload;
  }

  $line_items = $payload['line_items'];

  $max_id = 0;
  foreach ($line_items as $li) {
    if (isset($li['id']) && intval($li['id']) > $max_id) {
      $max_id = intval($li['id']);
    }
  }

  foreach ($order->get_items() as $item_id => $item) {
    $akk_selected = $item->get_meta('akk_selected');

    if (!empty($akk_selected)) {
      foreach ($akk_selected as $product_id => $qty) {
        $sub_product = wc_get_product($product_id);

        $addon_qty   = intval($qty[0]);
        $addon_price = isset($qty[1]) ? floatval($qty[1]) : ($sub_product ? $sub_product->get_price() : 0);

        if (!$sub_product || $addon_qty <= 0) {
          continue;
        }

        $max_id++;

        $addon = [
          'id'         => $max_id,
          'name'       => $sub_product->get_name(),
          'sku'       => $sub_product->get_sku(),
          'quantity'   => $addon_qty,
          'total_tax'   => 0,
          'price'      => wc_format_decimal($addon_price),
          'subtotal'   => 0,
          'total'      => 0,
          'parent_id'  => $item_id,
          'is_addon'   => true,
        ];

        // Insert right after parent item
        $insert_position = array_search($item_id, array_column($line_items, 'id')) + 1;
        if ($insert_position === false) {
          $line_items[] = $addon;
        } else {
          array_splice($line_items, $insert_position, 0, [$addon]);
        }
      }
    }
  }

  // Replace line_items in payload
  $payload['line_items'] = $line_items;

  return $payload;
}
