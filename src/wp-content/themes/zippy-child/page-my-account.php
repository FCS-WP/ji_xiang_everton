<?php
/**
 * Template name: WooCommerce - My Account
 *
 * This template adds My account to the sidebar.
 *
 * @package          Flatsome\Templates
 * @flatsome-version 3.19.0
 */

get_header(); ?>

<?php do_action( 'flatsome_before_page' ); ?>

<?php wc_get_template( 'myaccount/header.php' ); ?>

<div class="page-wrapper my-account mb">
	<div class="container" role="main">

		<?php if ( is_user_logged_in() ) { ?>
            <?php 

                $path_URL =  esc_url($_SERVER['REQUEST_URI']); 
                $last_segment = basename($path_URL);

                switch ($last_segment) {
                    case 'my-account':
                        ?>
                            <div class="center_element_theme">
                                <div class="col_main_page">
                                    <div class="list_tab_myaccount">
                                        <div class="item_tab_myaccount">
                                            <a href="/my-account/edit-account">
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
                                            <a href="/my-account/edit-account#password_current">
                                                <div class="icon_item">
                                                    <i class="icon-user"></i>
                                                </div>
                                                <div class="content_item"> 
                                                    <h5>Update My Password</h5>
                                                    <p>Keeps your security accesses in check</p>
                                                </div>
                                            </a>
                                        </div>
                                        <div class="item_tab_myaccount">
                                            <a href="/my-account/orders/">
                                                <div class="icon_item">
                                                    <i class="icon-shopping-basket"></i>
                                                </div>
                                                <div class="content_item"> 
                                                    <h5>My Order History</h5>
                                                    <p>Keeps track of your orders</p>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php
                        break;
                    case 'orders':
                        wc_get_template('myaccount/orders.php');
                        break;
                    case 'edit-account':
                        wc_get_template('myaccount/form-edit-account.php');
                        break;
                        
                    default:
                        return;
                }
            
            ?> 
            

		<?php } else { 

			

		} ?>

	</div>
</div>

<?php do_action( 'flatsome_after_page' ); ?>

<?php get_footer(); ?>
