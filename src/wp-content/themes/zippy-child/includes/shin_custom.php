<?php

use Zippy_Booking\Src\Woocommerce\Admin\Zippy_Woo_Manual_Order;

add_action('wp_enqueue_scripts', 'shin_scripts');

function shin_scripts()
{
  $version = time();

  wp_enqueue_style('main-style-css', THEME_URL . '-child' . '/assets/dist/css/main.min.css', array(), $version, 'all');

  add_action('wp_enqueue_scripts', 'enqueue_last_sccript', 999);

  wp_enqueue_script('sweet-alert2-js', THEME_URL . '-child' . '/assets/lib/sweetalert/sweetalert2.all.min.js', [], $version, true);

  wp_enqueue_style('sweet-alert2-css', THEME_URL . '-child' . '/assets/lib/sweetalert/sweetalert2.min.css', [], $version);
}

function enqueue_last_sccript()
{
  wp_enqueue_script('main-scripts', THEME_URL . '-child' . '/assets/dist/js/main.min.js', array('jquery'), time(), true);
}

function custom_lostpassword_url($url, $redirect)
{
  // Set your custom lost password page URL
  $custom_url = esc_url(wp_login_url()) . '?action=lostpassword';
  return $custom_url;
}
add_filter('lostpassword_url', 'custom_lostpassword_url', 10, 2);

add_action('woocommerce_new_order', 'custom_save_admin_order_meta', 10, 1);
function custom_save_admin_order_meta($order_id)
{
  // ✅ Only run on creating new orders, not updating
  if (!isset($_POST['custom_billing_meta_data']) || empty($_POST['custom_billing_meta_data'])) {
    return;
  }
  $order = wc_get_order($order_id);

  // Check and save custom fields if exist
  if (isset($_POST['_billing_outlet'])) {
    $order->update_meta_data('_billing_outlet', sanitize_text_field($_POST['_billing_outlet']));
  }
  if (isset($_POST['_billing_outlet_name'])) {
    $order->update_meta_data('_billing_outlet_name', sanitize_text_field($_POST['_billing_outlet_name']));
  }
  if (isset($_POST['_billing_outlet_address'])) {
    $order->update_meta_data('_billing_outlet_address', sanitize_text_field($_POST['_billing_outlet_address']));
  }
  if (isset($_POST['_billing_date'])) {
    $order->update_meta_data('_billing_date', sanitize_text_field($_POST['_billing_date']));
  }
  if (isset($_POST['_billing_delivery_to'])) {
    $order->update_meta_data('_billing_delivery_to', sanitize_text_field($_POST['_billing_delivery_to']));
  }
  if (isset($_POST['_billing_delivery_postal'])) {
    $order->update_meta_data('_billing_delivery_postal', sanitize_text_field($_POST['_billing_delivery_postal']));
  }
  if (isset($_POST['_billing_distance'])) {
    $order->update_meta_data('_billing_distance', sanitize_text_field($_POST['_billing_distance']));
  }
  if (isset($_POST['_billing_time'])) {
    $order->update_meta_data('_billing_time', sanitize_text_field($_POST['_billing_time']));
  }
  if (isset($_POST['_billing_method_shipping'])) {
    $order->update_meta_data('_billing_method_shipping', sanitize_text_field($_POST['_billing_method_shipping']));
  }
  if (isset($_POST['is_manual_order'])) {
    $order->update_meta_data('is_manual_order', sanitize_text_field($_POST['is_manual_order']));
  }

  $order->save();
}

add_action('woocommerce_admin_order_items_after_line_items', function ($order_id) {
  $order = wc_get_order($order_id);

  $is_manual = $order->get_meta('is_manual_order');
  $enable_edit = ($is_manual == 'yes' && empty($order->get_transaction_id()))
    || ($is_manual == 'no' && empty($order->get_payment_method_title()));

  echo do_shortcode('[admin_order_table order_id="' . esc_attr($order_id) . '" enable_edit="' . esc_attr($enable_edit) . '"]');
});

add_action('admin_head', 'custom_admin_order_styles');
function custom_admin_order_styles()
{
  $screen = get_current_screen();
  if ($screen && $screen->id === 'woocommerce_page_wc-orders') {
    echo '<style>
            .woocommerce_order_items_wrapper .woocommerce_order_items {
                display: none !important;
            }

            .wc-order-data-row.wc-order-totals-items {
                display: none !important;
            }

            .wc-order-data-row.wc-order-bulk-actions {
                display: none !important;
            }
        </style>';
  }
}

add_action('wp_footer', 'handle_no_scroll_when_open_modal_add_to_cart');
function handle_no_scroll_when_open_modal_add_to_cart()
{
?>
  <script>
    document.addEventListener('click', function(e) {
      const target = e.target.closest('.quick-view');
      if (target) {
        e.preventDefault();
        document.documentElement.classList.add('no-scroll');
      }
    });

    jQuery(document).on('mfpClose', function() {
      document.documentElement.classList.remove('no-scroll');
    });
  </script>
<?php
}
