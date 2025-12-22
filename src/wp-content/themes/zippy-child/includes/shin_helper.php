<?php

use Zippy_Booking\Src\Services\Price_Books\Price_Books_Woocommerce;

function slugify($string)
{
  // Convert the string to lowercase
  $string = strtolower($string);

  // Replace spaces and special characters with dashes
  $string = preg_replace('/[^a-z0-9]+/', '_', $string);

  // Remove leading and trailing dashes
  $string = trim($string, '_');

  return $string;
}

function pr($data)
{
  echo '<style>
  #debug_wrapper {
    position: fixed;
    top: 0px;
    left: 0px;
    z-index: 9999;
    background: #fff;
    color: #000;
    overflow: auto;
    width: 100%;
    height: 100%;
  }</style>';
  echo '<div id="debug_wrapper"><pre>';

  print_r($data); // or var_dump($data);
  echo "</pre></div>";
  die;
}

// Hook to initialize the custom endpoint
add_action('init', 'register_health_check_endpoint');

function register_health_check_endpoint()
{
  add_rewrite_rule('^health-check/?$', 'index.php?health_check=1', 'top');
}

// Hook to handle the custom query variable
add_filter('query_vars', 'add_health_check_query_var');

function add_health_check_query_var($vars)
{
  $vars[] = 'health_check';
  return $vars;
}

// Hook to handle the request
add_action('template_redirect', 'handle_health_check_endpoint');

function handle_health_check_endpoint()
{
  global $wp_query;

  if (isset($wp_query->query_vars['health_check']) && $wp_query->query_vars['health_check'] == 1) {
    header('Content-Type: application/json');
    $response = array(
      'status' => 'ok',
      'timestamp' => current_time('mysql')
    );
    echo json_encode($response);
    exit;
  }
}


// Get Tax

function get_tax_percent()
{
  $all_tax_rates = [];
  $tax_classes = WC_Tax::get_tax_classes();
  if (!in_array('', $tax_classes)) {
    array_unshift($tax_classes, '');
  }

  foreach ($tax_classes as $tax_class) {
    $taxes = WC_Tax::get_rates_for_tax_class($tax_class);
    $all_tax_rates = array_merge($all_tax_rates, $taxes);
  }

  if (empty($all_tax_rates)) return;
  return $all_tax_rates[0];
}

// Helper to calculate tax portion from inclusive price
function get_tax_inclusive_amount($amount, $rate)
{
  if (empty($rate) || $rate <= 0) {
    return 0;
  }
  $tax = $amount - ($amount / (1 + ($rate / 100)));
  return wc_format_decimal($tax, wc_get_price_decimals());
}

function get_subtotal_cart()
{
  return WC()->cart->get_subtotal('');
}

/**
 * Get minimum order and freeship values based on order mode from WooCommerce session
 *
 * @return array
 */
function get_minimum_rule_by_order_mode()
{
  $response = [
    'minimum_total_to_order'    => 0,
    'minimum_total_to_freeship' => 0,
  ];

  if (function_exists('WC') && WC()->session) {

    if (is_delivery()) {
      $response['minimum_total_to_order']    = floatval(get_option('minimum_order', true)) ?? 0;
      $response['minimum_total_to_freeship'] = WC()->session->get('minimum_order_to_freeship') ?? 0;
    }
  }

  return $response;
}


/**
 * Get a specific WooCommerce session value (cached per request)
 *
 * @return mixed
 */
function zippy_get_wc_session($key = null)
{
  if (! WC()->session) return null;

  $keys = get_keys_outlet_session();

  if ($key !== null) {
    return in_array($key, $keys) ? WC()->session->get($key) : null;
  }

  $session_data = array();
  foreach ($keys as $k) {
    $session_data[$k] = WC()->session->get($k);
  }

  return $session_data;
}


function zippy_get_delivery_time()
{
  if (empty(zippy_get_wc_session('time'))) return;
  return 'From ' . date("H:i", strtotime(zippy_get_wc_session('time')['from'])) .
    ' To ' . date("H:i", strtotime(zippy_get_wc_session('time')['to']));
}


