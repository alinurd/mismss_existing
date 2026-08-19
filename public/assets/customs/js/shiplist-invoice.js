$("table").on("click","#buatBtn",function(){
    $(".preloaderz").show();

    let orderStatusId = $(this).closest("tr").find("#btnStatusId").text();
    if(orderStatusId=="HOLD"){
        $(".preloaderz").hide();
        return Swal.fire(
            'Status Order HOLD!',
            'Silahkan ganti status terlebih dahulu',
            'error'
          )
    }

    let modalId = "createInvoice",
        typeId = $(".btn-select").attr("id");
        mismassOrderId = $(this).attr("data-id"),
        custId = $(this).attr("data-custId"),
        custTypeId = $(this).attr("data-custTypeId"),
        firstName = $(this).attr("data-firstName"),
        middleName = $(this).attr("data-middleName"),
        lastName = $(this).attr("data-lastName"),
        email = $(this).attr("data-email"),
        phone = $(this).attr("data-phone"),
        address = $(this).attr("data-address"),
        subDistrict = $(this).attr("data-subDistrict"),
        district = $(this).attr("data-district"),
        city = $(this).attr("data-city"),
        prov = $(this).attr("data-prov"),
        postalCode = $(this).attr("data-postalCode"),
        secondName = $(this).attr("data-secondName"),
        secondPhone = $(this).attr("data-secondPhone"),
        tanggal = $(this).attr("data-tanggal"),
        custTypeId = $(".btn-select").attr("id"),
        wareId = $(this).attr("data-warehouse"),
        servId = $(this).attr("data-service"),
        totalForeign = $(this).attr("data-total-foreign"),
        foreignTracks = $(this).attr("data-foreign-tracks"),
        note = $(this).attr("data-note"),
        images = $(this).attr("data-images"),
        baseUrl = $(this).attr("data-baseurl"),
        tableEl = "", listTable= "", inputHiddenEl= "", noteEl= "";
        
    resetModalInvoice(modalId);

    //Create Null Value Element Of MS Track
    $("#"+modalId+" .row-inputpos").html("");
    $("#"+modalId+" .row-mstrack").html("");
    inputHiddenEl = "<input type='hidden' name='trackId[]' value=''>";
    $("#"+modalId+" .row-inputPos").append(inputHiddenEl);

    //If MS Track Is Not Null
    if($(this).attr('data-ms-track')!=""){

        //Create MS Track Value
        inputHiddenEl = "<input type='hidden' class='cl"+$(this).attr("data-ms-track")+"' name='trackId[]' value='"+$(this).attr("data-ms-track")+"'>";
        $("#"+modalId+" .row-inputpos").append(inputHiddenEl);

        noteEl = note!=""?"<div class='bg-catatan'>Catatan</div>":"";

        //Create Table List For MS Track
        listTable = "<tr class='el"+$(this).attr('data-ms-track')+"'>"+
                    "<td>1</td>"+
                    "<td>"+
                        "<div class='fw-bold'>"+$(this).attr('data-ms-track')+"</div>"+
                        "<div style='display:flex'>"+
                            "<div style='margin-right:5px'>"+totalForeign+"</div>"+
                            "<a class='pointlink lookresiln' data-id='"+$(this).attr('data-ms-track')+"' data-resi-ln='"+foreignTracks+"' data-catatan='"+note+"' data-images='"+images+"' data-baseurl='"+baseUrl+"'><i class='fas fa-eye'></i></a>"+
                        "</div>"+
                        noteEl+
                    "</td>"+
                    "<td><div class='fw-bold'>"+$(this).attr('data-drop-created-at')+"</div></td>"+
                    "<td><div class='fw-bold'>"+$(this).attr('data-ship-created-at')+"</div></td>"+
                    "<td><a style='color:red' class='pointlink outBtn' data-ms-track='"+$(this).attr('data-ms-track')+"'><i class='fas fa-trash'></i></a></td>"+
                    "</tr>";
        tableEl = "<div style='overflow:auto'>"+
                    "<table class='table table-striped'>"+
                    "<thead style='position:sticky;top:0'>"+
                        "<tr>"+
                            "<th>#</th>"+
                            "<th>Resi Tracking</th>"+
                            "<th>Tgl Drop</th>"+
                            "<th>Tgl Shipment</th>"+
                            "<th>Hapus</th>"+
                        "</tr>"+
                    "</thead>"+
                    "<tbody>"+
                    listTable+
                    "</tbody>"+
                "</table>"+
                "</div>";
        $("#"+modalId+" .row-mstrack").append(tableEl);
    }

    $("#"+modalId+" .alert-success").show();
    $("#"+modalId+" .searchElem").show();
    $("#"+modalId+" .modal-footer").css({
        justifyContent:"space-between"
    });
    if(custTypeId=="IND"){
        $("#"+modalId+" .alert-success").hide();
        $("#"+modalId+" .searchElem").hide();
        $("#"+modalId+" .modal-footer").css({
            justifyContent:"right"
        });
    }

    $("#"+modalId+" input[name='convertToSGD']").prop("checked",false);
    $(".row-convert").hide();

    $("#"+modalId+" .modal-title").text(typeId=="IND"?"Buat Invoice Individual":"Buat Invoice Corporate");
    $("#"+modalId+" input[name='foreignSymbol']").val("SGD");
    $("#"+modalId+" label[for='tanggal']").text(tanggal);
    $("#"+modalId+" label[for='customer']").text(firstName+" "+middleName+" "+lastName);
    $("#"+modalId+" label[for='alamat']").text(address+", "+subDistrict+", "+district+", "+city+", "+prov+", "+postalCode);
    $("#"+modalId+" label[for='telpon']").text(phone);
    $("#"+modalId+" input[name='mismassOrderId']").val(mismassOrderId);
    $("#"+modalId+" input[name='dbCustId']").val(custId);
    $("#"+modalId+" input[name='dbCustTypeId']").val(custTypeId);
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

    //Set Local Storage
    let subTotals = [],
        itemTotals = [],
        cbmTotals = [],
        diskonTotals = [],
        kgTotals = [];

    localStorage.setItem("subTotals",JSON.stringify(subTotals));
    localStorage.setItem("itemTotals",JSON.stringify(itemTotals));
    localStorage.setItem("kgTotals",JSON.stringify(kgTotals));
    localStorage.setItem("cbmTotals",JSON.stringify(cbmTotals));
    localStorage.setItem("diskonTotals",JSON.stringify(diskonTotals));
    
    createServiceElement(modalId,filters={});

    $("#"+modalId+" #btnAddServiceElement").attr("data-custtypeid","");
    $("#"+modalId+" #btnAddServiceElement").attr("data-warehouseid","");
    $("#"+modalId+" #btnAddServiceElement").attr("data-serviceid","");
    $("#"+modalId+" #btnAddServiceElement").attr("data-mstrackid","");
    if($(this).attr('data-ms-track')!=""){
        $("#"+modalId+" #btnAddServiceElement").attr("data-custtypeid",custTypeId);
        $("#"+modalId+" #btnAddServiceElement").attr("data-warehouseid",wareId);
        $("#"+modalId+" #btnAddServiceElement").attr("data-serviceid",servId);
        $("#"+modalId+" #btnAddServiceElement").attr("data-mstrackid",$(this).attr('data-ms-track'));
        $("#"+modalId+" select[name='warehouse[]']").val(wareId).trigger("change").attr("disabled",true);
        $("#"+modalId+" select[name='service[]']").val(servId).trigger("change");
        $("#"+modalId+" input[name='warehouseval[]']").val(wareId);
        $("#"+modalId+" input[name='serviceval[]']").val(servId);
    }

    $(".preloaderz").hide();

    $("#"+modalId+" .adjustFeeElement").hide();
    $("#"+modalId+" input[name='subTotal[]']").rules("add", "greaterThanZero");
    // $("#"+modalId+" input[name='tanggalInvoice']").rules("add","checkValidDate");
    // $("#"+modalId+" input[name='tanggalInvoice']").attr("data-id",$(this).attr('data-ms-track'));

    $("#"+modalId+"").modal("show");
});

