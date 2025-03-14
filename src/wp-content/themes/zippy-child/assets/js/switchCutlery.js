$ = jQuery;

$(document).ready(function () {
    $("#switchButton").on("click", function () {
        $(this).toggleClass("active");

        if ($(this).hasClass("active")) {
            $("#billing_cutlery").val("NO");
        } else {
            $("#billing_cutlery").val("YES");
        }
    });
});