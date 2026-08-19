$("select[name='filterWarehouse']").select2();

$('.masking').mask('###.###.###.###.###', {
    reverse: true
});

$("select[name='filterCountry']").on("change",function(){
    $("select[name='filterRoute']").val("").trigger("change");
    $("select[name='filterCustType']").val("").trigger("change");
    $("select[name='filterWarehouse']").html("<option value=''>ALL WAREHOUSE</option>");
});

$("select[name='filterRoute']").on("change",function(){
    let countryId = $("select[name='filterCountry']").val(),
        routeId = $(this).val();

    if(countryId==""||routeId==""){
        $("select[name='filterWarehouse']").html("<option value=''>ALL WAREHOUSE</option>");
        return false;
    }

    $.ajax({
        type: "GET",
        url: location.origin+"/warehouse/get/list",
        data: {
            countryId : countryId,
            routeId : routeId,
            mode : "A"
        },
        success: function(msg) {
            $("select[name='filterWarehouse']").html("");
            $("select[name='filterWarehouse']").html(msg);
            $("select[name='filterCustType']").val("").trigger("change");
        }
    });
});

$("#view").on("click",function(){
    let filterTanggal = $("#filterTanggal").val(),
        filterWarehouse = $("#filterWarehouse").val(),
        filterCountry = $("#filterCountry").val(),
        filterRoute = $("#filterRoute").val();

    refreshTable(table,location.origin+"/servlist/table?filterTanggal="+filterTanggal+"&filterCountry="+filterCountry+"&filterRoute="+filterRoute+"&filterWarehouse="+filterWarehouse,"table_info");
});

$("#reset").on("click",function(){
    $("#filterTanggal").val("");
    $("#filterCountry").val("").trigger("change");
    $("#filterRoute").val("").trigger("change");
    $("#filterWarehouse").val("").trigger("change");
    $("#filterWarehouse").html("<option value=''>ALL WAREHOUSE</option>");

    refreshTable(table,location.origin+"/servlist/table?filterTanggal=&filterCountry=&filterRoute=&filterWarehouse=","table_info");
});

$('#tambahBtn').on('click', function() {
    $('#addService').modal('show');
    resetError($('#formTambahService')[0]);
});

$(document).on("change", ".country", function(){
    let modalId = "#"+$(this).closest(".modal").attr("id"),
        countryId = $(this).val(),
        selectedWareId = $(this).data("selected");

    $.ajax({
        type: "GET",
        url: location.origin+"/warehouse/get/list",
        data: {
            countryId : countryId,
            routeId : "",
            mode : "C"
        },
        success: function(msg) {
            let $warehouse = $(modalId+" .warehouse");
            $warehouse.html(msg);

            if(selectedWareId){
                $warehouse.val(selectedWareId);
            }
        }
    });
});

$('table').on('click','#editBtn',function(){
    let id = $(this).attr("data-id"),
        countryid = $(this).attr("data-countryid"),
        wareid = $(this).attr("data-wareid"),
        name = $(this).attr("data-name"),
        priceKg = $(this).attr("data-pricekg"),
        priceVol = $(this).attr("data-pricevol"),
        priceItem = $(this).attr("data-priceitem"),
        priceCbm = $(this).attr("data-pricecbm"),
        description = $(this).attr("data-description"),
        modal = "editService";

    $("#"+modal+" .modal-title").html("Edit Service "+name);    

    $("#"+modal+" #serviceID").val(id);

    let $country = $("#"+modal+" .country");
    $country.data("selected", wareid);
    $country.val(countryid).trigger("change");

    $("#"+modal+" #serviceName").val(name);
    $("#"+modal+" #serviceNameOld").val(name);
    $("#"+modal+" #priceKg").val(masking(priceKg.toString()));
    $("#"+modal+" #priceVol").val(masking(priceVol.toString()));
    $("#"+modal+" #priceItem").val(masking(priceItem.toString()));
    $("#"+modal+" #priceCbm").val(masking(priceCbm.toString()));
    $("#"+modal+" #description").val(description);

    $('#' + modal).modal('show');
});