$("#createInv").on("click",function(){
    // let orderStatusId = $(this).closest("tr").find("#btnStatusId").text();
    // if(orderStatusId=="HOLD"){
    //     $(".preloaderz").hide();
    //     return Swal.fire(
    //         'Status Order HOLD!',
    //         'Silahkan ganti status terlebih dahulu',
    //         'error'
    //       )
    // }
    let filterCustVal = $("input[name='filterCustomer']").val(),
        filterTanggalVal = $("input[name='filterTanggal']").val(),
        filterWareVal = $("input[name='filterWarehouse']").val();

    if(filterCustVal==""||filterWareVal==""||filterTanggalVal==""){
        return Swal.fire(
                    'Filter Belum Dipilih!',
                    'Silahkan Pilih Filter Dulu',
                    'error'
                )
    }

    if($("input[name='checkShipment']:checked").length==0){
        return Swal.fire(
                    'Belum Dichecklist!',
                    'Silahkan Checklist Data Dulu',
                    'error'
                )
    }

    $(".preloaderz").show();

    let modalId = "createInvoice",
        typeId = $(".btn-select").attr("id");
        mismassOrderId = $("input[name='checkShipment']").eq(0).attr("data-id"),
        custId = $("input[name='checkShipment']").eq(0).attr("data-custId"),
        custTypeId = $("input[name='checkShipment']").eq(0).attr("data-custTypeId"),
        firstName = $("input[name='checkShipment']").eq(0).attr("data-firstName"),
        middleName = $("input[name='checkShipment']").eq(0).attr("data-middleName"),
        lastName = $("input[name='checkShipment']").eq(0).attr("data-lastName"),
        email = $("input[name='checkShipment']").eq(0).attr("data-email"),
        phone = $("input[name='checkShipment']").eq(0).attr("data-phone"),
        address = $("input[name='checkShipment']").eq(0).attr("data-address"),
        subDistrict = $("input[name='checkShipment']").eq(0).attr("data-subDistrict"),
        district = $("input[name='checkShipment']").eq(0).attr("data-district"),
        city = $("input[name='checkShipment']").eq(0).attr("data-city"),
        prov = $("input[name='checkShipment']").eq(0).attr("data-prov"),
        postalCode = $("input[name='checkShipment']").eq(0).attr("data-postalCode"),
        secondName = $("input[name='checkShipment']").eq(0).attr("data-secondName"),
        secondPhone = $("input[name='checkShipment']").eq(0).attr("data-secondPhone"),
        tanggal = $("input[name='checkShipment']").eq(0).attr("data-tanggal"),
        custTypeId = $(".btn-select").attr("id"),
        wareId = $("input[name='checkShipment']").eq(0).attr("data-warehouse"),
        servId = $("input[name='checkShipment']").eq(0).attr("data-service"),
        tableEl = "", listTable= "", inputHiddenEl= "";
        
    resetModalInvoice(modalId);

    //Create Null Value Element Of MS Track
    $("#"+modalId+" .row-inputpos").html("");
    inputHiddenEl = "<input type='hidden' name='trackId[]' value=''>";
    $("#"+modalId+" .row-inputPos").append(inputHiddenEl);

    //If MS Track Is Not Null
    $("#"+modalId+" .row-inputpos").html("");
    $("#"+modalId+" .row-mstrack").html("");

    let checkShipLength = $("input[name='checkShipment']").length,
        checkShipNum = 1;

    for(let i=0;i<checkShipLength;i++){

        if($("input[name='checkShipment']").eq(i).is(":checked")){

            if($("#table-ci td:nth-child(7)").eq(i).text()=="HOLD"){
                $(".preloaderz").hide();
                return Swal.fire(
                    'Status Order HOLD!',
                    'Silahkan ganti status terlebih dahulu',
                    'error'
                  )
            }

            let ckDataMSTrack = $("input[name='checkShipment']").eq(i).attr("data-ms-track"),
                ckDropDate = $("input[name='checkShipment']").eq(i).attr("data-drop-created-at"),
                ckShipDate = $("input[name='checkShipment']").eq(i).attr("data-ship-created-at"),
                ckTotalForeign = $("input[name='checkShipment']").eq(i).attr("data-total-foreign"),
                ckForeignTracks = $("input[name='checkShipment']").eq(i).attr("data-foreign-tracks"),
                ckNote = $("input[name='checkShipment']").eq(i).attr("data-note"),
                ckImages = $("input[name='checkShipment']").eq(i).attr("data-images"),
                ckBaseUrl = $("input[name='checkShipment']").eq(i).attr("data-baseurl"),
                ckNoteEl = "";
                
            if(ckDataMSTrack==""){
                $(".preloaderz").hide();
                return Swal.fire(
                    'Tidak Bisa Digabung!',
                    'Data Ini Tidak Memiliki Resi Tracking',
                    'error'
                  )
            }


            //Create MS Track Value
            inputHiddenEl = "<input type='hidden' class='cl"+ckDataMSTrack+"' name='trackId[]' value='"+ckDataMSTrack+"'>";
            $("#"+modalId+" .row-inputpos").append(inputHiddenEl);

            ckNoteEl = ckNote!="" ? "<div class='bg-catatan'>Catatan</div>" : "";

            //Create Table List For MS Track
            listTable += "<tr class='el"+ckDataMSTrack+"'>"+
                        "<td>"+checkShipNum+"</td>"+
                        "<td>"+
                        "<div class='fw-bold'>"+ckDataMSTrack+"</div>"+
                        "<div style='display:flex'>"+
                            "<div style='margin-right:5px'>"+ckTotalForeign+"</div>"+
                            "<a class='pointlink lookresiln' data-id='"+ckDataMSTrack+"' data-resi-ln='"+ckForeignTracks+"' data-catatan='"+ckNote+"' data-images='"+ckImages+"' data-baseurl='"+ckBaseUrl+"'><i class='fas fa-eye'></i></a>"+
                        "</div>"+
                        ckNoteEl+
                        "</td>"+
                        "<td><div class='fw-bold'>"+ckDropDate+"</div></td>"+
                        "<td><div class='fw-bold'>"+ckShipDate+"</div></td>"+
                        "<td><a style='color:red' class='pointlink outBtn' data-ms-track='"+ckDataMSTrack+"'><i class='fas fa-trash'></i></a></td>"+
                        "</tr>";

            checkShipNum++;
        }
    }
        
    tableEl = "<div style='overflow:auto'>"+
                "<table class='table table-striped'>"+
                "<thead style='position:sticky;top:0'>"+
                    "<tr>"+
                        "<th>#</th>"+
                        "<th>Resi Tracking</th>"+
                        "<th>Tgl Drop</th>"+
                        "<th>Tgl Shipment</th>"+
                        "<th>Hapus</th>"+
                    "</tr>"+
                "</thead>"+
                "<tbody>"+
                listTable+
                "</tbody>"+
            "</table>"+
            "</div>";
    $("#"+modalId+" .row-mstrack").append(tableEl);

    $("#"+modalId+" .alert-success").show();
    $("#"+modalId+" .searchElem").show();
    $("#"+modalId+" .modal-footer").css({
        justifyContent:"space-between"
    });
    if(custTypeId=="IND"){
        $("#"+modalId+" .alert-success").hide();
        $("#"+modalId+" .searchElem").hide();
        $("#"+modalId+" .modal-footer").css({
            justifyContent:"right"
        });
    }

    $("#"+modalId+" input[name='convertToSGD']").prop("checked",false);
    $(".row-convert").hide();

    $("#"+modalId+" .modal-title").text(typeId=="IND"?"Buat Invoice Individual":"Buat Invoice Corporate");
    $("#"+modalId+" input[name='foreignSymbol']").val("SGD");
    $("#"+modalId+" label[for='tanggal']").text(tanggal);
    $("#"+modalId+" label[for='customer']").text(firstName+" "+middleName+" "+lastName);
    $("#"+modalId+" label[for='alamat']").text(address+", "+subDistrict+", "+district+", "+city+", "+prov+", "+postalCode);
    $("#"+modalId+" label[for='telpon']").text(phone);
    $("#"+modalId+" input[name='mismassOrderId']").val(mismassOrderId);
    $("#"+modalId+" input[name='dbCustId']").val(custId);
    $("#"+modalId+" input[name='dbCustTypeId']").val(custTypeId);
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

    //Set Local Storage
    let subTotals = [],
        itemTotals = [],
        cbmTotals = [],
        diskonTotals = [],
        kgTotals = [];

    localStorage.setItem("subTotals",JSON.stringify(subTotals));
    localStorage.setItem("itemTotals",JSON.stringify(itemTotals));
    localStorage.setItem("kgTotals",JSON.stringify(kgTotals));
    localStorage.setItem("cbmTotals",JSON.stringify(cbmTotals));
    localStorage.setItem("diskonTotals",JSON.stringify(diskonTotals));
    
    createServiceElement(modalId,filters={});

    if($(this).attr('data-ms-track')!=""){
        $("#"+modalId+" #btnAddServiceElement").attr("data-custtypeid",custTypeId);
        $("#"+modalId+" #btnAddServiceElement").attr("data-warehouseid",wareId);
        $("#"+modalId+" #btnAddServiceElement").attr("data-serviceid",servId);
        $("#"+modalId+" #btnAddServiceElement").attr("data-mstrackid",$("input[name='checkShipment']").eq(0).attr("data-ms-track"));

        $("#"+modalId+" select[name='warehouse[]']").val(wareId).trigger("change").attr("disabled",true);
        $("#"+modalId+" select[name='service[]']").val(servId).trigger("change");
        // if(custTypeId!="COR"){
        //     $("#"+modalId+" select[name='service[]']").attr("disabled",true);
        // }
        $("#"+modalId+" input[name='warehouseval[]']").val(wareId);
        $("#"+modalId+" input[name='serviceval[]']").val(servId);
    }

    $(".preloaderz").hide();

    $("#"+modalId+" input[name='subTotal[]']").rules("add", "greaterThanZero");
    // $("#"+modalId+" input[name='tanggalInvoice']").rules("add","checkValidDate");
    // $("#"+modalId+" input[name='tanggalInvoice']").attr("data-id",$(this).attr('data-ms-track'));

    $("#"+modalId+"").modal("show");


});

//Look Resi LN & Notes
$(document).on('click','.lookresiln',function(){
    let id = $(this).attr("data-id"),
        resiln = $(this).attr("data-resi-ln"),
        resilnArr = resiln.split(", "),
        resilnElm = "",
        catatan = $(this).attr("data-catatan"),
        images = $(this).attr("data-images"),
        baseUrl = $(this).attr("data-baseurl"),
        // catatanElm = isValidUrl(catatan) ? "<a href='"+catatan+"' target='_blank'>"+catatan+"</a>" : catatan,
        modalId = "#resiLnModalSL";

    console.log(images);
        
    $(modalId+" .row-resiln .card-body").html("");
    // $(modalId+" .row-catatan .text").text("");
    $(modalId+" .row-catatan textarea").val("");

    for(let i = 0;i<resilnArr.length;i++){
        resilnElm += "<div class='row'>"+
                        "<div class='col' style='padding-top:6px'>"+
                            "<div style='display:flex;'>"+
                                "<div class='noUrutResiLN'>"+(i+1)+".</div>"+
                                "<div style='width: -webkit-fill-available'>"+
                                    "<input type='text' name='resiln' class='form-control' value='"+resilnArr[i]+"' readonly>"+
                                "</div>"+
                            "</div>"+
                        "</div>"+
                    "</div>";
    }


    $(modalId+" .row-resiln .card-body").append(resilnElm);
    // $(modalId+" .row-catatan .text").html(catatanElm);
    $(modalId+" .row-catatan textarea").val(catatan);
    $(modalId+" .row-catatan textarea").attr("readonly",true);
    $(modalId+" h5").text("Resi LN Shipment "+id);

    $(modalId+" .uploadImageNote").nextAll().remove();
    $(modalId+" .uploadImageNote").hide();
    if(images!=""){
        let imageArrays = images.split(", ");
        for(let a=(imageArrays.length-1);a>=0;a--){
            imageId = imageArrays[a].split(".");
            $(modalId+" .uploadImageNote").after("<div class='img-preview' data-edit-img-view='true' id='img-view-"+a+"'>"+
                                            "<a href='"+baseUrl+"/assets/photos/"+imageArrays[a]+"' target='_blank'><img src='"+baseUrl+"/assets/photos/"+imageArrays[a]+"' title='"+imageArrays[a]+"'></a>"+
                                        "</div>");
        }
    }

    $(modalId).modal("show");
});

