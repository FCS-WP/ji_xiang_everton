<?php

add_filter('adp_get_date', function ($date) {


  try {
    $custom_date = new DateTime(sanitize_text_field('2025-08-25'));
    return $custom_date;
  } catch (Exception $e) {
    // fallback to default if invalid
  }

  // // Example: use an option stored in wp_options
  // $saved_date = get_option('my_discount_date');
  // if ($saved_date) {
  //   return new DateTime($saved_date);
  // }

  // return $date; // fallback to real date
}, 10, 1);
