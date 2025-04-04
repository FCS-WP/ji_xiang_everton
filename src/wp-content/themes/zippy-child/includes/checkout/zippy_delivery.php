<?php
add_filter('woocommerce_package_rates', 'customize_shipping_rates', 999);

function customize_shipping_rates($rates)
{
  if (empty($rates)) {
    return $rates;
  }

  if (isset($_SESSION['order_mode']) && $_SESSION['order_mode'] == 'delivery') {
    foreach ($rates as $rate_key => $rate) {
      if ($rate->method_id == 'free_shipping') {
        unset($rates[$rate_key]);
        
      }
    }
  }

  return $rates;
}
