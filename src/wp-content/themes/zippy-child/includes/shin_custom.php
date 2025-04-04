<?php
add_action('wp_enqueue_scripts', 'shin_scripts');

function shin_scripts()
{
  $version = time();

  wp_enqueue_style('main-style-css', THEME_URL . '-child' . '/assets/dist/css/main.min.css', array(), $version, 'all');

  wp_enqueue_script('main-scripts-js', THEME_URL . '-child' . '/assets/dist/js/main.min.js', array('jquery'), $version, true);

  // wp_enqueue_script('vanilla-calendar-js', THEME_URL . '-child' . '/assets/lib/vanilla-calendar.min.js', [], $version, true);
  // wp_enqueue_style('vanilla-calendar-css', THEME_URL . '-child' . '/assets/lib/vanilla-calendar.min.css', [], $version);

  wp_enqueue_script('sweet-alert2-js', THEME_URL . '-child' . '/assets/lib/sweetalert/sweetalert2.all.min.js', [], $version, true);
  wp_enqueue_style('sweet-alert2-css', THEME_URL . '-child' . '/assets/lib/sweetalert/sweetalert2.min.css', [], $version);
}