$("#formTambahService").validate({
    errorClass: "error fail-alert is-invalid",
    rules: {
        warehouse: "required",
        serviceName: {
            required: true,
            minlength: 5,
            remote: {
                url: location.origin+"/servlist/namechecking",
                type: "GET",
                data: {
                    serviceName: function() {
                        return $("#formTambahService #serviceName").val();
                    },
                    serviceNameOld: function() {
                        return $("#formTambahService #serviceNameOld").val();
                    },
                    warehouse: function() {
                        return $("#formTambahService .warehouse").val();
                    },
                }
            },
        },
        priceKg: "required",
        priceVol: "required",
        priceItem: "required",
        priceCbm: "required",
    },
    messages: {
        warehouse: "Pilih Salah Satu",
        serviceName: {
            required: "Tidak Boleh Kosong",
            minlength: "Kurang Dari 5 Karakter",
            remote: "Nama sudah ada",
        },
        priceKg: "Tidak Boleh Kosong",
        priceVol: "Tidak Boleh Kosong",
        priceItem: "Tidak Boleh Kosong",
        priceCbm: "Tidak Boleh Kosong",
    },
    submitHandler: function(form) {

        $.ajax({
            type: "POST",
            url: location.origin+"/servlist/tambah",
            data: $(form).serialize(),
            beforeSend: function() {
                loading(form);
            },
            success: function(msg) {
                var json = JSON.parse(msg);

                unLoading(form);

                if (json.status == "Berhasil") {

                    //NOTIF SUKSES
                    Swal.fire(json.status, json.text, 'success');

                    refreshTable(table,location.origin+"/servlist/table","table_info");

                    //RELOAD PAGE
                    // pageReload(location.origin+"/servlist");

                } else {

                    //NOTIF GAGAL
                    Swal.fire(json.status, json.text, 'error');

                }

                $("body").css("padding-right", "0");
            }

        });

    }

});

$("#formEditService").validate({
    errorClass: "error fail-alert is-invalid",
    rules: {
        warehouse: "required",
        serviceName: {
            required: true,
            minlength: 5,
            remote: {
                url: location.origin+"/servlist/namechecking",
                type: "GET",
                data: {
                    serviceName: function() {
                        return $("#formEditService #serviceName").val();
                    },
                    serviceNameOld: function() {
                        return $("#formEditService #serviceNameOld").val();
                    },
                    warehouse: function() {
                        return $("#formEditService .warehouse").val();
                    },
                }
            },
        },
        priceKg: "required",
        priceVol: "required",
        priceItem: "required",
        priceCbm: "required",
    },
    messages: {
        warehouse: "Pilih Salah Satu",
        serviceName: {
            required: "Tidak Boleh Kosong",
            minlength: "Kurang Dari 5 Karakter",
            remote: "Nama sudah ada",
        },
        priceKg: "Tidak Boleh Kosong",
        priceVol: "Tidak Boleh Kosong",
        priceItem: "Tidak Boleh Kosong",
        priceCbm: "Tidak Boleh Kosong",
    },
    submitHandler: function(form) {

        $.ajax({
            type: "POST",
            url: location.origin+"/servlist/edit",
            data: $(form).serialize(),
            beforeSend: function() {
                loading(form);
            },
            success: function(msg) {
                var json = JSON.parse(msg);
                
                unLoading(form);

                if (json.status == "Berhasil") {

                    //NOTIF SUKSES
                    Swal.fire(json.status, json.text, 'success');

                    refreshTable(table,location.origin+"/servlist/table","table_info");

                    //RELOAD PAGE
                    // pageReload(location.origin+"/servlist");

                } else {

                    //NOTIF GAGAL
                    Swal.fire(json.status, json.text, 'error');

                }
            }

        });

    }

});

var table = $('#table').DataTable({
    "paging": true,
    "searching": true,
    // "responsive": true,
    "processing": true,
    "serverSide": true,
    "processing": true,
    "serverSide": true,
    "order": [],
    "ajax": {
        "url": location.origin+"/servlist/table?filterTanggal=&filterWarehouse=",
        "type": "GET",
        "dataSrc": function(json){
            $("table").parent().css("overflow-x","auto");
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