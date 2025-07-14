
$("body").on("change", "#download_order_file select", function(){
    let file_type = $(this).val()
        customer_id = $("#customer_id").val();
    if(file_type != ""){
        downloadOrdersFile(file_type, customer_id);
    }
});
function downloadOrdersFile(fileType, customer_id) {
    $(".tp_loader").show();
    $(".message").text("")
}