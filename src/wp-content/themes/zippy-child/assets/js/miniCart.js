"use strict";
$ = jQuery;

$(document).ready(function ($) {
  let priceText = $(
    ".woocommerce-mini-cart__total .woocommerce-Price-amount bdi"
  ).text();
  let subTotalPriceValue = parseFloat(priceText.replace(/[^0-9.]/g, ""));
  let dataDelivery = $("#minimunOrder").attr("dataDelivery");
  let dataFreeship = $("#freeDelivery").attr("dataFreeship");
  let elementMinimunOrder = $("#minimunOrder");
  let elementFreeship = $("#freeDelivery");

  let widthPercentageDelivery = (subTotalPriceValue / dataDelivery) * 100;
  widthPercentageDelivery = Math.min(widthPercentageDelivery, 100);

  let widthPercentageFreeship = (subTotalPriceValue / dataFreeship) * 100;
  widthPercentageFreeship = Math.min(widthPercentageFreeship, 100);

  elementMinimunOrder.css("width", widthPercentageDelivery + "%");
  elementFreeship.css("width", widthPercentageFreeship + "%");
});

//Js Handle Variable product Contact for sale
jQuery(document).ready(function ($) {
  $("body").on("change", ".variations select", function (e) {
    const $select = $(e.target);
    const selectedValue = $select.val();
    const $buttonAddToCart = $(".single_add_to_cart_button");
    const $ctaAddToCart = $(".cta_add_to_cart");

    if (selectedValue === "Others") {
      if ($buttonAddToCart.find(".cta_add_to_cart").length === 0) {
        $buttonAddToCart.hide();
        $ctaAddToCart.show();
      }
    } else {
      if ($buttonAddToCart.find(".cta_add_to_cart").length === 0) {
        $buttonAddToCart.show();
        $ctaAddToCart.hide();
      }
    }
  });
});
