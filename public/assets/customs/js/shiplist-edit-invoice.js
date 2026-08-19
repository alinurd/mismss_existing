function revInvoice(form){
    $.ajax({
        type: "POST",
        url: location.origin+"/shiplist/edit/invoice",
        data: $(form).serialize(),
        beforeSend: function() {
            loading(form);
            $(".preloaderz .preloaderz-wrapper img").css("display","none");
            $(".preloaderz .preloaderz-wrapper .text").html("<h2>Revisi Invoice</h2><div>Loading ...</div>");
        },
        success: function(msg) {
            var json = JSON.parse(msg);

            unLoading(form);

            if (json.status == 200) {

                //NOTIF SUKSES
                Swal.fire({
                    icon: 'success',
                    title: json.title,
                    text: json.text,
                    showCancelButton: true,
                    reverseButtons:true,
                    cancelButtonColor:"#dc3545",
                    confirmButtonText: "Print Invoice",
                    cancelButtonText: "Close",
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.open(json.url, '_blank');
                    }
                });

                // let custTypeId = $('.btn-select').attr('id');
                // refreshTable(tableCi,location.origin+"/shiplist/table/order/"+custTypeId+"?filterTanggal=","table-ci_info");
                // refreshTable(tableCt,location.origin+"/shiplist/table/invoice/"+custTypeId+"?filterTanggal=&filterWarehouse=&filterService=","table-ct_info");
                // refreshTable(tableSt,location.origin+"/shiplist/table/tracking/"+custTypeId+"?mismassOrderId=&filterTanggal=&filterWarehouse=&filterService=","table-status_info");

                CheckFilterTable();

                //RELOAD PAGE
                // pageReload(location.origin+"/shiplist");

            } else {
                console.log(json);
                //NOTIF GAGAL
                Swal.fire(json.title, json.text, 'error');

            }
        }

    });
}