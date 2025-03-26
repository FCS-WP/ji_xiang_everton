"use strict";
$ = jQuery;



$(document).ready(function(){

    $("#openCateSticky").click(function(){
        $("#categoryList").toggle();
        
    }); 

    $(window).on("scroll", function() {
        var scrollTop = $(window).scrollTop();

        var positionYAngKuKueh = $("#angkukueh").offset().top;
        var positionYBabyShower = $("#babyshower").offset().top;
        var positionYMadeToOrder = $("#madetoorder").offset().top;
        var positionYSeasonalKueh = $("#seasonalkueh").offset().top;


        if(scrollTop >= positionYSeasonalKueh){
            $("#currentCategory").text('Seasonal Kueh');
        }else if (scrollTop >= positionYMadeToOrder ){
            $("#currentCategory").text('Made To Order');
        }else if (scrollTop >= positionYBabyShower){
            $("#currentCategory").text('Baby Shower');
        }else if (scrollTop >= positionYAngKuKueh){
            $("#currentCategory").text('Ang Ku Kueh');
        }
        else{
            $("#currentCategory").text('Ang Ku Kueh');
        }

    });
    
});

