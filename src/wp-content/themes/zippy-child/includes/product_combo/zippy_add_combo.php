<?php


add_action('woocommerce_before_add_to_cart_button', 'combo_display_sub_products_on_frontend');
function combo_display_sub_products_on_frontend()
{
  global $product;

  $list_sub_products = get_field('product_combo', $product->get_id());
  $combo_warning = get_field('combo_warning', $product->get_id());

?>
  <?php if (!empty($list_sub_products)): ?>
    <div class="akk-accordion">
      <div class="akk-accordion-header">AKK</div>
      <div class="akk-accordion-body">
        <div class="combo-warning akk-warning"><?php echo $combo_warning; ?></div>
        <div class="product-combo">
          <?php
          foreach ($list_sub_products as $sub_products) {
            if (empty($sub_products) || !is_array($sub_products)) continue;

            echo '<div class="akk-sub-products">';
            foreach ($sub_products as $sub_product_post) {
              $product_id = is_object($sub_product_post) ? $sub_product_post->ID : $sub_product_post;
              $sub_product = wc_get_product($product_id);
              if (!$sub_product) continue;

              $stock_level = $sub_products['stock_level'] ?? 999;
              $min_qty = $sub_products['minimum_quantity'] ?? 0;
              $image_url = get_the_post_thumbnail_url($sub_product->get_id(), 'full');

              echo '<div class="akk-sub-product">';
              echo '<div class="sub-product-image">';
              echo '<a data-fancybox="img-' . esc_attr($sub_product->get_id()) . '" href="' . esc_url($image_url) . '">';
              echo '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($sub_product->get_name()) . '" width="60">';
              echo '</a>';
              echo '<label>' . esc_html($sub_product->get_name()) . ' (' . wc_price($sub_product->get_price()) . ')</label><br>';
              echo '</div>';


              echo '<div class="sub-product-info">';
              echo render_flatsome_quantity_input($sub_product, $stock_level, $min_qty);
              echo '</div>';

              echo '</div>';
            }
            echo '</div>';
          }
          ?>
        </div>
      </div>
    </div>
  <?php endif; ?>
  <div class="akk-accordion">
    <div class="akk-accordion-header">Packing Instructions</div>
    <div class="akk-accordion-body">
      <input id="packing_instructions" name="packing_instructions" class="packing-input" type="text" placeholder="Leave your packing instructions here. Example: 5 per box">
    </div>
  </div>

  <script>
    jQuery(document).ready(function($) {
      $('.akk-accordion-header').on('click', function() {
        $(this).next('.akk-accordion-body').slideToggle();
      });

      const $qtyInputs = $('.akk-sub-product-qty');
      const $addToCartBtn = $('.single_add_to_cart_button');
      const $comboDisplay = $('#akk-combo-price');
      const $warning = $('.akk-warning');

      function updateComboPrice() {
        let total = 0;
        $qtyInputs.each(function() {
          const price = parseFloat($(this).data('price')) || 0;
          const qty = parseFloat($(this).val()) || 0;
          total += price * qty;
        });

        if ($addToCartBtn.length) {
          $addToCartBtn.text('Add $' + total.toFixed(1));
        }

        if ($comboDisplay.length) {
          $comboDisplay.text(total);
        }

        let totalQty = 0;
        $qtyInputs.each(function() {
          totalQty += parseInt($(this).val()) || 0;
        });

        if (totalQty < 10) {
          $addToCartBtn.prop('disabled', true);
          $warning.show();
        } else {
          $addToCartBtn.prop('disabled', false);
          $warning.hide();
        }
        $qtyInputs.each(function() {
          const $input = $(this);
          const currentVal = parseInt($input.val()) || 0;
          const minVal = parseInt($input.attr('min')) || 0;
          const $minusBtn = $input.siblings('.ux-quantity__button--minus');

          if (currentVal <= minVal) {
            $minusBtn.prop('disabled', true);
            $input.prop('readonly', true);
          } else {
            $minusBtn.prop('disabled', false);
            $input.prop('readonly', false);
          }
        });


      }


      $qtyInputs.on('input change', updateComboPrice);
      if ($('.product-combo').length > 0) {
        updateComboPrice();
      };
      if (typeof Fancybox !== 'undefined' && typeof Fancybox.bind === 'function') {
        Fancybox.bind('[data-fancybox]', {});
      }

    });
  </script>
<?php
}




add_filter('woocommerce_add_cart_item_data', 'capture_selected_sub_products', 10, 2);
function capture_selected_sub_products($cart_item_data, $product_id)
{
  if (!empty($_POST['akk_sub_products']) && is_array($_POST['akk_sub_products'])) {
    $selected = [];
    foreach ($_POST['akk_sub_products'] as $product_id => $qty) {
      $qty = intval($qty);
      if ($qty > 0) {
        $selected[$product_id] = $qty;
      }
    }

    if (!empty($selected)) {
      $cart_item_data['akk_selected'] = $selected;
      $cart_item_data['unique_key'] = md5(json_encode($selected));
    }
  }
  if (!empty($_POST['packing_instructions'])) {
    $cart_item_data['packing_instructions'] = $_POST['packing_instructions'];
  }


  return $cart_item_data;
}

add_action('woocommerce_cart_loaded_from_session', 'restore_combo_price_from_session');
function restore_combo_price_from_session($cart)
{
  foreach ($cart->get_cart() as $cart_item_key => $item) {
    if (isset($item['akk_selected'])) {
      $total_price = 0;
      foreach ($item['akk_selected'] as $product_id => $qty) {
        $product = wc_get_product($product_id);
        if ($product && $qty > 0) {
          $total_price += $product->get_price() * $qty;
        }
      }
      if ($total_price > 0) {
        $cart->cart_contents[$cart_item_key]['data']->set_price($total_price);
      }
    }
  }
}
