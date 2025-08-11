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
              zippy_refresh_mini_cart();

              setTimeout(() => {
                location.reload(true);
              }, 1000);
            });
          },
          error: function () {
            Swal.fire("Error!", "Something went wrong.", "error");
          },
        });
      }
    });
  });
});

// Reload Mini cart when added items.
$(document).ready(function () {
  $("body").on("click", ".single_add_to_cart_button", function (param) {
    setTimeout(() => {
      zippy_refresh_mini_cart();
    }, 1000);
  });
});

function zippy_refresh_mini_cart() {
  var $supports_html5_storage = true,
    cart_hash_key = wc_cart_fragments_params.cart_hash_key;

  try {
    $supports_html5_storage =
      "sessionStorage" in window && window.sessionStorage !== null;
    window.sessionStorage.setItem("wc", "test");
    window.sessionStorage.removeItem("wc");
    window.localStorage.setItem("wc", "test");
    window.localStorage.removeItem("wc");
  } catch (err) {
    $supports_html5_storage = false;
  }

  /* Cart session creation time to base expiration on */
  function set_cart_creation_timestamp() {
    if ($supports_html5_storage) {
      sessionStorage.setItem("wc_cart_created", new Date().getTime());
    }
  }

  /** Set the cart hash in both session and local storage */
  function set_cart_hash(cart_hash) {
    if ($supports_html5_storage) {
      localStorage.setItem(cart_hash_key, cart_hash);
      sessionStorage.setItem(cart_hash_key, cart_hash);
    }
  }

  $.ajax({
    url: wc_cart_fragments_params.wc_ajax_url
      .toString()
      .replace("%%endpoint%%", "get_refreshed_fragments"),
    type: "POST",
    data: {
      time: new Date().getTime(),
    },
    timeout: wc_cart_fragments_params.request_timeout,
    success: function (data) {
      if (data && data.fragments) {
        $.each(data.fragments, function (key, value) {
          $(key).replaceWith(value);
        });

        if ($supports_html5_storage) {
          sessionStorage.setItem(
            wc_cart_fragments_params.fragment_name,
            JSON.stringify(data.fragments)
          );
          set_cart_hash(data.cart_hash);

          if (data.cart_hash) {
            set_cart_creation_timestamp();
          }
        }

        $(document.body).trigger("wc_fragments_refreshed");
      }
    },
  });
}
