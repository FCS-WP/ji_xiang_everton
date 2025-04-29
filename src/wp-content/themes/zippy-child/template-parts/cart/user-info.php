<?php
if (!function_exists('WC') || !WC()->session->get('order_mode')) return;
?>

<div class="box_infor_method_shipping">
  <div class="items_infor_method_shipping">
    <div class="text_items">
      <h4>Order Mode:</h4>
      <p><?php echo esc_html(WC()->session->get('order_mode')); ?></p>
    </div>
    <div class="icon_items">
      <button id="removeMethodShipping">
        <img src="<?php echo esc_url(get_template_directory_uri() . '-child/assets/icons/edit-light.png'); ?>" alt="Edit">
      </button>
    </div>
  </div>

  <div class="items_infor_method_shipping">
    <div class="text_items">
      <h4>Select Outlet:</h4>
      <p><?php echo esc_html(WC()->session->get('outlet_name')); ?></p>
    </div>
  </div>

  <?php if (WC()->session->get('order_mode') === 'delivery') : ?>
    <div class="items_infor_method_shipping">
      <div class="text_items">
        <h4>Delivery Address:</h4>
        <p><?php echo esc_html(WC()->session->get('delivery_address')); ?></p>
      </div>
    </div>
  <?php endif; ?>

  <div class="items_infor_method_shipping">
    <div class="text_items">
      <h4><?php echo (WC()->session->get('order_mode') === 'takeaway') ? 'Takeaway' : 'Delivery'; ?> Time:</h4>
      <p>
        <?php
        $date = WC()->session->get('date');
        $time = WC()->session->get('time');
        echo esc_html(format_date_DdMY($date)) . '<br>';
        if (is_array($time)) {
          echo 'From ' . esc_html($time['from']) . ' To ' . esc_html($time['to']);
        }
        ?>
      </p>
    </div>
  </div>
</div>
