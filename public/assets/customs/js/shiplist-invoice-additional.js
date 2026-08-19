$("table").on("click","#createInvoiceAddBtn",function(){

    let modalId = "createInvoiceAdditional",
        mismassOrderId = $(this).attr("data-id"),
        msTracks = $(this).attr("data-mstracks"),
        tanggalInvoice = $(this).attr("data-tglInv");

    resetModalInvoice(modalId);
    $(".preloaderz").show();
    $(".preloaderz .preloaderz-wrapper img").css("display","block");
    $(".preloaderz .preloaderz-wrapper .text").html("<div>Loading...</div>");

    $.ajax({
        type: "GET",
        url: location.origin+"/shiplist/getdata/invoice",
        data: {
            mismassOrderId:mismassOrderId,
        },
        async: false,
        success: function(msg) {
            let num=0,
                addNon,
                totalCbm=0,
                totalWeight=0,
                totalItem=0,
                totalDiskon=0,
                totalBiaya=0,
                adjustFee=0,
                revisionNote,
                dokuInvoiceId,
                dokuLink,
                bankName,
                bankAccountName,
                bankAccountId,
                firstName,
                middleName,
                lastName,
                address,subDistrict,
                district,city,prov,
                email,postalCode,
                actualKg,
                json = JSON.parse(msg),
                typeId = $(".btn-select").attr("id"),
                subTotals = [],itemTotals = [],kgTotals = [],diskonTotals = [],cbmTotals=[],
                timeout=0,
                filters = [],
                satuanBeratVal;

                localStorage.setItem("subTotals",JSON.stringify(subTotals));
                localStorage.setItem("itemTotals",JSON.stringify(itemTotals));
                localStorage.setItem("kgTotals",JSON.stringify(kgTotals));
                localStorage.setItem("cbmTotals",JSON.stringify(cbmTotals));
                localStorage.setItem("diskonTotals",JSON.stringify(diskonTotals));
                
                let wareId, servId, custTypeId;
                json.data.forEach(e => {
                    dokuInvoiceId=e['doku_invoice_id'];
                    dokuLink=e['doku_link'];
                    bankName=e['bank_name'];
                    bankAccountName=e['bank_account_name'];
                    bankAccountId=e['bank_account_id'];
                    fcValue = e['fc_value'];
                    fcSymbol = e['fc_symbol'];
                    templateId = e['template_id'];

                    firstName = e['sender_first_name'],
                    middleName = e['sender_middle_name'],
                    lastName = e['sender_last_name'];
                    address = e['sender_address'];
                    subDistrict = e['sender_sub_district'];
                    district = e['sender_district'];
                    city = e['sender_city'];
                    prov = e['sender_prov'];
                    email = e['sender_email'];
                    postalCode = e['sender_postal_code'];
                    phone = e['sender_phone'];
                    secondName = "";
                    secondPhone = "";
                    wareId = e['warehouse_id'];
                    servId = e['service_id'];
                    custTypeId = e['cust_type_id'];
                    if(e['cust_type_id']=="IND"){
                        firstName = e['cons_first_name'];
                        middleName = e['cons_middle_name'];
                        lastName = e['cons_last_name'];
                        address = e['cons_address'];
                        subDistrict = e['cons_sub_district'];
                        district = e['cons_district'];
                        city = e['cons_city'];
                        prov = e['cons_prov'];
                        email = e['cons_email'];
                        postalCode = e['cons_postal_code'];
                        phone = e['cons_phone'];
                        secondName = e['sender_first_name'];
                        secondPhone = e['sender_phone'];
                    }
                });

                for(let e of json.data) {
                    $("#"+modalId+" input[name='dbFirstName']").val(firstName);
                    $("#"+modalId+" input[name='dbMiddleName']").val(middleName);
                    $("#"+modalId+" input[name='dbLastName']").val(lastName);
                    $("#"+modalId+" input[name='dbEmail']").val(email);
                    $("#"+modalId+" input[name='dbPhone']").val(phone);
                    $("#"+modalId+" input[name='dbAddress']").val(address);
                    $("#"+modalId+" input[name='dbSubDistrict']").val(subDistrict);
                    $("#"+modalId+" input[name='dbDistrict']").val(district);
                    $("#"+modalId+" input[name='dbCity']").val(city);
                    $("#"+modalId+" input[name='dbProv']").val(prov);
                    $("#"+modalId+" input[name='dbPostalCode']").val(postalCode);
                    $("#"+modalId+" input[name='dbSecondName']").val(secondName);
                    $("#"+modalId+" input[name='dbSecondPhone']").val(secondPhone);
                    
                    $("#"+modalId+" label[for='tanggal']").text(moment(e['orderCreatedAt']).format("DD MMMM YYYY"));
                    $("#"+modalId+" label[for='customer']").text(firstName+" "+middleName+" "+lastName);
                    $("#"+modalId+" label[for='alamat']").text(address+", "+subDistrict+", "+district+", "+city+", "+prov+", "+postalCode);
                    $("#"+modalId+" label[for='telpon']").text(phone);
                    $("#"+modalId+" label[for='noInvoice']").text(e['mismass_invoice_id']);
                    $("#"+modalId+" input[name='tanggalInvoice']").val(moment(e['mismass_invoice_date']).format("DD-MM-YYYY"));
    
                    $("#"+modalId+" input[name='mismassOrderId']").val(e['mismass_order_id']);
                    $("#"+modalId+" input[name='mismassInvoiceId']").val(e['mismass_invoice_id']);
    
                    $("#"+modalId+" input[name='createdAt']").val(e['created_at']);
                    $("#"+modalId+" input[name='serviceName']").val(e['service_name']);
                    $("#"+modalId+" input[name='mismassInvoiceLink']").val(e['mismass_invoice_link']);
    
                    $("#"+modalId+" input[name='custId']").val(e['cust_id']);
                    $("#"+modalId+" input[name='custTypeId']").val(e['cust_type_id']);
    
                    filters['warehouseId'] = e['warehouse_id'];
                    filters['serviceId'] = e['service_id'];
                    satuanBeratVal = e['item']>0?"ITEM":(e['length']>0?"VOL":(e['cbm']>0?"CBM":"KG"));
                    filters['satuanBerat'] = satuanBeratVal;
                    createServiceElementAdditional(modalId,filters);
    
                    $("#"+modalId+" #"+num+" input[name='senderFirstName[]']").val(e['sender_first_name']);
                    $("#"+modalId+" #"+num+" input[name='senderMiddleName[]']").val(e['sender_middle_name']);
                    $("#"+modalId+" #"+num+" input[name='senderLastName[]']").val(e['sender_last_name']);
                    $("#"+modalId+" #"+num+" input[name='senderEmail[]']").val(e['sender_email']);
                    $("#"+modalId+" #"+num+" input[name='senderPhone[]']").val(e['sender_phone']);
                    $("#"+modalId+" #"+num+" input[name='senderAddress[]']").val(e['sender_address']);
                    $("#"+modalId+" #"+num+" input[name='senderSubDistrict[]']").val(e['sender_sub_district']);
                    $("#"+modalId+" #"+num+" input[name='senderDistrict[]']").val(e['sender_district']);
                    $("#"+modalId+" #"+num+" input[name='senderCity[]']").val(e['sender_city']);
                    $("#"+modalId+" #"+num+" input[name='senderProv[]']").val(e['sender_prov']);
                    $("#"+modalId+" #"+num+" input[name='senderPostalCode[]']").val(e['sender_postal_code']);
    
                    $("#"+modalId+" #"+num+" input[name='consFirstName[]']").val(e['cons_first_name']);
                    $("#"+modalId+" #"+num+" input[name='consMiddleName[]']").val(e['cons_middle_name']);
                    $("#"+modalId+" #"+num+" input[name='consLastName[]']").val(e['cons_last_name']);
                    $("#"+modalId+" #"+num+" input[name='consEmail[]']").val(e['cons_email']);
                    $("#"+modalId+" #"+num+" input[name='consPhone[]']").val(e['cons_phone']);
                    $("#"+modalId+" #"+num+" input[name='consAddress[]']").val(e['cons_address']);
                    $("#"+modalId+" #"+num+" input[name='consSubDistrict[]']").val(e['cons_sub_district']);
                    $("#"+modalId+" #"+num+" input[name='consDistrict[]']").val(e['cons_district']);
                    $("#"+modalId+" #"+num+" input[name='consCity[]']").val(e['cons_city']);
                    $("#"+modalId+" #"+num+" input[name='consProv[]']").val(e['cons_prov']);
                    $("#"+modalId+" #"+num+" input[name='consPostalCode[]']").val(e['cons_postal_code']);
    
                    $("#"+modalId+" #"+num+" input[name='panjang[]']").val(0);
                    $("#"+modalId+" #"+num+" input[name='lebar[]']").val(0);
                    $("#"+modalId+" #"+num+" input[name='tinggi[]']").val(0);
                    $("#"+modalId+" #"+num+" input[name='kg[]']").val(0);
                    $("#"+modalId+" #"+num+" input[name='cbm[]']").val(0);
                    $("#"+modalId+" #"+num+" input[name='item[]']").val(0);
                    $("#"+modalId+" #"+num+" input[name='actualKg[]']").val(0);
                    $("#"+modalId+" #"+num+" input[name='pricePer[]']").val(0);

                    $("#"+modalId+" #"+num+" input[name='panjang[]']").attr("readonly",true);
                    $("#"+modalId+" #"+num+" input[name='lebar[]']").attr("readonly",true);
                    $("#"+modalId+" #"+num+" input[name='tinggi[]']").attr("readonly",true);
                    $("#"+modalId+" #"+num+" input[name='kg[]']").attr("readonly",true);
                    $("#"+modalId+" #"+num+" input[name='cbm[]']").attr("readonly",true);
                    $("#"+modalId+" #"+num+" input[name='item[]']").attr("readonly",true);
                    $("#"+modalId+" #"+num+" input[name='actualKg[]']").attr("readonly",true);
                    $("#"+modalId+" #"+num+" input[name='pricePer[]']").attr("readonly",true);

                    $("#"+modalId+" #"+num+" input[name='panjang[]']").rules("remove", "greaterThanZero");
                    $("#"+modalId+" #"+num+" input[name='lebar[]']").rules("remove", "greaterThanZero");
                    $("#"+modalId+" #"+num+" input[name='tinggi[]']").rules("remove", "greaterThanZero");
                    $("#"+modalId+" #"+num+" input[name='kg[]']").rules("remove", "greaterThanZero");
                    $("#"+modalId+" #"+num+" input[name='cbm[]']").rules("remove", "greaterThanZero");
                    $("#"+modalId+" #"+num+" input[name='item[]']").rules("remove", "greaterThanZero");
                    $("#"+modalId+" #"+num+" input[name='actualKg[]']").rules("remove", "greaterThanZero");
                    $("#"+modalId+" #"+num+" input[name='pricePer[]']").rules("remove", "greaterThanZero");

                    subTotals[num]=0;
                    itemTotals[num]=0;
                    kgTotals[num]=0;
                    diskonTotals[num]=0;
                    cbmTotals[num]=0;

                    localStorage.setItem("subTotals",JSON.stringify(subTotals));
                    localStorage.setItem("itemTotals",JSON.stringify(itemTotals));
                    localStorage.setItem("kgTotals",JSON.stringify(kgTotals));
                    localStorage.setItem("cbmTotals",JSON.stringify(cbmTotals));
                    localStorage.setItem("diskonTotals",JSON.stringify(diskonTotals));

                    $("#"+modalId+" #"+num+" .row-ware-serv").hide();
                    $("#"+modalId+" #"+num+" .row-detil-biaya").hide();

                    if(e['cust_type_id']=="IND"){
                        break;
                    }
                    num++;
                };              
    
                $("#"+modalId+" .modal-title").text(typeId=="IND"?"Buat Invoice Additional Individual":"Buat Invoice Additional Corporate");

                $(".preloaderz").hide();

                $("#"+modalId+" .row-edit-detil-penerima").hide();
                $("#"+modalId+" .alert-success").show();
                $("#"+modalId+" .searchElem").show();
                $("#"+modalId+" .modal-footer").css({
                    justifyContent:"space-between"
                });
                if(typeId=="IND"){
                    $("#"+modalId+" .alert-success").hide();
                    $("#"+modalId+" .searchElem").hide();
                    $("#"+modalId+" .modal-footer").css({
                        justifyContent:"right"
                    });
                }

                $("#"+modalId+" #btnAddServiceElementAdditional").attr("data-warehouseid",wareId);
                $("#"+modalId+" #btnAddServiceElementAdditional").attr("data-serviceid",servId);
                $("#"+modalId+" #btnAddServiceElementAdditional").attr("data-satuanberat",satuanBeratVal);

                $("#"+modalId+" input[name='pricePer[]']").rules("add", "greaterThanZero");
                // $("#"+modalId+" input[name='subTotal[]']").rules("add", "greaterThanZero");

                $("#"+modalId).modal("show");

        },
    });
});

