<?php


add_action('woocommerce_after_order_object_save', 'handle_get_items_admin', 10, 2);

function handle_get_items_admin($order, $data_store)
{
  if (!is_admin()) return;
  // No need run if not manual order
  if ($order->get_meta('is_manual_order') != 'yes') return;

  //Process add the shipping fee
  process_add_shipping_fee($order);

}

function process_add_shipping_fee($order)
{
  $distance = $order->get_meta('_billing_distance');
  if (isset($distance) && $distance > 0) {
    $shipping = new WC_Order_Item_Shipping;
    if ($order->get_shipping_total() > 0) return;
    $config = query_shipping();
    $shipping_fee = get_fee_from_config_fe(maybe_unserialize($config->minimum_order_to_delivery), $distance);

    $shipping->set_method_title("Shipping Fee");
    $shipping->set_total($shipping_fee);
    $order->add_item($shipping);
    $order->set_shipping_total($shipping_fee);
  }
}


function query_shipping()
{
  global $wpdb;
  $config_table = OUTLET_SHIPPING_CONFIG_TABLE_NAME;
  $query = $wpdb->prepare("SELECT * FROM $config_table");
  $config = $wpdb->get_row($query);
  return $config;
}

function get_fee_from_config_fe($config_data, $distance)
{
  foreach ($config_data as $rule) {
    $rule["lower_than"] = $rule["lower_than"] ?? 99999999;
    if ($distance >= $rule["greater_than"] && $distance <= $rule["lower_than"]) {
      return $rule["fee"];
    }
  }
  return 0;
}
