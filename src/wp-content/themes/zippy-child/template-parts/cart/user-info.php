  <?php if (!isset($_SESSION['order_mode'])) return; ?>

  <div class="box_infor_method_shipping">
    <div class="items_infor_method_shipping">
      <div class="text_items">
        <h4>Order Mode:</h4>
        <p><?php echo $_SESSION['order_mode']; ?></p>
      </div>
      <div class="icon_items">
        <button id="removeMethodShipping">
          <img src="<?php echo get_template_directory_uri() . '-child/assets/icons/edit-light.png'; ?>">
        </button>
      </div>
    </div>
    <div class="items_infor_method_shipping">
      <div class="text_items">
        <h4>Select Outlet:</h4>
        <p><?php echo $_SESSION['outlet_name']; ?></p>
      </div>
    </div>
    <?php
    if ($_SESSION['order_mode'] == 'delivery') {
    ?>
      <div class="items_infor_method_shipping">
        <div class="text_items">
          <h4>Delivery Address:</h4>
          <p><?php echo $_SESSION["delivery_address"]; ?></p>
        </div>
      </div>
    <?php
    }
    ?>
    <div class="items_infor_method_shipping">
      <div class="text_items">
        <h4><?php if ($_SESSION['order_mode'] == 'takeaway') {
              echo 'Takeaway';
            } else {
              echo 'Delivery';
            } ?> Time:</h4>
        <p><?php echo format_date_DdMY($_SESSION['date']); ?><br><?php echo 'From ' . $_SESSION['time']['from'] . ' To ' . $_SESSION['time']['to']; ?></p>
      </div>
    </div>
    <?php
    if (!empty($_SESSION["shipping_fee"])) {
    ?>
      <div class="items_infor_method_shipping">
        <div class="text_items">
          <h4>Shipping Fee:</h4>
          <p>$<?php echo $_SESSION["shipping_fee"]; ?></p>
        </div>
      </div>
    <?php
    }
    ?>
  </div>
