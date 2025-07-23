$(".js-datepicker").flatpickr({
    mode: "range",
    dateFormat: "Y-m-d",
    showMonths: 2,
    altInput: true,
    altFormat: "M d, Y",
})

$("body").on("change", "select#download_order", function(){
    let file_type = $(this).val()
        customer_id = $("#customer_id").val(),
        dateRange = $('input.date_range[type="hidden"]').val(),
        from_date = null,
        to_date = null;

    if (dateRange) {
        if (dateRange.includes(" to ")) {
            // range of days
            let dates = dateRange.split(" to ");
            from_date = dates[0] ? dates[0].trim() : null;
            to_date = dates[1] ? dates[1].trim() : null;
        } else {
            // one day
            from_date = dateRange.trim();
            to_date = dateRange.trim();
        }
    }
    
    if(file_type != ""){
        downloadOrdersFile(file_type, customer_id, from_date, to_date);
    }
});

function downloadOrdersFile(fileType, customer_id, from_date, to_date) {
    $(".tp_loader").show();
    $(".message").text("")
    $.ajax({
        url: '/wp-json/zippy-addons/v1/export-orders',
        method: 'GET',
        data: { 
            file_type: fileType,
            customer_id: customer_id,
            from_date: from_date,
            to_date: to_date
        },
        dataType: 'json',
        success: function(response) {
            $(".tp_loader").fadeOut();
            if (response.status == "success") {
                let file_data = response.data;
                alert_success(response.message);
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
                alert_error(response.message || 'Failed to generate file')
            }
        },
        error: function(xhr, status, error) {
            alert_error(error)
        }
    });
}


function alert_success(message){
    _alert("success", "Success", message)
}

function alert_error(message){
    _alert("error", "Error", message)
}


function _alert(type, title, message){
    Swal.fire({
        title: title,
        text: message,
        icon: type,
        confirmButtonText: 'Ok'
    })
}
