$("input[name='sameSender']").attr("checked",false);
$("#formOrder input").val("");
$("select[name='kodeNegara'],select[name='secondKodeNegara']").select2();

$("input[name='sameSender']").on("click",function(){
    if($(this).is(":checked")){
        let fullName = $("#middleName").val()==""?$("#firstName").val()+" "+$("#lastName").val():$("#firstName").val()+" "+$("#middleName").val()+" "+$("#lastName").val();
        $(this).val("S");
        $('#secondName').attr("required",false).attr("readonly",true).val(fullName);
        $('#secondKodeNegara').attr("required",false).attr("readonly","readonly").val($("#kodeNegara").val()).trigger("change");
        $('#secondPhone').attr("required",false).attr("readonly",true).val($("#phone").val());
    }else{
        $(this).val("NS");
        $('#secondName,#secondPhone').attr("required",true).attr("readonly",false).val("");
        $('#secondKodeNegara').val("").trigger("change").attr("required",true).removeAttr("readonly");
    }
});

let tncViewed = false;

$("#tncModal").on("hidden.bs.modal", function () {
    tncViewed = true;
});

$("#agreeTnc").on("change", function () {
    if ($(this).is(":checked") && !tncViewed) {
        $(this).prop("checked", false);
        $("#tncModal").modal("show");
    }
});

$("#formOrder").validate({
    errorClass: "error fail-alert is-invalid",
    rules:{
        email:{
            required:true,
            email:true,
        },
        agreeTnc:{
            required:true,
        },
    },
    messages: {
        firstName: "Tidak Boleh Kosong",
        // lastName: "Tidak Boleh Kosong",
        email: {
            required: "Tidak Boleh Kosong",
            email: "Format Email Salah",
        },
        kodeNegara: "Pilih Salah Satu",
        phone: "Tidak Boleh Kosong",
        address: "Tidak Boleh Kosong",
        // subDistrict: "Tidak Boleh Kosong",
        district: "Tidak Boleh Kosong",
        city: "Tidak Boleh Kosong",
        prov: "Tidak Boleh Kosong",
        postalCode: "Tidak Boleh Kosong",
        secondName: "Tidak Boleh Kosong",
        secondKodeNegara: "Pilih Salah Satu",
        secondPhone: "Tidak Boleh Kosong",
        knowFrom : "Pilih Salah Satu",
        agreeTnc: "Anda harus membaca dan menyetujui Syarat & Ketentuan terlebih dahulu",
    },
    errorPlacement: function(error, element) {
        if (element.attr("name") === "agreeTnc") {
            error.insertAfter(element.closest(".form-check"));
        } else {
            error.insertAfter(element);
        }
    },
    submitHandler: function(form) {


        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        $.ajax({
            type: "GET",
            url: location.origin+"/webform/input",
            data: $(form).serialize(),
            beforeSend: function() {
                $("#"+form.id+" button[type='submit']").attr("disabled",true);
                $("#"+form.id+" button[type='submit']").text("Loading...");
            },
            success: function(msg) {
                var json = JSON.parse(msg);

                $("#"+form.id+" button[type='submit']").attr("disabled",false);
                $("#"+form.id+" button[type='submit']").text("Submit");
                $("input[name='sameSender']").attr("checked",false);
                $("input[name='secondName'],input[name='secondPhone']").attr("readonly",false).attr("required",true);
                $('#secondKodeNegara').val("").trigger("change").attr("required",true).removeAttr("readonly");
                $("#agreeTnc").prop("checked",false);
                tncViewed = false;
                $("#"+form.id+" input").val("");

                window.location !== window.parent.location ? window.parent.postMessage(json, "*") : (json.status == "Success" ? Swal.fire(json.status, json.text, 'success') : ( json.noAdmin == null ? Swal.fire(json.status, json.text, 'error') : Swal.fire({
                    icon: 'error',
                    title: json.status,
                    text: json.text,
                    confirmButtonText: "Chat Admin",
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.open("https://api.whatsapp.com/send/?phone="+json.noAdmin, '_blank');
                    }
                })));
            }

        });

    }

});

const refInput = document.getElementById("referral");
const urlParams = new URLSearchParams(window.location.search);
const myParam = urlParams.get('paramName');
const hasParam = urlParams.has('paramName');
const allValues = urlParams.getAll('paramName');
for (const [key, value] of urlParams.entries()) {
    if(`${key}`=="ref"){
        refInput.value = `${value}`;
    }
}
// console.log(window.location.search);