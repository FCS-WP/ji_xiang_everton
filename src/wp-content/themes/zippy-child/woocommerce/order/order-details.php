<?php
defined('ABSPATH') || exit;

$order = wc_get_order($order_id);
if (! $order) {
	return;
}

$item_totals         = $order->get_order_item_totals();
$order_items         = $order->get_items(apply_filters('woocommerce_purchase_order_item_types', 'line_item'));
$show_purchase_note  = $order->has_status(apply_filters('woocommerce_purchase_note_order_statuses', ['completed', 'processing']));
$downloads           = $order->get_downloadable_items();
$show_customer_details = $order->get_user_id() === get_current_user_id();

if ($show_downloads) {
	wc_get_template('order/order-downloads.php', [
		'downloads'  => $downloads,
		'show_title' => true,
	]);
}
?>
<section class="woocommerce-order-details">
	<?php do_action('woocommerce_order_details_before_order_table', $order); ?>

	<h2 class="woocommerce-order-details__title"><?php esc_html_e('Order details', 'woocommerce'); ?></h2>

	<table class="woocommerce-table woocommerce-table--order-details shop_table order_details">
		<thead>
			<tr>
				<th class="woocommerce-table__product-name"><?php esc_html_e('Product', 'woocommerce'); ?></th>
				<th class="woocommerce-table__product-table"><?php esc_html_e('Total', 'woocommerce'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			do_action('woocommerce_order_details_before_order_table_items', $order);

			foreach ($order_items as $item_id => $item) {
				$product = $item->get_product();
				wc_get_template('order/order-details-item.php', [
					'order'              => $order,
					'item_id'            => $item_id,
					'item'               => $item,
					'show_purchase_note' => $show_purchase_note,
					'purchase_note'      => $product ? $product->get_purchase_note() : '',
					'product'            => $product,
				]);
			}

			do_action('woocommerce_order_details_after_order_table_items', $order);
			?>
		</tbody>
		<tfoot>
			<tr>
				<th><?php esc_html_e('Subtotal:', 'woocommerce'); ?></th>
				<td><?php echo $item_totals['cart_subtotal']['value']; ?></td>
			</tr>

			<?php if (defined('BILLING_METHOD') && $order->get_meta(BILLING_METHOD) === 'delivery'): ?>
				<tr>
					<th><?php esc_html_e('Shipping Fee:', 'woocommerce'); ?></th>
					<td><?php echo wc_price($order->get_shipping_total()); ?></td>
				</tr>

				<?php if ($order->get_total_fees() > 0): ?>
					<tr>
						<th><?php esc_html_e('Extra Fee:', 'woocommerce'); ?></th>
						<td><?php echo wc_price($order->get_total_fees()); ?></td>
					</tr>
				<?php endif; ?>
			<?php endif; ?>

			<tr>
				<th><?php esc_html_e('GST (included):', 'woocommerce'); ?></th>
				<td><?php echo wc_price($order->get_total() - ($order->get_total() / 1.09)); ?></td>
			</tr>

			<tr>
				<th><?php esc_html_e('Total:', 'woocommerce'); ?></th>
				<td><?php echo wc_price($order->get_total()); ?></td>
			</tr>

			<tr>
				<th><?php esc_html_e('Payment Method:', 'woocommerce'); ?></th>
				<td><?php echo esc_html($order->get_payment_method_title()); ?></td>
			</tr>

			<?php if ($order->get_customer_note()): ?>
				<tr>
					<th><?php esc_html_e('Note:', 'woocommerce'); ?></th>
					<td><?php echo wp_kses(nl2br(wptexturize($order->get_customer_note())), []); ?></td>
				</tr>
			<?php endif; ?>
		</tfoot>
	</table>

	<?php do_action('woocommerce_order_details_after_order_table', $order); ?>
</section>

<section class="woocommerce-addons-details">
	<h2 class="woocommerce-column__title" style="text-transform: capitalize;">
		<?php echo esc_html($order->get_meta(BILLING_METHOD)) . ' ' . esc_html__('details', 'woocommerce'); ?>
	</h2>

	<table class="shop_table">
		<tbody>
			<tr>
				<th><?php esc_html_e('Outlet Name', 'woocommerce'); ?></th>
				<td><?php echo esc_html($order->get_meta(BILLING_OUTLET)); ?></td>
			</tr>
			<tr>
				<th><?php esc_html_e('Outlet Address', 'woocommerce'); ?></th>
				<td><?php echo esc_html($order->get_meta(BILLING_OUTLET_ADDRESS)); ?></td>
			</tr>

			<?php if (function_exists('is_delivery') && is_delivery()): ?>
				<tr>
					<th><?php esc_html_e('Delivery Date', 'woocommerce'); ?></th>
					<td><?php echo esc_html($order->get_meta(BILLING_DATE)); ?></td>
				</tr>
				<tr>
					<th><?php esc_html_e('Delivery Time', 'woocommerce'); ?></th>
					<td><?php echo esc_html($order->get_meta(BILLING_TIME)); ?></td>
				</tr>
			<?php elseif (function_exists('is_takeaway') && is_takeaway()): ?>
				<tr>
					<th><?php esc_html_e('Takeaway Date', 'woocommerce'); ?></th>
					<td><?php echo esc_html($order->get_meta(BILLING_DATE)); ?></td>
				</tr>
				<tr>
					<th><?php esc_html_e('Takeaway Time', 'woocommerce'); ?></th>
					<td><?php echo esc_html($order->get_meta(BILLING_TIME)); ?></td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>
</section>

<?php
do_action('woocommerce_after_order_details', $order);

if ($show_customer_details) {
	wc_get_template('order/order-details-customer.php', ['order' => $order]);
}
