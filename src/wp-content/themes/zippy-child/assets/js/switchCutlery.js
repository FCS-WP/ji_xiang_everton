$ = jQuery;

$(document).ready(function () {
    $("#switchButton").on("click", function () {
        $(this).toggleClass("active");

        if ($(this).hasClass("active")) {
            $("#billing_cutlery").val("YES");
            $("#labelSwitch").text('Yes, I need.');
        } else {
            $("#billing_cutlery").val("NO");
            $("#labelSwitch").text('No, thanks.');
        }
    });
});