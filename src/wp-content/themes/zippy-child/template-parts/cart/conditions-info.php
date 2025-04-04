<?php

function handle_process_bar_notification($current, $total, $type)
{
  // Avoid division by zero
  $percentage = ($total > 0) ? round(($current / $total) * 100, 2) : 0;

  if ($current >= $total) {
    return [
      'message' => match ($type) {
        'order' => "Yay! You've hit the min order of",
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



//Prepare param 

$minium_order = $args['rules']['minimun_total_to_order'];
$minium_delivery = $args['rules']['minimun_total_to_freeship'];
$total_cart = $args['total_cart'];

if (
  !empty($_SESSION['order_mode']) && $_SESSION['order_mode'] === 'delivery'
  && ($minium_order != 0 || $minium_delivery != 0)
) {
?>
  <div class="inforMinimunRule">

    <?php if ($minium_delivery != 0): ?>
      <div class="rule_checkout_mini_cart">
        <?php
        $shipping_condition_info =  handle_process_bar_notification($total_cart, $minium_delivery, 'delivery');
        ?>
        <div class="minimum_order">
          <p>
            <span id="freeshipNeedMore"><?php echo $shipping_condition_info['message']; ?>
            </span>
          </p>
          <div id="freeDeliveryProgress" class="bar_process_full">
            <div id="freeDelivery" style=" <?php echo  $shipping_condition_info['style'];?>" class="bar_process_custom"></div>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <?php if ($minium_order != 0): ?>
      <div class="rule_checkout_mini_cart">
        <?php
        $order_condition_info =  handle_process_bar_notification($total_cart, $minium_order, 'order');
        ?>
        <div class="minimum_order">
          <p>
            <span id="deliveryNeedMore"><?php echo $order_condition_info['message']; ?>
            </span>
          </p>
          <div id="minimunOrderProgress" class="bar_process_full">
            <div id="minimunOrder" style=" <?php echo  $order_condition_info['style'];?>" class="bar_process_custom"></div>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>
<?php
}
?>
