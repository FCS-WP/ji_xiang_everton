"use strict";
var $ = jQuery;

$(document).ready(function () {
  $("#openCateSticky").on("click", function () {
    $("#categoryList").toggle();
  });

  var headerOffset = 120;
  var $currentCategory = $("#currentCategory");
  var $sections = $(".jsScroll");

  function updateCurrentCategory() {
    var scrollTop = $(window).scrollTop();
    var currentTitle = "";

    $sections.each(function () {
      var $section = $(this);
      var offsetTop = $section.offset().top - headerOffset;
      var offsetBottom = offsetTop + $section.outerHeight();

      if (scrollTop >= offsetTop && scrollTop < offsetBottom) {
        currentTitle = $section.data("title");
        return false; // stop loop
      }
    });

    if (currentTitle) {
      $currentCategory.text(currentTitle);
    }
  }

  // Run on scroll
  $(window).on("scroll", updateCurrentCategory);

  // Run once on page load
  updateCurrentCategory();
});
