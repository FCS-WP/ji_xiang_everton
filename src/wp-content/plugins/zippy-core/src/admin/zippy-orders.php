<?php

/**
 * MPDA Consent Management
 *
 * @package MPDA_Consent
 */

namespace Zippy_Core\Src\Admin;

defined('ABSPATH') or die();

use Zippy_Core\Utils\Zippy_Utils_Core;
use Dompdf\Dompdf;

class Zippy_Orders
{
  protected static $_instance = null;

  /**
   * @return Zippy_Orders
   */

  public static function get_instance()
  {
    if (is_null(self::$_instance)) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  public function __construct()
  {

    add_action('woocommerce_order_list_table_restrict_manage_orders', array($this, 'filter_orders'), 1);
    add_filter('woocommerce_order_list_table_restrict_manage_orders', array($this, 'clear_default_filter'), 2);
    add_filter('woocommerce_order_query_args', array($this, 'custom_filter_woocommerce_orders'), 10, 1);
    add_action('admin_head', array($this, 'custom_woocommerce_filter_styles'));
    add_action('admin_init', array($this, 'export_woocommerce_orders'));
  }


  public function clear_default_filter($output)
  {
    return "";
  }

  public function filter_orders()
  {
    $screen = get_current_screen();
    if ($screen->id !== 'woocommerce_page_wc-orders') {
      return;
    }

    // From ... To ...
    $from_date = isset($_GET['from_date']) ? sanitize_text_field($_GET['from_date']) : '';
    $to_date = isset($_GET['to_date']) ? sanitize_text_field($_GET['to_date']) : '';
    ?>
    <div>
      <label for="from_date">From</label>
      <input type="date" name="from_date" placeholder="<?php esc_attr_e('From Date', 'woocommerce'); ?>" value="<?php echo esc_attr($from_date); ?>" />
    </div>
    <div>
      <label for="to_date">To</label>
      <input type="date" name="to_date" placeholder="<?php esc_attr_e('To Date', 'woocommerce'); ?>"
      value="<?php echo esc_attr($to_date); ?>" />
    </div>
    <?php

    // Order Status
    $order_statuses = wc_get_order_statuses();
    $current_status = isset($_GET['order_status']) ? sanitize_text_field($_GET['order_status']) : '';
    ?>
    <select name="order_status">
      <option value="" <?php selected($current_status, ''); ?>><?php _e('All Statuses', 'woocommerce'); ?></option>
      <?php foreach ($order_statuses as $status_slug => $status_name): ?>
        <option value="<?php echo esc_attr($status_slug); ?>" <?php selected($current_status, $status_slug); ?>>
          <?php echo esc_html($status_name); ?>
        </option>
      <?php endforeach; ?>
    </select>
    <?php
     // Customer
      $current_customer_type = isset($_GET['customer_type']) ? sanitize_text_field($_GET['customer_type']) : '';
    ?>
      <select name="customer_type">
          <option value="" <?php selected($current_customer_type, ''); ?>><?php _e('By Order Type', 'woocommerce'); ?></option>
          <option value="registered" <?php selected($current_customer_type, 'registered'); ?>><?php _e('Member Orders', 'woocommerce'); ?></option>
          <option value="guest" <?php selected($current_customer_type, 'guest'); ?>><?php _e('Guest Orders', 'woocommerce'); ?></option>
      </select>

    <?php
      $current_export_format = isset($_GET['file_type']) ? sanitize_text_field($_GET['file_type']) : 'csv';
    ?>
    <!-- Export PDF -->
    <div class="export_pdf">
      <select name="file_type">
        <option value="csv" <?php selected($current_export_format, 'csv'); ?>><?php _e('Export as CSV', 'woocommerce'); ?></option>
        <option value="pdf" <?php selected($current_export_format, 'pdf'); ?>><?php _e('Export as PDF', 'woocommerce'); ?></option>
      </select>

      <button type="submit" name="export_orders" value="1" class="button"><?php _e('Export Orders', 'woocommerce'); ?></button>
    </div>
    
    <?php
  }

  public function custom_filter_woocommerce_orders($query_args)
  {
    // Filter by Customer Email
    if (isset($_GET['_customer_user']) && !empty($_GET['_customer_user'])) {
      $query_args['customer_id'] = absint($_GET['_customer_user']);
    } elseif (isset($_GET['customer_type']) && !empty($_GET['customer_type'])) {
      if ($_GET['customer_type'] === 'guest') {
        $query_args['customer_id'] = 0; // customer_id = 0 means guest order
      }
    }

    // Filter by From ... To ...
    $from_date = isset($_GET['from_date']) ? sanitize_text_field($_GET['from_date']) : '';
    $to_date = isset($_GET['to_date']) ? sanitize_text_field($_GET['to_date']) : '';

    if (!empty($from_date) || !empty($to_date)) {
      $date_range = '';
      if (!empty($from_date)) {
        $date_range .= $from_date;
      }
      if (!empty($to_date)) {
        $date_range .= !empty($date_range) ? '...' . $to_date : $to_date;
      }
      if (!empty($date_range)) {
        $query_args['date_created'] = $date_range;
      }
    }

    // Order  Status
    if (isset($_GET['order_status']) && !empty($_GET['order_status'])) {
      $query_args['status'] = sanitize_text_field($_GET['order_status']);
    }

    return $query_args;
  }


  

  function export_woocommerce_orders()
  {
    if (isset($_GET['export_orders']) && $_GET['export_orders'] == '1' && current_user_can('manage_woocommerce')) {
      // Arg
      $query_args = array(
        'limit' => -1,
        'type' => 'shop_order',
      );

      $file_type = isset($_GET['file_type']) ? sanitize_text_field($_GET['file_type']) : 'csv';

      // get query arg from current filter
      $query_args = self::custom_filter_woocommerce_orders($query_args);

      // order list
      $orders = wc_get_orders($query_args);
      $order_data = [];
      foreach ($orders as $order) {
        $quantity = 0;
        foreach ($order->get_items() as $item) {
          $quantity += $item->get_quantity();
        }

        $billing_date = get_post_meta($order->get_id(), '_billing_date', true);

        //  billing_date to M d, Y
        $formatted_date = '';
        if (!empty($billing_date)) {
          $timestamp = is_numeric($billing_date) ? (int) $billing_date : strtotime($billing_date);
          if ($timestamp !== false) {
            $formatted_date = date_i18n('F j, Y', $timestamp);
          }
        }

        $order_data[] = array(
          'order_date' => $formatted_date ?: 'N/A',
          'transaction_id' => $order->get_transaction_id(),
          'payment_method' => $order->get_payment_method_title(),
          'amount' => "$" . $order->get_total(),
          'payment_status' => $order->get_status(),
        );
      }

      // File Columns
      $columns = [
        'Order Date',
        'Transaction ID',
        'Payment Method',
        'Amount',
        'Payment Status'
      ];


      if ($file_type == 'pdf') {
        $html = '<!DOCTYPE html>
                <html><head><meta charset="UTF-8"></head><body>
                <h1 style="text-align: center">Billing Report</h1>
                <table border="1" style="width:100%; border-collapse: collapse;"><tr>';

        foreach ($columns as $col) {
          $html .= "<th style='padding:10px 0'>$col</th>";
        }

        $html .= '</tr>';

        foreach ($order_data as $data) {
          $html .= '<tr>';
          $html .= '<td style="padding: 5px">' . $data['order_date'] . '</td>';
          $html .= '<td style="padding: 5px">' . $data['transaction_id'] . '</td>';
          $html .= '<td style="padding: 5px">' . $data['payment_method'] . '</td>';
          $html .= '<td style="padding: 5px">' . $data['amount'] . '</td>';
          $html .= '<td style="padding: 5px">' . $data['payment_status'] . '</td>';
          $html .= '</tr>';
        }

        $html .= '</table></body></html>';

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $dompdf->stream('orders_export_' . date('Y-m-d_H-i-s') . '.pdf', array('Attachment' => true));

      } elseif ($file_type == 'csv') {

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=orders_export_' . date('Y-m-d_H-i-s') . '.csv');

        $output = fopen('php://output', 'w');

        //   UTF-8
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Add Column
        fputcsv($output, $columns, ',');

        // Data
        foreach ($order_data as $data) {
          fputcsv($output, [
            $data['order_date'],
            $data['transaction_id'],
            $data['payment_method'],
            $data['amount'],
            $data['payment_status'],
          ], ',');
        }

        // CSV Content
        fclose($output);
        exit;
      }
    }
  }

  // CSS
  public function custom_woocommerce_filter_styles()
  {
    $screen = get_current_screen();
    if ($screen->id !== 'woocommerce_page_wc-orders') {
      return;
    }
    ?>
    <style>
      #wc-orders-filter .alignleft.actions:not(.bulkactions){
        display: flex;
      }
      #wc-orders-filter .alignleft.actions:not(.bulkactions) #filter-by-date{
        display: none;
      }
      #wc-orders-filter .alignleft.actions .export_pdf{
        order: 10;
      }
    </style>
    <?php
  }
}
