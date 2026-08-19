timerEndpoint="";

// $('#filterWarehouse').select2({
//     width: "40%"
// });

$('#filterWarehouse').select2({
    width: "40%",
    templateResult: function (data) {
        if (!data.id) return data.text;

        // Pisahkan kata menjadi 2 bagian: "ini" + "biru"
        let text = data.text.split(" - ");

        let html = `
            <span>
                ${text[0]} -
                ${text[1]} -
                ${text[2]} -
                <span style="color: blue;">${text[3]}</span>
            </span>
        `;

        return $(html);
    },

    templateSelection: function (data) {
        if (!data.id) return data.text;

        let text = data.text.split(" - ");

        let html = `
            <span>
                ${text[0]} -
                ${text[1]} -
                ${text[2]} -
                <span style="color: blue;">${text[3]}</span>
            </span>
        `;

        return $(html);
    }
});

table= $('#table').DataTable({
        "paging": true,
        "searching": true,
        // "responsive": true,
        "processing": true,
        "serverSide": true,
        "processing": true,
        "serverSide": true,
        "order": [],
        "ajax": {
            "url": location.origin+"/shiptrip/table/whabroad/drop/IND?filterTanggal=&filterWarehouse=",
            "type": "GET",
            "dataSrc": function(json){
                $("#table").parent().css("overflow-x","auto");
                return json.data;
            }
        },
        "columnDefs": [{
            "targets": [],
            "orderable": true,
        }],
        "fixedHeader": false,
        "ordering": true,
        "info": true,
        "autoWidth": true,
        "lengthChange": true,
        "pageLength": pageLength,
        "language": {
            "info": dt_info,
            "infoEmpty": dt_info_empty,
            "infoFiltered": dt_info_filter,
            "search": dt_search_label,
            "searchPlaceholder": dt_search_placeholder,
            "zeroRecords": dt_zero_data,
            "thousands": dt_thousands,
            "processing": dt_processing,
        }
});
btnEl = "<div class='col-sm-12 col-md-3'><button id='updateShip' class='btn btn-primary' style='width:100%'>Update Shipment</button></div>";
tabEl = "<div class='col-sm-12 col-md-7 col-pa-order'>"+
                "<ul class='nav nav-pills' id='navType'>"+
                    "<li class='nav-item' data-id='0'>"+
                        "<a class='nav-link left-nav pointlink active' id='drop' data-toggle='tab' role='tab'>Drop List</a>"+
                    "</li>"+
                    "<li class='nav-item' data-id='0'>"+
                        "<a class='nav-link right-nav pointlink' id='missed' data-toggle='tab' role='tab'>Missed Packet</a>"+
                    "</li>"+
                    "<li class='nav-item' data-id='1'>"+
                        "<a class='nav-link left-nav pointlink' id='to' data-toggle='tab' role='tab'>Proses</a>"+
                    "</li>"+
                    "<li class='nav-item' data-id='1'>"+
                        "<a class='nav-link right-nav pointlink' id='in' data-toggle='tab' role='tab'>Arrive</a>"+
                    "</li>"+
                    "<li class='nav-item' data-id='11'>"+
                        "<a class='nav-link right-nav pointlink' id='pay' data-toggle='tab' role='tab'>Payment</a>"+
                    "</li>"+
                    "<li class='nav-item' data-id='2'>"+
                        "<a class='nav-link left-nav pointlink' id='vendor' data-toggle='tab' role='tab'>Vendor</a>"+
                    "</li>"+
                    "<li class='nav-item' data-id='2'>"+
                        "<a class='nav-link center-nav pointlink' id='courier' data-toggle='tab' role='tab'>Kurir</a>"+
                    "</li>"+
                    "<li class='nav-item' data-id='2'>"+
                        "<a class='nav-link center-nav pointlink' id='pickup' data-toggle='tab' role='tab'>Pickup</a>"+
                    "</li>"+
                    "<li class='nav-item' data-id='2'>"+
                        "<a class='nav-link right-nav pointlink' id='deliver' data-toggle='tab' role='tab'>Delivered</a>"+
                    "</li>"+
                "</ul>"+  
        "</div>";

$("#table_filter").parent(".col-md-7").parent(".row").css("justify-content","space-between");
$("#table_length").parent(".col-md-6").removeClass("col-md-6").addClass("col-md-2").css("display","flex").css("align-items","center").css("justify-content","flex-end");
$("#table_filter").parent(".col-md-6").removeClass("col-md-6").addClass("col-md-3").css("display","flex").css("align-items","center").css("justify-content","flex-end");
$(tabEl).insertBefore($("#table_length").parent(".col-md-2"));
// $(btnEl).insertAfter($("#table_filter").parent(".col-md-3"));

//Tanggal Jam Update Shipment
$('input[name="tanggalShipment"]').daterangepicker({
    singleDatePicker: true,
    autoApply:true,
    autoUpdateInput: false,
    forceParse: false,
    timePicker:true,
    timePicker24Hour: true,
    drops: 'up',
    locale: {
        format: 'DD/MM/YYYY HH:mm'
    },
});
$('input[name="tanggalShipment"]').on('cancel.daterangepicker', function (ev, picker) {
    $(this).val('');
});
$('input[name="tanggalShipment"]').on('apply.daterangepicker', function (ev, picker) {
    let startDate = picker.startDate;
    $(this).val(startDate.format('DD/MM/YYYY HH:mm'));
});

//Navigate Customer Type
$("#navChooseCust li a").on("click",function(){
    let navShip = $("#navChoose li .active").attr("id"),
        navType = $("#navType li .active").attr("id"),
        filterWarehouse = "",
        filterTanggal = $("input[name='filterTanggal']").val(),
        custTypeId = $(this).attr("id");

    refreshTable(table,location.origin+"/shiptrip/table/"+navShip+"/"+navType+"/"+custTypeId+"?filterTanggal="+filterTanggal+"&filterWarehouse="+filterWarehouse,"table_info");
});

