<div class="quickcheckout-order-info">
  <table>
    <tbody>
      <tr>
        <td>Outlet Name:</td>
        <td>
          <?php echo WC()->session->get('outlet_name'); ?>
          <input type="hidden" name="billing_outlet" id="billing_outlet" value="<?php echo esc_attr(zippy_get_wc_session('outlet_name') ?? ''); ?>">
          <input type="hidden" name="billing_outlet_address" id="billing_outlet_address" value="<?php echo esc_attr(zippy_get_wc_session('outlet_address') ?? ''); ?>">

        </td>
      </tr>
      <?php
      if (is_delivery()) {
      ?>
        <tr>
          <td>Delivery Address:</td>
          <td><?php echo WC()->session->get('delivery_address'); ?></td>
        </tr>
      <?php
      }
      ?>
      <tr>
        <td> Delivery Date:</td>
        <td>
          <?php echo WC()->session->get('date'); ?>
          <input type="hidden" name="billing_date" id="billing_date" value="<?php echo esc_attr(zippy_get_wc_session('date') ?? ''); ?>">
        </td>
      </tr>
      <tr>
        <td>Delivery Time:</td>
        <td>
          <?php echo zippy_get_delivery_time(); ?>
          <input type="hidden" name="billing_time" id="billing_time" value="<?php echo zippy_get_delivery_time(); ?>">
        </td>

      </tr>
    </tbody>
  </table>
</div>
