
$(function(){
    $(document).on('click', '.lightbox-zippy-btn', function(e){
        e.preventDefault()
        const product_id = $(this).data('product_id');
        $('#lightbox-zippy-form').attr('data-product_id', product_id);
    }) 
    $(document).on('click', '.btn-close-lightbox', function(){
        $('.mfp-close').trigger('click');
    })
});