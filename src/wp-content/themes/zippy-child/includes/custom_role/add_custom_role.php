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

    // Staff role
    add_role(
        'staff',
        'Staff',
        [
            'read' => true,
            'level_0' => true,
        ]
    );
}
add_action('init', 'custom_add_custom_user_roles');

