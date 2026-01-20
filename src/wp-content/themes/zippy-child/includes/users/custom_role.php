<?php

function add_vendor_user_role() {
    add_role(
        'vendor', // Role slug
        'Vendor', // Display name
        array(
            'read' => true, // allow basic access (needed for login)
        )
    );
}
add_action('init', 'add_vendor_user_role');

function redirect_vendor_from_admin() {
    if ( is_admin() && ! defined( 'DOING_AJAX' ) && current_user_can( 'vendor' ) ) {
        wp_redirect( home_url() );
        exit;
    }
}
add_action( 'admin_init', 'redirect_vendor_from_admin' );

function hide_admin_bar_for_vendor() {
    if ( current_user_can( 'vendor' ) ) {
        show_admin_bar( false );
    }
}
add_action( 'after_setup_theme', 'hide_admin_bar_for_vendor' );