$(".row-reg,.row-detail,.row-check,.row-detil-second,.row-phone-second,.row-label-second").hide();
$("#IND").addClass("btn-select");
$('#custTypeId').val("IND");
$(".select2").select2();
$(".resiln").attr("data-edit-form","");
$("input[name='file[]']").attr("data-edit-img",false);

$("select[name='formatResi']").on("change",function(){

    let id = $(this).val();
    $("input[name='anchorTrack'],input[name='trackId']").val("");
    $(".col-format-resi").addClass("col-md-6").removeClass("col-md-4").show();
    $(".col-track-id").addClass("col-md-6").removeClass("col-md-4").show();
    $(".row-wareserv,.col-warehouse").show();
    $(".col-warehouse").removeClass("col-md-6");
    $(".col-service").hide();
    $(".col-anchor-track").hide();
    $("input[name='trackId']").attr("required",true).attr("readonly",true);
    $("input[name='anchorTrack']").attr("required",false);
    // $("input[name='anchorTrack']").rules("remove", "checkPrimaryTrack");
    $("label[for='trackId']").text("Nomor Resi Primary");
    $("select[name='warehouse'],select[name='service']").val(null).trigger('change.select2').removeAttr("readonly");

    if(id=="SEC"){
        $(".col-anchor-track").show();
        $(".row-wareserv,.col-warehouse,.col-service").hide();
        $("input[name='anchorTrack']").attr("required",true);
        // $("input[name='anchorTrack']").rules("add", "checkPrimaryTrack");
        $(".col-track-id,.col-anchor-track,.col-format-resi").removeClass("col-md-6").addClass("col-md-4");
        $("label[for='trackId']").text("Nomor Resi Secondary");
    }

    $("select[name='statusCust']").val("").trigger("change");
    $("select[name='statusCust'],select[name='regCust']").removeAttr("readonly");
    $(".row-reg").hide();
    $(".row-detail").hide();
});

$("#navChooseCust .nav-link").on("click",function(){
    $("select,input").attr("readonly",false);
    $("select,input").val("");

    let id = $(this).attr('id');
    $('#custTypeId').val(id);

    $("#navChooseCust .nav-link").removeClass("btn-select");
    $(this).addClass("btn-select");

    $(".card-customer").show();
    $(".row-reg,.row-detail").hide();
    
    $("label[for='customer']").text("Client");
    $("#statusCust option[value='']").text("Pilih Client");
    
    $("select[name='warehouse']").attr("data-noserv","true");
    $("input[name='anchorTrack']").attr("data-noserv","true");
    if(id=="IND"){
        $("label[for='customer']").text("Customer");
        $("#statusCust option[value='']").text("Pilih Customer");
        $("select[name='warehouse']").removeAttr("data-noserv");
        $("input[name='anchorTrack']").removeAttr("data-noserv");
    }
});

$("#statusCust").on("change",function(){
    let value = $(this).val(),
        custTypeId = $('.btn-select').attr('id'),
        regList = "";
    if(value=="REG"){
        regList = getCustList(custTypeId);
        // console.log(regList);
        $(".row-reg").show();
        $(".row-detail").hide();
        $("select[name='regCust']").attr("required", true);
        $('select[name="regCust"]').html(regList);
        
        $("label[for='Reg']").text("Client");
        if(custTypeId=="IND"){
            $("label[for='Reg']").text("Customer");
        }
    }else{
        $(".row-reg").hide();
        $(".row-detail").show();

        if(custTypeId=="IND"){
            $('.row-check').show();
            $(".row-label-second,.row-detil-second,.row-phone-second").show();
            $('#secondName,#secondPhone').attr("required",true).attr("readonly",false).val("");
            $('#secondKodeNegara').attr("required",true).removeAttr("readonly").val("").trigger("change");
            $('label[for="consLabel"]').text('Consignee / Penerima');
        }else{
            $('.row-check').hide();
            $(".row-label-second,.row-detil-second,.row-phone-second").hide();
            $('#secondName,#secondPhone').attr("required",false).attr("readonly",false).val("");
            $('#secondKodeNegara').attr("required",false).attr("readonly","readonly").val("").trigger("change");
            $('label[for="consLabel"]').text('Sender / Pengirim');
        }

        $("select[name='regCust']").attr("required", false);
        $(".card-customer input,.card-customer select:not(#statusCust)").val("").trigger("change.select2").removeAttr("readonly");

    }  
});

