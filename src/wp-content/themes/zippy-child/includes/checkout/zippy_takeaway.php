<?php


function handle_submit_takeaway()
{
  if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit_take_away'])) {
    $_SESSION['status_popup'] = true;

    $outlet = sanitize_text_field($_POST['selectOutlet']);
    $takeaway_time = sanitize_text_field($_POST['selectTakeAwayTime']);
    $selectDateTakeaway = sanitize_text_field($_POST['selectDateTakeaway']);
    $formatted_date = date("D, d M Y", strtotime($selectDateTakeaway));

    $_SESSION['selectTakeAwayTime'] = $takeaway_time;
    $_SESSION['selectOutlet'] = $outlet;
    $_SESSION['selectDateTakeaway'] = $formatted_date;
  }
}
// add_action('init', 'handle_submit_takeaway');


function remove_cart_session()
{
  // Empty cart
  WC()->cart->empty_cart();
  //Empty Session
  WC()->session->destroy_session();

  wp_send_json_success(['message' => 'Cart session removed']);
}

add_action('wp_ajax_remove_cart_session', 'remove_cart_session');
add_action('wp_ajax_nopriv_remove_cart_session', 'remove_cart_session');
