$.validator.addMethod("greaterThanZero", function(value, element) {
    return this.optional(element) || (parseFloat(value.replace(/\./g, "").replace(/\,/g, ".")) > 0);
}, "Tidak Boleh Nol");

$.validator.addMethod("specialChar", function(value, element) {
    var regex = /^[a-zA-Z0-9\)\(\/\-]+$/g;
    return this.optional(element) || (regex.test(value));
}, "Karakter '/' Tidak Boleh");

$.validator.addMethod("checkForeignTrack", function(value, element) {
    var validatorSettings = this.settings,
        edit = element.attributes["data-edit-form"].value;
        isValid = false;

    $.ajax({
        url: location.origin+"/shiptrip/check/foreigntrackid",
        type: "GET",
        async: false,
        data: {
            id:value,
            edit:edit,
        },
        dataType: "json",
        error: function() {
            validatorSettings.messages[element.name] = response.message; 
            isValid = false;
        },
        success: function(response) {
            if (response.status) {
                isValid = true;
                for(let i=0;i<$("input[name='resiln[]']").length;i++){
                    if(element.id!=$("input[name='resiln[]']").eq(i).attr("id")){
                        if(element.value==$("input[name='resiln[]']").eq(i).val()){
                                isValid = false;
                                validatorSettings.messages[element.name] = response.message;
                                break;
                        }
                    }
                }
            } else {
                validatorSettings.messages[element.name] = response.message; 
                isValid = false;
            }
        }
    });
    return isValid; 
}, "Resi LN Duplikat");

$.validator.addMethod("checkValidDate", function(value, element) {
    let validatorSettings = this.settings,
        isValid = false; 

    $.ajax({
        url: location.origin+"/shiptrip/check/shipmentdate",
        type: "GET",
        async: false,
        data: {
            id:element.attributes["data-id"].value,
            date:value
        },
        error: function() {
            validatorSettings.messages[element.name] = response.message; 
            isValid = false;
        },
        dataType: "json",
        success: function(response) {
            if (response.status) {
                isValid = true;
            } else {
                validatorSettings.messages[element.name] = response.message; 
                isValid = false;
            }
        }
    });
    return isValid; 
}, "Tanggal Shipment Tidak Sinkron");

$.validator.addMethod("checkValidDateNow", function(value, element) {
    let validatorSettings = this.settings,
        isValid = false; 

    $.ajax({
        url: location.origin+"/shiptrip/check/shipmentdatenow",
        type: "GET",
        async: false,
        data: {
            date:value
        },
        dataType: "json",
        error: function() {
            validatorSettings.messages[element.name] = response.message; 
            isValid = false;
        },
        success: function(response) {
            if(response.status) {
                isValid = true;
            } else {
                validatorSettings.messages[element.name] = response.message; 
                isValid = false;
            }
        }
    });
    return isValid; 
}, "Tanggal Melebihi Tanggal & Jam Hari Ini");

$.validator.addMethod("checkValidDateHourNow", function(value, element) {
    let validatorSettings = this.settings,
        isValid = false; 

    $.ajax({
        url: location.origin+"/shiptrip/check/shipmentdatehournow",
        type: "GET",
        async: false,
        data: {
            date:value
        },
        dataType: "json",
        error: function() {
            validatorSettings.messages[element.name] = response.message; 
            isValid = false;
        },
        success: function(response) {
            if(response.status) {
                isValid = true;
            } else {
                validatorSettings.messages[element.name] = response.message; 
                isValid = false;
            }
        }
    });
    return isValid; 
}, "Tanggal Melebihi Tanggal & Jam Hari Ini");

$.validator.addMethod("checkValidHour", function(value, element) {
    let validatorSettings = this.settings,
        isValid = true; 

    if(value.split(" ")[1]=="00:00"){
        validatorSettings.messages[element.name] = "Jam Shipment Tidak Valid";
        isValid = false;
    }

    return isValid; 
}, "Jam Shipment Tidak Valid");

$.validator.addMethod("checkResiLN", function(value, element) {
    let validatorSettings = this.settings,
        isValid = false; 

    $.ajax({
        type: "GET",
        async: false,
        url: location.origin+"/shiptrip/check/resiln",
        data: {
            id:value,
            edit:element.attributes["data-edit-form"].value,
        },
        dataType: "json",
        error: function() {
            validatorSettings.messages[element.name] = response.message; 
            isValid = false;
        },
        success: function(response) {

            if(!response.status){
                isValid = false;        
                validatorSettings.messages[element.name] = response.message;
                return isValid;
            }

            for(let i=0;i<=length-1;i++){
                if($("input[name='resiln[]']").eq(i).val()==value){
                    validatorSettings.messages[element.name] = response.message; 
                    isValid = false;
                    return isValid;
                }
            }
            isValid = true;
            return isValid;   
            
            // let json = JSON.parse(data),
            //     length = $("input[name='resiln[]']").length;

            // errorMessages["checkResiLn"] = json.message;

            // if(json.status){
            //     for(let i=0;i<=length-1;i++){
            //         if($("input[name='resiln[]']").eq(i).val()==value){
            //             return false;
            //         }
            //     }
            //     return json.status;
            // }
        }
    });

    return isValid;

}, "Resi LN Telah Diinput");