//Navigate Choose Tab
$("#navType li").hide();
$("#navType li[data-id='0']").show();
$("#navChoose li a").on("click",function(){
    // $("#filterWarehouse").val("").trigger("change");

    clearTimeout(timerEndpoint);

    let id = $(this).attr("id"),
        filterWarehouse = "",
        filterTanggal = $("input[name='filterTanggal']").val(),
        custTypeId = $("#navChooseCust li .active").attr("id");

    $("input[name='filterTanggal']").remove();
    $(".btn-not-end").before("<input type='text' class='form-control w-auto' style='flex:1;' name='filterTanggal' placeholder='Filter Tanggal Drop' autocomplete='off' readonly>");
    $(".col-filter-not-end").show();
    $(".col-filter-end").hide();
    callFilterTanggal();


    $("#navType li").hide();
    $("#navType li a").removeClass("active");
    $("#in").text("Arrive");
    $("#navType li[data-id='11']").hide();
    $("a#in").removeClass("center-nav").addClass("right-nav");
    $("input[name='filterTanggal']").attr("placeholder","Tanggal Shipment");

    $("#table thead").html("");
    $("#table thead").html("<tr>"+
                            "<th>No.</th>"+
                            "<th>Resi Tracking</th>"+
                            "<th>Warehouse & Service</th>"+
                            "<th>User Update</th>"+
                            "<th>Shipper</th>"+
                            "<th>Consignee</th>"+
                            "<th>Resi LN</th>"+
                            "<th>Action</th>"+
                        "</tr>");

    timerEndpoint = setTimeout(() => {
        $("table tbody tr td:nth-child(9)").hide();
        console.log("executed");
    }, 5000);

    if(id=="whabroad"){
        $("#navType li[data-id='0']").show();
        $("#navType li #drop").addClass("active");
        $("input[name='filterTanggal']").attr("placeholder","Tanggal Drop");
    }else if(id=="end"){
        $("#navType li[data-id='2']").show();
        $("#navType li #vendor").addClass("active");

        $("input[name='filterTanggal']").remove();
        $(".col-filter-end .flex-wrap").prepend("<input type='text' class='form-control w-auto' style='flex:1;' name='filterTanggal' placeholder='Filter Tanggal Shipment' autocomplete='off' readonly>");
        $(".col-filter-not-end").hide();
        $(".col-filter-end").show();
        callFilterTanggal();

        $("#table thead").html("");
        $("#table thead").html("<tr>"+
                                "<th>No.</th>"+
                                "<th>Resi Tracking</th>"+
                                "<th>Resi Lokal</th>"+
                                "<th>Warehouse & Service</th>"+
                                "<th>User Update</th>"+
                                "<th>Consignee</th>"+
                                "<th>Status Tracking</th>"+
                                "<th>Action</th>"+
                            "</tr>");

        timerEndpoint = setTimeout(() => {
            $("table tbody tr td:nth-child(9)").hide();
            console.log("executed");
        }, 5000);

    }else{
        $("#navType li[data-id='1']").show();
        if(id=="jkt"){
            $("a#in").removeClass("right-nav").addClass("center-nav");
            $("#navType li[data-id='11']").show();
            $("#in").text("Consolidation");
        }
        $("#navType li #to").addClass("active");
    }

    navType = $("#navType li .active").attr("id")
    
    refreshTable(table,location.origin+"/shiptrip/table/"+id+"/"+navType+"/"+custTypeId+"?filterTanggal="+filterTanggal+"&filterWarehouse="+filterWarehouse,"table_info");
});

//Navigate Type
$("#navType li a").on("click",function(){

    // $("#filterWarehouse,"+
    //     "input[name='filterTanggal']").val("").trigger("change");

    let navShip = $("#navChoose li .active").attr("id"),
        navType = $(this).attr("id"),
        filterWarehouse = "",
        filterTanggal = $("input[name='filterTanggal']").val(),
        custTypeId = $("#navChooseCust li .active").attr("id");

    refreshTable(table,location.origin+"/shiptrip/table/"+navShip+"/"+navType+"/"+custTypeId+"?filterTanggal="+filterTanggal+"&filterWarehouse="+filterWarehouse,"table_info");    

    if(navShip=="end"){
        if(navType=="vendor"){

            $("#table thead").html("");
            $("#table thead").html("<tr>"+
                                "<th>No.</th>"+
                                "<th>Resi Tracking</th>"+
                                "<th>Resi Lokal</th>"+
                                "<th>Warehouse & Service</th>"+
                                "<th>User Update</th>"+
                                "<th>Consignee</th>"+
                                "<th>Status Tracking</th>"+
                                "<th>Action</th>"+
                            "</tr>");  
        
            timerEndpoint = setTimeout(() => {
                $("table tbody tr td:nth-child(9)").hide();
                console.log("executed");
            }, 5000);
            

        }else if(navType=="courier"){

            $("#table thead").html("");
            $("#table thead").html("<tr>"+
                                "<th>No.</th>"+
                                "<th>Resi Tracking</th>"+
                                "<th>Warehouse & Service</th>"+
                                "<th>User Update</th>"+
                                "<th>Consignee</th>"+
                                "<th>User Kurir</th>"+
                                "<th>Status Tracking</th>"+
                                "<th>Action</th>"+
                            "</tr>");
        
        }else if(navType=="pickup"){

            $("#table thead").html("");
            $("#table thead").html("<tr>"+
                            "<th>No.</th>"+
                            "<th>Resi Tracking</th>"+
                            "<th>Warehouse & Service</th>"+
                            "<th>User Update</th>"+
                            "<th>Consignee</th>"+
                            "<th>Status Tracking</th>"+
                            "<th>Action</th>"+
                            "<th style='display:none'></th>"+
                        "</tr>");

            timerEndpoint = setTimeout(() => {
                $("table tbody tr td:nth-child(8)").hide();
                $("table tbody tr td:nth-child(9)").hide();
                console.log("executed");
            }, 5000);
            
        }else{

            $("#table thead").html("");
            $("#table thead").html("<tr>"+
                            "<th>No.</th>"+
                            "<th>Resi Tracking</th>"+
                            "<th>Endpoint</th>"+
                            "<th>Warehouse & Service</th>"+
                            "<th>User Update</th>"+
                            "<th>Consignee</th>"+
                            "<th style='display:none'></th>"+
                            "<th style='display:none'></th>"+
                        "</tr>");

            timerEndpoint = setTimeout(() => {
                $("table tbody tr td:nth-child(7)").hide();
                $("table tbody tr td:nth-child(8)").hide();
                console.log("executed");
            }, 5000);

        }
    }else{

        $("#table thead").html("");
        if(navType!="pay"){

            $("#table thead").html("<tr>"+
                                    "<th>No.</th>"+
                                    "<th>Resi Tracking</th>"+
                                    "<th>Warehouse & Service</th>"+
                                    "<th>User Update</th>"+
                                    "<th>Shipper</th>"+
                                    "<th>Consignee</th>"+
                                    "<th>Resi LN</th>"+
                                    "<th>Action</th>"+
                                    "<th style='display:none'></th>"+
                                "</tr>");

            timerEndpoint = setTimeout(() => {
                $("table tbody tr td:nth-child(9)").hide();
                console.log("executed");
            }, 5000);

        }else{
            $("#table thead").html("<tr>"+
                                    "<th>No.</th>"+
                                    "<th>Resi Tracking</th>"+
                                    "<th>Warehouse & Service</th>"+
                                    "<th>User Update</th>"+
                                    "<th>Shipper</th>"+
                                    "<th>Consignee</th>"+
                                    "<th>Resi LN</th>"+
                                    "<th style='display:none'></th>"+
                                    "<th style='display:none'></th>"+
                                "</tr>");
            
            timerEndpoint = setTimeout(() => {
                $("table tbody tr td:nth-child(8)").hide();
                $("table tbody tr td:nth-child(9)").hide();
                console.log("executed");
            }, 5000);                    
        }
            

    }    

    if(navType=="pay"){
        timerEndpoint = setTimeout(() => {
            $("table tbody tr td:nth-child(8)").hide();
            console.log("executed");
        }, 1000);
    }
});

