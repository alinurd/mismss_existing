$("#formGantiPass").validate({

    errorClass: "error fail-alert is-invalid",
    rules: {
        passlama: {
            required: true,
            minlength: 8
        },
        passbaru: {
            required: true,
            minlength: 8,
        },
        passbarulagi: {
            required: true,
            minlength: 8,
            equalTo: "input[name='passbaru']",
        },
    },
    messages: {
        passlama: {
            required: "Tidak Boleh Kosong",
            minlength: "Minimal 8 Karakter",
        },
        passbaru: {
            required: "Tidak Boleh Kosong",
            minlength: "Minimal 8 Karakter",
        },
        passbarulagi: {
            required: "Tidak Boleh Kosong",
            minlength: "Minimal 8 Karakter",
            equalTo: "Password Tidak Sama",
        },
    },
    submitHandler: function(form) {

        $.ajax({
            url: location.origin+"/profile/gantipass",
            type: 'GET',
            data: $(form).serialize(),
            success: function(msg) {

                var json = JSON.parse(msg);
                if (json.status == 200) {

                    //Notif Sukses
                    Swal.fire(json.status, json.text, 'success');
                    $('#modalGantiPass').modal('hide');
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();
                    $('body').css('padding-right', '0');

                    //Reload Halaman
                    pageReload(json.url);

                } else {

                    //Notif Gagal
                    Swal.fire(json.status, json.text, 'error');

                    //Reset Form
                    $('#formGantiPass')[0].reset();

                }
            }
        });

    }

});

$(".copyLinkRef").on("click",function(){
    copyLink(); 
});

$('a[data-toggle="tooltip"]').tooltip({
    animated: 'fade',
    placement: 'top',
    trigger: 'click',
});

setInterval(function () {
    $('a[data-toggle="tooltip"]').tooltip('hide'); 
}, 2000);

function copyLink() {
    var copyText = document.getElementById("referral");
    navigator.clipboard.writeText(copyText.value);
}