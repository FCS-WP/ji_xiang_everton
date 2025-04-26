<?php

function handle_process_bar_notification($current, $total, $type)
{
  // Avoid division by zero
  $percentage = ($total > 0) ? round(($current / $total) * 100, 2) : 0;

  if ($current >= $total) {
    return [
      'message' => match ($type) {
        'order' => "Yay! You've hit the min order of $" . $total,
        'delivery' => "Yay! You've hit the min order for free delivery",
        default => '',
      },
      'style' => 'background-color: #2ba862; width: 100%',
    ];
  }

  $hit_minimum = $total - $current;
  return [
    'message' => match ($type) {
      'order' => '$' . $hit_minimum . ' more for minimum order',
      'delivery' => '$' . $hit_minimum . ' more for Free delivery',
      default => '',
    },
    'style' => 'background-color: #f1b32c; width: ' . $percentage . '%',
  ];
}

// Prepare params
$minimum_order = $args['rules']['minimum_total_to_order'];
$minimum_delivery = $args['rules']['minimum_total_to_freeship'];
$total_cart = $args['total_cart'];

$order_mode = function_exists('WC') ? WC()->session->get('order_mode') : null;

if (
  !empty($order_mode) && $order_mode === 'delivery'
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
