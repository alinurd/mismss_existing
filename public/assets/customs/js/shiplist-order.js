$("table").on("click","#btnStatusId",function(){
    let custTypeId = $(".btn-select").attr("id"),
        filter = $("#filterTanggalOrder").val(),
        orderId = $(this).attr("data-orderid"),
        status = $(this).text();

        $.ajax({
            type: "GET",
            url: location.origin+"/shiplist/edit/order/status",
            data: {
                status:status,
                orderId:orderId
            },
            success: function(msg) {
                let json=JSON.parse(msg);
                if(json.status=="Berhasil"){
                    if($("#btnStatusId").hasClass("btnStatusReady")){
                        $("btnStatusId").removeClass("btnStatusReady");
                        $("btnStatusId").addClass("btnStatusHold");
                    }else{
                        $("btnStatusId").addClass("btnStatusReady");
                        $("btnStatusId").removeClass("btnStatusHold");
                    }

                    return true;
                }

                Swal.fire({
                    title: json.status,
                    text: json.text,
                    icon: "error"
                });
            }
        });

        refreshTable(tableCi,location.origin+"/shiplist/table/order/"+custTypeId+"?filterTanggal=","table-ci_info");
});

//Delete Order
$("table").on("click","#hapusOrderBtn",function(){
    let id = $(this).attr("data-id"),
        name = $(this).attr("data-name"),
        msTrackId = $(this).attr("data-mstrackid");

    (async () => {

        const first = await Swal.fire({
            title: `Hapus Data Order`,
            html: `<div style='font-size:18px;margin-top:10px'>Anda Akan Menghapus Data Order <b style='color:red'>${name}</b> !</div>`,
            input: 'text',
            inputPlaceholder: 'Tulis alasan Anda...',
            inputAttributes: { required: true },
            icon: 'question',
            showCancelButton: true,
            cancelButtonText: 'Cancel',
            confirmButtonText: 'Hapus',
            allowOutsideClick: false,
            inputValidator: (value) => {
                if (!value) return 'Alasan wajib diisi!';
            },
            allowOutsideClick: false,
            customClass: {
                actions: 'swal-custom-actions',
                cancelButton: 'swal-custom-cancel',
                confirmButton: 'swal-custom-confirm',
                denyButton: 'swal-custom-deny'
            }
        });
    
        if (first.isDismissed) return;

        let note = first.value;
    
        if (!checkConn()) return;

        let resiTrackText = "";
        if(msTrackId!==""){
            let linkTrackId = "<a href='"+location.origin+"/shiptrip/tracking?id="+msTrackId+"' target='_blank'>"+msTrackId+"</a>";
            resiTrackText = `<div style='font-size:20px;margin-top:5px'>Data Resi Tracking <b>${linkTrackId}</b> Juga Akan Dihapus!</div>`;
        }
    
        const confirm = await confirmAction({
            title: "Apakah Anda Yakin?",
            html: `<div style='font-size:20px;margin-top:10px'>Data Order <b style='color:red'>${name}</b> Akan Dihapus!</div>
                    ${resiTrackText}
                    <div style='font-size:20px;margin-top:5px'>Alasan : <b>${note}</b></div>`
        });
    
        if (!confirm.isConfirmed) return;
    
        deleteOrder({
            id,
            note
        });
    
    })();
    
});
function deleteOrder(array){
    $.ajax({
        type: "POST",
        url: location.origin+"/shiplist/hapus/order",
        data: {
            id:array['id'],
            note:array['note']
        },
        beforeSend: function(){
            $(".preloaderz").show();
            $(".preloaderz .preloaderz-wrapper img").css("display","none");
            $(".preloaderz .preloaderz-wrapper .text").html("<h2>Hapus Order</h2><div>Loading...</div>");
        },
        success: function(msg) {
            $(".preloaderz").hide();
            let json = JSON.parse(msg),
                custTypeId = $(".btn-select").attr("id"),
                icon = "error";

            if(json.status==200){
                icon = "success";
            }

            Swal.fire({
                title: json.title,
                text: json.text,
                icon: icon
            });

            refreshTable(tableCi,location.origin+"/shiplist/table/order/"+custTypeId+"?filterTanggal=","table-ci_info");

        }
    });
}

$("table").on("click","#changeToCorBtn",function(){
    let id = $(this).attr("data-id"),
        token = $("meta[name='csrf-token']").attr("content"),
        name = $(this).attr("data-name");
    Swal.fire({
        title: "Pindah Ke Corporate?",
        html: "<div style='font-size:25px'>Data <b>"+name+"</b> ini akan dipindahkan menjadi Corporate. Apakah anda yakin?</div>",
        icon: 'question',
        showDenyButton: false,
        showCancelButton: true,
        showConfirmButton: true,
        confirmButtonText: `Yakin`,
        customClass: {
            cancelButton: 'order-1',
            denyButton: 'order-2',
          },
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "POST",
                url: location.origin+"/shiplist/changeto/corporate",
                data: {
                    "id" : id,
                    "_token" : token
                },
                beforeSend: function() {
                    $(".preloaderz .preloaderz-wrapper img").css("display","none");
                    $(".preloaderz .preloaderz-wrapper .text").html("<h2>Pindah Ke Corporate</h2><div>Loading ...</div>");
                },
                success: function(msg) {
                    let json = JSON.parse(msg),
                        icon = json.status==200?"success":"error",
                        custTypeId = $('.btn-select').attr('id');

                    $(".preloaderz").hide();
                    refreshTable(tableCi,location.origin+"/shiplist/table/order/"+custTypeId+"?filterTanggal=","table-ci_info");

                    Swal.fire({
                        title: json.title,
                        text: json.msg,
                        icon: icon
                    });

                }
            });
        }
    });
});