//View Filter
$(".filter").on("click",function(){
    let navShip = $("#navChoose li .active").attr("id"),
        navType = $("#navType li .active").attr("id"),
        filterWarehouse = $("#filterWarehouse").val(),
        filterTanggal = $("input[name='filterTanggal']").val(),
        custTypeId = $("#navChooseCust li .active").attr("id");

    $("#selectMode").attr("data-id",1);
    $("#selectMode").html("<i class='fas fa-check'></i> Select All");
    $("input[name='checkShipment']").prop('checked', false);

    refreshTable(table,location.origin+"/shiptrip/table/"+navShip+"/"+navType+"/"+custTypeId+"?filterTanggal="+filterTanggal+"&filterWarehouse="+filterWarehouse,"table_info");
});

//Reset Filter
$(".resetFilter").on("click",function(){
    $("#filterWarehouse,"+
    "input[name='filterTanggal']").val("").trigger("change");

    $("#selectMode").attr("data-id",1);
    $("#selectMode").html("<i class='fas fa-check'></i> Select All");
    $("input[name='checkShipment']").prop('checked', false);

    let navShip = $("#navChoose li .active").attr("id"),
        navType = $("#navType li .active").attr("id"),
        filterWarehouse = $("#filterWarehouse").val(),
        filterTanggal = $("input[name='filterTanggal']").val(),
        custTypeId = $("#navChooseCust li .active").attr("id");

    refreshTable(table,location.origin+"/shiptrip/table/"+navShip+"/"+navType+"/"+custTypeId+"?filterTanggal="+filterTanggal+"&filterWarehouse="+filterWarehouse,"table_info");
});

//Select All CheckShipment
$("#selectMode").on("click",function(){
    let id = $(this).attr("data-id");

    if(id==1){
        let filterWarehouse = $("#filterWarehouse").val(),
        filterTanggal = $("input[name='filterTanggal']").val();

        if(filterWarehouse==""||filterTanggal==""){
            Swal.fire("Filter Belum Dipilih", "Tentukan Filter Warehouse Dan Tanggal Terlebih dahulu", 'error');
            return false;
        }

        $(this).attr("data-id",0);
        $(this).html("<i class='fas fa-times'></i> Unselect All");
        $("input[name='checkShipment']").prop('checked', true);
    }else{
        $(this).attr("data-id",1);
        $(this).html("<i class='fas fa-check'></i> Select All");
        $("input[name='checkShipment']").prop('checked', false);
    }
    // console.log($(this).attr("data-id"));
});

//Look Resi LN & Notes
$("#table").on('click','.lookresiln',function(){
    let id = $(this).attr("data-id"),
        editable = $(this).attr("data-editable"),
        resiln = $(this).attr("data-resi-ln"),
        resilnArr = resiln.split(", "),
        resilnElm = "",
        catatan = $(this).attr("data-catatan"),
        images = $(this).attr("data-images"),
        baseUrl = $(this).attr("data-baseurl"),
        catatanElm = isValidUrl(catatan) ? "<a href='"+catatan+"' target='_blank'>"+catatan+"</a>" : catatan,
        modalId = "#resiLnModal";
        
    $(modalId+" .row-resiln .card-body").html("");
    $(modalId+" .row-catatan .text").text("");

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
    // $(modalId+" input[name='note']").val(catatan);
    $(modalId+" textarea[name='note']").val(catatan);
    $(modalId+" input[name='msTrackId']").val(id);
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

    $(modalId+" button[type='submit']").show();
    $(modalId+" textarea[name='note']").attr("readonly",false);
    if(editable==0){
        $(modalId+" button[type='submit']").hide();
        $(modalId+" textarea[name='note']").attr("readonly",true);
    }

    $(modalId).modal("show");
});

