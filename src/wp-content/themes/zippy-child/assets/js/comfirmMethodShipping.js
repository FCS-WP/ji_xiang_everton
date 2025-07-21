"use strict";
$ = jQuery;

$(document).ready(function () {
  $(document).on("click", "#removeMethodShipping", function () {
    Swal.fire({
      title: "Are you sure to change order mode?",
      text: "Your current cart will be cleared",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Yes!",
      cancelButtonText: "Cancel",
      customClass: {
        popup: "confirmRemovePopup",
      },
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: "/wp-admin/admin-ajax.php",
          type: "POST",
          data: {
            action: "remove_cart_session",
          },
          success: function (response) {
            Swal.fire({
              title: "Deleted!",
              text: "Your cart has been cleared.",
              icon: "success",
              customClass: {
                popup: "popupAlertDeleteSuccess",
              },
            }).then(() => {
              $(document.body).trigger("updated_wc_div");

              location.reload();
            });
          },
          error: function () {
            Swal.fire("Error!", "Something went wrong.", "error");
          },
        });
      }
    });
  });

  $(document).on(
    "click",
    ".single_add_to_cart_button.added",
    function (event, fragments, cart_hash, $button) {
      console.log("first");
      Swal.fire({
        title: "Added to Cart!",
        text: "Product was successfully added to your cart.",
        icon: "success",
        confirmButtonText: "Continue Shopping",
        timer: 2000,
        timerProgressBar: true,
      });
    }
  );
});
