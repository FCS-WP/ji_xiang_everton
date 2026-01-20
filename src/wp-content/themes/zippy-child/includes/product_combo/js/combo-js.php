<script>
  jQuery(function($) {
    'use strict';

    // Init 
    const $document = $(document);
    const $qtyInputs = $('.akk-sub-product-qty');
    const $addToCartBtn = $('.single_add_to_cart_button');
    const $comboDisplay = $('#akk-combo-price');
    const $totalQtyInput = $('.total-product-quantity');
    const $minmaxSelect = $('#min_max_option');
    const $btnContactForSale = $('.contact_for_sale_btn');
    const $productCombo = $('.product-combo');
    const MIN_MAX_PRICE_MAP = JSON.parse($('#combo_extra_price').val());
    const originalBtnText = $addToCartBtn.text();
    const minmaxOptionOther = 'others';

    // const extraPrice = parseFloat($('#combo_extra_price').val()) || 0;
    function getExtraPriceByMinMax() {
      const selected = parseInt($minmaxSelect.val(), 10);
      return MIN_MAX_PRICE_MAP[selected] || 0;
    }


    // Accordion
    $document.on('click', '.akk-accordion-header', function() {
      $(this).next('.akk-accordion-body').stop(true, true).slideToggle(200);
    });

    //Min / Max option handling
    function toggleMinMaxUI(value) {
      if (value && value.toLowerCase() === minmaxOptionOther) {
        $btnContactForSale.show();
        $addToCartBtn.hide();
      } else {
        $btnContactForSale.hide();
        $addToCartBtn.show();
      }
    }

    toggleMinMaxUI($minmaxSelect.val());

    $minmaxSelect.on('change', function() {
      toggleMinMaxUI(this.value);
    });

    $minmaxSelect.on('change', function() {
      toggleMinMaxUI(this.value);
      updateComboPrice(); // recalc price when option changes
    });


    //Combo price calculation
    function updateComboPrice() {
      let total = 0;

      $qtyInputs.each(function() {
        const price = parseFloat($(this).data('price')) || 0;
        const qty = parseInt($(this).val(), 10) || 0;
        total += price * qty;
      });

      const multiplier = parseInt($totalQtyInput.val(), 10) || 1;
      total *= multiplier;

      if (total <= 0) {
        $addToCartBtn.text(originalBtnText);
        $comboDisplay.text('0');
        return;
      }

      // const totalExtraPrice = extraPrice > 0 ? multiplier * extraPrice : 0;
      const extraPrice = getExtraPriceByMinMax();
      const totalExtraPrice = extraPrice > 0 ? multiplier * extraPrice : 0;

      const extraText = totalExtraPrice ? ` + $${totalExtraPrice.toFixed(2)}` : '';

      $addToCartBtn.text(`Add $${total.toFixed(2)}${extraText}`);
      $comboDisplay.text(total.toFixed(2));

      /* Disable minus button at min */
      $qtyInputs.each(function() {
        const $input = $(this);
        const currentVal = parseInt($input.val(), 10) || 0;
        const minVal = parseInt($input.attr('min'), 10) || 0;
        const $minusBtn = $input.siblings('.ux-quantity__button--minus');

        $minusBtn.prop('disabled', currentVal <= minVal);
        $input.prop('readonly', false);
      });
    }

    $document.on('input change', '.akk-sub-product-qty, .total-product-quantity', updateComboPrice);

    if ($productCombo.length) {
      updateComboPrice();
    }

    /* =========================
     * Validation helpers
     * ========================= */
    function showAlert(html) {
      Swal.fire({
        icon: 'warning',
        title: 'Attention',
        html: html,
        confirmButtonText: 'OK',
        confirmButtonColor: '#e74c3c'
      });
    }

    function getTotalQty() {
      let total = 0;
      $qtyInputs.each(function() {
        total += parseInt($(this).val(), 10) || 0;
      });
      return total;
    }

    function checkMinMaxOption(e) {
      if (!$minmaxSelect.length) return true;

      const option = $minmaxSelect.val();
      if (!option) {
        e.preventDefault();
        showAlert('Please select an option before adding to cart!');
        return false;
      }

      if (option.toLowerCase() === minmaxOptionOther) {
        return true;
      }

      const requiredQty = parseInt(option, 10);
      const totalQty = getTotalQty();

      if (totalQty !== requiredQty) {
        e.preventDefault();
        showAlert(
          `Please select exactly <strong>${requiredQty}</strong> items.<br>
         <strong>(${totalQty} / ${requiredQty})</strong>`
        );
        return false;
      }

      return true;
    }

    //Add to cart validation
    $addToCartBtn.on('click', function(e) {
      if (!checkMinMaxOption(e)) return false;

      let totalQty = 0;
      let groupTotal = 0;

      $qtyInputs.each(function() {
        const qty = parseInt($(this).val(), 10) || 0;
        totalQty += qty;

        if ($(this).data('group') !== undefined) {
          groupTotal += qty;
        }
      });

      const minOrder = parseInt($productCombo.data('min-order'), 10) || 1;
      const comboName = $productCombo.data('combo-name') || 'items';

      const groupsData = <?php echo json_encode($groups); ?>;
      const requiredGroupQty = parseInt(groupsData.quantity_products_group, 10) || 0;

      if (requiredGroupQty && groupTotal !== requiredGroupQty) {
        e.preventDefault();
        showAlert(`Please select exactly ${requiredGroupQty} items in this group.`);
        return false;
      }

      if (totalQty < minOrder && $productCombo.length) {
        e.preventDefault();
        showAlert(`Please select at least ${minOrder} ${comboName} in total!`);
        return false;
      }
    });

    //Fancybox
    if (window.Fancybox?.bind) {
      Fancybox.bind('[data-fancybox]', {});
    }

  });
</script>
