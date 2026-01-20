<?php



// Prepare params
$minimum_order = $args['rules']['minimum_total_to_order'];
$minimum_delivery = $args['rules']['minimum_total_to_freeship'];
$total_cart = $args['total_cart'];


if (
  is_delivery()
  && ($minimum_order != 0 || $minimum_delivery != 0)
) :
?>
  <div class="inforMinimunRule">

    <?php if ($minimum_delivery != 0) : ?>
      <div class="rule_checkout_mini_cart">
        <?php
        $shipping_condition_info = handle_process_bar_notification($total_cart, $minimum_delivery, 'delivery');
        ?>
        <div class="minimum_order">
          <p>
            <span id="freeshipNeedMore"><?php echo esc_html($shipping_condition_info['message']); ?></span>
          </p>
          <div id="freeDeliveryProgress" class="bar_process_full">
            <div id="freeDelivery" style="<?php echo esc_attr($shipping_condition_info['style']); ?>" class="bar_process_custom"></div>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <?php if ($minimum_order != 0) : ?>
      <div class="rule_checkout_mini_cart">
        <?php
        $order_condition_info = handle_process_bar_notification($total_cart, $minimum_order, 'order');
        ?>
        <div class="minimum_order">
          <p>
            <span id="deliveryNeedMore"><?php echo esc_html($order_condition_info['message']); ?></span>
          </p>
          <div id="minimunOrderProgress" class="bar_process_full">
            <div id="minimunOrder" style="<?php echo esc_attr($order_condition_info['style']); ?>" class="bar_process_custom"></div>
          </div>
        </div>
      </div>
    <?php endif; ?>

  </div>
<?php
endif;
?>
