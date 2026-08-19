function voidInvoice(array){
    $.ajax({
        type: "POST",
        url: location.origin+"/void/create",
        data: {
            invoiceId:"INV/AJV/"+array['invoiceId'],
            note:array['note'],
            mode:array['mode']
        },
        beforeSend: function(){
            $(".preloaderz").show();
            $(".preloaderz .preloaderz-wrapper img").css("display","none");
            $(".preloaderz .preloaderz-wrapper .text").html("<h2>"+array['notif']+"</h2><div>Loading...</div>");
        },
        success: function(msg) {
            $(".preloaderz").hide();
            let json = JSON.parse(msg);
            if(json.status==200){
                Swal.fire({
                    title: json.title,
                    text: json.text,
                    icon: "success"
                });
            }else{
                Swal.fire({
                    title: json.title,
                    text: json.text,
                    icon: "error"
                });
            }

            CheckFilterTable();
        }
    });
}