function handle_process_bar_notification($current, $total, $type)
{
  // Avoid division by zero
  $percentage = ($total > 0) ? round(($current / $total) * 100, 2) : 0;

  if ($current >= $total) {
    return [
      'message' => match ($type) {
        'order' => "Yay! You've hit the min order of $" . $total,
        'delivery' => "Yay! You've hit the min order for free delivery",
        default => '',
      },
      'style' => 'background-color: #2ba862; width: 100%',
    ];
  }

  $hit_minimum = wp_strip_all_tags(wc_price($total - $current));
  return [
    'message' => match ($type) {
      'order' =>  $hit_minimum . ' more for minimum order',
      'delivery' =>  $hit_minimum . ' more for Free delivery',
      default => '',
    },
    'style' => 'background-color: #f1b32c; width: ' . $percentage . '%',
  ];
}


function build_whatsapp_link($product)
{
  return 'https://api.whatsapp.com/send?phone=6592700510&text=Hello!%20I%20am%20looking%20to%20enquire%20about%20the%20' . $product->get_name() . '';
}

function is_existing_shipping()
{
  if (is_admin()) return;
  if (empty(WC()->session->get('order_mode'))) return false;
  return true;
}

function is_takeaway()
{
  if (is_admin()) return;

  if (!is_existing_shipping() || WC()->session->get('order_mode') !== 'takeaway') return false;

  return true;
}


function is_delivery()
{
  if (is_admin()) return;

  if (!is_existing_shipping() || WC()->session->get('order_mode') !== 'delivery') return false;

  return true;
}


function get_pricing_price($product, $display = false)
{
  $price = $product->get_price_html();
  if ($display) return $price;

  $price = html_entity_decode(strip_tags($product->get_price_html()));
  $price = preg_replace('/[^0-9\.,]/', '', $price);
  $price_formated = empty($price) ? $product->get_price() : $price;

  return floatval($price_formated); //
}

/**
 * Zippy custom price rules
 * @param mixed $product
 * @param mixed $quantity
 * @param mixed $user_id
 * @return array|float|null
 */

function get_product_pricing_rules($product, $quantity, $user_id = null)
{
  if (! class_exists(Zippy_Functions::class)) {
    return null;
  }

  $adp = new Zippy_Functions();
  $product_price = $adp->getDiscountedProductPrice($product, $quantity, true, $user_id);
  return $product_price;
}

// function get_product_pricing_rules($product, $quantity, $user_id = null)
// {
//   if (! class_exists(Price_Books_Woocommerce::class)) {
//     return null;
//   }

//   $adp = new Price_Books_Woocommerce($product, $user_id);
//   $product_price = $adp->get_price_book_pricing($product);
//   return $product_price;
// }

/**
 * Default price rules from ADP plugin
 * @param mixed $product
 * @param mixed $quantity
 * @param mixed $user_id
 */
// function get_product_pricing_rules($product, $quantity, $user_id = null)
// {
//   $fc = new WDP_Functions;
//   $product_price = $fc->get_discounted_product_price($product, $quantity, true);

//   return $product_price;
// }


function get_product_group_id($product_id, $groups)
{
  foreach ($groups as $index => $group_products) {
    if (is_object($group_products)) {
      if ($group_products->ID == $product_id) {
        return $index;
      }
    }
    if (is_array($group_products)) {
      foreach ($group_products as $group_product) {
        if ($group_product->ID == $product_id) {
          return $index;
        }
      }
    }
  }
  return null;
}

function get_product_group_max_quantity($product_id, $groups)
{
  if (is_array($groups)) {
    return $groups['quantity_products_group'] ?? 999;
  }
  // return null;
}


function is_composite_product($product)
{
  return $product->get_type() == 'composite' ? true : false;
}


function metersToKilometers(float $meters): string
{
  $km = $meters / 1000;
  return number_format($km, 2) . " KM";
}

function get_keys_outlet_session()
{
  return array(
    'date',
    'time',
    'order_mode',
    'extra_fee',
    'outlet_address',
    'outlet_name',
    'delivery_address',
    'status_popup',
  );
}
