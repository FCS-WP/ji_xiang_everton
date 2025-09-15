<script>
  jQuery(document).ready(function($) {
    $('.akk-accordion-header').on('click', function() {
      $(this).next('.akk-accordion-body').slideToggle();
    });

    const $qtyInputs = $('.akk-sub-product-qty');
    const $addToCartBtn = $('.single_add_to_cart_button');
    const $warning = $('.akk-warning');

    $addToCartBtn.on('click', function(e) {
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
