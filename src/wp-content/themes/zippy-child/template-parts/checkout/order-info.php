<table class="shop_table cart_custom" cellspacing="0">
  <thead>
    <tr>
      <th class="product-thumbnail">Image</th>
      <th class="product-name">Product Name</th>
      <th class="product-price_custom">Price</th>
      <th class="product-quantity">Quantity</th>
      <th class="product-subtotal_custom">Total</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $cart_subtotal = 0;
    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
      $_product   = $cart_item['data'];
      $product_id = $cart_item['product_id'];

      if ($_product && $_product->exists() && $cart_item['quantity'] > 0) {
        $product_permalink = get_permalink($product_id);
        $cart_subtotal += $cart_item['line_total'];
    ?>

        <tr>
          <td class="product-thumbnail">
            <?php echo $_product->get_image(); ?>
          </td>

          <td class="product-name">
            <a href="<?php echo esc_url($product_permalink); ?>">
              <?php echo $_product->get_name(); ?>
            </a>
            <div class="akk-sub-products-list">
              <?php foreach ($cart_item['akk_selected'] as $sub_product_id => $qty): ?>
              <?php if ($qty <= 0) continue;
                $sub_product=wc_get_product($sub_product_id);
                if (!$sub_product) continue; ?>
                <p class="akk-sub-product"><strong> <?php echo esc_html($sub_product->get_name()).' ('.wc_price($sub_product->get_price()).')' . ' x ' . intval($qty)  ?></strong></p>
              <?php endforeach; ?>
            </div>
          </td>

          <td class="product-price_custom">
            <?php echo wc_price($_product->get_price()); ?>
          </td>

          <td class="product-quantity">
            x <?php echo esc_html($cart_item['quantity']); ?>
          </td>

          <td class="product-subtotal_custom">
            <?php echo wc_price($cart_item['line_total']); ?>
          </td>
        </tr>

    <?php }
    }
    $rule = get_minimum_rule_by_order_mode();
    $fee_delivery = 0;
    $extra_fee = !empty(WC()->session->get('extra_fee')) ? WC()->session->get('extra_fee') : 0;

    if ($cart_subtotal < $rule["minimum_total_to_order"]) {
      if (WC()->session->get('order_mode') !== 'takeaway') {
        $fee_delivery = WC()->session->get('shipping_fee');
      }
    }
    ?>
    <tr>
      <td colspan="4" class="text-right"><strong>Sub-total:</strong></td>
      <td><?php echo wc_price($cart_subtotal); ?></td>
    </tr>
    <tr>
      <td colspan="4" class="text-right">
        <strong>Delivery Fee:</strong>
      </td>
      <td><?php echo WC()->cart->get_cart_shipping_total(); ?></td>
    </tr>
    <?php if ($extra_fee != 0): ?>
      <tr>
        <td colspan="4" class="text-right"><strong>Extra Fee:</strong></td>
        <td><?php echo wc_price(WC()->session->get('extra_fee')); ?></td>
      </tr>
    <?php endif; ?>
    <tr>
      <td colspan="4" class="text-right"><strong>GST:</strong></td>
      <td><?php echo wc_cart_totals_taxes_total_html(); ?></td>
    </tr>

    <tr>
      <td colspan="4" class="text-right"><strong>Total:</strong></td>
      <td><strong><?php echo wc_cart_totals_order_total_html() ?></strong></td>
    </tr>
  </tbody>
</table>