//Show Modal Edit Shipment
$("#table").on('click','#editBtn',function(){

    // Swal.fire("Under Maintenance", "Masih Dalam Pengerjaan", 'info');

    let tableEl = "",
        listTable = "",
        id = $(this).attr("data-ms-track"),
        primaryTrack = $(this).attr("data-primary-mstrack"),
        dropTime = $(this).attr("data-drop-created-at"),
        modalId = "#editShipmentModal",
        // inputHidden = "<input type='hidden' name='trackId'>",
        shipmentTime = $(this).attr("data-ship-created-at"),
        typeMsTrack = $(this).attr("data-type-mstrack"),
        latestTracking = $(this).attr("data-latest-tracking"),
        latestTrackingTime = $(this).attr("data-latest-tracking-time"),
        secondaryMsTracks = $(this).attr("data-secondary-mstracks"),
        custName = $(this).attr("data-name"),
        custPhone = $(this).attr("data-phone"),
        custAddress = $(this).attr("data-address"),
        secondName = $(this).attr("data-second-name"),
        secondPhone = $(this).attr("data-second-phone"),
        type = $("#navChooseCust .nav-item .active").attr("id"),
        wareId = $(this).attr("data-warehouse-id"),
        servId = $(this).attr("data-service-id"),
        foreignTracks = $(this).attr("data-foreign-tracks"),
        images = $(this).attr("data-images"),
        baseUrl = $(this).attr("data-baseurl"),
        note = $(this).attr("data-note"),
        role = $(this).attr("data-roleid"),
        roleFirstNum = role.toString()[0];

    //Reset Resi LN Element
    $(modalId+" .row-resi-ln:not(#00000)").hide();

    //Reset Table Pos
    $(modalId+" #tablePos").html("");
    $(modalId+" input[name='file[]']").val("");
    $(modalId+" .img-preview").remove();
    $(modalId+" #warehouse").removeAttr("disabled");
    $(modalId+" #service").removeAttr("disabled");
    $(modalId+" input[name='warehouse']").remove();
    $(modalId+" input[name='service']").remove();
    $(modalId+" .row-preview-resiLN .card-body").html("");
    $(modalId+" #warehouseview").val("");
    $(modalId+" #serviceview").val("");

    tableEl = "<table class='table table-striped'>"+
                "<thead style='position:sticky;top:0'>"+
                    "<tr>"+
                        "<th>Resi Primary</th>"+
                        "<th>Resi Secondary</th>"+
                        "<th>Tgl Drop</th>"+
                        "<th>Shipment</th>"+
                        "<th>Status Saat Ini</th>"+
                        "<th>Tgl Status</th>"+
                    "</tr>"+
                "</thead>"+
                "<tbody>"+
                    "<tr>"+
                    "<td>"+primaryTrack+"</td>"+
                    "<td>"+secondaryMsTracks+"</td>"+
                    "<td>"+dropTime+"</td>"+
                    "<td>"+shipmentTime+"</td>"+
                    "<td class='fw-bold text-success'>"+latestTracking+"</td>"+
                    "<td>"+latestTrackingTime+"</td>"+
                    "</tr>";
                "</tbody>"+
            "</table>";
    
    $(modalId+" #tablePos").append(tableEl);
    $(modalId+" input[name='msTrackId']").val(id);
    $(modalId+" h5").text("Edit Shipment "+id);

    $(".col-consignee").show();
    $(modalId+" .consName").text(custName);
    $(modalId+" .consPhone").text(custPhone);
    $(modalId+" .consAddress").text(custAddress);
    $(modalId+" .sendName").text(secondName);
    $(modalId+" .sendPhone").text(secondPhone);
    if(type=="COR"){
        $(modalId+" .sendName").text("");
        $(modalId+" .sendPhone").text("");
        $(".col-consignee").hide();
        $(modalId+" .sendName").text(custName);
        $(modalId+" .sendPhone").text(custPhone);
        $(modalId+" .sendAddress").text(custAddress);
    }

    $(modalId+" #catatan").val(note);
    $(modalId+" #warehouse").val(wareId).trigger("change");
    setTimeout(() => {
        $(modalId+" #service").val(servId);

        // console.log($("#navType .nav-item .active").attr("id"));
        if($("#navType .nav-item .active").attr("id")!="drop"&&$("#navType .nav-item .active").attr("id")!="missed"){
            console.log("triggered");
            $(modalId+" #warehouse").attr("disabled",true);
            $(modalId+" #warehouse").after("<input type='hidden' name='warehouse' value='"+wareId+"' >");
        }

        if(typeMsTrack=="SEC"){
            $(modalId+" #warehouse").attr("disabled",true);
            $(modalId+" #service").attr("disabled",true);
            $(modalId+" #warehouse").after("<input type='hidden' name='warehouse' value='"+wareId+"' >");
            $(modalId+" #service").after("<input type='hidden' name='service' value='"+servId+"' >");
        }

    }, 1000);

    setTimeout(() => {
        $(modalId+" #warehouseview").val($(modalId+" #warehouse option:selected").text());
        $(modalId+" #serviceview").val($(modalId+" #service option:selected").text());
    }, 1200);
    
    let foreignArrays = foreignTracks.split(", "),
        resilnElmView = "";
    for(let i=0;i<foreignArrays.length;i++){
        $(modalId+" .resiln").attr("data-edit-form",id);
        $(modalId+" .addResiLN").attr("data-edit-btn",id);
        // console.log("urut : "+i);
        //////////////////////////////////////////////////////////////
        // dataEditBtn = id;
        //////////////////////////////////////////////////////////////
        if(i==0){
            $(modalId+" #AKVNFLDOSJ").val(foreignArrays[i]);
        }else{
                // $(modalId+" .addResiLN").click();
                $(modalId+" .row-resi-ln").eq(i).attr("id",makeId(5));
                $(modalId+" input[name='resiln[]']").eq(i).attr("id",makeId(10));
                $(modalId+" input[name='resiln[]']").eq(i).attr("data-edit-form",id);
                $(modalId+" .addResiLN").eq(i).attr("id",makeId(10));
                $(modalId+" .addResiLN").eq(i).attr("data-edit-btn",id);
                $(modalId+" input[name='resiln[]']").eq(i).val(foreignArrays[i]);
                $(modalId+" .row-resi-ln").eq(i).show();
                // $(modalId+" .row-resi-ln").eq(i+1).remove();
            // }
        }

        resilnElmView += "<div class='row'>"+
                        "<div class='col' style='padding-top:6px'>"+
                            "<div style='display:flex;'>"+
                                "<div class='noUrutResiLN'>"+(i+1)+".</div>"+
                                "<div style='width: -webkit-fill-available'>"+
                                    "<input type='text' name='resilnview' class='form-control' value='"+foreignArrays[i]+"' readonly>"+
                                "</div>"+
                            "</div>"+
                        "</div>"+
                    "</div>";
    

    }
    // $(".row-resi-ln").eq(foreignArrays.length).remove();

    $(modalId+" .row-preview-resiLN .card-body").append(resilnElmView);

    let parent = "#editShipmentModal";
    updateNomorUrutResiLN(parent);

    $(modalId+" input[name='file[]']").attr("data-edit-img",true);

    if(images!=""){
        let imageArrays = images.split(", ");
        for(let a=(imageArrays.length-1);a>=0;a--){
            imageId = imageArrays[a].split(".");
            $(modalId+" .uploadImage").after("<div class='img-preview' data-edit-img-view='true' id='img-view-"+a+"'>"+
                                            "<input type='hidden' name='imageOld[]' value='"+imageId[0]+"' >"+
                                            "<a href='"+baseUrl+"/assets/photos/"+imageArrays[a]+"' target='_blank'><img src='"+baseUrl+"/assets/photos/"+imageArrays[a]+"' title='"+imageArrays[a]+"'></a>"+
                                            "<button type='button' class='close deleteFoto' data-status='imgold' data-name='"+imageArrays[a]+"'>"+
                                                "<span aria-hidden='true'>×</span>"+
                                            "</button>"+
                                        "</div>");
        }
    }


    $(".row-warehouse,.row-resiLN").show();
    $(".row-preview-warehouse,.row-preview-resiLN").hide();
    if(roleFirstNum=="5"){
        $(".row-warehouse,.row-resiLN").hide();
        $(".row-preview-warehouse,.row-preview-resiLN").show();
    }

    $(modalId).modal("show");
});

