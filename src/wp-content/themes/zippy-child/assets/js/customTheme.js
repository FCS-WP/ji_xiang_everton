"use strict";
$ = jQuery;



$(document).ready(function(){

    $("#openCateSticky").click(function(){
        $("#categoryList").toggle();
        
    }); 

    $(window).on("scroll", function() {
        var scrollTop = $(window).scrollTop();
        
        $(".jsScroll").each(function(index,item){
            let offsetTop = $(item).offset().top;        
            let dataTitle = $(item).data("title");
            if(scrollTop >= offsetTop){
                $("#currentCategory").text(dataTitle);
            }
        })

    });
    
});

