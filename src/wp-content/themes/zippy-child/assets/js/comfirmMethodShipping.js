"use strict";
$ = jQuery;

$(document).ready(function() {
    $("#removeMethodShipping").click(function() {
        Swal.fire({
            title: 'Are you sure to change order mode?',
            text: "Your current cart will be cleared",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                
                $.ajax({
                    url: '/wp-admin/admin-ajax.php', 
                    type: 'POST',
                    data: {
                        action: 'remove_cart_session'
                    },
                    success: function(response) {
                        Swal.fire('Deleted!', 'Your cart has been cleared.', 'success')
                        .then(() => {
                            location.reload(); 
                        });
                    },
                    error: function() {
                        Swal.fire('Error!', 'Something went wrong.', 'error');
                    }
                });
            }
        });
    });

    function showAlertRemoveProduct(minimunQuanity) {
        Swal.fire({
            title: 'Warning',
            text: 'This product requires a minimum of ' + minimunQuanity,
            icon: 'warning',
            confirmButtonText: 'OK'
        });
    }
});