//Show Modal Update Shipment
$("#table").on("click","#updateBtn",function(){
    let nowStep = "", nextStep = "",
        listTable = "", inputHidden = "",
        tableEl = "", alertText = "",
        modalId = "#updateShipmentModal",
        type = $("#navChooseCust .nav-item .active").attr("id"),
        tabUpdate = $("#navChoose .nav-item .active").attr("id"),
        tabType = $("#navType .nav-item .active").attr("id"),
        cons = "<div class='fw-bold'>"+$(this).attr('data-name')+"</div><div>"+$(this).attr('data-phone')+"</div><div>"+$(this).attr('data-city')+", "+$(this).attr('data-prov')+", "+$(this).attr('data-postal-code')+"</div>", 
        sender = "<div class='fw-bold'>"+$(this).attr('data-second-name')+"</div><div>"+$(this).attr('data-second-phone')+"</div>";

    //Reset All
    $(modalId+" .col-track-status-manual,"+
    modalId+" .col-track-status-skip,"+
    modalId+" .col-set-tanggal,"+
    modalId+" .row-status").hide();

    $(modalId+" #modeStatus,"+
    modalId+" #trackStatusManual,"+
    modalId+" #trackStatusSkip,"+
    modalId+" #tanggalShipment").val("");

    $(modalId+" #trackStatusManual,"+
    modalId+" #trackStatusSkip,"+
    modalId+" #tanggalShipment").attr("required",false);

    $(modalId+" #statusNow,"+
    modalId+" #statusNext").text("");

    $(modalId+" #tablePos,"+
    modalId+" #inputPos").html("");

    $(modalId+" .col-mode-status").removeClass("col-md-4");
    $(modalId+" .col-mode-status").removeClass("col-md-6");
    ////////////
    
    //Hide Skip Option
    $(modalId+" #modeStatus option[value='SKIP']").hide();
    $(modalId+" #trackStatusSkip option[value='6']").show();
    if(tabUpdate=="sgn"&&tabType=="to"){
        $(modalId+" #modeStatus option[value='SKIP']").show();
    }else if(tabUpdate=="btm"&&tabType=="in"){
        $(modalId+" #modeStatus option[value='SKIP']").show();
        $(modalId+" #trackStatusSkip option[value='6']").hide();
    }
    /////////////

    if(type=="COR"){
        sender = "<div class='fw-bold'>"+$(this).attr('data-name')+"</div><div>"+$(this).attr('data-phone')+"</div><div>"+$(this).attr('data-city')+", "+$(this).attr('data-prov')+", "+$(this).attr('data-postal-code')+"</div>";
        cons = "-";
    }
    nowStep = $(this).attr("data-now-step");
    nextStep = $(this).attr("data-next-step");
    shipDate = $(this).attr("data-track-created-by")!=""?"<div>TRACK:"+$(this).attr("data-track-created-at")+"</div><div>SHIP:"+$(this).attr("data-ship-created-at")+"</div>":"";
    dropNShip = $(this).attr("data-next-step-id")==3 ? "<div>DROP:"+$(this).attr('data-drop-created-at')+"</div>" : shipDate+"<div>DROP:"+$(this).attr('data-drop-created-at')+"</div>";   
    inputHidden += "<input type='hidden' class='cl"+$(this).attr("data-ms-track")+"' name='trackId[]' value='"+$(this).attr("data-ms-track")+"'>";
    listTable += "<tr class='el"+$(this).attr('data-ms-track')+"'>"+
                "<td>1</td>"+
                "<td><div class='fw-bold'>"+$(this).attr('data-ms-track')+"</div>"+dropNShip+"</td>"+
                "<td><div class='fw-bold'>"+$(this).attr('data-warehouse')+"</div></td>"+
                "<td><div class='fw-bold'>"+$(this).attr('data-service')+"</div></td>"+
                "<td>"+sender+"</td>"+
                "<td>"+cons+"</td>"+
                "<td><div>"+$(this).attr('data-total-foreign')+"</div></td>"+
                "<td><a href='#' style='color:red' id='outBtn' data-ms-track='"+$(this).attr('data-ms-track')+"'><i class='fas fa-trash'></i></a></td>"+
                "</tr>";

                tableEl = "<table class='table table-striped'>"+
                "<thead style='position:sticky;top:0'>"+
                    "<tr>"+
                        "<th>#</th>"+
                        "<th>Resi Tracking</th>"+
                        "<th>Warehouse</th>"+
                        "<th>Service</th>"+
                        "<th>Shipper</th>"+
                        "<th>Consignee</th>"+
                        "<th>Resi LN</th>"+
                        "<th>Hapus</th>"+
                    "</tr>"+
                "</thead>"+
                "<tbody>"+
                listTable+
                "</tbody>"+
            "</table>";
    $(modalId+" #tablePos").append(tableEl);
    $(modalId+" #inputPos").append(inputHidden);

    $(modalId+" input[name='tanggalShipment']").attr("data-id",$(this).attr('data-ms-track'));

    $(modalId+" .alert-text").html("");
    alertText = "Tentukan tanggal dan jam tracking. Tidak boleh di bawah tanggal dan jam Tracking paling terbaru dan tidak boleh melebihi tanggal sekarang."+
                "<hr>"+
                "Tanggal Tracking Terbaru <strong>"+$(this).attr("data-track-created-at")+"</strong><br>"+
                "Jam Tracking Terbaru <strong>"+$(this).attr("data-track-created-hour-at")+"</strong><br>"+
                "Tanggal Sekarang <strong>"+$(this).attr("data-date-now")+"</strong>";
    if($(this).attr("data-next-step-id")==3){
        alertText = "Tentukan tanggal keberangkatan dan tidak boleh di bawah tanggal Drop paling terbaru dan tidak boleh melebihi tanggal sekarang."+
                    "<hr>"+
                    "Tanggal Drop Terbaru <strong>"+$(this).attr('data-drop-created-at')+"</strong><br>"+
                    "Tanggal Sekarang <strong>"+$(this).attr("data-date-now")+"</strong>";
    }
    $(modalId+" .alert-text").append(alertText);

    $(modalId+" #statusNow").text(nowStep);
    $(modalId+" #statusNext").text(nextStep);
    $(modalId+" #modeStatus").attr("data-auto-next-status",nextStep);
    $(modalId+" h5").text("Update Shipment "+$(this).attr("data-ms-track"));
    $(modalId).modal("show");
});

