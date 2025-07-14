<?php
add_action('wp_enqueue_scripts', 'shin_scripts');

function shin_scripts()
{
  $version = time();

  wp_enqueue_style('main-style-css', THEME_URL . '-child' . '/assets/dist/css/main.min.css', array(), $version, 'all');

  wp_enqueue_script('main-scripts-js', THEME_URL . '-child' . '/assets/dist/js/main.min.js', array('jquery'), $version, true);

  wp_enqueue_script('sweet-alert2-js', THEME_URL . '-child' . '/assets/lib/sweetalert/sweetalert2.all.min.js', [], $version, true);

  wp_enqueue_style('sweet-alert2-css', THEME_URL . '-child' . '/assets/lib/sweetalert/sweetalert2.min.css', [], $version);
}

function custom_lostpassword_url($url, $redirect)
{
  // Set your custom lost password page URL
  $custom_url = esc_url(wp_login_url()) . '?action=lostpassword';
  return $custom_url;
}
add_filter('lostpassword_url', 'custom_lostpassword_url', 10, 2);