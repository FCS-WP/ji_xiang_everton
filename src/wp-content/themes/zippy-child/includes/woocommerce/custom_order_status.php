<?php

// 1. Register Custom Order Status

use Zippy_Booking\Src\Woocommerce\Admin\Zippy_Woo_Email_Packed;

function register_custom_order_status()
{
  register_post_status('wc-packed', array(
    'label'                     => 'Packed',
    'public'                    => true,
    'exclude_from_search'       => false,
    'show_in_admin_all_list'    => true,
    'show_in_admin_status_list' => true,
    'label_count'               => _n_noop('Packed (%s)', 'Packed (%s)')
  ));
}
add_action('init', 'register_custom_order_status');

function add_custom_order_status_to_woocommerce($order_statuses)
{
  $new_order_statuses = array();

  // Insert our custom status after 'on-hold'
  foreach ($order_statuses as $key => $status) {
    $new_order_statuses[$key] = $status;

    if ('wc-on-hold' === $key) {
      $new_order_statuses['wc-packed'] = 'Packed';
    }
  }

  return $new_order_statuses;
}
add_filter('wc_order_statuses', 'add_custom_order_status_to_woocommerce');

function add_custom_order_status_to_reports($order_statuses)
{
  $order_statuses[] = 'packed';
  return $order_statuses;
}
add_filter('woocommerce_reports_order_statuses', 'add_custom_order_status_to_reports');

add_filter('woocommerce_email_classes', 'register_custom_order_status_email');

function register_custom_order_status_email($email_classes)
{
  if (!class_exists(Zippy_Woo_Email_Packed::class)) {
    return $email_classes;
  }

  $email_classes['WC_Email_Packed'] = new Zippy_Woo_Email_Packed();
  return $email_classes;
}

add_action('woocommerce_order_status_packed', function ($order_id) {
  do_action('woocommerce_order_status_packed_notification', $order_id);
});
