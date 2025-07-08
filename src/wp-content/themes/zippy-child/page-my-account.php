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

<?php do_action('flatsome_before_page'); ?>

<?php wc_get_template('myaccount/header.php'); ?>

<div class="page-wrapper my-account mb">
	<div class="container" role="main">
		<div id="download_order_file" class="download_button">
			<span class="tp_loader"></span>
			<div class="select_wrapper">
				<select name="download_order" id="download_order">
					<option value="">Download</option>
					<option value="pdf">Download all transactions (PDF)</option>
					<option value="csv">Download all transactions (CSV)</option>
				</select>
				<p class="message"></p>
			</div>
		</div>

		<?php if (have_posts()) :
			while (have_posts()) : the_post();
				the_content();
			endwhile;
		endif; ?>
	</div>
</div>

<?php do_action('flatsome_after_page'); ?>

<?php get_footer(); ?>