$("#regCust").on("change",function(){
    let id = $(this).val();
    getCustData(id);
});

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
    $(".row-label-second,.row-detil-second,.row-phone-second").show();
});

$("#formCreateShipment").validate({
    errorClass: "error fail-alert is-invalid",
    rules:{
        email:{
            required:true,
            email:true,
            remote: {
                url: location.origin+"/check/checkphoneandemail",
                type: "GET",
                data: {
                    email: function() {
                        return $("#formCreateShipment #email").val();
                    },
                    statusCust:function(){
                        return $("#formCreateShipment #statusCust").val();
                    }
                } 
            },
        },
        phone:{
            required:true,
            remote: {
                url: location.origin+"/check/checkphoneandemail",
                type: "GET",
                data: {
                    phone: function() {
                        return $("#formCreateShipment #phone").val();
                    },
                    statusCust:function(){
                        return $("#formCreateShipment #statusCust").val();
                    }
                } 
            },
        },
    },
    messages: {
        statusCust: "Pilih Salah Satu",
        firstName: "Tidak Boleh Kosong",
        email: {
            required: "Tidak Boleh Kosong",
            email: "Format Email Salah",
            remote: "Email Sudah Terdaftar"
        },
        kodeNegara: "Pilih Salah Satu",
        phone: {
            required:"Tidak Boleh Kosong",
            remote: "Telpon Sudah Terdaftar"
        },
        address: "Tidak Boleh Kosong",
        district: "Tidak Boleh Kosong",
        city: "Tidak Boleh Kosong",
        prov: "Tidak Boleh Kosong",
        postalCode: "Tidak Boleh Kosong",
        secondName: "Tidak Boleh Kosong",
        secondKodeNegara: "Pilih Salah Satu",
        secondPhone: "Tidak Boleh Kosong",
        warehouse: "Pilih Salah Satu",
        service: "Pilih Salah Satu",
        formatResi: "Pilih Salah Satu",
        tanggalDrop: "Tidak Boleh Kosong",
        trackId: "Tidak Boleh Kosong",
        anchorTrack: "Tidak Boleh Kosong",
        'resiln[]': "Tidak Boleh Kosong",
        'file[]': "Tidak Boleh Kosong"

    },
    submitHandler: function(form, event) {

        event.preventDefault();
        var form_data = new FormData($(form)[0]);
            // files = document.getElementById('file[]').files;
        // for (var x = 0; x < files.length; x++) {
        //     form_data.append("file[]", files[x]);
        // }

        // for (let [key, value] of form_data) {
        //     console.log(`${key}: ${value}`)
        // }

        $.ajax({
            type: "POST",
            url: location.origin+"/newship/buat",
            // data: $(form).serialize(),
            data: form_data,
            contentType: false,
            processData:false,
            beforeSend: function() {
                loading(form);
            },
            error: function(xhr) {
                console.log(xhr.responseText);
            },
            success: function(json) {
                // var json = JSON.parse(msg);
                
                if (json.status == 200) {
                    unLoading(form);
                    Swal.fire(json.title, json.message, 'success');
                    pageReload(location.origin+"/newship");
                } else {
                    if(json.status == 500){
                        $(".preloaderz .preloaderz-wrapper img").css("display","block");
                        $(".preloaderz").hide();
                        $('body').removeClass('modal-open');
                        $('.modal-backdrop').remove();
                        $('body').css('padding-right', '0');
                        $(".preloaderz .preloaderz-wrapper .text").html("Loading...");

                        Swal.fire({
                            icon: 'warning',
                            title: "Tunggu Antrian Whatsapp Blast...",
                            html: `Now : <b>`+json.now+`</b> <br>Last Created : <b>`+json.createdtime+`</b> <br><br>Submit lagi dalam <b class='swalwatimer'>${json.queuetime}</b> detik`,
                            timer: json.queuetime*1000,
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                const timerInterval = setInterval(() => {
                                    json.queuetime--;
                                    Swal.getHtmlContainer().getElementsByClassName('swalwatimer')[0].textContent = json.queuetime;
                    
                                    if (json.queuetime <= 0) {
                                        clearInterval(timerInterval);
                                    }
                                }, 1000);
                            }
                        });
                    }else{
                        unLoading(form);
                        Swal.fire(json.title, json.message, 'error');
                    }

                }

                // unLoading(form);

                // if (json.status == 200) {

                //     Swal.fire(json.title, json.message, 'success');
                //     pageReload(location.origin+"/newship");

                // } else {

                //     Swal.fire(json.title, json.message, 'error');

                // }
                
            }

        });
    }
});

