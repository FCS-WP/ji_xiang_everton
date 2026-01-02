<div class="quickcheckout-order-info">
  <table>
    <tbody>
      <tr>
        <td>Outlet Name:</td>
        <td>
          <?php echo WC()->session->get('outlet_name'); ?>
          <input type="hidden" name="_billing_outlet" id="_billing_outlet" value="<?php echo esc_attr(zippy_get_wc_session('outlet_name') ?? ''); ?>">
          <input type="hidden" name="_billing_outlet_address" id="_billing_outlet_address" value="<?php echo esc_attr(zippy_get_wc_session('outlet_address') ?? ''); ?>">

        </td>
      </tr>

      <?php if (is_delivery()) : ?>
        <tr>
          <td>Delivery Address:</td>
          <td><?php echo get_delivery_address(); ?></td>
        </tr>
        <tr>
          <td> Delivery Date:</td>
          <td>
            <?php echo date('D, j M Y', strtotime(WC()->session->get('date'))); ?>
            <input type="hidden" name="_billing_date" id="_billing_date" value="<?php echo esc_attr(zippy_get_wc_session('date') ?? ''); ?>">
          </td>
        </tr>
        <tr>
          <td>Delivery Time:</td>
          <td>
            <?php echo zippy_get_delivery_time(); ?>
            <input type="hidden" name="_billing_time" id="_billing_time" value="<?php echo zippy_get_delivery_time(); ?>">
          </td>

        </tr>
      <?php endif ?>

      <?php if (is_takeaway()): ?>
        <tr>
          <td> Takeaway Date:</td>
          <td>
            <?php echo date('D, j M Y', strtotime(WC()->session->get('date'))); ?>
            <input type="hidden" name="_billing_date" id="_billing_date" value="<?php echo esc_attr(zippy_get_wc_session('date') ?? ''); ?>">
          </td>
        </tr>
        <tr>
          <td>Takeaway Time:</td>
          <td>
            <?php echo zippy_get_delivery_time(); ?>
            <input type="hidden" name="_billing_time" id="_billing_time" value="<?php echo zippy_get_delivery_time(); ?>">
          </td>

        </tr>
      <?php endif; ?>

    </tbody>
  </table>
</div>
