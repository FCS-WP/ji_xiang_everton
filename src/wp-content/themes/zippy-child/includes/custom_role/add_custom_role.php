<?php
function custom_add_custom_user_roles()
{
    $keep_roles = ['administrator', 'customer', 'subscriber'];

    global $wp_roles;

    if (! isset($wp_roles)) {
        $wp_roles = new WP_Roles();
    }

    foreach ($wp_roles->roles as $role_slug => $role_details) {
        if (! in_array($role_slug, $keep_roles)) {
            remove_role($role_slug);
        }
    }
    // Member tiers
    add_role(
        'member_tier_1',
        'Member Tier 1',
        [
            'read' => true,
            'level_0' => true,
        ]
    );

    add_role(
        'member_tier_2',
        'Member Tier 2',
        [
            'read' => true,
            'level_0' => true,
        ]
    );

    add_role(
        'member_tier_3',
        'Member Tier 3',
        [
            'read' => true,
            'level_0' => true,
        ]
    );

    // Vendor tiers
    add_role(
        'vendor_tier_1',
        'Vendor Tier 1',
        [
            'read' => true,
            'level_0' => true,
        ]
    );

    add_role(
        'vendor_tier_2',
        'Vendor Tier 2',
        [
            'read' => true,
            'level_0' => true,
        ]
    );

    add_role(
        'vendor_tier_3',
        'Vendor Tier 3',
        [
            'read' => true,
            'level_0' => true,
        ]
    );


}
add_action('init', 'custom_add_custom_user_roles');


add_action('init', function () {
    // Group 1: Front counter & packer
    add_role('front_counter_packer', 'Front Counter & Packer', [
        'read'                 => true,
        'manage_woocommerce'   => true,   // needed to access Woo menu
        'read_shop_order'      => true,
        'edit_shop_orders'     => true,
        'delete_shop_orders'   => false,
        'publish_shop_orders'  => false,  // cannot create new orders
    ]);

    // Group 2: Order Managers
    add_role('order_manager', 'Order Manager', [
        'read'                 => true,
        'manage_woocommerce'   => true,
        'read_shop_order'      => true,
        'edit_shop_orders'     => true,
        'delete_shop_orders'   => true,
        'publish_shop_orders'  => true,   // can create orders
    ]);
});
