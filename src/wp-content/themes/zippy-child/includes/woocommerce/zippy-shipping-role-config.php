<?php

function zippy_get_minimum_order_by_user_delivery()
{
    global $wpdb;

    $default_minimum = floatval(get_option('minimum_order', 0));

    if (!is_user_logged_in()) {
        return $default_minimum;
    }

    $user = wp_get_current_user();
    $role_slug = $user->roles[0] ?? null;

    if (!$role_slug) {
        return $default_minimum;
    }

    $table = $wpdb->prefix . 'addons_shipping_role_config';

    $row = $wpdb->get_row(
        $wpdb->prepare(
            "
            SELECT min_order, start_date, end_date
            FROM {$table}
            WHERE role_user = %s
              AND service_type = %s
              AND deleted_at IS NULL
            LIMIT 1
            ",
            $role_slug,
            'delivery'
        ),
        ARRAY_A
    );

    if (!$row || floatval($row['min_order']) <= 0) {
        return $default_minimum;
    }

    $now = current_datetime();

    $start = !empty($row['start_date'])
        ? new DateTimeImmutable($row['start_date'], wp_timezone())
        : null;

    $end = !empty($row['end_date'])
        ? new DateTimeImmutable($row['end_date'], wp_timezone())
        : null;

    if ($start && $now < $start) {
        return $default_minimum;
    }

    if ($end && $now > $end) {
        return $default_minimum;
    }
    
    return floatval($row['min_order']);
}