$.validator.addMethod(
    "noSameShippingNumber", 
    function(value, element) {
        // return this.optional(element) || (parseFloat(value) > 0);
        let num=0;
        $("#formBuatResi input[name='noResi[]']").each(function(index, value) {
        	let a = $(this).val();
         		b = `${index}`;
            $("#formBuatResi input[name='noResi[]']").each(function(index, value) {
            	if(b!=`${index}`){
            	    if($(this).val()!=""){
                    	if(a==$(this).val()){
                        	num++;
                        }
            	    }
                }	
            });
        });
        // console.log(num);
        return this.optional(element) || (parseInt(num) < 1);
    }, 
    "Shipping Number Tidak Boleh Sama"
);

// $.validator.addMethod("checkPrimaryTrack", function(value, element) {
//     let validatorSettings = this.settings,
//         isValid = false,
//         wareId,
//         custId,
//         servId,
//         secTrackId,
//         secName,
//         secKode, 
//         secPhone,
//         custTypeId,
//         firstName,
//         middleName,
//         lastName,
//         countryId,
//         phone,
//         email,
//         address,
//         subDistrict,
//         district,
//         city,
//         prov,
//         postalCode;

//     $.ajax({
//         url: location.origin+"/shiptrip/check/primarytrackid",
//         type: "GET",
//         async: false,
//         data: {id:value},
//         dataType: "json",
//         error: function() {
//             validatorSettings.messages[element.name] = "An error occurred during validation.";
//             isValid = false;
//         },
//         success: function(response) {

//             if(!response.status){
//                 validatorSettings.messages[element.name] = response.message; 
//                 isValid = false;
//                 return isValid;
//             }

//             isValid = true;
//             validatorSettings.messages[element.name] = response.message; 
//             wareId = response.wareId;
//             servId = response.servId;
//             custId = response.cust.id;
//             custTypeId = response.cust.typeId;
//             firstName = response.cust.firstName;
//             middleName = response.cust.middleName;
//             lastName = response.cust.lastName;
//             countryId = response.cust.kodeNegara;
//             phone = response.cust.phone;
//             email = response.cust.email;
//             address = response.cust.address;
//             subDistrict = response.cust.subDistrict;
//             district = response.cust.district;
//             city = response.cust.city;
//             prov = response.cust.prov;
//             postalCode = response.cust.postalCode;
//             secTrackId = response.secTrackId;
//             secName = response.secondName;
//             secKode = response.secondKodeNegara;
//             secPhone = response.secondPhone;
//         }
//     });

//     if(!isValid){
//         return isValid;
//     }

//     $(".col-warehouse").addClass("col-md-6");
//     $(".row-wareserv,.col-warehouse,.col-service").show();
//     $(".row-track-id").show();
//     $("label[for='trackId']").text("Nomor Resi Secondary");        
//     $("input[name='trackId']").val(secTrackId).attr("readonly",true);        
//     $("select[name='warehouse']").val(wareId).trigger('change').attr("readonly",true);         

//     $("select[name='statusCust']").val("REG").trigger('change.select2').attr("readonly",true);
//     regList = "<option value='"+custId+"'>"+firstName+" "+middleName+" "+lastName+"</option>";
//     $(".row-reg").show();
//     $(".row-detail").hide();
//     $("select[name='regCust']").attr("required", true);
//     $('select[name="regCust"]').html(regList);
//     $("label[for='Reg']").text("Client");
//     if(custTypeId=="IND"){
//         $("label[for='Reg']").text("Customer");
//     }

//     setTimeout(() => {
//         $("select[name='regCust']").val(custId).trigger('change.select2').attr("readonly",true);
//         $("select[name='service']").val(servId).trigger('change.select2').attr("readonly",true);
//     }, 200);
//     // setTimeout(() => {
//     //     $(".row-detail").show();
//     //     $('.row-check').hide();
//     //     if(custTypeId=="IND"){
//     //         $(".row-label-second,.row-detil-second,.row-phone-second").show();
//     //         $('label[for="consLabel"]').text('Consignee / Penerima');
//     //     }else{
//     //         $(".row-label-second,.row-detil-second,.row-phone-second").hide();
//     //         $('label[for="consLabel"]').text('Sender / Pengirim');
//     //     }

//     //     $("#firstName").val(firstName).attr("readonly",true);
//     //     $("#middleName").val(middleName).attr("readonly",true);
//     //     $("#lastName").val(lastName).attr("readonly",true);
//     //     $("#kodeNegara").val(countryId).trigger("change.select2").attr("readonly",true);
//     //     $("#phone").val(phone).attr("readonly",true);
//     //     $("#email").val(email).attr("readonly",true);
//     //     $("#address").val(address).attr("readonly",true);
//     //     $("#subDistrict").val(subDistrict).attr("readonly",true);
//     //     $("#district").val(district).attr("readonly",true);
//     //     $("#city").val(city).attr("readonly",true);
//     //     $("#prov").val(prov).attr("readonly",true);
//     //     $("#postalCode").val(postalCode).attr("readonly",true);
//     //     $("input[name='secondName']").val(secName).attr("readonly",true);
//     //     $("select[name='secondKodeNegara']").val(secKode).trigger("change.select2").attr("readonly",true);
//     //     $("input[name='secondPhone']").val(secPhone).attr("readonly",true);
//     // }, 300);

//     return isValid; 

// }, "Default error message for myAjaxValidation");