//Delete Shipment
$("table").on("click","#hapusShipmentBtn",function(){
    let trackId = $(this).attr("data-id");

    (async () => {

        const first = await Swal.fire({
            title: `Hapus Shipment ${trackId}`,
            html: `<div style='font-size:18px;margin-top:10px'>Anda Akan Menghapus Shipment <b style='color:red'>${trackId}</b> !</div>`,
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
    
        const confirm = await confirmAction({
            title: "Apakah Anda Yakin?",
            html: `<div style='font-size:20px;margin-top:10px'>Data Shipment <b style='color:red'>${trackId}</b> Akan Dihapus!</div>
                    <div style='font-size:20px;margin-top:5px'>Alasan : <b>${note}</b></div>`
        });
    
        if (!confirm.isConfirmed) return;
    
        deleteShipment({
            trackId,
            note
        });
    
    })();
    
});

//Update Shipment
$("#updateShip").on("click",function(){
    let total = 0, num = 1,
        nowStep = "", nextStep = "",
        listTable = "", inputHidden = "",
        tableEl = "",
        shipDateNow = "", shipDateHourNow = "", dropDate = "", shipHour = "",
        filterWarehouse = $("#filterWarehouse").val(),
        filterTanggal = $("input[name='filterTanggal']").val(),
        modalId = "#updateShipmentModal",
        sender = "",cons = "",
        type = $("#navChooseCust .nav-item .active").attr("id"),
        tabUpdate = $("#navChoose .nav-item .active").attr("id"),
        tabType = $("#navType .nav-item .active").attr("id");

    let firstShipDate = true,
        shipDateValue = "",
        shipId = "";

    let whAnchor = "", servAnchor = "",
        anchorReturn = true, anchorTitle = "", anchorText = "";

    //Reset All
    $(modalId+" .col-track-status-manual,"+
    modalId+" .col-track-status-skip,"+
    modalId+" .col-set-tanggal,"+
    modalId+" .row-status").hide();

    $(modalId+" #modeStatus,"+
    modalId+" #trackStatusSkip,"+
    modalId+" #trackStatusManual,"+
    modalId+" #tanggalShipment").val("");

    $(modalId+" #trackStatusManual,"+
    modalId+" #trackStatusSkip,"+
    modalId+" #tanggalShipment").attr("required",false);

    $(modalId+" #statusNow,"+
    modalId+" #statusNext").text("");

    $(modalId+" #tablePos,"+
    modalId+" #inputPos").html("");

    $(modalId+" .col-mode-status").removeClass("col-md-4");
    $(modalId+" .col-mode-status").removeClass("col-md-6");
    ////////////

    //Hide Skip Option
    $(modalId+" #modeStatus option[value='SKIP']").hide();
    $(modalId+" #trackStatusSkip option[value='6']").show();
    if(tabUpdate=="sgn"&&tabType=="to"){
        $(modalId+" #modeStatus option[value='SKIP']").show();
    }else if(tabUpdate=="btm"&&tabType=="in"){
        $(modalId+" #modeStatus option[value='SKIP']").show();
        $(modalId+" #trackStatusSkip option[value='6']").hide();
    }
    /////////////

    $("input[name='checkShipment']").each(function(){
        if($(this).is(":checked")){

            sender = "<div class='fw-bold'>"+$(this).attr('data-second-name')+"</div><div>"+$(this).attr('data-second-phone')+"</div>";
            cons = "<div class='fw-bold'>"+$(this).attr('data-name')+"</div><div>"+$(this).attr('data-phone')+"</div><div>"+$(this).attr('data-city')+", "+$(this).attr('data-prov')+", "+$(this).attr('data-postal-code')+"</div>";
            if(type=="COR"){
                sender = "<div class='fw-bold'>"+$(this).attr('data-name')+"</div><div>"+$(this).attr('data-phone')+"</div><div>"+$(this).attr('data-city')+", "+$(this).attr('data-prov')+", "+$(this).attr('data-postal-code')+"</div>";
                cons = "-";
            }

            nowStep = $(this).attr("data-now-step");
            nextStep = $(this).attr("data-next-step");
            shipDate = $(this).attr("data-track-created-by")!=""?"<div>TRACK:"+$(this).attr("data-track-created-at")+"</div><div>SHIP:"+$(this).attr("data-ship-created-at")+"</div>":"";
            dropNShip = $(this).attr("data-next-step-id")==3 ? "<div>DROP:"+$(this).attr('data-drop-created-at')+"</div>" : shipDate+"<div>DROP:"+$(this).attr('data-drop-created-at')+"</div>";   
            inputHidden += "<input type='hidden' class='cl"+$(this).attr("data-ms-track")+"' name='trackId[]' value='"+$(this).attr("data-ms-track")+"'>";
            listTable += "<tr class='el"+$(this).attr('data-ms-track')+"'>"+
                        "<td>"+num+"</td>"+
                        "<td><div class='fw-bold'>"+$(this).attr('data-ms-track')+"</div>"+dropNShip+"</td>"+
                        "<td><div class='fw-bold'>"+$(this).attr('data-warehouse')+"</div></td>"+
                        "<td><div class='fw-bold'>"+$(this).attr('data-service')+"</div></td>"+
                        "<td>"+sender+"</td>"+
                        "<td>"+cons+"</td>"+
                        "<td><div>"+$(this).attr('data-total-foreign')+"</div></td>"+
                        "<td><a href='#' style='color:red' id='outBtn' data-ms-track='"+$(this).attr('data-ms-track')+"'><i class='fas fa-trash'></i></a></td>"+
                        "</tr>";
            
            if(!firstShipDate){

                if(whAnchor!=$(this).attr('data-warehouse')){
                    anchorTitle = "Warehouse Tidak Sama";
                    anchorText = $(this).attr('data-ms-track')+" Memiliki Warehouse Yang Berbeda";
                    anchorReturn = false;
                    return false;
                }

                // if(servAnchor!=$(this).attr('data-service')){
                //     anchorTitle = "Service Tidak Sama";
                //     anchorText = $(this).attr('data-ms-track')+" Memiliki Service Yang Berbeda";
                //     anchorReturn = false;
                //     return false;
                // }

                if(Date.parse(shipDateValue)<Date.parse($(this).attr("data-track-created-at"))){
                    shipId = $(this).attr('data-ms-track');
                    dropDate = $(this).attr('data-drop-created-at');
                    shipHourValue = $(this).attr('data-track-created-hour-at');
                    shipDateValue = $(this).attr('data-track-created-at');
                    shipDateNow = $(this).attr("data-date-now");
                    shipDateHourNow = $(this).attr("data-date-hour-now");
                    nextId = $(this).attr("data-next-step-id");
                }
            }

            if(firstShipDate){
                whAnchor = $(this).attr('data-warehouse');
                servAnchor = $(this).attr('data-service');
                shipId = $(this).attr('data-ms-track');
                dropDate = $(this).attr('data-drop-created-at');
                shipHourValue = $(this).attr('data-track-created-hour-at');
                shipDateValue = $(this).attr('data-track-created-at');
                shipDateNow = $(this).attr("data-date-now");
                shipDateHourNow = $(this).attr("data-date-hour-now");
                firstShipDate = false;
                nextId = $(this).attr("data-next-step-id");
            }        

            total++;
            num++;
        }
    });

    if(!anchorReturn){
        Swal.fire(anchorTitle, anchorText, 'error');
        return false;
    }

    $(modalId+" input[name='tanggalShipment']").attr("data-id",shipId);

    //If there is NO selection yet
    if(total==0){
        Swal.fire("Shipment Belum Terseleksi", "Belum Ada Shipment Yang Diseleksi, Silahkan Pilih Warehouse Dan Filter Tanggal Terlebih Dahulu", 'error');
        return false;
    }

    if(filterWarehouse==""||filterTanggal==""){
        Swal.fire("Filter Belum Dipilih", "Tentukan Filter Warehouse Dan Tanggal Terlebih dahulu", 'error');
        return false;
    }

    //Create Table & Add Element
    tableEl = "<table class='table table-striped'>"+
                "<thead style='position:sticky;top:0'>"+
                    "<tr>"+
                        "<th>#</th>"+
                        "<th>Resi Tracking</th>"+
                        "<th>Warehouse</th>"+
                        "<th>Service</th>"+
                        "<th>Shipper</th>"+
                        "<th>Consignee</th>"+
                        "<th>Resi LN</th>"+
                        "<th>Hapus</th>"+
                    "</tr>"+
                "</thead>"+
                "<tbody>"+
                listTable+
                "</tbody>"+
            "</table>";
    $(modalId+" #tablePos").append(tableEl);
    $(modalId+" #inputPos").append(inputHidden);

    $(".alert-text").html("");
    alertText = "Tentukan tanggal dan jam tracking. Tidak boleh di bawah tanggal dan jam Tracking paling terbaru dan tidak boleh melebihi tanggal sekarang."+
                "<hr>"+
                "Tanggal Tracking Terbaru <strong>"+shipDateValue+"</strong><br>"+
                "Jam Tracking Terbaru <strong>"+shipHourValue+"</strong><br>"+
                "Tanggal Sekarang <strong>"+shipDateNow+"</strong>";
    if(nextId==3){
        alertText = "Tentukan tanggal keberangkatan dan tidak boleh di bawah tanggal Drop paling terbaru dan tidak boleh melebihi tanggal sekarang."+
                    "<hr>"+
                    "Tanggal Drop Terbaru <strong>"+dropDate+"</strong><br>"+
                    "Tanggal Sekarang <strong>"+shipDateNow+"</strong>";
    }
    $(modalId+" .alert-text").append(alertText);

    $(modalId+" #statusNow").text(nowStep);
    $(modalId+" #statusNext").text(nextStep);
    $(modalId+" #modeStatus").attr("data-auto-next-status",nextStep);
    $(modalId+" h5").text("Update Shipment");
    $(modalId).modal("show");
});

//Mode Status
$("#modeStatus").on("change",function(){
    let id = $(this).val(),
        autoNextStatus = $(this).attr("data-auto-next-status"),
        modalId = "#updateShipmentModal";

    //Reset All
    $(modalId+" .col-track-status-manual").hide();
    $(modalId+" .col-track-status-skip").hide();

    $(modalId+" #trackStatusManual,"+
    modalId+" #trackStatusSkip,"+
    modalId+" #tanggalShipment").val("");

    $(modalId+" #trackStatusManual,"+
    modalId+" #trackStatusSkip,"+
    modalId+" #tanggalShipment").attr("required",false);

    $(modalId+" #statusNext").text("");
    //////////

    if(id=="AUTO"){
        $(modalId+" .col-mode-status").removeClass("col-md-4");
        $(modalId+" .col-mode-status").addClass("col-md-6");
        $(modalId+" .col-set-tanggal").show();
        $(modalId+" .col-set-tanggal").removeClass("col-md-4");
        $(modalId+" .col-set-tanggal").addClass("col-md-6");
        $(modalId+" #tanggalShipment").attr("required",true);
        $(modalId+" #statusNext").text(autoNextStatus);
    }else if(id=="MANUAL"){
        $(modalId+" .col-mode-status").removeClass("col-md-6");
        $(modalId+" .col-mode-status").addClass("col-md-4");
        $(modalId+" .col-track-status-manual,"+
        modalId+" .col-set-tanggal").show();
        $(modalId+" .col-set-tanggal").removeClass("col-md-6");
        $(modalId+" .col-set-tanggal").addClass("col-md-4");
        $(modalId+" .col-track-status-manual").removeClass("col-md-6");
        $(modalId+" .col-track-status-manual").addClass("col-md-4");
        $(modalId+" #trackStatusManual,"+
        modalId+" #tanggalShipment").attr("required",true);
    }else{
        $(modalId+" .col-mode-status").removeClass("col-md-6");
        $(modalId+" .col-mode-status").addClass("col-md-4");
        $(modalId+" .col-track-status-skip,"+
        modalId+" .col-set-tanggal").show();
        $(modalId+" .col-set-tanggal").removeClass("col-md-6");
        $(modalId+" .col-set-tanggal").addClass("col-md-4");
        $(modalId+" .col-track-status-skip").removeClass("col-md-6");
        $(modalId+" .col-track-status-skip").addClass("col-md-4");
        $(modalId+" #trackStatusSkip,"+
        modalId+" #tanggalShipment").attr("required",true);
    }

    $(modalId+" .row-status").show();
});

//Track Status Manual
$("#trackStatusManual").on("change",function(){
    let value = $(this).val(),
        manualNextStatus = $("#trackStatusManual option[value='"+value+"']").attr("data-value"),
        modalId = "#updateShipmentModal";
    
    $(modalId+" #statusNext").text(manualNextStatus);
});

//Track Status Skip
$("#trackStatusSkip").on("change",function(){
    let value = $(this).val(),
        skipNextStatus = $("#trackStatusSkip option[value='"+value+"']").attr("data-value"),
        modalId = "#updateShipmentModal";
    
    $(modalId+" #statusNext").text(skipNextStatus);
});

//Out From update Shipment
$("#tablePos").on("click","#outBtn",function(){
    let id = $(this).attr("data-ms-track"),
        length = $("#formUpdateShipment #outBtn").length;

    if(length==1){
        return false;
    }

    $("table .el"+id).remove();
    $(".cl"+id).remove();

    checkTablePosList();
});
function checkTablePosList(){
    let num=1;
        length = $("#tablePos table tbody tr").length;
    for(let i=1;i<=length;i++){
        $("#tablePos table tbody tr:nth-child("+i+") td:nth-child(1)").text(num);
        num++;
    }
}

//Update Endpoint
$("#table").on("click","#endUpdateBtn",function(){
    let modalId = "#endUpdateModal",
        msTrackId = $(this).attr("data-ms-track-id"),
        trackStatusId = $(this).attr("data-track-status"),
        shippingNumber = $(this).attr("data-shipping-number"),
        custTypeId = $(this).attr("data-cust-type"),
        resi = custTypeId == "IND" ? (msTrackId != "" ? msTrackId : shippingNumber) : shippingNumber,
        navType = $("#navType li .active").attr("id"),
        option = "";
        
    $(".row-reason,.row-location,.row-receiver,.row-bukti-foto").hide();

    $(modalId+" #trackStatusId").html("");
    $(modalId+" .preview-bukti-foto").html("<img src='"+location.origin+"/assets/dist/pic/none.png' style='width:100%;aspect-ratio:1;object-fit:cover'/>");
    $(modalId+" #buktiFoto").val("");

    if(navType=="vendor"){
        option = "<option value='' hidden>Pilih Status Tracking</option>";
        if(trackStatusId<18||trackStatusId==""){
            option += "<option value='18'>Paket Dalam Pengiriman Ke</option>";
        }
        if(trackStatusId<19||trackStatusId==""){
            option += "<option value='19'>Paket Telah Tiba Di</option>";
        }
        if(trackStatusId<20||trackStatusId==""){
            option += "<option value='20'>PENDING</option>";
        }
        if(trackStatusId<21||trackStatusId==""){
            option += "<option value='21'>REJECT</option>";
        }
        if(trackStatusId<22||trackStatusId==""){
            option += "<option value='22'>SELESAI</option>";
        }
        $(modalId+" #trackStatusId").append(option);
    }else if(navType=="courier"){
        option = "<option value='' hidden>Pilih Status Tracking</option>"+
                    "<option value='18'>Paket Dalam Pengiriman Ke</option>"+
                    "<option value='19'>Paket Telah Tiba Di</option>"+
                    "<option value='20'>PENDING</option>"+
                    "<option value='21'>REJECT</option>"+
                    "<option value='22'>SELESAI</option>";
        $(modalId+" #trackStatusId").append(option);
    }else if(navType=="pickup"){
        option = "<option value='' hidden>Pilih Status Tracking</option>"+
                "<option value='13'>Paket Dipickup Sendiri Oleh Customer</option>";
        $(modalId+" #trackStatusId").append(option);
    }

    $(modalId+" input[name='shippingNumber']").val(shippingNumber);
    $(modalId+" input[name='msTrackId']").val(msTrackId);
    $(modalId+" input[name='custTypeId']").val(custTypeId);

    $(modalId+" h5").text("Update Status Resi "+resi);
    $(modalId).modal("show");
});

$("#endUpdateModal").on("change","#trackStatusId",function(){
    let value = $(this).val();

    if(value==22||value==13){
        $(".row-reason,.row-location").hide();
        $(".row-receiver,.row-bukti-foto").show();
        $("#reason,#location").attr("required",false);
        $("#receiver").attr("required",true);
    }else if(value==20||value==21){
        $(".row-location,.row-receiver,.row-bukti-foto").hide();
        $(".row-reason").show();
        $("#location,#receiver").attr("required",false);
        $("#reason").attr("required",true);
    }else if(value==18||value==19){
        $(".row-reason,.row-receiver,.row-bukti-foto").hide();
        $(".row-location").show();
        $("#reason,#receiver").attr("required",false);
        $("#location").attr("required",true);
    }
});

$("#buktiFoto").on("change", function(e){
    let divFile = document.getElementById('buktiFoto').files;

    imgURL = URL.createObjectURL(divFile[0]);
    $(".preview-bukti-foto").html("<img src='"+imgURL+"' title='"+e.target.files[0].name+"' style='width:100%;aspect-ratio:1;object-fit:cover'/>");
});

$("#formEditShipment").validate({
    errorClass: "error fail-alert is-invalid",
    messages: {
        warehouse: "Pilih Salah Satu",
        service: "Pilih Salah Satu",
        'resiln[]': "Tidak Boleh Kosong",
    },
    submitHandler: function(form, event) {
        event.preventDefault();
        var form_data = new FormData($(form)[0]);
        $.ajax({
            type: "POST",
            url: location.origin+"/shiptrip/edit/shipment",
            // data: $(form).serialize(),
            data: form_data,
            contentType: false,
            processData:false,
            beforeSend: function() {
                loading(form);
            },
            success: function(msg) {
                var json = JSON.parse(msg);

                unLoading(form);

                if (json.status == 200) {

                    Swal.fire(json.title, json.message, 'success');
                    refreshTable(table,location.origin+"/shiptrip/table/"+$("#navChoose li .active").attr("id")+"/"+$("#navType li .active").attr("id")+"/"+$("#navChooseCust li .active").attr("id"),"table_info");
                    // pageReload(location.origin+"/shiptrip");

                } else {

                    Swal.fire(json.title, json.message, 'error');

                }
            }
        });
    }
});

$("#formEditCatatan").validate({
    errorClass: "error fail-alert is-invalid",
    messages: {
        "note": "Tidak Boleh Kosong",
    },
    submitHandler: function(form) {
        $.ajax({
            type: "POST",
            url: location.origin+"/shiptrip/edit/note",
            data: $(form).serialize(),
            beforeSend: function() {
                loading(form);
            },
            success: function(msg) {
                var json = JSON.parse(msg);

                unLoading(form);
                $("#resiLnModal").modal("hide");
                $("#tanggalShipment").val("");

                //Reset Filter
                $("#selectMode").attr("data-id",1);
                $("#selectMode").html("<i class='fas fa-check'></i> Select All");
                $("input[name='checkShipment']").prop('checked', false);

                if(json.status!=200){
                    Swal.fire(json.title, json.msg, 'error');
                    return false;
                }

                Swal.fire(json.title, json.msg, 'success');
                refreshTable(table,location.origin+"/shiptrip/table/"+$("#navChoose li .active").attr("id")+"/"+$("#navType li .active").attr("id")+"/"+$("#navChooseCust li .active").attr("id"),"table_info");
            }
        });
    }
});

$("#formUpdateShipment").validate({
    errorClass: "error fail-alert is-invalid",
    messages: {
        "modeStatus": "Pilih Salah Satu",
        "trackStatusManual": "Pilih Salah Satu",
        "tanggalShipment": "Tidak Boleh Kosong",
    },
    submitHandler: function(form) {
        $.ajax({
            type: "POST",
            url: location.origin+"/shiptrip/update/shipment",
            data: $(form).serialize(),
            beforeSend: function() {
                loading(form);
            },
            success: function(msg) {
                var json = JSON.parse(msg);

                unLoading(form);
                $("#updateShipmentModal").modal("hide");
                $("#tanggalShipment").val("");

                //Reset Filter
                $("#selectMode").attr("data-id",1);
                $("#selectMode").html("<i class='fas fa-check'></i> Select All");
                $("input[name='checkShipment']").prop('checked', false);

                if(json.status!=200){
                    Swal.fire(json.title, json.text, 'error');
                    return false;
                }

                Swal.fire(json.title, json.text, 'success');
                refreshTable(table,location.origin+"/shiptrip/table/"+$("#navChoose li .active").attr("id")+"/"+$("#navType li .active").attr("id")+"/"+$("#navChooseCust li .active").attr("id"),"table_info");
            }
        });
    }
});

$("#formEndUpdate").validate({
    errorClass: "error fail-alert is-invalid",
    messages: {
        "trackStatusId": "Pilih Salah Satu",
        "location": "Tidak Boleh Kosong",
        "reason": "Tidak Boleh Kosong",
        "receiver": "Tidak Boleh Kosong",
    },
    submitHandler: function(form,event) {

        event.preventDefault();
        var form_data = new FormData($(form)[0]);

        $.ajax({
            type: "POST",
            url: location.origin+"/shiptrip/update/endpoint",
            // data: $(form).serialize(),
            data: form_data,
            contentType: false,
            processData:false,
            beforeSend: function() {
                loading(form);
            },
            success: function(msg) {
                var json = JSON.parse(msg);

                unLoading(form);
                $("#endUpdateModal").modal("hide");

                //Reset Filter
                // $("#selectMode").attr("data-id",1);
                // $("#selectMode").html("<i class='fas fa-check'></i> Select All");
                // $("input[name='checkShipment']").prop('checked', false);

                if(json.status!=200){
                    Swal.fire(json.title, json.text, 'error');
                    return false;
                }

                Swal.fire(json.title, json.text, 'success');
                refreshTable(table,location.origin+"/shiptrip/table/"+$("#navChoose li .active").attr("id")+"/"+$("#navType li .active").attr("id")+"/"+$("#navChooseCust li .active").attr("id"),"table_info");
            }
        });
    }
});

getWarehouseList();

$(document).on("shown.bs.tab", "#navChooseCust a, #navChoose a, #navType a", function () {
    getWarehouseList();
});

function getWarehouseList(){
    let navCust = $("#navChooseCust li .active").attr("id"),
        navTab = $("#navChoose li .active").attr("id"),
        navType = $("#navType li .active").attr("id");

        $("#filterWarehouse").val("").trigger("change");

        $.ajax({
            type: "GET",
            url: location.origin+"/shiptrip/warehouse/list",
            data: {
                navCust : navCust,
                navTab : navTab,
                navType : navType
            },
            beforeSend: function() {
                $("#filterWarehouse").html("");
                $("#filterWarehouse").html("<option value=''>Loading</option>");
            },
            success: function(msg) {
                var json = JSON.parse(msg);
                $("#filterWarehouse").html("");
                $("#filterWarehouse").html(json.data);
            }
        });
}

$("input[name='resiln[]']").rules("add", "checkForeignTrack");
$("input[name='tanggalShipment']").rules("add","checkValidHour");
$("input[name='tanggalShipment']").rules("add","checkValidDate");
$("input[name='tanggalShipment']").rules("add","checkValidDateHourNow");
