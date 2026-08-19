$("#updateAnnouncerBtn").on("click",function(){
    let text = $(".marquee-container .marquee").text();
    $("#announcerModal textarea[name='text']").val(text);
    $("#announcerModal").modal('show');
});

$("#formAnnouncerUpdate").validate({
    errorClass: "error fail-alert is-invalid",
    messages: {
        text: "Tidak Boleh Kosong"
    },
    submitHandler: function(form) {

        $.ajax({
            type: "POST",
            url: location.origin+"/announcer/update",
            data: $(form).serialize(),
            beforeSend: function() {
                loading(form);
            },
            success: function(msg) {
                let json = JSON.parse(msg),
                    status = "info";

                unLoading(form);

                if (json.status==200) {
                    status = "success";  
                    $(".marquee-container .marquee").text(json.data);
                }

                Swal.fire(json.title, json.text, status);
                $("body").css("padding-right", "0");
            }

        });

    }

});