$("table").on("click","#editInvoiceAddBtn",function(){

    let modalId = "editInvoiceAdditional",
        mismassOrderId = $(this).attr("data-id"),
        msTracks = $(this).attr("data-mstracks"),
        tanggalInvoice = $(this).attr("data-tglInv");

    resetModalInvoice(modalId);
    $(".preloaderz").show();
    $(".preloaderz .preloaderz-wrapper img").css("display","block");
    $(".preloaderz .preloaderz-wrapper .text").html("<div>Loading...</div>");

    $.ajax({
        type: "GET",
        url: location.origin+"/shiplist/getdata/invoice",
        data: {
            mismassOrderId:mismassOrderId,
        },
        async: false,
        success: function(msg) {
            let num=0,
                addNon,
                totalCbm=0,
                totalWeight=0,
                totalItem=0,
                totalDiskon=0,
                totalBiaya=0,
                adjustFee=0,
                invAdd,
                revisionNote,
                dokuInvoiceId,
                dokuLink,
                bankName,
                bankAccountName,
                bankAccountId,
                firstName,
                middleName,
                lastName,
                address,subDistrict,
                district,city,prov,
                email,postalCode,
                actualKg,
                json = JSON.parse(msg),
                typeId = $(".btn-select").attr("id"),
                subTotals = [],itemTotals = [],kgTotals = [],diskonTotals = [],cbmTotals=[],
                timeout=0
                filters = [],
                satuanBeratVal="";
                
                localStorage.setItem("subTotals",JSON.stringify(subTotals));
                localStorage.setItem("itemTotals",JSON.stringify(itemTotals));
                localStorage.setItem("kgTotals",JSON.stringify(kgTotals));
                localStorage.setItem("cbmTotals",JSON.stringify(cbmTotals));
                localStorage.setItem("diskonTotals",JSON.stringify(diskonTotals));
                
                let wareId, servId, custTypeId;
                json.data.forEach(e => {
                    revisionNote=e['revision_note'];
                    dokuInvoiceId=e['doku_invoice_id'];
                    dokuLink=e['doku_link'];
                    bankName=e['bank_name'];
                    bankAccountName=e['bank_account_name'];
                    bankAccountId=e['bank_account_id'];
                    fcValue = e['fc_value'];
                    fcSymbol = e['fc_symbol'];
                    templateId = e['template_id'];

                    firstName = e['sender_first_name'],
                    middleName = e['sender_middle_name'],
                    lastName = e['sender_last_name'];
                    address = e['sender_address'];
                    subDistrict = e['sender_sub_district'];
                    district = e['sender_district'];
                    city = e['sender_city'];
                    prov = e['sender_prov'];
                    email = e['sender_email'];
                    postalCode = e['sender_postal_code'];
                    phone = e['sender_phone'];
                    secondName = "";
                    secondPhone = "";
                    wareId = e['warehouse_id'];
                    servId = e['service_id'];
                    custTypeId = e['cust_type_id'];
                    if(e['cust_type_id']=="IND"){
                        firstName = e['cons_first_name'];
                        middleName = e['cons_middle_name'];
                        lastName = e['cons_last_name'];
                        address = e['cons_address'];
                        subDistrict = e['cons_sub_district'];
                        district = e['cons_district'];
                        city = e['cons_city'];
                        prov = e['cons_prov'];
                        email = e['cons_email'];
                        postalCode = e['cons_postal_code'];
                        phone = e['cons_phone'];
                        secondName = e['sender_first_name'];
                        secondPhone = e['sender_phone'];
                    }
                });

                json.data.forEach(e => {
                    setTimeout(() => {                   
                    addNon = 0;                   
                    $("#"+modalId+" input[name='revisionNote']").val(revisionNote);
                    $("#"+modalId+" input[name='dbFirstName']").val(firstName);
                    $("#"+modalId+" input[name='dbMiddleName']").val(middleName);
                    $("#"+modalId+" input[name='dbLastName']").val(lastName);
                    $("#"+modalId+" input[name='dbEmail']").val(email);
                    $("#"+modalId+" input[name='dbPhone']").val(phone);
                    $("#"+modalId+" input[name='dbAddress']").val(address);
                    $("#"+modalId+" input[name='dbSubDistrict']").val(subDistrict);
                    $("#"+modalId+" input[name='dbDistrict']").val(district);
                    $("#"+modalId+" input[name='dbCity']").val(city);
                    $("#"+modalId+" input[name='dbProv']").val(prov);
                    $("#"+modalId+" input[name='dbPostalCode']").val(postalCode);
                    $("#"+modalId+" input[name='dbSecondName']").val(secondName);
                    $("#"+modalId+" input[name='dbSecondPhone']").val(secondPhone);
                    
                    $("#"+modalId+" label[for='tanggal']").text(moment(e['orderCreatedAt']).format("DD MMMM YYYY"));
                    $("#"+modalId+" label[for='customer']").text(firstName+" "+middleName+" "+lastName);
                    $("#"+modalId+" label[for='alamat']").text(address+", "+subDistrict+", "+district+", "+city+", "+prov+", "+postalCode);
                    $("#"+modalId+" label[for='telpon']").text(phone);
                    $("#"+modalId+" label[for='noInvoice']").text(e['mismass_invoice_id']);
                    $("#"+modalId+" input[name='tanggalInvoice']").val(moment(e['mismass_invoice_date']).format("DD-MM-YYYY"));
    
                    $("#"+modalId+" input[name='mismassOrderId']").val(e['mismass_order_id']);
                    $("#"+modalId+" input[name='mismassInvoiceId']").val(e['mismass_invoice_id']);

                    $("#"+modalId+" input[name='invAdd']").val(e['inv_add']);
    
                    $("#"+modalId+" input[name='createdAt']").val(e['created_at']);
                    $("#"+modalId+" input[name='serviceName']").val(e['service_name']);
                    $("#"+modalId+" input[name='mismassInvoiceLink']").val(e['mismass_invoice_link']);
    
                    $("#"+modalId+" input[name='custId']").val(e['cust_id']);
                    $("#"+modalId+" input[name='custTypeId']").val(e['cust_type_id']);

                    filters['warehouseId'] = e['warehouse_id'];
                    filters['serviceId'] = e['service_id'];
                    satuanBeratVal = e['item']>0?"ITEM":(e['length']>0?"VOL":(e['cbm']>0?"CBM":"KG"));
                    filters['satuanBerat'] = satuanBeratVal;
                    createServiceElementAdditional(modalId,filters);
    
                    $("#"+modalId+" #"+num+" input[name='senderFirstName[]']").val(e['sender_first_name']);
                    $("#"+modalId+" #"+num+" input[name='senderMiddleName[]']").val(e['sender_middle_name']);
                    $("#"+modalId+" #"+num+" input[name='senderLastName[]']").val(e['sender_last_name']);
                    $("#"+modalId+" #"+num+" input[name='senderEmail[]']").val(e['sender_email']);
                    $("#"+modalId+" #"+num+" input[name='senderPhone[]']").val(e['sender_phone']);
                    $("#"+modalId+" #"+num+" input[name='senderAddress[]']").val(e['sender_address']);
                    $("#"+modalId+" #"+num+" input[name='senderSubDistrict[]']").val(e['sender_sub_district']);
                    $("#"+modalId+" #"+num+" input[name='senderDistrict[]']").val(e['sender_district']);
                    $("#"+modalId+" #"+num+" input[name='senderCity[]']").val(e['sender_city']);
                    $("#"+modalId+" #"+num+" input[name='senderProv[]']").val(e['sender_prov']);
                    $("#"+modalId+" #"+num+" input[name='senderPostalCode[]']").val(e['sender_postal_code']);

                    $("#"+modalId+" #"+num+" input[name='consFirstName[]']").val(e['cons_first_name']);
                    $("#"+modalId+" #"+num+" input[name='consMiddleName[]']").val(e['cons_middle_name']);
                    $("#"+modalId+" #"+num+" input[name='consLastName[]']").val(e['cons_last_name']);
                    $("#"+modalId+" #"+num+" input[name='consEmail[]']").val(e['cons_email']);
                    $("#"+modalId+" #"+num+" input[name='consPhone[]']").val(e['cons_phone']);
                    $("#"+modalId+" #"+num+" input[name='consAddress[]']").val(e['cons_address']);
                    $("#"+modalId+" #"+num+" input[name='consSubDistrict[]']").val(e['cons_sub_district']);
                    $("#"+modalId+" #"+num+" input[name='consDistrict[]']").val(e['cons_district']);
                    $("#"+modalId+" #"+num+" input[name='consCity[]']").val(e['cons_city']);
                    $("#"+modalId+" #"+num+" input[name='consProv[]']").val(e['cons_prov']);
                    $("#"+modalId+" #"+num+" input[name='consPostalCode[]']").val(e['cons_postal_code']);
    
                    $("#"+modalId+" #"+num+" input[name='panjang[]']").val(e['length']);
                    $("#"+modalId+" #"+num+" input[name='lebar[]']").val(e['width']);
                    $("#"+modalId+" #"+num+" input[name='tinggi[]']").val(e['height']);
                    $("#"+modalId+" #"+num+" input[name='kg[]']").val(e['weight'].toFixed(2).replace(".",","));
                    $("#"+modalId+" #"+num+" input[name='cbm[]']").val(e['cbm'].toFixed(2).replace(".",","));
                    $("#"+modalId+" #"+num+" input[name='item[]']").val(e['item']);
                    $("#"+modalId+" #"+num+" input[name='actualKg[]']").val(e['actual_weight'].toFixed(2).replace(".",","));
    
                    $("#"+modalId+" #"+num+" input[name='pricePer[]']").val(masking(e['service_price_per'].toString()));

                    $("#"+modalId+" #"+num+" input[name='panjang[]']").attr("readonly",true);
                    $("#"+modalId+" #"+num+" input[name='lebar[]']").attr("readonly",true);
                    $("#"+modalId+" #"+num+" input[name='tinggi[]']").attr("readonly",true);
                    $("#"+modalId+" #"+num+" input[name='kg[]']").attr("readonly",true);
                    $("#"+modalId+" #"+num+" input[name='cbm[]']").attr("readonly",true);
                    $("#"+modalId+" #"+num+" input[name='item[]']").attr("readonly",true);
                    $("#"+modalId+" #"+num+" input[name='actualKg[]']").attr("readonly",true);
                    $("#"+modalId+" #"+num+" input[name='pricePer[]']").attr("readonly",true);

                    $("#"+modalId+" #"+num+" input[name='panjang[]']").rules("remove", "greaterThanZero");
                    $("#"+modalId+" #"+num+" input[name='lebar[]']").rules("remove", "greaterThanZero");
                    $("#"+modalId+" #"+num+" input[name='tinggi[]']").rules("remove", "greaterThanZero");
                    $("#"+modalId+" #"+num+" input[name='kg[]']").rules("remove", "greaterThanZero");
                    $("#"+modalId+" #"+num+" input[name='cbm[]']").rules("remove", "greaterThanZero");
                    $("#"+modalId+" #"+num+" input[name='item[]']").rules("remove", "greaterThanZero");
                    $("#"+modalId+" #"+num+" input[name='actualKg[]']").rules("remove", "greaterThanZero");
                    $("#"+modalId+" #"+num+" input[name='pricePer[]']").rules("remove", "greaterThanZero");
    
                    if(e['discount']>0){
                        onChangeAdditionalService($("#"+modalId+" #"+num+" select[name='additionalService[]']").val("DSK"));
                        addNon++;
                    }

                    let hargaService = 0;
                    if(satuanBeratVal=="ITEM"){
                        hargaService = e['item']*e['service_price_per'];
                    }else if(satuanBeratVal=="CBM"){
                        hargaService = e['cbm']*e['service_price_per'];
                    }else{
                        hargaService = kgRound(e['weight'])*e['service_price_per'];
                    }
                    $("#"+modalId+" #"+num+" input[name='discount[]']").val(masking(e['discount'].toString()));
                    $("#"+modalId+" #"+num+" input[name='hargaService[]']").val(masking(hargaService).toString());
                    $("#"+modalId+" #"+num+" input[name='hargaServiceAfter[]']").val(masking(hargaService-e['discount']).toString());
                    $("#"+modalId+" #"+num+" input[name='discount"+num+"']").val(e['discount']);
                    $("#"+modalId+" #"+num+" input[name='hargaService"+num+"']").val(hargaService);
                    $("#"+modalId+" #"+num+" input[name='hargaServiceAfter"+num+"']").val(hargaService-e['discount']);

                    if(e['packing']>0){
                        onChangeAdditionalService($("#"+modalId+" #"+num+" select[name='additionalService[]']").val("KAY"));
                        addNon++;
                    }
                    $("#"+modalId+" #"+num+" input[name='packing[]']").val(masking(e['packing'].toString()));
                    $("#"+modalId+" #"+num+" input[name='packingPer[]']").val(masking(e['packing_per']).toString());
                    $("#"+modalId+" #"+num+" input[name='packingTotal[]']").val(masking(e['packing_total']).toString());
                    $("#"+modalId+" #"+num+" input[name='packingDesc[]']").val(e['packing_desc']);
                    $("#"+modalId+" #"+num+" input[name='packing"+num+"']").val(e['packing']);
                    $("#"+modalId+" #"+num+" input[name='packingPrice"+num+"']").val(e['packing_price']);
                    $("#"+modalId+" #"+num+" input[name='packingPer"+num+"']").val(e['packing_per']);
                    $("#"+modalId+" #"+num+" input[name='packingTotal"+num+"']").val(e['packing_total']);
                    $("#"+modalId+" #"+num+" input[name='packingDesc"+num+"']").val(e['packing_desc']);
    
                    if(e['insurance_item_price']>0){
                        onChangeAdditionalService($("#"+modalId+" #"+num+" select[name='additionalService[]']").val("ASR"));
                        addNon++;
                    }
                    $("#"+modalId+" #"+num+" input[name='insurancePriceItem[]']").val(masking(e['insurance_item_price'].toString()));
                    $("#"+modalId+" #"+num+" input[name='insurancePercent[]']").val(masking(e['insurance_percent'].toString()));
                    $("#"+modalId+" #"+num+" input[name='insuranceTotal[]']").val(masking(e['insurance_total'].toString()));
                    $("#"+modalId+" #"+num+" input[name='insurancePriceItem"+num+"']").val(e['insurance_item_price']);
                    $("#"+modalId+" #"+num+" input[name='insurancePercent"+num+"']").val(e['insurance_percent']);
                    $("#"+modalId+" #"+num+" input[name='insuranceTotal"+num+"']").val(e['insurance_total']);
    
                    if(e['extra_cost_price']>0){
                        onChangeAdditionalService($("#"+modalId+" #"+num+" select[name='additionalService[]']").val("EON"));
                        addNon++;
                    }
                    $("#"+modalId+" #"+num+" input[name='extraCostPrice[]']").val(masking(e['extra_cost_price'].toString()));
                    $("#"+modalId+" #"+num+" input[name='extraCostDest[]']").val(e['extra_cost_dest']);
                    $("#"+modalId+" #"+num+" input[name='extraCostVendorName[]']").val(e['extra_cost_vendor_name']);
                    $("#"+modalId+" #"+num+" input[name='extraCostShippingNum[]']").val(e['extra_cost_shipping_number']);
                    $("#"+modalId+" #"+num+" input[name='extraCostPrice"+num+"']").val(e['extra_cost_price']);
                    $("#"+modalId+" #"+num+" input[name='extraCostDest"+num+"']").val(e['extra_cost_dest']);
                    $("#"+modalId+" #"+num+" input[name='extraCostVendorName"+num+"']").val(e['extra_cost_vendor_name']);
                    $("#"+modalId+" #"+num+" input[name='extraCostShippingNum"+num+"']").val(e['extra_cost_shipping_number']);
    
                    if(e['document']>0){
                        onChangeAdditionalService($("#"+modalId+" #"+num+" select[name='additionalService[]']").val("DOC"));
                        addNon++;
                    }
                    $("#"+modalId+" #"+num+" input[name='document[]']").val(masking(e['document'].toString()));
                    $("#"+modalId+" #"+num+" input[name='documentPer[]']").val(masking(e['document_per']).toString());
                    $("#"+modalId+" #"+num+" input[name='documentTotal[]']").val(masking(e['document_total']).toString());
                    $("#"+modalId+" #"+num+" input[name='documentDesc[]']").val(e['document_desc']);
                    $("#"+modalId+" #"+num+" input[name='document"+num+"']").val(e['document']);
                    $("#"+modalId+" #"+num+" input[name='documentPer"+num+"']").val(e['document_per']);
                    $("#"+modalId+" #"+num+" input[name='documentTotal"+num+"']").val(e['document_total']);
                    $("#"+modalId+" #"+num+" input[name='documentDesc"+num+"']").val(e['document_desc']);
    
                    if(e['fee_item_price']>0){
                        onChangeAdditionalService($("#"+modalId+" #"+num+" select[name='additionalService[]']").val("FEE"));
                        addNon++;
                    }
                    $("#"+modalId+" #"+num+" input[name='feePriceItem[]']").val(masking(e['fee_item_price'].toString()));
                    $("#"+modalId+" #"+num+" input[name='feePercent[]']").val(masking(e['fee_percent'].toString()));
                    $("#"+modalId+" #"+num+" input[name='feeTotal[]']").val(masking(e['fee_total'].toString()));
                    $("#"+modalId+" #"+num+" input[name='feePriceItem"+num+"']").val(e['fee_item_price']);
                    $("#"+modalId+" #"+num+" input[name='feePercent"+num+"']").val(e['fee_percent']);
                    $("#"+modalId+" #"+num+" input[name='feeTotal"+num+"']").val(e['fee_total']);
    
                    if(e['tax_item_price']>0){
                        onChangeAdditionalService($("#"+modalId+" #"+num+" select[name='additionalService[]']").val("TAX"));
                        addNon++;
                    }
                    $("#"+modalId+" #"+num+" input[name='taxPriceItem[]']").val(masking(e['tax_item_price'].toString()));
                    $("#"+modalId+" #"+num+" input[name='taxPercent[]']").val(masking(e['tax_percent'].toString()));
                    $("#"+modalId+" #"+num+" input[name='taxTotal[]']").val(masking(e['tax_total'].toString()));
                    $("#"+modalId+" #"+num+" input[name='taxPriceItem"+num+"']").val(e['tax_item_price']);
                    $("#"+modalId+" #"+num+" input[name='taxPercent"+num+"']").val(e['tax_percent']);
                    $("#"+modalId+" #"+num+" input[name='taxTotal"+num+"']").val(e['tax_total']);
    
                    if(e['import_permit']>0){
                        onChangeAdditionalService($("#"+modalId+" #"+num+" select[name='additionalService[]']").val("IPM"));
                        addNon++;
                    }
                    $("#"+modalId+" #"+num+" input[name='import[]']").val(masking(e['import_permit'].toString()));
                    $("#"+modalId+" #"+num+" input[name='importPer[]']").val(masking(e['import_permit_per'].toString()));
                    $("#"+modalId+" #"+num+" input[name='importTotal[]']").val(masking(e['import_permit_total'].toString()));
                    $("#"+modalId+" #"+num+" input[name='importDesc[]']").val(e['import_permit_desc']);
                    $("#"+modalId+" #"+num+" input[name='import"+num+"']").val(e['import_permit']);
                    $("#"+modalId+" #"+num+" input[name='importPer"+num+"']").val(e['import_permit_per']);
                    $("#"+modalId+" #"+num+" input[name='importTotal"+num+"']").val(e['import_permit_total']);
                    $("#"+modalId+" #"+num+" input[name='importDesc"+num+"']").val(e['import_permit_desc']);

                    if(e['export_permit']>0){
                        onChangeAdditionalService($("#"+modalId+" #"+num+" select[name='additionalService[]']").val("EPM"));
                        addNon++;
                    }
                    $("#"+modalId+" #"+num+" input[name='export[]']").val(masking(e['export_permit'].toString()));
                    $("#"+modalId+" #"+num+" input[name='exportPer[]']").val(masking(e['export_permit_per'].toString()));
                    $("#"+modalId+" #"+num+" input[name='exportTotal[]']").val(masking(e['export_permit_total'].toString()));
                    $("#"+modalId+" #"+num+" input[name='exportDesc[]']").val(e['export_permit_desc']);
                    $("#"+modalId+" #"+num+" input[name='export"+num+"']").val(e['export_permit']);
                    $("#"+modalId+" #"+num+" input[name='exportPer"+num+"']").val(e['export_permit_per']);
                    $("#"+modalId+" #"+num+" input[name='exportTotal"+num+"']").val(e['export_permit_total']);
                    $("#"+modalId+" #"+num+" input[name='exportDesc"+num+"']").val(e['export_permit_desc']);
    
                    if(e['dr_medicine']>0){
                        onChangeAdditionalService($("#"+modalId+" #"+num+" select[name='additionalService[]']").val("MED"));
                        addNon++;
                    }
                    $("#"+modalId+" #"+num+" input[name='medicine[]']").val(masking(e['dr_medicine'].toString()));
                    $("#"+modalId+" #"+num+" input[name='medicinePer[]']").val(masking(e['dr_medicine_per'].toString()));
                    $("#"+modalId+" #"+num+" input[name='medicineTotal[]']").val(masking(e['dr_medicine_total'].toString()));
                    $("#"+modalId+" #"+num+" input[name='medicineDesc[]']").val(e['dr_medicine_desc']);
                    $("#"+modalId+" #"+num+" input[name='medicinePrice"+num+"']").val(e['dr_medicine']);
                    $("#"+modalId+" #"+num+" input[name='medicinePer"+num+"']").val(e['dr_medicine_per']);
                    $("#"+modalId+" #"+num+" input[name='medicineTotal"+num+"']").val(e['dr_medicine_total']);
                    $("#"+modalId+" #"+num+" input[name='medicineDesc"+num+"']").val(e['dr_medicine_desc']);
    
                    if(e['pickup_weight']>0){
                        onChangeAdditionalService($("#"+modalId+" #"+num+" select[name='additionalService[]']").val("PCK"));
                        addNon++;
                    }
                    $("#"+modalId+" #"+num+" input[name='pickUpWeight[]']").val(masking(e['pickup_weight'].toString()));
                    $("#"+modalId+" #"+num+" input[name='pickUpCharge[]']").val(masking(e['pickup_charge'].toString()));
                    $("#"+modalId+" #"+num+" input[name='pickUpWeight"+num+"']").val(e['pickup_weight']);
                    $("#"+modalId+" #"+num+" input[name='pickUpCharge"+num+"']").val(e['pickup_charge']);

                    if(e['other_pickup_fee']>0){
                        onChangeAdditionalService($("#"+modalId+" #"+num+" select[name='additionalService[]']").val("PEF"));
                        addNon++;
                    }
                    $("#"+modalId+" #"+num+" input[name='pickUpFee[]']").val(masking(e['other_pickup_fee'].toString()));
                    $("#"+modalId+" #"+num+" input[name='pickUpFee"+num+"']").val(e['other_pickup_fee']);

                    if(e['additional_nom']>0){
                        onChangeAdditionalService($("#"+modalId+" #"+num+" select[name='additionalService[]']").val("ADD"));
                        addNon++;
                    }
                    $("#"+modalId+" #"+num+" input[name='additionalDesc[]']").val(e['additional_desc']);
                    $("#"+modalId+" #"+num+" input[name='additionalNominal[]']").val(masking(e['additional_nom'].toString()));
                    $("#"+modalId+" #"+num+" input[name='additionalDesc"+num+"']").val(e['additional_desc']);
                    $("#"+modalId+" #"+num+" input[name='additionalNominal"+num+"']").val(e['additional_nom']);

                    if(addNon===0){
                        $("#"+modalId+" #"+num+" select[name='additionalService[]']").val("NON");
                        $("#"+modalId+" #"+num+" select[name='additionalService[]']").attr("required",true);
                    }
    
                    $("#"+modalId+" #"+num+" input[name='subTotal[]']").val(masking(e['sub_total'].toString()));
    
                    subTotals[num]=e['sub_total'];
                    itemTotals[num]=e['item'];
                    kgTotals[num]=e['weight'];
                    diskonTotals[num]=e['discount'];
                    cbmTotals[num]=e['cbm'];
    
                    totalWeight+=e['weight'];
                    totalCbm+=e['cbm'];
                    totalItem+=e['item'];
                    totalDiskon+=e['discount'];
                    totalBiaya+=e['sub_total'];
                    adjustFee+=e['adjust_fee'];
                    num++;
                    timeout+=100;
                }, timeout);
                });  

            setTimeout(() => {
                localStorage.setItem("subTotals",JSON.stringify(subTotals));
                localStorage.setItem("itemTotals",JSON.stringify(itemTotals));
                localStorage.setItem("kgTotals",JSON.stringify(kgTotals));
                localStorage.setItem("cbmTotals",JSON.stringify(cbmTotals));
                localStorage.setItem("diskonTotals",JSON.stringify(diskonTotals));
    
                $("#"+modalId+" .modal-title").text(typeId=="IND"?"Edit Invoice Individual":"Edit Invoice Corporate");

                if(adjustFee==0){
                    $("#"+modalId+" select[name='adjustFeeChange']").val("TIDAK");
                    onChangeAdjustFee("#"+modalId+" select[name='adjustFeeChange']");
                }else{
                    $("#"+modalId+" select[name='adjustFeeChange']").val("ADA");
                    onChangeAdjustFee("#"+modalId+" select[name='adjustFeeChange']");
                }

                $("#"+modalId+"  input[name='adjustFee']").val(masking(adjustFee.toString()));
                $("#"+modalId+"  .totalBeratAll").text(masking(floatOrInt(totalWeight).toString()));
                $("#"+modalId+"  .totalItemAll").text(masking(totalItem.toString()));
                $("#"+modalId+"  .totalCbmAll").text(masking(floatOrInt(totalCbm).toString()));
                $("#"+modalId+"  .totalDiskonAll").text(masking(totalDiskon.toString()));
                $("#"+modalId+"  .totalSubTotalAll").text(masking(floatOrInt(totalBiaya).toString()));
                $("#"+modalId+" .totalHargaBoard div .value").text(masking(parseInt(totalBiaya+adjustFee).toString()));
                // $("#"+modalId+" .totalItemBoard div .value").text(masking(totalItem.toString()));
                // $("#"+modalId+" .totalDiskonBoard div .value").text(masking(totalDiskon.toString()));
                // $("#"+modalId+" .totalCbmBoard div .value").text(masking(totalCbm.toString()));
                // $("#"+modalId+" .totalKiloBoard div .value").text(masking(floatOrInt(totalWeight).toString()));  
                
                //############################################
                if(dokuLink!=""){
                    onChangePembayaran($("#"+modalId+" select[name='pembayaran']").val("DOKU"));
                }else{
                    onChangePembayaran($("#"+modalId+" select[name='pembayaran']").val("BANK"));
                }
                $("#"+modalId+" select[name='templateId']").val(templateId);
                $("#"+modalId+" input[name='invoiceDoku']").val(dokuInvoiceId);
                $("#"+modalId+" input[name='linkDoku']").val(dokuLink);
                $("#"+modalId+" input[name='namaBank']").val(bankName);
                $("#"+modalId+" input[name='namaRekening']").val(bankAccountName);
                $("#"+modalId+" input[name='noRekening']").val(bankAccountId);
    
                $("#"+modalId+" .row-convert").hide();
                $("#"+modalId+" input[name='convertToSGD']").prop("checked",false);
                $("#"+modalId+" input[name='foreignRateValue']").attr("required",false);
                if(fcSymbol!=""){
                    let finalRate = fcValue,
                        totalHargaRp = totalBiaya+adjustFee,
                        totalHargaConvert = totalHargaRp/finalRate;
                        finalResult = totalHargaConvert.toFixed(2);
                        
                    // console.log("totalHarga : "+totalHargaRp);
                    $("#"+modalId+" input[name='convertToSGD']").prop("checked",true);
                    $("#"+modalId+" .row-convert").show();
                    $("#"+modalId+" input[name='foreignRateValue']").val(masking(finalRate.toString())).attr("required",true);
                    $("#"+modalId+" input[name='foreignSymbol']").val(fcSymbol);
                    $("#"+modalId+" input[name='totalHargaRP']").val(masking(totalHargaRp.toString()));
                    $("#"+modalId+" input[name='totalHargaConvert']").val(masking(finalResult.toString()));
                    $("#"+modalId+" input[name='foreignRateValue']").rules("add", "greaterThanZero");
                }
                //############################################

                $(".preloaderz").hide();
            }, timeout);

            $("#"+modalId+" .alert-success").show();
            $("#"+modalId+" .searchElem").show();
            $("#"+modalId+" .modal-footer").css({
                justifyContent:"space-between"
            });
            $("#"+modalId+" .row-edit-detil-penerima").hide();
            if(typeId=="IND"){
                $("#"+modalId+" .alert-success").hide();
                $("#"+modalId+" .searchElem").hide();
                $("#"+modalId+" .modal-footer").css({
                    justifyContent:"right"
                });
                $("#"+modalId+" .row-edit-detil-penerima").show();
                $("#"+modalId+" #btnEditDetilPenerima").attr("data-id",mismassOrderId);
                $("#"+modalId+" #btnEditDetilPenerima").attr("data-firstName",firstName);
                $("#"+modalId+" #btnEditDetilPenerima").attr("data-middleName",middleName);
                $("#"+modalId+" #btnEditDetilPenerima").attr("data-lastName",lastName);
                $("#"+modalId+" #btnEditDetilPenerima").attr("data-phone",phone);
                $("#"+modalId+" #btnEditDetilPenerima").attr("data-email",email);
                $("#"+modalId+" #btnEditDetilPenerima").attr("data-address",address);
                $("#"+modalId+" #btnEditDetilPenerima").attr("data-subDistrict",subDistrict);
                $("#"+modalId+" #btnEditDetilPenerima").attr("data-district",district);
                $("#"+modalId+" #btnEditDetilPenerima").attr("data-city",city);
                $("#"+modalId+" #btnEditDetilPenerima").attr("data-prov",prov);
                $("#"+modalId+" #btnEditDetilPenerima").attr("data-postalCode",postalCode);
            }

            // $("#"+modalId+" #btnAddServiceElement").attr("data-custtypeid","");
            // $("#"+modalId+" #btnAddServiceElement").attr("data-warehouseid","");
            // $("#"+modalId+" #btnAddServiceElement").attr("data-serviceid","");
            // $("#"+modalId+" #btnAddServiceElement").attr("data-mstrackid","");
            // if(msTracks!=""){
            //     $("#"+modalId+" #btnAddServiceElement").attr("data-custtypeid",custTypeId);
            //     $("#"+modalId+" #btnAddServiceElement").attr("data-warehouseid",wareId);
            //     $("#"+modalId+" #btnAddServiceElement").attr("data-serviceid",servId);
            //     $("#"+modalId+" #btnAddServiceElement").attr("data-mstrackid",msTracks);
            // }
            $("#"+modalId+" #btnAddServiceElementAdditional").attr("data-warehouseid",wareId);
            $("#"+modalId+" #btnAddServiceElementAdditional").attr("data-serviceid",servId);
            $("#"+modalId+" #btnAddServiceElementAdditional").attr("data-satuanberat",satuanBeratVal);

            $("#"+modalId+" input[name='pricePer[]']").rules("add", "greaterThanZero");
            // $("#"+modalId+" input[name='subTotal[]']").rules("add", "greaterThanZero");

            $("#"+modalId).modal("show");

        },
    });
});