$(document).on("click",".outBtn",function(){
    let id = $(this).attr("data-ms-track"),
        length = $(".row-mstrack table .outBtn").length;

    if(length==1){
        return false;
    }

    $("table .el"+id).remove();
    $(".cl"+id).remove();

    checkTablePosList();
});
function checkTablePosList(){
    let num=1;
        length = $(".row-mstrack table tbody tr").length;
    for(let i=1;i<=length;i++){
        $(".row-mstrack table tbody tr:nth-child("+i+") td:nth-child(1)").text(num);
        num++;
    }
}

$("table").on("click","#voidInvoiceBtn",function(){
    let invoiceId = mismassOrderId = $(this).attr("data-id");

    (async () => {

        const first = await Swal.fire({
            title: `Apakah Anda Yakin Akan Void<br>Invoice INV/AJV/${invoiceId}`,
            text: 'Pilih mode Void Invoice',
            input: 'text',
            inputPlaceholder: 'Tulis alasan Anda...',
            inputAttributes: { required: true },
            icon: 'question',
            showCancelButton: true,
            cancelButtonText: 'Cancel',
            confirmButtonText: 'Void Invoice',
            showDenyButton: true,
            denyButtonText: 'Void Invoice + Resi',
            allowOutsideClick: false,
            inputValidator: (value) => {
                if (!value) return 'Alasan wajib diisi!';
            },
            allowOutsideClick: false,
            preDeny: (value) => {
                const note = Swal.getInput().value;
                if (!note) {
                    Swal.showValidationMessage('Alasan wajib diisi!');
                    return false;
                }
                return note;
            },
            customClass: {
                actions: 'swal-custom-actions',
                cancelButton: 'swal-custom-cancel',
                confirmButton: 'swal-custom-confirm',
                denyButton: 'swal-custom-deny'
            }
        });
    
        if (first.isDismissed) return;
    
        let note = first.value;
        let mode, notif;
    
        if (first.isConfirmed) {
            mode  = "VI";
            notif = "Void Invoice";
        }
    
        if (first.isDenied) {
            mode  = "INR";
            notif = "Void Invoice + Tracking";
        }
    
        if (!checkConn()) return;
    
        const confirm = await confirmAction({
            title: "Apakah anda yakin?",
            html: `
                <p>Anda akan melakukan <b style='color:red'>${notif}</b>.</p>
                <div style='font-size:18px;margin-top:10px'>No. Invoice</div>
                <div style='font-size:25px;font-weight:bold'>INV/AJV/${invoiceId}</div>
            `
        });
    
        if (!confirm.isConfirmed) return;
    
        voidInvoice({
            note,
            mode,
            notif,
            invoiceId
        });
    
    })();
    
});

$("table").on("click","#editInvoiceBtn",function(){

    let modalId = "editInvoice",
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
                timeout=0;
                
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
    
                    createServiceElement(modalId,filters = {});
    
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
                    
                    // console.log($("#"+modalId+" #"+num+" input[name='senderFirstName[]']").val());
    
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
    
                    $("#"+modalId+" #"+num+" select[name='warehouse[]']").val(e['warehouse_id']);
                    onChangeWarehouseGetServiceList($("#"+modalId+" #"+num+" select[name='warehouse[]']"));
                    $("#"+modalId+" #"+num+" select[name='warehouse[]']").attr("disabled",false);
                    if(msTracks!=""){
                        $("#"+modalId+" #"+num+" select[name='warehouse[]']").attr("disabled",true);
                    }
    
                    $("#"+modalId+" #"+num+" select[name='service[]']").val(e['service_id']);
                    onChangeServiceGetUOMList($("#"+modalId+" #"+num+" select[name='service[]']"));
    
                    let satuanBeratVal = e['item']>0?"ITEM":(e['length']>0?"VOL":(e['cbm']>0?"CBM":"KG"));
                    $("#"+modalId+" #"+num+" select[name='satuanBerat']").val(satuanBeratVal);
                    onChangeSatuanBerat($("#"+modalId+" #"+num+" select[name='satuanBerat']"));
    
                    $("#"+modalId+" #"+num+" input[name='panjang[]']").val(e['length']);
                    $("#"+modalId+" #"+num+" input[name='lebar[]']").val(e['width']);
                    $("#"+modalId+" #"+num+" input[name='tinggi[]']").val(e['height']);
                    $("#"+modalId+" #"+num+" input[name='kg[]']").val(e['weight'].toFixed(2).replace(".",","));
                    $("#"+modalId+" #"+num+" input[name='cbm[]']").val(e['cbm'].toFixed(2).replace(".",","));
                    $("#"+modalId+" #"+num+" input[name='item[]']").val(e['item']);
                    $("#"+modalId+" #"+num+" input[name='actualKg[]']").val(e['actual_weight'].toFixed(2).replace(".",","));
    
                    // let pricePer = e['sub_total']-(e['packing_price']+e['import_permit_price']+e['document_price']+e['dr_medicine_price']+e['insurance_total']+e['fee_total']+e['tax_total']+e['extra_cost_price']);
                    $("#"+modalId+" #"+num+" input[name='pricePer[]']").val(masking(e['service_price_per'].toString()));
    
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
            
            // console.log($("#"+modalId+" .totalHargaBoard div .value").text());

            // if(dokuInvoiceId!=""){
            //     onChangePembayaran($("#"+modalId+" select[name='pembayaran']").val("DOKU"));
            // }else{
            //     onChangePembayaran($("#"+modalId+" select[name='pembayaran']").val("BANK"));
            // }
            // $("#"+modalId+" select[name='templateId']").val(templateId);
            // $("#"+modalId+" input[name='invoiceDoku']").val(dokuInvoiceId);
            // $("#"+modalId+" input[name='linkDoku']").val(dokuLink);
            // $("#"+modalId+" input[name='namaBank']").val(bankName);
            // $("#"+modalId+" input[name='namaRekening']").val(bankAccountName);
            // $("#"+modalId+" input[name='noRekening']").val(bankAccountId);

            // $("#"+modalId+" .row-convert").hide();
            // $("#"+modalId+" input[name='convertToSGD']").prop("checked",false);
            // $("#"+modalId+" input[name='foreignRateValue']").attr("required",false);
            // if(fcSymbol!=""){
            //     let finalRate = fcValue,
            //         totalHargaRp = totalBiaya,
            //         totalHargaConvert = totalHargaRp/finalRate;
            //         finalResult = totalHargaConvert.toFixed(2);
                    
            //     console.log("totalHarga : "+totalHargaRp);
            //     $("#"+modalId+" input[name='convertToSGD']").prop("checked",true);
            //     $("#"+modalId+" .row-convert").show();
            //     $("#"+modalId+" input[name='foreignRateValue']").val(masking(finalRate.toString())).attr("required",true);
            //     $("#"+modalId+" input[name='foreignSymbol']").val(fcSymbol);
            //     $("#"+modalId+" input[name='totalHargaRP']").val(masking(totalHargaRp.toString()));
            //     $("#"+modalId+" input[name='totalHargaConvert']").val(masking(finalResult.toString()));
            // }

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

            $("#"+modalId+" #btnAddServiceElement").attr("data-custtypeid","");
            $("#"+modalId+" #btnAddServiceElement").attr("data-warehouseid","");
            $("#"+modalId+" #btnAddServiceElement").attr("data-serviceid","");
            $("#"+modalId+" #btnAddServiceElement").attr("data-mstrackid","");
            if(msTracks!=""){
                $("#"+modalId+" #btnAddServiceElement").attr("data-custtypeid",custTypeId);
                $("#"+modalId+" #btnAddServiceElement").attr("data-warehouseid",wareId);
                $("#"+modalId+" #btnAddServiceElement").attr("data-serviceid",servId);
                $("#"+modalId+" #btnAddServiceElement").attr("data-mstrackid",msTracks);
            }

            $("#"+modalId+" input[name='pricePer[]']").rules("add", "greaterThanZero");
            $("#"+modalId+" input[name='subTotal[]']").rules("add", "greaterThanZero");
            // $("#"+modalId+" input[name='adjustFee']").rules("add", "greaterThanZero");

            $("#"+modalId).modal("show");

        },
    });
});

$("table").on("click","#pindahBtn",function(){
    let id = $(this).attr("data-id"),
        alertText = "Tentukan waktu pembayaran invoice dan tidak boleh di bawah tanggal track terbaru dan tidak boleh melebihi tanggal sekarang."+
                    "<hr>"+
                    "Tanggal Track Terbaru <strong>"+$(this).attr('data-last-track-date')+"</strong><br>"+
                    "Tanggal Sekarang <strong>"+$(this).attr("data-date-now")+"</strong>";

    $("#updateInvoice .alert-text").html("");
    $("#updateInvoice h5").text("Update Invoice "+id);
    $("#updateInvoice input[name='invoiceId']").val(id);
    $("#updateInvoice input[name='tanggalJamInvoice']").attr("data-id",id);
    $("#updateInvoice input[name='tanggalJamInvoice']").rules("add","checkValidDate");
    $("#updateInvoice input[name='tanggalJamInvoice']").rules("add","checkValidDateHourNow");
    $("#updateInvoice input[name='tanggalJamInvoice']").rules("add","checkValidHour");
    $("#updateInvoice .alert-text").append(alertText);
    $("#updateInvoice").modal("show");
});

$("#formUpdateInvoice").validate({
    errorClass: "error fail-alert is-invalid",
    messages: {
        "tanggalJamInvoice": "Tidak Boleh Kosong",
    },
    submitHandler: function(form) {
        let jam = $("#updateInvoice input[name='tanggalJamInvoice']").val().split(" ");
        if(jam[1]=="00:00"){
            notifUpdateFirst();
        }else{
            notifUpdateSecond();
        }
        
    }

});

