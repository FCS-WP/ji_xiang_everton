$(function () {
  $(document).on("click", ".lightbox-zippy-btn", function (e) {
    e.preventDefault();
    const prod_id = $(this).data("prod");
    const product_id = $(this).data("product_id");
    $("#lightbox-zippy-form").attr(
      "data-product_id",
      prod_id ? prod_id : product_id
    );
  });

  $(document).on("click", ".btn-close-lightbox", function () {
    $(".mfp-close").trigger("click");
  });

  document.addEventListener("custom_added_to_cart", function (e) {
    Swal.fire({
      title: "Successfully",
      text: "Product added to cart!",
      icon: "success",
      customClass: {
        confirmButton: "custom-swal-btn-success",
      },
      allowOutsideClick: false,
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.reload();
      }
    });
  });
});