$(document).on("click","#btnAddServiceElementAdditional",function(event){
    let modalId = $(this).closest(".modal").attr("id"),
        wareId = $(this).attr("data-warehouseid"),
        servId = $(this).attr("data-serviceid"),
        satuanBerat = $(this).attr("data-satuanberat"),
        filters = [];

    filters['warehouseId'] = wareId;
    filters['serviceId'] = servId;
    filters['satuanBerat'] = satuanBerat;

    createServiceElementAdditional(modalId,filters);

});

$("#formBuatInvoiceAdditional").on("submit", function (e) {
    e.preventDefault();
}).validate({
    errorClass: "error fail-alert is-invalid",
    rules:{
        "invoiceDoku":{
            remote:{
                url: location.origin+"/check/invoicelinkdoku",
                type: "GET",
                data: {
                    invoiceDoku: function() {
                        return $("#formBuatInvoice #invoiceDoku").val();
                    },
                    mismassOrderId: function() {
                        return $("#formBuatInvoice input[name='mismassOrderId']").val();
                    },
                } 
            }
        },
        "linkDoku":{
            remote:{
                url: location.origin+"/check/invoicelinkdoku",
                type: "GET",
                data: {
                    linkDoku: function() {
                        return $("#formBuatInvoice #linkDoku").val();
                    },
                    mismassOrderId: function() {
                        return $("#formBuatInvoice input[name='mismassOrderId']").val();
                    },
                } 
            }
        },
    },
    messages: {
        "tanggalInvoice": "Tidak Boleh Kosong",
        "consFirstName[]": "Tidak Boleh Kosong",
        "consMiddleName[]": "Tidak Boleh Kosong",
        "consLastName[]": "Tidak Boleh Kosong",
        "consEmail[]": "Tidak Boleh Kosong",
        "consPhone[]": "Tidak Boleh Kosong",
        "consAddress[]": "Tidak Boleh Kosong",
        "consDistrict[]": "Tidak Boleh Kosong",
        "consCity[]": "Tidak Boleh Kosong",
        "consProv[]": "Tidak Boleh Kosong",
        "consPostalCode[]": "Tidak Boleh Kosong",
        "warehouse[]":"Pilih Salah Satu",
        "service[]":"Pilih Salah Satu",
        "satuanBerat":"Pilih Salah Satu",
        "panjang[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "lebar[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "tinggi[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "item[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "kg[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "cbm[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "pricePer[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "actualKg[]": "Tidak Boleh Kosong",
        "insurancePriceItem[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "insurancePercent[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "feePriceItem[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "feePercent[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "taxPriceItem[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "taxPercent[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "extraCostPrice[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "extraCostDest[]": "Tidak Boleh Kosong",
        "extraCostVendorName[]": "Tidak Boleh Kosong",
        "extraCostShippingNum[]": "Tidak Boleh Kosong",
        "packing[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "additionalDesc[]": "Tidak Boleh Kosong",
        "additionalNominal[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "discount[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "import[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "importPricePer[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "export[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "exportPricePer[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "document[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "documentPer[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "medicine[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "medicine[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "pickUpWeight[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "pickUpCharge[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "pickUpFee[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "subTotal[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol/Minus"
        },
        "additionalService[]": "Pilih 'No Additional' Jika Tidak Ada Biaya Tambahan",
        "pembayaran": "Pilih Salah Satu",
        "invoiceDoku": {
            required:"Tidak Boleh Kosong",
            remote:"No. Order Number Doku Telah Terdaftar"
        },
        "linkDoku": {
            required:"Tidak Boleh Kosong",
            remote:"Link Telah Terdaftar"
        },
        "namaBank": "Tidak Boleh Kosong",
        "namaRekening": "Tidak Boleh Kosong",
        "noRekening": "Tidak Boleh Kosong",
        "foreignRateValue": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "adjustFeeChange": {
            "required":"Pilih Salah Satu"
        },
        "adjustFee": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "templateId": "Pilih Salah Satu",
    },
    submitHandler: async function(form) {

        console.log(form);

        //Check Connection
        if(!checkConn()){
            return false;
        }

        const result = await confirmAction({
            title: "Apakah anda yakin?",
            html: `<p>Anda akan melakukan <b style='color:red'>Buat Invoice Additional</b>.</p>
                <div style='font-size:18px;margin-top:10px'>Total Biaya</div>
                <div style='font-size:25px;font-weight:bold'>`+allSubTotal(form)+`</div>`
        });

        if (result.isConfirmed) {
            createInvoiceAddSubmit(form);
        }

        return false;
    }

});

$("#formEditInvoiceAdditional").on("submit", function (e) {
    e.preventDefault();
}).validate({
    errorClass: "error fail-alert is-invalid",
    rules:{
        "invoiceDoku":{
            remote:{
                url: location.origin+"/check/invoicelinkdoku",
                type: "GET",
                data: {
                    invoiceDoku: function() {
                        return $("#formEditInvoice #invoiceDoku").val();
                    },
                    mismassOrderId: function() {
                        return $("#formEditInvoice input[name='mismassOrderId']").val();
                    },
                } 
            }
        },
        "linkDoku":{
            remote:{
                url: location.origin+"/check/invoicelinkdoku",
                type: "GET",
                data: {
                    linkDoku: function() {
                        return $("#formEditInvoice #linkDoku").val();
                    },
                    mismassOrderId: function() {
                        return $("#formEditInvoice input[name='mismassOrderId']").val();
                    },
                } 
            }
        }
    },
    messages: {
        "tanggalInvoice": "Tidak Boleh Kosong",
        "warehouse[]":"Pilih Salah Satu",
        "service[]":"Pilih Salah Satu",
        "satuanBerat":"Pilih Salah Satu",
        "panjang[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "lebar[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "tinggi[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "item[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "kg[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "cbm[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "pricePer[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "actualKg[]": "Tidak Boleh Kosong",
        "insurancePriceItem[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "insurancePercent[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "feePriceItem[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "feePercent[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "taxPriceItem[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "taxPercent[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "extraCostPrice[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "extraCostDest[]": "Tidak Boleh Kosong",
        "extraCostVendorName[]": "Tidak Boleh Kosong",
        "extraCostShippingNum[]": "Tidak Boleh Kosong",
        "packing[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "additionalDesc[]": "Tidak Boleh Kosong",
        "additionalNominal[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "discount[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "import[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "importPricePer[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "export[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "exportPricePer[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "document[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "documentPer[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "medicine[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "medicine[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "pickUpWeight[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "pickUpCharge[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "pickUpFee[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "subTotal[]": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol/Minus"
        },
        "additionalService[]": "Pilih 'No Additional' Jika Tidak Ada Biaya Tambahan",
        "pembayaran": "Pilih Salah Satu",
        "invoiceDoku": {
            required:"Tidak Boleh Kosong",
            remote:"No. Order Number Doku Telah Terdaftar"
        },
        "linkDoku": {
            required:"Tidak Boleh Kosong",
            remote:"Link Telah Terdaftar"
        },
        "namaBank": "Tidak Boleh Kosong",
        "namaRekening": "Tidak Boleh Kosong",
        "noRekening": "Tidak Boleh Kosong",
        "foreignRateValue": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "adjustFee": {
            "required":"Tidak Boleh Kosong",
            "greaterThanZero":"Tidak Boleh Nol"
        },
        "templateId": "Pilih Salah Satu",
        "revisionNote": "Tidak Boleh Kosong"
    },
    submitHandler: async function(form) {        

        // $.ajax({
        //     type: "POST",
        //     url: location.origin+"/shiplist/edit/invoice",
        //     data: $(form).serialize(),
        //     beforeSend: function() {
        //         loading(form);
        //         $(".preloaderz .preloaderz-wrapper img").css("display","none");
        //         $(".preloaderz .preloaderz-wrapper .text").html("<h2>Revisi Invoice</h2><div>Loading ...</div>");
        //     },
        //     success: function(msg) {
        //         var json = JSON.parse(msg);

        //         unLoading(form);

        //         if (json.status == 200) {

        //             //NOTIF SUKSES
        //             Swal.fire({
        //                 icon: 'success',
        //                 title: json.title,
        //                 text: json.text,
        //                 showCancelButton: true,
        //                 reverseButtons:true,
        //                 cancelButtonColor:"#dc3545",
        //                 confirmButtonText: "Print Invoice",
        //                 cancelButtonText: "Close",
        //             }).then((result) => {
        //                 if (result.isConfirmed) {
        //                     window.open(json.url, '_blank');
        //                 }
        //             });

        //             let custTypeId = $('.btn-select').attr('id');
        //             refreshTable(tableCi,location.origin+"/shiplist/table/order/"+custTypeId+"?filterTanggal=","table-ci_info");
        //             refreshTable(tableCt,location.origin+"/shiplist/table/invoice/"+custTypeId+"?filterTanggal=&filterWarehouse=&filterService=","table-ct_info");
        //             refreshTable(tableSt,location.origin+"/shiplist/table/tracking/"+custTypeId+"?mismassOrderId=&filterTanggal=&filterWarehouse=&filterService=","table-status_info");

        //             //RELOAD PAGE
        //             // pageReload(location.origin+"/shiplist");

        //         } else {
        //             console.log(json);
        //             //NOTIF GAGAL
        //             Swal.fire(json.title, json.text, 'error');

        //         }
        //     }

        // });
        //Check Connection
        if(!checkConn()){
            return false;
        }

        const result = await confirmAction({
            title: "Apakah anda yakin?",
            html: `<p>Anda akan melakukan <b style='color:red'>revisi Invoice</b>.</p>
                <div style='font-size:18px;margin-top:10px'>Total Biaya</div>
                <div style='font-size:25px;font-weight:bold'>`+allSubTotal(form)+`</div>`
        });

        if (result.isConfirmed) {
            revInvoice(form);
        }

        return false;
    }

});

function createInvoiceAddSubmit(form){
    $.ajax({
        type: "GET",
        url: location.origin+"/shiplist/buat/invoice/additional",
        data: $(form).serialize(),
        beforeSend: function() {
            loading(form);
            $(".preloaderz .preloaderz-wrapper img").css("display","none");
            $(".preloaderz .preloaderz-wrapper .text").html("<h2>Buat Invoice Additional</h2><div>Loading ...</div>");
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

                CheckFilterTable();

                // let custTypeId = $('.btn-select').attr('id');
                // refreshTable(tableCi,location.origin+"/shiplist/table/order/"+custTypeId+"?filterTanggal=","table-ci_info");
                // refreshTable(tableCt,location.origin+"/shiplist/table/invoice/"+custTypeId+"?filterTanggal=&filterWarehouse=&filterService=","table-ct_info");
                // refreshTable(tableSt,location.origin+"/shiplist/table/tracking/"+custTypeId+"?mismassOrderId=&filterTanggal=&filterWarehouse=&filterService=","table-status_info");

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

function createServiceElementAdditional(modalId,filters){

    //Menentukan ID Service
    let id = $("#"+modalId+" .services").length,
        idUrut = id+1,
        margin = $("#"+modalId+" .services").length<1?"":"mt-5",
        subTotalArr,itemArr,kgArr,diskonArr;
        
    if(id==30){
        Swal.fire(
            'Gagal!',
            'Data Service Sudah Melebihi 30',
            'error'
        );
        return false;
    }

     //local Storage
     subTotalArr = JSON.parse(localStorage.getItem("subTotals"));
     itemArr = JSON.parse(localStorage.getItem("itemTotals"));
     kgArr = JSON.parse(localStorage.getItem("kgTotals"));
     cbmArr = JSON.parse(localStorage.getItem("cbmTotals"));
     diskonArr = JSON.parse(localStorage.getItem("diskonTotals"));
     subTotalArr[id]=0;
     itemArr[id]=0;
     kgArr[id]=0;
     cbmArr[id]=0;
     diskonArr[id]=0;

    //Tambah Warehouse Option
    let b = JSON.parse(wareH.replace(/&quot;/g,'"')),
        selectWareH;
    b.forEach(element => {
        selectWareH += "<option value='"+element["id"]+"'>"+element["id"]+" - "+element["name"]+" - "+element["location"]+"</option>";
    });

    //Tambah Additional Service Option
    let a = JSON.parse(addServ.replace(/&quot;/g,'"')),
        selectAddServ;
    a.forEach(element => {
        selectAddServ += "<option value='"+element["id"]+"'>"+element["name"]+"</option>";
    });

    let senderFirstName,senderMiddleName,senderLastName,senderEmail,senderPhone,senderAddress,senderSubDistrict,senderDistrict,senderCity,senderProv,senderPostalCode, 
        consFirstName,consMiddleName,consLastName,consEmail,consPhone,consAddress,consSubDistrict,consDistrict,consCity,consProv,consPostalCode,
        dbFirstName,dbMiddleName,dbLastName,dbEmail,dbPhone,dbAddress,dbSubDistrict,dbDistrict,dbCity,dbProv,dbPostalCode,dbSecondName,dbSecondPhone,
        rowSenderStatus,rowConsStatus,dbCustTypeId;

    dbCustTypeId = $(".btn-select").attr("id");
    dbFirstName = $("#"+modalId+" input[name='dbFirstName']").val();   
    dbMiddleName = $("#"+modalId+" input[name='dbMiddleName']").val();  
    dbLastName = $("#"+modalId+" input[name='dbLastName']").val();  
    dbEmail = $("#"+modalId+" input[name='dbEmail']").val();  
    dbPhone = $("#"+modalId+" input[name='dbPhone']").val();  
    dbAddress = $("#"+modalId+" input[name='dbAddress']").val();  
    dbSubDistrict = $("#"+modalId+" input[name='dbSubDistrict']").val();  
    dbDistrict = $("#"+modalId+" input[name='dbDistrict']").val();  
    dbCity = $("#"+modalId+" input[name='dbCity']").val();  
    dbProv = $("#"+modalId+" input[name='dbProv']").val();  
    dbPostalCode = $("#"+modalId+" input[name='dbPostalCode']").val();
    dbSecondName = $("#"+modalId+" input[name='dbSecondName']").val();
    dbSecondPhone = $("#"+modalId+" input[name='dbSecondPhone']").val();

    if(dbCustTypeId == "IND"){
        rowSenderStatus="style='display:none'";
        rowConsStatus="style='display:none'";
        consFirstName=dbFirstName;
        consMiddleName=dbMiddleName;
        consLastName=dbLastName;
        consEmail=dbEmail;
        consPhone=dbPhone;
        consAddress=dbAddress;
        consSubDistrict=dbSubDistrict;
        consDistrict=dbDistrict;
        consCity=dbCity;
        consProv=dbProv;
        consPostalCode=dbPostalCode;
        senderFirstName=dbSecondName;
        senderMiddleName="";
        senderLastName="";
        senderEmail="";
        senderPhone=dbSecondPhone;
        senderAddress="";
        senderSubDistrict="";
        senderDistrict="";
        senderCity="";
        senderProv="";
        senderPostalCode="";
        label = "Service";
    }else if(dbCustTypeId == "COR"){
        rowSenderStatus="style='display:none'";
        rowConsStatus="";
        senderFirstName=dbFirstName;
        senderMiddleName=dbMiddleName;
        senderLastName=dbLastName;
        senderEmail=dbEmail;
        senderPhone=dbPhone;
        senderAddress=dbAddress;
        senderSubDistrict=dbSubDistrict;
        senderDistrict=dbDistrict;
        senderCity=dbCity;
        senderProv=dbProv;
        senderPostalCode=dbPostalCode;
        consFirstName="";
        consMiddleName="";
        consLastName="";
        consEmail="";
        consPhone="";
        consAddress="";
        consSubDistrict="";
        consDistrict="";
        consCity="";
        consProv="";
        consPostalCode="";
        label = "Customer";
    }

    //Element Services
    let servicesElement = "<div class='services "+margin+"' id='"+id+"'>"+
    "<input type='hidden' name='pricePerKg'>"+
    "<input type='hidden' name='pricePerVol'>"+
    "<input type='hidden' name='pricePerItem'>"+
    "<input type='hidden' name='pricePerCbm'>"+
    "<input type='hidden' name='insurancePriceItem"+id+"' class='form-control insurancePriceItem' value='0'>"+
    "<input type='hidden' name='insurancePercent"+id+"' class='form-control insurancePercent' value='0'>"+
    "<input type='hidden' name='insuranceTotal"+id+"' class='form-control insuranceTotal' value='0'>"+
    "<input type='hidden' name='feePriceItem"+id+"' class='form-control feePriceItem' value='0'>"+
    "<input type='hidden' name='feePercent"+id+"' class='form-control feePercent' value='0'>"+
    "<input type='hidden' name='feeTotal"+id+"' class='form-control feeTotal' value='0'>"+
    "<input type='hidden' name='taxPriceItem"+id+"' class='form-control taxPriceItem' value='0'>"+
    "<input type='hidden' name='taxPercent"+id+"' class='form-control taxPercent' value='0'>"+
    "<input type='hidden' name='taxTotal"+id+"' class='form-control taxTotal' value='0'>"+
    "<input type='hidden' name='extraCostPrice"+id+"' class='form-control extraCostPrice' value='0'>"+
    "<input type='hidden' name='extraCostDest"+id+"' class='form-control extraCostDest' value=''>"+
    "<input type='hidden' name='extraCostVendorName"+id+"' class='form-control extraCostVendorName' value=''>"+
    "<input type='hidden' name='extraCostShippingNum"+id+"' class='form-control extraCostShippingNum' value=''>"+
    "<input type='hidden' name='packing"+id+"' class='form-control packing' value='0'>"+
    "<input type='hidden' name='packingPer"+id+"' class='form-control packingPer' value='0'>"+
    "<input type='hidden' name='packingTotal"+id+"' class='form-control packingTotal' value='0'>"+
    "<input type='hidden' name='packingDesc"+id+"' class='form-control packingDesc' value=''>"+
    "<input type='hidden' name='import"+id+"' class='form-control import' value='0'>"+
    "<input type='hidden' name='importPer"+id+"' class='form-control importPer' value='0'>"+
    "<input type='hidden' name='importTotal"+id+"' class='form-control importTotal' value='0'>"+
    "<input type='hidden' name='importDesc"+id+"' class='form-control importDesc' value=''>"+
    "<input type='hidden' name='export"+id+"' class='form-control export' value='0'>"+
    "<input type='hidden' name='exportPer"+id+"' class='form-control exportPer' value='0'>"+
    "<input type='hidden' name='exportTotal"+id+"' class='form-control exportTotal' value='0'>"+
    "<input type='hidden' name='exportDesc"+id+"' class='form-control exportDesc' value=''>"+
    "<input type='hidden' name='document"+id+"' class='form-control document' value='0'>"+
    "<input type='hidden' name='documentPer"+id+"' class='form-control documentPer' value='0'>"+
    "<input type='hidden' name='documentTotal"+id+"' class='form-control documentTotal' value='0'>"+
    "<input type='hidden' name='documentDesc"+id+"' class='form-control documentDesc' value=''>"+
    "<input type='hidden' name='medicine"+id+"' class='form-control medicine' value='0'>"+
    "<input type='hidden' name='medicinePer"+id+"' class='form-control medicinePer' value='0'>"+
    "<input type='hidden' name='medicineTotal"+id+"' class='form-control medicineTotal' value='0'>"+
    "<input type='hidden' name='medicineDesc"+id+"' class='form-control medicineDesc' value=''>"+
    "<input type='hidden' name='pickUpWeight"+id+"' class='form-control pickUpWeight' value='0'>"+
    "<input type='hidden' name='pickUpCharge"+id+"' class='form-control pickUpCharge' value='0'>"+
    "<input type='hidden' name='pickUpFee"+id+"' class='form-control pickUpFee' value='0'>"+
    "<input type='hidden' name='discount"+id+"' class='form-control discount' value='0'>"+
    "<input type='hidden' name='hargaService"+id+"' class='form-control hargaService' value='0'>"+
    "<input type='hidden' name='hargaServiceAfter"+id+"' class='form-control hargaServiceAfter' value='0'>"+
    "<input type='hidden' name='additionalDesc"+id+"' class='form-control additionalDesc' value=''>"+
    "<input type='hidden' name='additionalNominal"+id+"' class='form-control additionalNominal' value='0'>"+
    "<div class='row'>"+
    "<div class='col'>"+
    "<h5>"+label+"#"+idUrut+"</h5>"+
    "</div>"+
    "<div class='col text-right'>"+
    "<button type='button' class='close deleteService'>"+
    "<span aria-hidden='true'>&times;</span>"+
    "</button>"+
    "</div>"+
    "</div>"+

    "<div class='row-sender' "+rowSenderStatus+">"+
    "<div class='row'>"+
        "<div class='col'>"+
            "<label for='senderLabel'>Sender / Pengirim</label>"+
        "</div>"+
    "</div>"+
    "<div class='row mt-2'>"+
        "<div class='col-md-4 col-12'>"+
            "<label for='senderFirstName'>Nama Depan</label>"+
            "<input type='text' class='form-control' value='"+senderFirstName+"' onkeyup='this.value = this.value.toUpperCase()' name='senderFirstName[]' id='"+makeId(8)+"' placeholder='First Name / Nama Depan'>"+
        "</div>"+
        "<div class='col-md-4 col-12'>"+
            "<label for='senderMiddleName'>Nama Tengah</label>"+
            "<input type='text' class='form-control' value='"+senderMiddleName+"' onkeyup='this.value = this.value.toUpperCase()' name='senderMiddleName[]' id='"+makeId(8)+"' placeholder='Middle Name / Nama Tengah (Optional)'>"+
        "</div>"+
        "<div class='col-md-4 col-12'>"+
            "<label for='senderLastName'>Nama Terakhir</label>"+
            "<input type='text' class='form-control' value='"+senderLastName+"' onkeyup='this.value = this.value.toUpperCase()' name='senderLastName[]' id='"+makeId(8)+"' placeholder='Last Name / Nama Terakhir'>"+
        "</div>"+
    "</div>"+
    "<div class='row mt-2'>"+
        "<div class='col-md-6 col-12'>"+
            "<label for='senderEmail'>Alamat Email</label>"+
            "<input type='email' class='form-control' value='"+senderEmail+"' name='senderEmail[]' id='"+makeId(8)+"' placeholder='Email Address'>"+
        "</div>"+
        "<div class='col-md-6 col-12'>"+
            "<label for='senderPhone'>Nomor Whatsapp</label>"+
            "<input type='text' class='form-control' value='"+senderPhone+"' name='senderPhone[]' id='"+makeId(8)+"' placeholder='Whatsapp Number'>"+
        "</div>"+
    "</div>"+
    "<div class='row mt-2'>"+
        "<div class='col-md-6 col-12'>"+
            "<label for='senderAddress'>Alamat</label>"+
            "<input type='text' class='form-control' value='"+senderAddress+"' onkeyup='this.value = this.value.toUpperCase()' name='senderAddress[]' id='"+makeId(8)+"' placeholder='Address'>"+
        "</div>"+
        "<div class='col-md-6 col-12'>"+
            "<label for='senderSubDistrict'>Kelurahan</label>"+
            "<input type='text' class='form-control' value='"+senderSubDistrict+"' onkeyup='this.value = this.value.toUpperCase()' name='senderSubDistrict[]' id='"+makeId(8)+"' placeholder='Sub-District'>"+
        "</div>"+
    "</div>"+
    "<div class='row mt-2'>"+
        "<div class='col-md-6 col-12'>"+
            "<label for='senderDistrict'>Kecamatan</label>"+
            "<input type='text' class='form-control' value='"+senderDistrict+"' onkeyup='this.value = this.value.toUpperCase()' name='senderDistrict[]' id='"+makeId(8)+"' placeholder='District'>"+
        "</div>"+
        "<div class='col-md-6 col-12'>"+
            "<label for='senderCity'>Kabupaten / Kota</label>"+
            "<input type='text' class='form-control' value='"+senderCity+"' onkeyup='this.value = this.value.toUpperCase()' name='senderCity[]' id='"+makeId(8)+"' placeholder='City'>"+
        "</div>"+
    "</div>"+
    "<div class='row mt-2'>"+
        "<div class='col-md-6 col-12'>"+
            "<label for='senderProv'>Provinsi</label>"+
            "<input type='text' class='form-control' value='"+senderProv+"' onkeyup='this.value = this.value.toUpperCase()' name='senderProv[]' id='"+makeId(8)+"' placeholder='Region'>"+
        "</div>"+
        "<div class='col-md-6 col-12'>"+
            "<label for='senderPostalCode'>Kode Pos</label>"+
            "<input type='text' class='form-control' value='"+senderPostalCode+"' name='senderPostalCode[]' id='"+makeId(8)+"' placeholder='Postal Code'>"+
        "</div>"+
    "</div>"+
    "<hr>"+
    "</div>"+

    "<div class='row-cons mt-2' "+rowConsStatus+">"+
    "<div class='row'>"+
        "<div class='col'>"+
            "<label for='consLabel'>Consignee / Penerima</label>"+
        "</div>"+
    "</div>"+
    "<div class='row mt-2'>"+
        "<div class='col-md-4 col-12'>"+
            "<label for='consFirstName'>Nama Depan</label>"+
            "<input type='text' class='form-control' value='"+consFirstName+"' onkeyup='this.value = this.value.toUpperCase()' name='consFirstName[]' id='"+makeId(8)+"' placeholder='First Name / Nama Depan' required>"+
        "</div>"+
        "<div class='col-md-4 col-12'>"+
            "<label for='consMiddleName'>Nama Tengah</label>"+
            "<input type='text' class='form-control' value='"+consMiddleName+"' onkeyup='this.value = this.value.toUpperCase()' name='consMiddleName[]' id='"+makeId(8)+"' placeholder='Middle Name / Nama Tengah (Optional)'>"+
        "</div>"+
        "<div class='col-md-4 col-12'>"+
            "<label for='consLastName'>Nama Terakhir</label>"+
            "<input type='text' class='form-control' value='"+consLastName+"' onkeyup='this.value = this.value.toUpperCase()' name='consLastName[]' id='"+makeId(8)+"' placeholder='Last Name / Nama Terakhir'>"+
        "</div>"+
    "</div>"+
    "<div class='row mt-2'>"+
        "<div class='col-md-6 col-12'>"+
            "<label for='consEmail'>Alamat Email</label>"+
            "<input type='email' class='form-control' value='"+consEmail+"' name='consEmail[]' id='"+makeId(8)+"' placeholder='Email Address'>"+
        "</div>"+
        "<div class='col-md-6 col-12'>"+
            "<label for='consPhone'>Nomor Whatsapp</label>"+
            "<input type='text' class='form-control' value='"+consPhone+"' name='consPhone[]' id='"+makeId(8)+"' placeholder='Whatsapp Number' required>"+
        "</div>"+
    "</div>"+
    "<div class='row mt-2'>"+
        "<div class='col-md-6 col-12'>"+
            "<label for='consAddress'>Alamat</label>"+
            "<input type='text' class='form-control' value='"+consAddress+"' onkeyup='this.value = this.value.toUpperCase()' name='consAddress[]' id='"+makeId(8)+"' placeholder='Address' required>"+
        "</div>"+
        "<div class='col-md-6 col-12'>"+
            "<label for='consSubDistrict'>Kelurahan</label>"+
            "<input type='text' class='form-control' value='"+consSubDistrict+"' onkeyup='this.value = this.value.toUpperCase()' name='consSubDistrict[]' id='"+makeId(8)+"' placeholder='Sub-District'>"+
        "</div>"+
    "</div>"+
    "<div class='row mt-2'>"+
        "<div class='col-md-6 col-12'>"+
            "<label for='consDistrict'>Kecamatan</label>"+
            "<input type='text' class='form-control' value='"+consDistrict+"' onkeyup='this.value = this.value.toUpperCase()' name='consDistrict[]' id='"+makeId(8)+"' placeholder='District'>"+
        "</div>"+
        "<div class='col-md-6 col-12'>"+
            "<label for='consCity'>Kabupaten / Kota</label>"+
            "<input type='text' class='form-control' value='"+consCity+"' onkeyup='this.value = this.value.toUpperCase()' name='consCity[]' id='"+makeId(8)+"' placeholder='City' required>"+
        "</div>"+
    "</div>"+
    "<div class='row mt-2'>"+
        "<div class='col-md-6 col-12'>"+
            "<label for='consProv'>Provinsi</label>"+
            "<input type='text' class='form-control' value='"+consProv+"' onkeyup='this.value = this.value.toUpperCase()' name='consProv[]' id='"+makeId(8)+"' placeholder='Region' required>"+
        "</div>"+
        "<div class='col-md-6 col-12'>"+
            "<label for='consPostalCode'>Kode Pos</label>"+
            "<input type='text' class='form-control' value='"+consPostalCode+"' name='consPostalCode[]' id='"+makeId(8)+"' placeholder='Postal Code' required>"+
        "</div>"+
    "</div>"+
    "<hr>"+
    "</div>"+

    "<div class='row row-ware-serv mt-2' style='display:none'>"+
    "<div class='col'>"+
    "<div class='form-group form-group-warehouse'>"+
    "<label for='warehouse'>Warehouse</label>"+
    "<select name='warehouse[]' id='"+makeId(8)+"' class='form-control' required>"+
    "<option value='' hidden>Pilih Warehouse</option>"+selectWareH+
    "</select>"+
    "<input name='warehouseval[]' id='"+makeId(8)+"' class='form-control' type='hidden'>"+
    "</div>"+
    "</div>"+
    "<div class='col col-service'>"+
    "<div class='form-group form-group-service'>"+
    "<label for='service'>Service</label>"+
    "<select name='service[]' id='"+makeId(8)+"' class='form-control' required></select>"+
    "<input name='serviceval[]' id='"+makeId(8)+"' class='form-control' type='hidden'>"+
    "</div>"+
    "</div>"+
    "<div class='col'>"+
    "<div class='form-group'>"+
    "<label for='satuanBerat'>Satuan Berat</label>"+
    "<select name='satuanBerat' id='"+makeId(8)+"' class='form-control'></select>"+
    "</div>"+
    "</div>"+
    "</div>"+
    "<div class='row row-detil-biaya mt-2' style='display:none'>"+
    "<div class='col col-volume'>"+
    "<div class='form-group'>"+
    "<label for='panjang'>Panjang (Cm)</label>"+
    "<input type='text' name='panjang[]' id='"+makeId(8)+"' class='form-control masking'>"+
    "</div>"+
    "</div>"+
    "<div class='col col-volume'>"+
    "<div class='form-group'>"+
    "<label for='lebar'>Lebar (Cm)</label>"+
    "<input type='text' name='lebar[]' id='"+makeId(8)+"' class='form-control masking'>"+
    "</div>"+
    "</div>"+
    "<div class='col col-volume'>"+
    "<div class='form-group'>"+
    "<label for='tinggi'>Tinggi (Cm)</label>"+
    "<input type='text' name='tinggi[]' id='"+makeId(8)+"' class='form-control masking'>"+
    "</div>"+
    "</div>"+
    "<div class='col col-kg'>"+
    "<div class='form-group'>"+
    "<label for='beratKg'>Berat (Kg)</label>"+
    "<input type='text' name='kg[]' id='"+makeId(8)+"' class='form-control maskingComma'>"+
    "</div>"+
    "</div>"+
    "<div class='col col-cbm'>"+
    "<div class='form-group'>"+
    "<label for='beratKg'>Jumlah (CBM)</label>"+
    "<input type='text' name='cbm[]' id='"+makeId(8)+"' class='form-control maskingComma'>"+
    "</div>"+
    "</div>"+
    "<div class='col col-item'>"+
    "<div class='form-group'>"+
    "<label for='item'>Jumlah Item</label>"+
    "<input type='text' name='item[]' id='"+makeId(8)+"' class='form-control masking'>"+
    "</div>"+
    "</div>"+
    "<div class='col col-priceper'>"+
    "<div class='form-group'>"+
    "<label for='pricePer'>Harga/Kg</label>"+
    "<input type='text' name='pricePer[]' id='"+makeId(8)+"' value='0' class='form-control masking' required>"+
    "</div>"+
    "</div>"+
    "<div class='col col-actual'>"+
    "<div class='form-group'>"+
    "<label for='actualKg'>Aktual (Kg)</label>"+
    "<input type='text' name='actualKg[]' id='"+makeId(8)+"' class='form-control maskingComma'>"+
    "</div>"+
    "</div>"+
    "</div>"+
    "<div class='row row-additional mt-2'>"+
    "<div class='col'>"+
    "<div class='form-group'>"+
    "<label for='AdditionalService'>Add Additional Service</label>"+
    "<select name='additionalService[]' id='"+makeId(8)+"' class='form-control' required>"+
    "<option value='' hidden>Pilih Additional Service</option>"+selectAddServ+
    "</select>"+
    "</div>"+
    "</div>"+
    "</div>"+
    "<div class='row mt-2'>"+
    "<div class='col'>"+
    "<div class='form-group'>"+
    "<label for='subTotal'>Sub Total</label>"+
    "<input type='text' name='subTotal[]' id='"+makeId(8)+"' class='form-control' readonly>"+
    "</div>"+
    "</div>"+
    "</div>"+
    "</div>";

    $("#"+modalId+" .row-services").append(servicesElement);

    $("#"+modalId+" #"+id+" select[name='warehouse[]']").val(filters.warehouseId).trigger("change").attr("disabled",true);
    $("#"+modalId+" #"+id+" select[name='service[]']").val(filters.serviceId).trigger("change").attr("disabled",true);
    $("#"+modalId+" #"+id+" select[name='satuanBerat']").val(filters.satuanBerat);
    $("#"+modalId+" #"+id+" input[name='warehouseval[]']").val(filters.warehouseId);
    $("#"+modalId+" #"+id+" input[name='serviceval[]']").val(filters.serviceId);

    $("#"+modalId+" #"+id+" input[name='panjang[]']").val(0);
    $("#"+modalId+" #"+id+" input[name='lebar[]']").val(0);
    $("#"+modalId+" #"+id+" input[name='tinggi[]']").val(0);
    $("#"+modalId+" #"+id+" input[name='kg[]']").val(0);
    $("#"+modalId+" #"+id+" input[name='cbm[]']").val(0);
    $("#"+modalId+" #"+id+" input[name='item[]']").val(0);
    $("#"+modalId+" #"+id+" input[name='actualKg[]']").val(0);
    $("#"+modalId+" #"+id+" input[name='pricePer[]']").val(0);

    // $("#"+modalId+" #"+id+" .col-service").hide();
    // $("#"+modalId+" #"+id+" .col-kg").hide();
    // $("#"+modalId+" #"+id+" .col-cbm").hide();
    // $("#"+modalId+" #"+id+" .col-volume").hide();
    // $("#"+modalId+" #"+id+" .col-item").hide();
    // $("#"+modalId+" #"+id+" .row-detil-biaya").hide();

}