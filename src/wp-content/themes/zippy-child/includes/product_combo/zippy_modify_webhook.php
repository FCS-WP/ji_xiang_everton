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

  // Copy existing line_items
  $line_items = $payload['line_items'];
  $index = 0;

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

        $addon = [
          'id'         =>  $sub_product->get_id(), // avoid clash with WC IDs
          'name'       => $sub_product->get_name(),
          'sku'       => $sub_product->get_sku(),
          'quantity'   => $addon_qty,
          'total_tax'   => 0,
          'price'      => wc_format_decimal($addon_price),
          'subtotal'   => 0,
          'total'      => 0,
          'parent_id'  => $item_id, // reference parent line item
          'is_addon'   => true,     // flag for downstream systems
        ];
        array_splice($line_items, $index + 1, 0, [$addon]);
        $index++;
      }
    }
    $index++;
  }

  // Replace line_items in payload
  $payload['line_items'] = $line_items;

  return $payload;
}
