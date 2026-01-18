<?php
add_action('manage_users_extra_tablenav', 'custom_user_role_filter_dropdown');
function custom_user_role_filter_dropdown($which) {
    if ($which !== 'top') return;

    $selected = isset($_GET['user_role_filter']) ? $_GET['user_role_filter'] : '';
    $roles = wp_roles()->get_names();

    echo '<div class="alignleft actions">';
    echo '<label for="user_role_filter" class="screen-reader-text">' . __('Filter by Role') . '</label>';
    echo '<select name="user_role_filter" id="user_role_filter">';
    echo '<option value="">' . __('— Filter by Role —', 'textdomain') . '</option>';

    foreach ($roles as $role_slug => $role_name) {
        printf(
            '<option value="%s"%s>%s</option>',
            esc_attr($role_slug),
            selected($selected, $role_slug, false),
            esc_html($role_name)
        );
    }

    echo '</select>';
    submit_button(__('Filter'), '', 'filter_role_button', false);
    echo '</div>';
}


add_filter('pre_get_users', 'custom_filter_users_by_role');
function custom_filter_users_by_role($query) {
    global $pagenow;

    if (is_admin() && 'users.php' === $pagenow && !empty($_GET['user_role_filter'])) {
        $query->set('role', sanitize_text_field($_GET['user_role_filter']));
    }
}
