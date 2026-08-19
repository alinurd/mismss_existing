function deleteShipment(array){
    $.ajax({
        type: "POST",
        url: location.origin+"/shiptrip/hapus/shipment",
        data: {
            trackId:array['trackId'],
            note:array['note']
        },
        beforeSend: function(){
            $(".preloaderz").show();
            $(".preloaderz .preloaderz-wrapper img").css("display","none");
            $(".preloaderz .preloaderz-wrapper .text").html("<h2>Hapus Shipment</h2><div>Loading...</div>");
        },
        success: function(msg) {
            $(".preloaderz").hide();
            let json = JSON.parse(msg);

            if(json.status==200){
                let navship = $("#navChoose li .active").attr("id"),
                    navtype = $("#navType li .active").attr("id"),
                    custtype = $("#navChooseCust li .active").attr("id");
                    
                Swal.fire({
                    title: json.title,
                    text: json.text,
                    icon: "success"
                });

                refreshTable(table,location.origin+"/shiptrip/table/"+navship+"/"+navtype+"/"+custtype,"table_info");
            }else{
                Swal.fire({
                    title: json.title,
                    text: json.text,
                    icon: "error"
                });
            }

        }
    });
}