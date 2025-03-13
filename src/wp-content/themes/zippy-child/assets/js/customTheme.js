"use strict";
$ = jQuery;
$(document).ready(function () {
    $(".image-fade_in_back a, a.woocommerce-LoopProduct-link").on("click", function (event) {
        event.preventDefault();
        event.stopPropagation();
    });
});