$("#btnEditDetilPenerima").on("click",function(){
    let id = $(this).attr("data-id"),
        firstName = $(this).attr("data-firstName"),
        middleName = $(this).attr("data-middleName"),
        lastName = $(this).attr("data-lastName"),
        phone = $(this).attr("data-phone"),
        email = $(this).attr("data-email"),
        address = $(this).attr("data-address"),
        subDistrict = $(this).attr("data-subDistrict"),
        district = $(this).attr("data-district"),
        city = $(this).attr("data-city"),
        prov = $(this).attr("data-prov"),
        postalCode = $(this).attr("data-postalCode"),
        modalId = "editDetilPenerima",
        title = "Edit Data Penerima Individual";

    $(".preloaderz").show();

    $("#"+modalId+" input[name='firstName']").val(firstName);
    $("#"+modalId+" input[name='middleName']").val(middleName);
    $("#"+modalId+" input[name='lastName']").val(lastName);
    $("#"+modalId+" input[name='phone']").val(phone);
    $("#"+modalId+" input[name='phoneOld']").val(phone);
    $("#"+modalId+" input[name='email']").val(email);
    $("#"+modalId+" input[name='emailOld']").val(email);
    $("#"+modalId+" input[name='subDistrict']").val(subDistrict);
    $("#"+modalId+" input[name='district']").val(district);
    $("#"+modalId+" input[name='city']").val(city);
    $("#"+modalId+" input[name='address']").val(address);
    $("#"+modalId+" input[name='prov']").val(prov);
    $("#"+modalId+" input[name='postalCode']").val(postalCode);
    $("#"+modalId+" input[name='orderId']").val(id);
    $("#"+modalId+" .modal-title").text(title);

    $("#"+modalId).modal("show");

    $(".preloaderz").hide();
});

$(".copyHandle").on("click",function(){
    let modalId = $(this).closest(".modal").attr("id");
    copyText(modalId);
});

$("input[name='convertToSGD']").on("click",function(){
    let modalId = $(this).closest(".modal").attr("id");
    $("#"+modalId+" .row-convert").hide();
    $("#"+modalId+" input[name='foreignRateValue']").val(0);
    $("#"+modalId+" input[name='foreignRateValue']").attr("required",false);
    $("#"+modalId+" form").validate();
    $("#"+modalId+" input[name='foreignRateValue']").rules("remove", "greaterThanZero");
    if($(this).is(":checked")){
        $.ajax({
            type: "GET",
            url: location.origin+"/foreignrate",
            beforeSend:function(){
                $("#"+modalId+" .row-convert").before("<div id='loadload'>Loading...</div>");
            },
            success:function(msg){
                let finalRate = parseInt(JSON.parse(msg)),
                    totalHargaRp = parseInt($("#"+modalId+" .totalHargaBoard div .value").text().replace(/\./g, ""));

                $("#"+modalId+" #loadload").remove();
                $("#"+modalId+" .row-convert").show();
                $("#"+modalId+" input[name='foreignRateValue']").attr("required",true);
                $("#"+modalId+" input[name='foreignSymbol']").val("SGD");
                // $("#"+modalId+" input[name='foreignRateValue']").val(masking(finalRate.toString()));
                $("#"+modalId+" input[name='foreignRateValue']").val("10.000");
                let rate = $("#"+modalId+" input[name='foreignRateValue']").val().replace(/\./g, ""),
                    totalHargaConvert = totalHargaRp/parseInt(rate);
                    finalResult = totalHargaConvert.toFixed(2);
                $("#"+modalId+" input[name='totalHargaRP']").val(masking(totalHargaRp.toString()));
                $("#"+modalId+" input[name='totalHargaConvert']").val(masking(finalResult.toString()));
                $("#"+modalId+" form").validate();
                $("#"+modalId+" input[name='foreignRateValue']").rules("add", "greaterThanZero");
                Swal.fire({
                    title: "Perhatian!",
                    text: "Pastikan Cek Dulu Nilai Tukar SGD ke Rupiah",
                    icon: "info"
                  });
            },
        });
    }
});

///////////////////////////////////////////////////////////////////////////////////////////////////////

$('table').on('click','.resendBtn',function(){
    let id = $(this).attr('data-id');

    Swal.fire({
        title: "Apakah Anda Yakin Resend Link Invoice "+id+" ?",
        icon: 'question',
        showDenyButton: false,
        showCancelButton: true,
        showConfirmButton: true,
        confirmButtonText: `Yakin`,
        customClass: {
            cancelButton: 'order-1',
            confirmButton: 'order-2',
          },
    }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            $.ajax({
                type: "GET",
                url: location.origin+"/shiplist/resend",
                data: {id:id},
                success: function(msg) {
                    let json = JSON.parse(msg),
                        status = "error";
        
                    if(json.status==200){
                        status = "success";
                    }
        
                    Swal.fire(json.title, json.text, status);
                }
            });
        }
    });
});

$("#formBuatInvoice").on("submit", function (e) {
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
            html: `<p>Anda akan <b style='color:red'>membuat Invoice</b>.</p>
                   <div style='font-size:18px;margin-top:10px'>Total Biaya</div>
                   <div style='font-size:25px;font-weight:bold'>`+allSubTotal(form)+`</div>`
        });
    
        if (result.isConfirmed) {
            queue(form);
        }

        return false;
    }

});

