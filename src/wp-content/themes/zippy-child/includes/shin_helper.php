<?php
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
    $order_mode = WC()->session->get('order_mode');

    if ($order_mode === 'delivery') {
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

  $keys = array(
    'date',
    'time',
    'order_mode',
    'extra_fee',
    'outlet_address',
    'outlet_name',
    'delivery_address'
  );

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

  $hit_minimum = $total - $current;
  return [
    'message' => match ($type) {
      'order' => '$' . $hit_minimum . ' more for minimum order',
      'delivery' => '$' . $hit_minimum . ' more for Free delivery',
      default => '',
    },
    'style' => 'background-color: #f1b32c; width: ' . $percentage . '%',
  ];
}


function build_whatsapp_link($product)
{
  return 'https://api.whatsapp.com/send?phone=6592700510&text=Hello!%20I%20am%20looking%20to%20enquire%20about%20the%20' . $product->get_name() . '';
}
