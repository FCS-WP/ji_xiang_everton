<script>
  jQuery(document).ready(function($) {
    $('.akk-accordion-header').on('click', function() {
      $(this).next('.akk-accordion-body').slideToggle();
    });

    const $qtyInputs = $('.akk-sub-product-qty');
    const $addToCartBtn = $('.single_add_to_cart_button');
    const $comboDisplay = $('#akk-combo-price');
    const $warning = $('.akk-warning');
    const $totalQtyInput = $('.total-product-quantity');
    const originalText = $addToCartBtn.text();
    const originalType = $addToCartBtn.attr('type');
    const $minmaxSelect = $('#min_max_option');
    const $btnContactForSale = $('.contact_for_sale_btn');
    const extra_price = $('#combo_extra_price').val() ? parseFloat($('#combo_extra_price').val()) : 0;

    //Min max options
    const minmaxOptionOther = 'others';

    if (!$minmaxSelect.val()) {
      $btnContactForSale.hide();
    }

    $minmaxSelect.on('change', function() {
      const val = $(this).val();

      if (val && val.toLowerCase() === minmaxOptionOther) {
        $btnContactForSale.show();
        $addToCartBtn.hide();
      } else {
        $btnContactForSale.hide();
        $addToCartBtn.show();
      }
    });


    function updateComboPrice() {
      let total = 0;
      $qtyInputs.each(function() {
        const price = parseFloat($(this).data('price')) || 0;
        const qty = parseFloat($(this).val()) || 0;
        total += price * qty;
      });

      const productQuantityInput = parseInt($totalQtyInput.val()) || 0;
      if (productQuantityInput > 0) {
        total = total * productQuantityInput;
      }

      if (total === 0) {
        return;
      }

      if ($addToCartBtn.length) {
        $addToCartBtn.text('Add $' + total.toFixed(2) + ' + $' + extra_price.toFixed(2));
      }

      if ($comboDisplay.length) {
        $comboDisplay.text(total);
      }

      $qtyInputs.each(function() {
        const $input = $(this);
        const currentVal = parseInt($input.val()) || 0;
        const minVal = parseInt($input.attr('min')) || 0;
        const $minusBtn = $input.siblings('.ux-quantity__button--minus');

        if (currentVal <= minVal) {
          $minusBtn.prop('disabled', true);
        } else {
          $minusBtn.prop('disabled', false);
          $input.prop('readonly', false);
        }
      });
    }

    $qtyInputs.on('input change', updateComboPrice);
    if ($('.product-combo').length > 0) {
      updateComboPrice();
    }

    $totalQtyInput.on('input change', function() {
      updateComboPrice();
    });

    function checkMinMaxOption(e) {
      if ($minmaxSelect.length == 0) return true;
      const minmaxOption = $('#min_max_option').val();
      if (!minmaxOption) {
        e.preventDefault();
        Swal.fire({
          icon: 'warning',
          title: 'Attention',
          text: `Please select an option before adding to cart!`,
          confirmButtonText: 'OK',
          confirmButtonColor: '#e74c3c'
        });
        return false;
      }


      if (minmaxOption.toLowerCase() != minmaxOptionOther) {
        let totalQty = 0;
        $('.akk-sub-product-qty').each(function() {
          totalQty += parseInt($(this).val()) || 0;
        });

        if (totalQty < parseInt(minmaxOption)) {
          e.preventDefault();
          Swal.fire({
            icon: 'warning',
            title: 'Attention',
            html: `Please select <strong>${minmaxOption}</strong> items before adding to cart!<br>
            <strong>(${totalQty} / ${minmaxOption})</strong>`,
            confirmButtonText: 'OK',
            confirmButtonColor: '#e74c3c'
          });
          return false;
        }

        if (totalQty > parseInt(minmaxOption)) {
          e.preventDefault();
          Swal.fire({
            icon: 'warning',
            title: 'Attention',
            html: `You have selected more than <strong>${minmaxOption}</strong> items!<br>
            <strong>(${totalQty} / ${minmaxOption})</strong>`,
            confirmButtonText: 'OK',
            confirmButtonColor: '#e74c3c'
          });
          return false;
        }
      }

      return true;
    }

    $addToCartBtn.on('click', function(e) {
      if (!checkMinMaxOption(e)) {
        return false;
      }

      let totalQty = 0;
      let groupTotal = 0;

      $qtyInputs.each(function() {
        const qty = parseInt($(this).val()) || 0;
        totalQty += qty;

        const groupId = $(this).data('group');
        if (groupId !== undefined) {
          groupTotal += qty;
        }
      });

      let minOrder = parseInt($('.product-combo').data('min-order')) || 1;
      let comboName = $('.product-combo').data('combo-name') || 'items';

      let groupsData = <?php echo json_encode($groups); ?>;
      let required = parseInt(groupsData.quantity_products_group) || 0;

      if (required > 0 && groupTotal < required) {
        e.preventDefault();
        Swal.fire({
          icon: 'warning',
          title: 'Attention',
          text: 'Please select at least ' + required + ' items in this group before adding to cart!',
          confirmButtonText: 'OK',
          confirmButtonColor: '#e74c3c'
        });
        return false;
      }
      if (required > 0 && groupTotal > required) {
        e.preventDefault();
        Swal.fire({
          icon: 'warning',
          title: 'Attention',
          text: 'Total is ' + required + ' items in this group before adding to cart!',
          confirmButtonText: 'OK',
          confirmButtonColor: '#e74c3c'
        });
        return false;
      }

      if (totalQty < minOrder && $('.product-combo').length > 0) {
        e.preventDefault();
        Swal.fire({
          icon: 'warning',
          title: 'Attention',
          text: 'Please select at least ' + minOrder + ' ' + comboName + ' in total!',
          confirmButtonText: 'OK',
          confirmButtonColor: '#e74c3c'
        });
        return false;
      }
    });


    if (typeof Fancybox !== 'undefined' && typeof Fancybox.bind === 'function') {
      Fancybox.bind('[data-fancybox]', {});
    }
  });
</script>