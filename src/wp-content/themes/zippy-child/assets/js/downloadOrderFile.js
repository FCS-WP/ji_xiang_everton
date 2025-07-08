
$("body").on("change", "#download_order_file select", function(){
    let file_type = $(this).val();
    if(file_type != ""){
        downloadOrdersFile(file_type);
    }
});
function downloadOrdersFile(fileType) {
    $(".tp_loader").show();
    $(".message").text("")
    $.ajax({
        url: '/wp-json/zippy-addons/v1/export-orders',
        method: 'GET',
        data: { 
            file_type: fileType
        },
        dataType: 'json',
        success: function(response) {
            $(".tp_loader").fadeOut();
            if (response.status == "success") {
                let file_data = response.data;
                $(".message").text(response.message)
                
                if($(file_data).empty().length > 0){
                    var mimeType = file_data.file_type == 'pdf' ? 'application/pdf' : 'text/csv;charset=UTF-8';
                
                    var byteCharacters = atob(file_data.file_base64);
                    var byteNumbers = new Array(byteCharacters.length);
                    for (var i = 0; i < byteCharacters.length; i++) {
                        byteNumbers[i] = byteCharacters.charCodeAt(i);
                    }
                    var byteArray = new Uint8Array(byteNumbers);
                    var blob = new Blob([byteArray], { type: mimeType });

                    // Generate download link
                    if (window.navigator && window.navigator.msSaveOrOpenBlob) {
                        // IE/Edge
                        window.navigator.msSaveOrOpenBlob(blob, file_data.file_name);
                    } else {
                        var url = window.URL.createObjectURL(blob);
                        var link = document.createElement('a');
                        link.href = url;
                        link.download = file_data.file_name;
                        link.style.display = 'none';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                        window.URL.revokeObjectURL(url);
                    }   
                }
            } else {
                alert('Error: ' + (response.message || 'Failed to generate file'));
            }
        },
        error: function(xhr, status, error) {
            $(".tp_loader").fadeOut();
            console.log(error);
        }
    });
}