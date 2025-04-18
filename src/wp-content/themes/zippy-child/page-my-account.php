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
            <?php while ( have_posts() ) : the_post(); ?>
                <?php the_content(); ?>
            <?php endwhile; // end of the loop. ?>
		<?php } ?>
	</div>
</div>

<?php do_action( 'flatsome_after_page' ); ?>

<?php get_footer(); ?>