$("#anchorTrack").on("keyup",function(value, element){
    let id = $(this).val(),
        noServ = $(this).attr("data-noserv"),
        status = true;

    $.ajax({
        url: location.origin+"/shiptrip/check/primarytrackid",
        type: "GET",
        async: false,
        data: {
            id:id
        },
        dataType: "json",
        success: function(response) {

            if(!response.status){
                
                let validator = $("#formCreateShipment").validate(),
                    elementId = "anchorTrack",
                    errorMessage = response.message;

                // Manually set the error
                validator.invalid[elementId] = true;
                validator.submitted[elementId] = errorMessage;
                validator.errorMap[elementId] = errorMessage;
                validator.errorList.push({
                    message: errorMessage,
                    element: $("#" + elementId)[0],
                    // method: "manual"
                });

                // Display the errors
                validator.showErrors();

                status = false;
                return response.status;
            }

            // isValid = true;
            // validatorSettings.messages[element.name] = response.message; 
            wareId = response.wareId;
            servId = response.servId;
            custId = response.cust.id;
            custTypeId = response.cust.typeId;
            firstName = response.cust.firstName;
            middleName = response.cust.middleName;
            lastName = response.cust.lastName;
            countryId = response.cust.kodeNegara;
            phone = response.cust.phone;
            email = response.cust.email;
            address = response.cust.address;
            subDistrict = response.cust.subDistrict;
            district = response.cust.district;
            city = response.cust.city;
            prov = response.cust.prov;
            postalCode = response.cust.postalCode;
            secTrackId = response.secTrackId;
            secName = response.secondName;
            secKode = response.secondKodeNegara;
            secPhone = response.secondPhone;
        }
    });

    if(!status){
        return status;
    }

    $(".row-wareserv,.col-warehouse").show();
    if(noServ==undefined){
        $(".col-service").show();
        $(".col-warehouse").addClass("col-md-6");
    }
    $(".row-track-id").show();
    $("label[for='trackId']").text("Nomor Resi Secondary");     
    $("select[name='warehouse']").attr("readonly",true).val(wareId).trigger('change');
    $("select[name='statusCust']").attr("readonly",true).val("REG").trigger('change');
    setTimeout(() => {
        $("select[name='regCust']").attr("readonly",true).val(custId).trigger('change');
        $("select[name='service']").attr("readonly",true).val(servId).trigger('change.select2'); 
    }, 200); 
    setTimeout(() => {      
        $(".row-check").hide();
        $("input[name='trackId']").attr("readonly",true).val(secTrackId);
        $("input[name='secondName']").attr("readonly",true).val(secName);
        $("select[name='secondKodeNegara']").attr("readonly",true).val(secKode).trigger("change.select2");
        $("input[name='secondPhone']").attr("readonly",true).val(secPhone); 
        // console.log(secName);
        // console.log(secKode);
        // console.log(secPhone);
    }, 1000);
})

$("input[name='resiln[]']").rules("add", "checkForeignTrack");
$("input[name='tanggalDrop']").rules("add","checkValidDateNow");