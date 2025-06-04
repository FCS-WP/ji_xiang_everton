<?php

/**
 * My Account Dashboard
 *
 * Shows the first intro screen on the account dashboard.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/dashboard.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.4.0
 */

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

$allowed_html = array(
    'a' => array(
        'href' => array(),
    ),
);
?>
<!-- My Account Dashboard -->
<div class="center_element_theme">
    <div class="col_main_page">
        <div class="list_tab_myaccount">
            <div class="item_tab_myaccount">
                <a href="<?php echo esc_url(wc_get_endpoint_url('edit-account')) ?>">
                    <div class="icon_item">
                        <i class="icon-user"></i>
                    </div>
                    <div class="content_item">
                        <h5>Update My Profile</h5>
                        <p>Access your account details</p>
                    </div>
                </a>
            </div>
            <div class="item_tab_myaccount">
                <a href="<?php echo esc_url( wc_get_endpoint_url( 'orders' ) ) ?>">
                    <div class="icon_item">
                        <i class="icon-shopping-basket"></i>
                    </div>
                    <div class="content_item">
                        <h5>My Order History</h5>
                        <p>Keeps track of your orders</p>
                    </div>
                </a>
            </div>
            <div class="item_tab_myaccount">
                <a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address' )) ?>">
                    <div class="icon_item">
                        <i class="icon-map-pin-fill"></i>
                    </div>
                    <div class="content_item">
                        <h5>My Address Book</h5>
                        <p>Keeps track of your addresses</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>