$("#formEditInvoice").on("submit", function (e) {
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

function allSubTotal(form){
    let id = form.id,
        total = 0,
        count = $("#"+id+" input[name='subTotal[]']").length,
        adjustFee = $("#"+id+" input[name='adjustFee']").val().replace(/\./g, "");

    for(let i=0;i<count;i++){
        total+=parseInt($("#"+id+" #"+i+" input[name='subTotal[]']").val().replace(/\./g, ""));
    }

    total+=parseInt(adjustFee);

    return "Rp."+masking(total.toString());
}

$("#formEditDetilPenerima").validate({
    errorClass: "error fail-alert is-invalid",
    rules: {
        firstName: "required",
        // lastName: "required",
        email: {
            required:true,
            email:true,
            remote: {
                url: location.origin+"/check/checkphoneandemail",
                type: "GET",
                data: {
                    email: function() {
                        return $("#formEditDetilPenerima #email").val();
                    },
                    statusCust:function(){
                        return $("#formEditDetilPenerima #statusCust").val();
                    },
                    emailOld: function() {
                        return $("#formEditDetilPenerima #emailOld").val();
                    },
                } 
            },
        },
        phone: {
            required:true,
            remote: {
                url: location.origin+"/check/checkphoneandemail",
                type: "GET",
                data: {
                    phone: function() {
                        return $("#formEditDetilPenerima #phone").val();
                    },
                    statusCust:function(){
                        return $("#formEditDetilPenerima #statusCust").val();
                    },
                    phoneOld: function() {
                        return $("#formEditDetilPenerima #phoneOld").val();
                    },
                } 
            },
        },
        address: "required",
        // subDistrict: "required",
        district: "required",
        city: "required",
        prov: "required",
        postalCode: "required",
    },
    messages: {
        firstName: "Tidak Boleh Kosong",
        // lastName: "Tidak Boleh Kosong",
        email: {
            required:"Tidak Boleh Kosong",
            email:"Format Email Salah",
            remote:"Email Sudah Terdaftar"
        },
        phone: {
            required:"Tidak Boleh Kosong",
            remote:"Telpon Sudah Terdaftar"
        },
        address: "Tidak Boleh Kosong",
        // subDistrict: "Tidak Boleh Kosong",
        district: "Tidak Boleh Kosong",
        city: "Tidak Boleh Kosong",
        prov: "Tidak Boleh Kosong",
        postalCode: "Tidak Boleh Kosong",
    },
    submitHandler: function(form) {
        
        let modalId = "editDetilPenerima",
            modalId2 = "editInvoice";
            btn = "btnEditDetilPenerima",
            firstNameEdit = $("#"+modalId+" input[name='firstName']").val(),
            middleNameEdit = $("#"+modalId+" input[name='middleName']").val(),
            lastNameEdit = $("#"+modalId+" input[name='lastName']").val(),
            phoneEdit = $("#"+modalId+" input[name='phone']").val(),
            emailEdit = $("#"+modalId+" input[name='email']").val(),
            subDistrictEdit = $("#"+modalId+" input[name='subDistrict']").val(),
            districtEdit = $("#"+modalId+" input[name='district']").val(),
            cityEdit = $("#"+modalId+" input[name='city']").val(),
            addressEdit = $("#"+modalId+" input[name='address']").val(),
            provEdit = $("#"+modalId+" input[name='prov']").val(),
            postalCodeEdit = $("#"+modalId+" input[name='postalCode']").val();
            
        firstName = $("#"+btn).attr("data-firstName",firstNameEdit);
        middleName = $("#"+btn).attr("data-middleName",middleNameEdit);
        lastName = $("#"+btn).attr("data-lastName",lastNameEdit);
        phone = $("#"+btn).attr("data-phone",phoneEdit);
        email = $("#"+btn).attr("data-email",emailEdit);
        address = $("#"+btn).attr("data-address",addressEdit);
        subDistrict = $("#"+btn).attr("data-subDistrict",subDistrictEdit);
        district = $("#"+btn).attr("data-district",districtEdit);
        city = $("#"+btn).attr("data-city",cityEdit);
        prov = $("#"+btn).attr("data-prov",provEdit);
        postalCode = $("#"+btn).attr("data-postalCode",postalCodeEdit);
        
        $("#"+modalId2+" input[name='consFirstName[]']").val(firstNameEdit);
        $("#"+modalId2+" input[name='consMiddleName[]']").val(middleNameEdit);
        $("#"+modalId2+" input[name='consLastName[]']").val(lastNameEdit);
        $("#"+modalId2+" input[name='consPhone[]']").val(phoneEdit);
        $("#"+modalId2+" input[name='consEmail[]']").val(emailEdit);
        $("#"+modalId2+" input[name='consSubDistrict[]']").val(subDistrictEdit);
        $("#"+modalId2+" input[name='consDistrict[]']").val(districtEdit);
        $("#"+modalId2+" input[name='consCity[]']").val(cityEdit);
        $("#"+modalId2+" input[name='consAddress[]']").val(addressEdit);
        $("#"+modalId2+" input[name='consProv[]']").val(provEdit);
        $("#"+modalId2+" input[name='consPostalCode[]']").val(postalCodeEdit);
        
        let middleNameE = middleNameEdit==""?"":" "+middleNameEdit+" ",
            lastNameE = lastNameEdit==""?'':" "+lastNameEdit;
    
        $("#"+modalId2+" label[for='customer']").text(firstNameEdit+middleNameE+lastNameE);
        $("#"+modalId2+" label[for='alamat']").text(addressEdit+", "+subDistrictEdit+", "+districtEdit+", "+cityEdit+", "+provEdit+", "+postalCodeEdit);
        $("#"+modalId2+" label[for='telpon']").text(phoneEdit);
        
        Swal.fire("Berhasil", "Data Penerima Akan Tersimpan Jika Melakukan Submit & Blast", 'success');
        
        unLoading(form);

        // $.ajax({
        //     type: "GET",
        //     url: location.origin+"/shiplist/edit/detilcons",
        //     data: $(form).serialize(),
        //     beforeSend: function() {
        //         loading(form);
        //     },
        //     success: function(msg) {
        //         var json = JSON.parse(msg),
        //             modalId = "editDetilPenerima",
        //             modalId2 = "editInvoice";

        //         unLoading(form);

        //         if (json.status == "Berhasil") {

        //             Swal.fire(json.status, json.text, 'success');

        //             if(json.data!=""){
        //                 $("#"+modalId+" input[name='firstName']").val(json.data['firstName']);
        //                 $("#"+modalId+" input[name='middleName']").val(json.data['middleName']);
        //                 $("#"+modalId+" input[name='lastName']").val(json.data['lastName']);
        //                 $("#"+modalId+" input[name='phone']").val(json.data['phone']);
        //                 $("#"+modalId+" input[name='phoneOld']").val(json.data['phone']);
        //                 $("#"+modalId+" input[name='email']").val(json.data['email']);
        //                 $("#"+modalId+" input[name='emailOld']").val(json.data['email']);
        //                 $("#"+modalId+" input[name='subDistrict']").val(json.data['subDistrict']);
        //                 $("#"+modalId+" input[name='district']").val(json.data['district']);
        //                 $("#"+modalId+" input[name='city']").val(json.data['city']);
        //                 $("#"+modalId+" input[name='address']").val(json.data['address']);
        //                 $("#"+modalId+" input[name='prov']").val(json.data['prov']);
        //                 $("#"+modalId+" input[name='postalCode']").val(json.data['postalCode']);
                        
        //                 $("#"+modalId2+" input[name='consFirstName[]']").val(json.data['firstName']);
        //                 $("#"+modalId2+" input[name='consMiddleName[]']").val(json.data['middleName']);
        //                 $("#"+modalId2+" input[name='consLastName[]']").val(json.data['lastName']);
        //                 $("#"+modalId2+" input[name='consPhone[]']").val(json.data['phone']);
        //                 $("#"+modalId2+" input[name='consEmail[]']").val(json.data['email']);
        //                 $("#"+modalId2+" input[name='consSubDistrict[]']").val(json.data['subDistrict']);
        //                 $("#"+modalId2+" input[name='consDistrict[]']").val(json.data['district']);
        //                 $("#"+modalId2+" input[name='consCity[]']").val(json.data['city']);
        //                 $("#"+modalId2+" input[name='consAddress[]']").val(json.data['address']);
        //                 $("#"+modalId2+" input[name='consProv[]']").val(json.data['prov']);
        //                 $("#"+modalId2+" input[name='consPostalCode[]']").val(json.data['postalCode']);
    
        //                 let middleName = json.data['middleName']==null?'':json.data['middleName'],
        //                     lastName = json.data['lastName']==null?'':json.data['lastName'];
    
        //                 $("#"+modalId2+" label[for='customer']").text(json.data['firstName']+" "+middleName+" "+lastName);
        //                 $("#"+modalId2+" label[for='alamat']").text(json.data['address']+", "+json.data['subDistrict']+", "+json.data['district']+", "+json.data['city']+", "+json.data['prov']+", "+json.data['postalCode']);
        //                 $("#"+modalId2+" label[for='telpon']").text(json.data['phone']);
        //             }
                    
        //             let custTypeId = $('.btn-select').attr('id');
        //             refreshTable(tableCi,location.origin+"/shiplist/table/order/"+custTypeId+"?filterTanggal=","table-ci_info");
        //             refreshTable(tableCt,location.origin+"/shiplist/table/invoice/"+custTypeId+"?filterTanggal=&filterWarehouse=&filterService=","table-ct_info");
        //             refreshTable(tableSt,location.origin+"/shiplist/table/tracking/"+custTypeId+"?mismassOrderId=&filterTanggal=&filterWarehouse=&filterService=","table-status_info");

        //         } else {

        //             Swal.fire(json.status, json.text, 'error');

        //         }
        //     }

        // });

    }

});

///////////////////////////////////////////////////////////////////////////////////////////////////////

$(document).on("change","#pembayaran",function(){
    onChangePembayaran(this);
});
function onChangePembayaran(a){
    let val = $(a).val(),
        modalId = $(a).closest(".modal").attr("id");
    if(val=="DOKU"){
        // $("#"+modalId+" .col-doku,#"+modalId+" .row-detil-pembayaran").show();
        $("#"+modalId+" .col-bank").hide();
        // $("#"+modalId+" .col-doku input").attr("required",true);
        $("#"+modalId+" .col-bank input").attr("required",false);
    }else{
        // $("#"+modalId+" .col-doku").hide();
        $("#"+modalId+" .col-bank,#"+modalId+" .row-detil-pembayaran").show();
        // $("#"+modalId+" .col-doku input").attr("required",false);
        $("#"+modalId+" .col-bank input").attr("required",true);
    }
    $("#"+modalId+" .col-bank input").val('');
    $("#"+modalId+" .col-doku input").val('');
}

$(document).on("click","#btnAddServiceElement",function(event){
    let modalId = $(this).closest(".modal").attr("id"),
        wareId = $(this).attr("data-warehouseid"),
        servId = $(this).attr("data-serviceid"),
        msTrackId = $(this).attr("data-mstrackid"),
        custTypeId = $(this).attr("data-custtypeid"),
        filters = [];

    filters['warehouseId'] = wareId;
    filters['serviceId'] = servId;
    filters['msTrackId'] = msTrackId;
    filters['custTypeId'] = custTypeId;

    createServiceElement(modalId,filters);

    // if(msTrackId!=""){
    //     $("#"+modalId+" select[name='warehouse[]']").val(wareId).trigger("change").attr("disabled",true);
    //     $("#"+modalId+" select[name='service[]']").val(servId).trigger("change").attr("disabled",true);
    //     $("#"+modalId+" input[name='warehouseval[]']").val(wareId);
    //     $("#"+modalId+" input[name='serviceval[]']").val(servId);
    // }

    // event.stopPropagation();
});

$(document).on("click",".deleteService",function(){
    let modalId = $(this).closest(".modal").attr("id"),
        servicesLength = $("#"+modalId+" .services").length,
        serviceElementId = $(this).closest("#"+modalId+" .services").attr("id");        

    if(servicesLength>1){
        $("#"+serviceElementId).remove();
        servicesLength = $("#"+modalId+" .services").length;

        for(let i=0;i<=servicesLength-1;i++){
            $("#"+modalId+" .services").eq(i).attr("id",i);
            $("#"+modalId+" .services h5").eq(i).html("Services#"+(i+1));
            $("#"+modalId+" .services .insurancePriceItem").eq(i).attr("name","insurancePriceItem"+i);
            $("#"+modalId+" .services .insurancePercent").eq(i).attr("name","insurancePercent"+i);
            $("#"+modalId+" .services .insuranceTotal").eq(i).attr("name","insuranceTotal"+i);
            $("#"+modalId+" .services .feePriceItem").eq(i).attr("name","feePriceItem"+i);
            $("#"+modalId+" .services .feePercent").eq(i).attr("name","feePercent"+i);
            $("#"+modalId+" .services .feeTotal").eq(i).attr("name","feeTotal"+i);
            $("#"+modalId+" .services .taxPriceItem").eq(i).attr("name","taxPriceItem"+i);
            $("#"+modalId+" .services .taxPercent").eq(i).attr("name","taxPercent"+i);
            $("#"+modalId+" .services .taxTotal").eq(i).attr("name","taxTotal"+i);
            $("#"+modalId+" .services .extraCostPrice").eq(i).attr("name","extraCostPrice"+i);
            $("#"+modalId+" .services .extraCostDest").eq(i).attr("name","extraCostDest"+i);
            $("#"+modalId+" .services .extraCostVendorName").eq(i).attr("name","extraCostVendorName"+i);
            $("#"+modalId+" .services .extraCostShippingNum").eq(i).attr("name","extraCostShippingNum"+i);

            // $("#"+modalId+" .services .packingPrice").eq(i).attr("name","packingPrice"+i);
            $("#"+modalId+" .services .packing").eq(i).attr("name","packing"+i);
            $("#"+modalId+" .services .packingPer").eq(i).attr("name","packingPer"+i);
            $("#"+modalId+" .services .packingTotal").eq(i).attr("name","packingTotal"+i);
            $("#"+modalId+" .services .packingDesc").eq(i).attr("name","packingDesc"+i);

            // $("#"+modalId+" .services .importPrice").eq(i).attr("name","importPrice"+i);
            $("#"+modalId+" .services .import").eq(i).attr("name","import"+i);
            $("#"+modalId+" .services .importPer").eq(i).attr("name","importPer"+i);
            $("#"+modalId+" .services .importTotal").eq(i).attr("name","importTotal"+i);
            $("#"+modalId+" .services .importDesc").eq(i).attr("name","importDesc"+i);

            $("#"+modalId+" .services .export").eq(i).attr("name","export"+i);
            $("#"+modalId+" .services .exportPer").eq(i).attr("name","exportPer"+i);
            $("#"+modalId+" .services .exportTotal").eq(i).attr("name","exportTotal"+i);
            $("#"+modalId+" .services .exportDesc").eq(i).attr("name","exportDesc"+i);

            // $("#"+modalId+" .services .documentPrice").eq(i).attr("name","documentPrice"+i);
            $("#"+modalId+" .services .document").eq(i).attr("name","document"+i);
            $("#"+modalId+" .services .documentPer").eq(i).attr("name","documentPer"+i);
            $("#"+modalId+" .services .documentTotal").eq(i).attr("name","documentTotal"+i);
            $("#"+modalId+" .services .documentDesc").eq(i).attr("name","documentDesc"+i);

            // $("#"+modalId+" .services .medicinePrice").eq(i).attr("name","medicinePrice"+i);
            $("#"+modalId+" .services .medicine").eq(i).attr("name","medicine"+i);
            $("#"+modalId+" .services .medicinePer").eq(i).attr("name","medicinePer"+i);
            $("#"+modalId+" .services .medicineTotal").eq(i).attr("name","medicineTotal"+i);
            $("#"+modalId+" .services .medicineDesc").eq(i).attr("name","medicineDesc"+i);

            $("#"+modalId+" .services .pickUpWeight").eq(i).attr("name","pickUpWeight"+i);
            $("#"+modalId+" .services .pickUpCharge").eq(i).attr("name","pickUpCharge"+i);

            $("#"+modalId+" .services .pickUpFee").eq(i).attr("name","pickUpFee"+i);

            $("#"+modalId+" .services .additionalDesc").eq(i).attr("name","additionalDesc"+i);
            $("#"+modalId+" .services .additionalNominal").eq(i).attr("name","additionalNominal"+i);

            $("#"+modalId+" .services .discount").eq(i).attr("name","discount"+i);

            $("#"+modalId+" .services .hargaService").eq(i).attr("name","hargaService"+i);
            $("#"+modalId+" .services .hargaServiceAfter").eq(i).attr("name","hargaServiceAfter"+i);
        }

        let subTotalArr = JSON.parse(localStorage.getItem("subTotals")),
            itemArr = JSON.parse(localStorage.getItem("itemTotals")),
            diskonArr = JSON.parse(localStorage.getItem("diskonTotals")),
            cbmArr = JSON.parse(localStorage.getItem("cbmTotals")),
            kgArr = JSON.parse(localStorage.getItem("kgTotals"));
        subTotalArr.splice(serviceElementId,1);
        itemArr.splice(serviceElementId,1);
        kgArr.splice(serviceElementId,1);
        cbmArr.splice(serviceElementId,1);
        diskonArr.splice(serviceElementId,1);
        localStorage.setItem("subTotals",JSON.stringify(subTotalArr));
        localStorage.setItem("itemTotals",JSON.stringify(itemArr));
        localStorage.setItem("kgTotals",JSON.stringify(kgArr));
        localStorage.setItem("cbmTotals",JSON.stringify(cbmArr));
        localStorage.setItem("diskonTotals",JSON.stringify(diskonArr));

        allCalc(modalId);
    }    
});

$(document).on("click",".deleteAdditional",function(){
    let modalId = $(this).closest(".modal").attr("id"),
        additionalElement = $(this).closest("#"+modalId+" .row"),
        serviceElementId = $(this).closest("#"+modalId+" .services").attr("id"),
        addElementDataId = additionalElement.attr("data-id"),
        additionalElementLength = $("#"+modalId+" #"+serviceElementId+" .row-additional-element").length;

    if(additionalElementLength==1){
        $("#"+modalId+" #"+serviceElementId+" select[name='additionalService[]']").attr("required",true);
    }

    additionalElement.remove();
    $("#"+modalId+" #"+serviceElementId+" option[value='"+addElementDataId+"']").show();
    removeAdditionalResetData(serviceElementId,addElementDataId,modalId);
    partialCalc(modalId,serviceElementId);;
    ruleForAdditional(addElementDataId,serviceElementId,"remove",modalId);
});

$(document).on("change","select[name='warehouse[]']",function(){
    onChangeWarehouseGetServiceList(this);
});

function onChangeWarehouseGetServiceList(a){
    let id = $(a).val(),
        modalId = $(a).closest(".modal").attr("id"),
        serviceElementId = $(a).closest("#"+modalId+" .services").attr("id"),
        formGroup = $(a).closest("#"+modalId+" .form-group-warehouse");

        // formGroup.append("<input type='hidden' name='warehouseval[]' value='"+$(a).val()+"'>");
    $("#"+modalId+" #"+serviceElementId+" input[name='warehouseval[]']").val(id);

    $.ajax({
        type: "GET",
        url: location.origin+"/check/getservlist?inv=true",
        data: {
            warehouseId:id,
        },
        async:false,
        success: function(msg) {
            let json = JSON.parse(msg);
            $("#"+modalId+" #"+serviceElementId+" .col-service").show();
            $("#"+modalId+" #"+serviceElementId+" select[name='service[]']").html(json.data).attr("required",true);
            $("#"+modalId+" #"+serviceElementId+" select[name='satuanBerat']").val("");
            $("#"+modalId+" #"+serviceElementId+" .col-item").hide();
            $("#"+modalId+" #"+serviceElementId+" .col-volume").hide();
            $("#"+modalId+" #"+serviceElementId+" .col-kg").hide();
            $("#"+modalId+" #"+serviceElementId+" .col-cbm").hide();
            $("#"+modalId+" #"+serviceElementId+" .col-priceper").hide();
            $("#"+modalId+" #"+serviceElementId+" input[name='cbm[]']").val(0);
            $("#"+modalId+" #"+serviceElementId+" input[name='kg[]']").val(0);
            $("#"+modalId+" #"+serviceElementId+" input[name='panjang[]']").val(0);
            $("#"+modalId+" #"+serviceElementId+" input[name='lebar[]']").val(0);
            $("#"+modalId+" #"+serviceElementId+" input[name='tinggi[]']").val(0);
            $("#"+modalId+" #"+serviceElementId+" input[name='item[]']").val(0);
            $("#"+modalId+" #"+serviceElementId+" input[name='pricePer[]']").val(0);
            $("#"+modalId+" #"+serviceElementId+" input[name='subTotal[]']").val(0);
            // if(modalId=="createInvoice"){
            let subTotalArr = JSON.parse(localStorage.getItem("subTotals")),
                itemArr = JSON.parse(localStorage.getItem("itemTotals")),
                diskonArr = JSON.parse(localStorage.getItem("diskonTotals")),
                cbmArr = JSON.parse(localStorage.getItem("cbmTotals")),
                kgArr = JSON.parse(localStorage.getItem("kgTotals"));
            subTotalArr[serviceElementId] = 0;
            itemArr[serviceElementId] = 0;
            kgArr[serviceElementId] = 0;
            diskonArr[serviceElementId] = 0;
            cbmArr[serviceElementId] = 0;
            localStorage.setItem("subTotals",JSON.stringify(subTotalArr));
            localStorage.setItem("itemTotals",JSON.stringify(itemArr));
            localStorage.setItem("kgTotals",JSON.stringify(kgArr));
            localStorage.setItem("diskonTotals",JSON.stringify(diskonArr));
            localStorage.setItem("cbmTotals",JSON.stringify(cbmArr));

            allCalc(modalId);
            // }
        }
    });
}

$(document).on("change","select[name='service[]']",function(){
    onChangeServiceGetUOMList(this);
});

function onChangeServiceGetUOMList(a){
    let id = $(a).val(),
        modalId = $(a).closest(".modal").attr("id"),
        serviceElementId = $(a).closest("#"+modalId+" .services").attr("id"),
        formGroup = $(a).closest("#"+modalId+" .form-group-service");

        // formGroup.append("<input type='hidden' name='serviceval[]' value='"+$(a).val()+"'>");
    $("#"+modalId+" #"+serviceElementId+" input[name='serviceval[]']").val(id);

    $.ajax({
        type: "GET",
        url: location.origin+"/check/getservdata",
        data: {
            serviceId:id,
        },
        async:false,
        success: function(msg) {
            let json = JSON.parse(msg);
            $("#"+modalId+" #"+serviceElementId+" input[name='pricePerKg']").val(json.priceKg);
            $("#"+modalId+" #"+serviceElementId+" input[name='pricePerVol']").val(json.priceVol);
            $("#"+modalId+" #"+serviceElementId+" input[name='pricePerItem']").val(json.priceItem);
            $("#"+modalId+" #"+serviceElementId+" input[name='pricePerCbm']").val(json.priceCbm);

            $.ajax({
                type: "GET",
                url: location.origin+"/check/getuomlist",
                data: {
                    serviceId:id,
                },
                async:false,
                success: function(msg) {
                    let json = JSON.parse(msg);
                    $("#"+modalId+" #"+serviceElementId+" select[name='satuanBerat']").html(json.data).attr("required",true);
                    $("#"+modalId+" #"+serviceElementId+" select[name='satuanBerat']").val("");
                    $("#"+modalId+" #"+serviceElementId+" .col-item").hide();
                    $("#"+modalId+" #"+serviceElementId+" .col-volume").hide();
                    $("#"+modalId+" #"+serviceElementId+" .col-kg").hide();
                    $("#"+modalId+" #"+serviceElementId+" .col-cbm").hide();
                    $("#"+modalId+" #"+serviceElementId+" .col-priceper").hide();
                    $("#"+modalId+" #"+serviceElementId+" input[name='cbm[]']").val(0);
                    $("#"+modalId+" #"+serviceElementId+" input[name='kg[]']").val(0);
                    $("#"+modalId+" #"+serviceElementId+" input[name='panjang[]']").val(0);
                    $("#"+modalId+" #"+serviceElementId+" input[name='lebar[]']").val(0);
                    $("#"+modalId+" #"+serviceElementId+" input[name='tinggi[]']").val(0);
                    $("#"+modalId+" #"+serviceElementId+" input[name='item[]']").val(0);
                    $("#"+modalId+" #"+serviceElementId+" input[name='pricePer[]']").val(0);
                    $("#"+modalId+" #"+serviceElementId+" input[name='subTotal[]']").val(0);
                    // if(modalId=="createInvoice"){
                    let subTotalArr = JSON.parse(localStorage.getItem("subTotals")),
                        itemArr = JSON.parse(localStorage.getItem("itemTotals")),
                        diskonArr = JSON.parse(localStorage.getItem("diskonTotals")),
                        cbmArr = JSON.parse(localStorage.getItem("cbmTotals")),
                        kgArr = JSON.parse(localStorage.getItem("kgTotals"));
                    subTotalArr[serviceElementId] = 0;
                    itemArr[serviceElementId] = 0;
                    kgArr[serviceElementId] = 0;
                    diskonArr[serviceElementId] = 0;
                    cbmArr[serviceElementId] = 0;
                    localStorage.setItem("subTotals",JSON.stringify(subTotalArr));
                    localStorage.setItem("itemTotals",JSON.stringify(itemArr));
                    localStorage.setItem("kgTotals",JSON.stringify(kgArr));
                    localStorage.setItem("cbmTotals",JSON.stringify(cbmArr));
                    localStorage.setItem("diskonTotals",JSON.stringify(diskonArr));
                    
                    allCalc(modalId);
                    // }
                }
            });
        }
    });
}

$(document).on("change","select[name='satuanBerat']",function(){
    onChangeSatuanBerat(this);
});

function onChangeSatuanBerat(a){
    let val = $(a).val(),
        modalId = $(a).closest(".modal").attr("id"),
        formId = $(a).closest("#"+modalId+" form").attr("id"),
        serviceElementId = $(a).closest("#"+modalId+" .services").attr("id"),
        pricePerVal;
    
    $("#"+modalId+" .row-detil-biaya").show();
    
    if(val=="KG"){
        $("#"+modalId+" #"+serviceElementId+" .col-kg").show();
        $("#"+modalId+" #"+serviceElementId+" .col-volume,"+
          "#"+modalId+" #"+serviceElementId+" .col-item,"+
          "#"+modalId+" #"+serviceElementId+" .col-actual,"+
          "#"+modalId+" #"+serviceElementId+" .col-cbm").hide();
        $("#"+modalId+" #"+serviceElementId+" input[name='kg[]']").attr("required",true).attr("readonly",false);
        $("#"+modalId+" #"+serviceElementId+" input[name='panjang[]'],"+
          "#"+modalId+" #"+serviceElementId+" input[name='lebar[]'],"+
          "#"+modalId+" #"+serviceElementId+" input[name='tinggi[]'],"+
          "#"+modalId+" #"+serviceElementId+" input[name='item[]'],"+
          "#"+modalId+" #"+serviceElementId+" input[name='actualKg[]'],"+
          "#"+modalId+" #"+serviceElementId+" input[name='cbm[]']").attr("required",false);
        $("#"+modalId+" #"+serviceElementId+" label[for='pricePer']").html("Harga/Kg");
        $("#"+modalId+" #"+formId).validate();
        $("#"+modalId+" #"+serviceElementId+" input[name='kg[]']").rules("add", "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='cbm[]']").rules("remove", "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='panjang[]']").rules("remove", "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='lebar[]']").rules("remove", "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='tinggi[]']").rules("remove", "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='item[]']").rules("remove", "greaterThanZero");
        pricePerVal = $("#"+modalId+" #"+serviceElementId+" input[name='pricePerKg']").val();
    }else if(val=="VOL"){
        $("#"+modalId+" #"+serviceElementId+" .col-volume,#"+modalId+" #"+serviceElementId+" .col-kg,#"+modalId+" #"+serviceElementId+" .col-actual").show();
        $("#"+modalId+" #"+serviceElementId+" .col-item,#"+modalId+" #"+serviceElementId+" .col-cbm").hide();
        $("#"+modalId+" #"+serviceElementId+" input[name='panjang[]'],#"+modalId+" #"+serviceElementId+" input[name='lebar[]'],#"+modalId+" #"+serviceElementId+" input[name='tinggi[]'],#"+modalId+" #"+serviceElementId+" input[name='actualKg[]']").attr("required",true);
        $("#"+modalId+" #"+serviceElementId+" input[name='item[]'],#"+modalId+" #"+serviceElementId+" input[name='kg[]'],#"+modalId+" #"+serviceElementId+" input[name='cbm[]']").attr("required",false);
        $("#"+modalId+" #"+serviceElementId+" input[name='kg[]']").attr("readonly",true);
        $("#"+modalId+" #"+serviceElementId+" label[for='pricePer']").html("Harga/Vol");
        $("#"+modalId+" #"+formId).validate();
        $("#"+modalId+" #"+serviceElementId+" input[name='panjang[]']").rules("add", "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='lebar[]']").rules("add", "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='tinggi[]']").rules("add", "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='item[]']").rules("remove", "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='kg[]']").rules("remove", "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='cbm[]']").rules("remove", "greaterThanZero");
        pricePerVal = $("#"+modalId+" #"+serviceElementId+" input[name='pricePerVol']").val();
    }else if(val=="CBM"){
        $("#"+modalId+" #"+serviceElementId+" .col-cbm").show();
        $("#"+modalId+" #"+serviceElementId+" .col-volume,"+
          "#"+modalId+" #"+serviceElementId+" .col-item,"+
          "#"+modalId+" #"+serviceElementId+" .col-kg,"+
          "#"+modalId+" #"+serviceElementId+" .col-actual").hide();
        $("#"+modalId+" #"+serviceElementId+" input[name='cbm[]']").attr("required",true).attr("readonly",false);
        $("#"+modalId+" #"+serviceElementId+" input[name='panjang[]'],"+
          "#"+modalId+" #"+serviceElementId+" input[name='lebar[]'],"+
          "#"+modalId+" #"+serviceElementId+" input[name='tinggi[]'],"+
          "#"+modalId+" #"+serviceElementId+" input[name='kg[]'],"+
          "#"+modalId+" #"+serviceElementId+" input[name='item[]'],"+
          "#"+modalId+" #"+serviceElementId+" input[name='actualKg[]']").attr("required",false);
        $("#"+modalId+" #"+serviceElementId+" label[for='pricePer']").html("Harga/CBM");
        $("#"+modalId+" #"+formId).validate();
        $("#"+modalId+" #"+serviceElementId+" input[name='cbm[]']").rules("add", "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='kg[]']").rules("remove", "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='panjang[]']").rules("remove", "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='lebar[]']").rules("remove", "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='tinggi[]']").rules("remove", "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='item[]']").rules("remove", "greaterThanZero");
        pricePerVal = $("#"+modalId+" #"+serviceElementId+" input[name='pricePerCbm']").val();
    }else{
        $("#"+modalId+" #"+serviceElementId+" .col-item").show();
        $("#"+modalId+" #"+serviceElementId+" .col-volume,#"+modalId+" #"+serviceElementId+" .col-kg,#"+modalId+" #"+serviceElementId+" .col-cbm,#"+modalId+" #"+serviceElementId+" .col-actual").hide();
        $("#"+modalId+" #"+serviceElementId+" input[name='item[]']").attr("required",true);
        $("#"+modalId+" #"+serviceElementId+" input[name='panjang[]'],#"+modalId+" #"+serviceElementId+" input[name='lebar[]'],#"+modalId+" #"+serviceElementId+" input[name='tinggi[]'],#"+modalId+" #"+serviceElementId+" input[name='kg[]'],#"+modalId+" #"+serviceElementId+" input[name='actualKg[]'],#"+modalId+" #"+serviceElementId+" input[name='cbm[]']").attr("required",false);
        $("#"+modalId+" #"+serviceElementId+" label[for='pricePer']").html("Harga/Item");
        $("#"+modalId+" #"+formId).validate();
        $("#"+modalId+" #"+serviceElementId+" input[name='item[]']").rules("add", "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='panjang[]']").rules("remove", "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='lebar[]']").rules("remove", "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='tinggi[]']").rules("remove", "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='kg[]']").rules("remove", "greaterThanZero");
        $("#"+modalId+" #"+serviceElementId+" input[name='cbm[]']").rules("remove", "greaterThanZero");
        pricePerVal = $("#"+modalId+" #"+serviceElementId+" input[name='pricePerItem']").val();
    }

    $("#"+modalId+" #"+serviceElementId+" .col-priceper").show();
    $("#"+modalId+" #"+serviceElementId+" input[name='kg[]']").val(0);
    $("#"+modalId+" #"+serviceElementId+" input[name='panjang[]']").val(0);
    $("#"+modalId+" #"+serviceElementId+" input[name='lebar[]']").val(0);
    $("#"+modalId+" #"+serviceElementId+" input[name='tinggi[]']").val(0);
    $("#"+modalId+" #"+serviceElementId+" input[name='item[]']").val(0);
    $("#"+modalId+" #"+serviceElementId+" input[name='cbm[]']").val(0);
    $("#"+modalId+" #"+serviceElementId+" input[name='pricePer[]']").val(masking(pricePerVal).toString());
    let wareId = $("#"+modalId+" #"+serviceElementId+" input[name='warehouseval[]']").val();
    if(checkPricePerEditable(wareId)){
        $("#"+modalId+" #"+serviceElementId+" input[name='pricePer[]']").attr("required",true);
        $("#"+modalId+" #"+serviceElementId+" input[name='pricePer[]']").attr("readonly",false);
        $("#"+modalId+" input[name='pricePer[]']").rules("add", "greaterThanZero");
    }else{
        $("#"+modalId+" #"+serviceElementId+" input[name='pricePer[]']").attr("required",false);
        $("#"+modalId+" #"+serviceElementId+" input[name='pricePer[]']").attr("readonly",true);
        $("#"+modalId+" input[name='pricePer[]']").rules("remove", "greaterThanZero");
    }

    $("#"+modalId+" #"+serviceElementId+" input[name='subTotal[]']").val(0);

    // if(modalId=="createInvoice"){
    let subTotalArr = JSON.parse(localStorage.getItem("subTotals")),
        itemArr = JSON.parse(localStorage.getItem("itemTotals")),
        diskonArr = JSON.parse(localStorage.getItem("diskonTotals")),
        kgArr = JSON.parse(localStorage.getItem("kgTotals"));
    subTotalArr[serviceElementId] = 0;
    itemArr[serviceElementId] = 0;
    kgArr[serviceElementId] = 0;
    diskonArr[serviceElementId] = 0;
    localStorage.setItem("subTotals",JSON.stringify(subTotalArr));
    localStorage.setItem("itemTotals",JSON.stringify(itemArr));
    localStorage.setItem("kgTotals",JSON.stringify(kgArr));
    localStorage.setItem("diskonTotals",JSON.stringify(diskonArr));

    allCalc(modalId);
    // }
}

$(document).on("change","select[name='additionalService[]']",function(){
    onChangeAdditionalService(this);
});
function onChangeAdditionalService(a){
    let value = $(a).val(),
        modalId = $(a).closest(".modal").attr("id"),
        serviceElementId = $(a).closest(".services").attr("id"),
        choosedElement;

    if(value==="NON"){
        let additionalElementLength = $("#"+modalId+" .row-additional-element").length,
            additionalElement,
            addElementDataId;

        if(additionalElementLength>0){
            for(let i=0;i<additionalElementLength;i++){
                additionalElement = $("#"+modalId+" #"+serviceElementId+" .row-additional-element").eq(0);
                addElementDataId = additionalElement.attr("data-id");
                additionalElement.remove();
                $("#"+modalId+" #"+serviceElementId+" option[value='"+addElementDataId+"']").show();
                removeAdditionalResetData(serviceElementId,addElementDataId,modalId);
                partialCalc(modalId,serviceElementId);
                ruleForAdditional(addElementDataId,serviceElementId,"remove",modalId);
            }
            $("#"+modalId+" #"+serviceElementId+" select[name='additionalService[]']").attr("required",true);
        }

    }else{
        choosedElement = createAdditionalServiceElement(value,serviceElementId)
        $("#"+modalId+" #"+serviceElementId+" .row-additional").before(choosedElement);
        $("#"+modalId+" #"+serviceElementId+" option[value='"+value+"']").hide();
        $(a).val("");
        $("#"+modalId+" #"+serviceElementId+" select[name='additionalService[]']").attr("required",false);
        ruleForAdditional(value,serviceElementId,"add",modalId);
    }
}

$(document).on("keyup","input[name='pricePer[]']",keyUpFunc);
$(document).on("keyup","input[name='cbm[]']",keyUpFuncKC);
$(document).on("keyup","input[name='kg[]']",keyUpFuncKC);
$(document).on("keyup","input[name='actualKg[]']",keyUpFuncKC);
$(document).on("keyup","input[name='panjang[]']",keyUpFunc);
$(document).on("keyup","input[name='lebar[]']",keyUpFunc);
$(document).on("keyup","input[name='tinggi[]']",keyUpFunc);
$(document).on("keyup","input[name='item[]']",keyUpFunc);
$(document).on("keyup","input[name='discount[]']",keyUpFunc);
$(document).on("keyup","input[name='additionalNominal[]']",keyUpFunc);
$(document).on("keyup","input[name='additionalDesc[]']",function(){
    let val = $(this).val(),
        modalId = $(this).closest(".modal").attr("id"),
        serviceElementId = $(this).closest("#"+modalId+" .services").attr("id");
    $("#"+modalId+" input[name='additionalDesc"+serviceElementId+"']").val(val);
});
$(document).on("keyup","input[name='medicine[]']",keyUpFunc);
$(document).on("keyup","input[name='medicinePer[]']",keyUpFunc);
$(document).on("keyup","input[name='medicineDesc[]']",function(){
    let val = $(this).val(),
        modalId = $(this).closest(".modal").attr("id"),
        serviceElementId = $(this).closest("#"+modalId+" .services").attr("id");
    $("#"+modalId+" input[name='medicineDesc"+serviceElementId+"']").val(val);
});
$(document).on("keyup","input[name='document[]']",keyUpFunc);
$(document).on("keyup","input[name='documentPer[]']",keyUpFunc);
$(document).on("keyup","input[name='documentDesc[]']",function(){
    let val = $(this).val(),
        modalId = $(this).closest(".modal").attr("id"),
        serviceElementId = $(this).closest("#"+modalId+" .services").attr("id");
    $("#"+modalId+" input[name='documentDesc"+serviceElementId+"']").val(val);
});
$(document).on("keyup","input[name='import[]']",keyUpFunc);
$(document).on("keyup","input[name='importPer[]']",keyUpFunc);
$(document).on("keyup","input[name='importDesc[]']",function(){
    let val = $(this).val(),
        modalId = $(this).closest(".modal").attr("id"),
        serviceElementId = $(this).closest("#"+modalId+" .services").attr("id");
    $("#"+modalId+" input[name='importDesc"+serviceElementId+"']").val(val);
});
$(document).on("keyup","input[name='export[]']",keyUpFunc);
$(document).on("keyup","input[name='exportPer[]']",keyUpFunc);
$(document).on("keyup","input[name='exportDesc[]']",function(){
    let val = $(this).val(),
        modalId = $(this).closest(".modal").attr("id"),
        serviceElementId = $(this).closest("#"+modalId+" .services").attr("id");
    $("#"+modalId+" input[name='exportDesc"+serviceElementId+"']").val(val);
});
$(document).on("keyup","input[name='packing[]']",keyUpFunc);
$(document).on("keyup","input[name='packingPer[]']",keyUpFunc);
$(document).on("keyup","input[name='packingDesc[]']",function(){
    let val = $(this).val(),
        modalId = $(this).closest(".modal").attr("id"),
        serviceElementId = $(this).closest("#"+modalId+" .services").attr("id");
    $("#"+modalId+" input[name='packingDesc"+serviceElementId+"']").val(val);
});
$(document).on("keyup","input[name='insurancePriceItem[]']",keyUpFunc);
$(document).on("keyup","input[name='insurancePercent[]']",keyUpFunc);
$(document).on("keyup","input[name='feePriceItem[]']",keyUpFunc);
$(document).on("keyup","input[name='feePercent[]']",keyUpFunc);
$(document).on("keyup","input[name='taxPriceItem[]']",keyUpFunc);
$(document).on("keyup","input[name='taxPercent[]']",keyUpFunc);
$(document).on("keyup","input[name='extraCostPrice[]']",keyUpFunc);
$(document).on("keyup","input[name='extraCostDest[]']",function(){
    let val = $(this).val(),
        modalId = $(this).closest(".modal").attr("id"),
        serviceElementId = $(this).closest("#"+modalId+" .services").attr("id");
    $("#"+modalId+" input[name='extraCostDest"+serviceElementId+"']").val(val);
});
$(document).on("keyup","input[name='extraCostVendorName[]']",function(){
    let val = $(this).val(),
        modalId = $(this).closest(".modal").attr("id"),
        serviceElementId = $(this).closest("#"+modalId+" .services").attr("id");
    $("#"+modalId+" input[name='extraCostVendorName"+serviceElementId+"']").val(val);
});
$(document).on("keyup","input[name='extraCostShippingNum[]']",function(){
    let val = $(this).val(),
        modalId = $(this).closest(".modal").attr("id"),
        serviceElementId = $(this).closest("#"+modalId+" .services").attr("id");
    $("#"+modalId+" input[name='extraCostShippingNum"+serviceElementId+"']").val(val);
});
$(document).on("keyup","input[name='pickUpWeight[]']",keyUpFunc);
$(document).on("keyup","input[name='pickUpCharge[]']",keyUpFunc);
$(document).on("keyup","input[name='pickUpFee[]']",keyUpFunc);
$(document).on("keyup","input[name='foreignRateValue']",function(){
    $(this).mask(masK, {
        reverse: true
    });
    let modalId = $(this).closest(".modal").attr("id"),
        value = $(this).val().replace(/\./g, ""),
        totalHargaRp = $("#"+modalId+" input[name='totalHargaRP']").val().replace(/\./g, ""),
        totalHargaConvert = totalHargaRp/value;
        finalResult = totalHargaConvert.toFixed(2);
    $("#"+modalId+" input[name='totalHargaConvert']").val(masking(finalResult.toString()));
});
$(document).on("keyup","input[name='adjustFee']",keyUpFunc);

function notifUpdateFirst(){
    Swal.fire("Gagal", "Set Jamnya terlebih dulu!", 'error');
}

function notifUpdateSecond(event){
    Swal.fire({
        title: "Apakah Anda Yakin Akan Update Invoice Ini Ke Sukses? <br><br><div style='font-size:15px'>Pastikan cek dulu pembayaran customer di DOKU dan BANK apakah sudah benar LUNAS atau BELUM !</div>",
        icon: 'question',
        showDenyButton: false,
        showCancelButton: true,
        showConfirmButton: true,
        denyButtonText: `Yakin`,
        customClass: {
            cancelButton: 'order-1',
            denyButton: 'order-2',
          },
    }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            
            $.ajax({
                type: "GET",
                data:{
                    "id":$("#updateInvoice input[name='invoiceId']").val(),
                    "tanggalJamInvoice":$("#updateInvoice input[name='tanggalJamInvoice']").val()
                },
                url: location.origin+"/shiplist/pindah/status",
                success:function(msg){    
                    let json = JSON.parse(msg);                
                    if(json.status==200){
                        Swal.fire(json.header, json.text, 'success');
                        let custTypeId = $('.btn-select').attr('id');
                        refreshTable(tableCi,location.origin+"/shiplist/table/order/"+custTypeId+"?filterTanggal=","table-ci_info");
                        CheckFilterTable();
                        $("#updateInvoice input[name='invoiceId']").val("");
                        $("#updateInvoice input[name='tanggalJamInvoice']").val("");
                        $("#updateInvoice").modal("hide");
                        return true;
                    }
                    
                    Swal.fire(json.header, json.text, 'error');
                }
            });
        }
    })
}

$(".adjustFeeBtn").on("click",function(){
    $("#adjustFeeModal").modal("show");
});

$(document).on("click",".pickUpChargeBtn",function(){
    $("#pickUpChargeModal").modal("show");
});

$("select[name='adjustFeeChange']").on("change",function(){
    onChangeAdjustFee(this);
});
function onChangeAdjustFee(a){
    let val = $(a).val(),
        modalId = $(a).closest(".modal").attr("id");

    if(val=="ADA"){
        $("#"+modalId+" .adjustFeeElement").show();
        $("#"+modalId+" input[name='adjustFee']").attr("required",true);
        $("#"+modalId+" input[name='adjustFee']").rules("add", "greaterThanZero");
    }else{
        $("#"+modalId+" .adjustFeeElement").hide();
        $("#"+modalId+" input[name='adjustFee']").attr("required",false);
        $("#"+modalId+" input[name='adjustFee']").rules("remove", "greaterThanZero");
    }

    $("#"+modalId+" input[name='adjustFee']").val("0");
}