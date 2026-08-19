$("select[name='filterWarehouse'],select[name='filterRoute'],select[name='filterCountry'],select[name='filterCustType']").select2();

$("#tambahWarehouse select[name='country'],#tambahWarehouse select[name='route'],#editWarehouse select[name='country'],#editWarehouse select[name='route']").select2({
    width:'100%'
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
        filterRoute = $("#filterRoute").val(),
        filterCustType = $("#filterCustType").val();

    refreshTable(table,location.origin+"/warehouse/table?filterTanggal="+filterTanggal+"&filterCountry="+filterCountry+"&filterRoute="+filterRoute+"&filterWarehouse="+filterWarehouse+"&filterCustType="+filterCustType,"table_info"); 
});

$("#reset").on("click",function(){
    $("#filterTanggal").val("");
    $("#filterCountry").val("").trigger("change");
    $("#filterRoute").val("").trigger("change");
    $("#filterCustType").val("").trigger("change");
    $("select[name='filterWarehouse']").html("<option value=''>ALL WAREHOUSE</option>");

    refreshTable(table,location.origin+"/warehouse/table?filterTanggal=&filterWarehouse=&filterCountry=&filterRoute=&filterCustType=","table_info");

});

$('#tambahBtn').on('click', function() {
    $('#tambahWarehouse').modal('show');
    resetError($('#formTambahWarehouse')[0]);
});

$('table').on('click','#editBtn',function(){
    let id = $(this).attr("data-id"),
        name = $(this).attr("data-name"),
        location = $(this).attr("data-location"),
        country = $(this).attr("data-country"),
        route = $(this).attr("data-route"),
        modal = "editWarehouse";

    $("#editWarehouse .modal-title").html("Edit Warehouse "+id);    

    $("#editWarehouse #warehouseID").val(id).attr("readonly",true);
    $("#editWarehouse #warehouseIDOld").val(id);
    $("#editWarehouse #warehouseName").val(name);
    $("#editWarehouse #warehouseLoc").val(location);
    $("#editWarehouse #country").val(country).trigger("change");
    $("#editWarehouse #route").val(route).trigger("change");

    $('#' + modal).modal('show');
});

$("#formTambahWarehouse").validate({

    errorClass: "error fail-alert is-invalid",
    rules: {
        warehouseID: {
            required: true,
            minlength: 4,
            remote: {
                url: location.origin+"/warehouse/inputchecking",
                type: "GET",
                data: {
                    warehouseID: function() {
                        return $("#formTambahWarehouse #warehouseID").val();
                    },
                    warehouseIDOld: function() {
                        return $("#formTambahWarehouse #warehouseIDOld").val();
                    },
                }
            },
        },
        warehouseLoc: "required",
    },
    messages: {
        warehouseID: {
            required: "Tidak Boleh Kosong",
            minlength: "Kurang Dari 4 Karakter",
            remote: "ID sudah ada",
        },
        warehouseLoc: "Tidak Boleh Kosong",
        country: "Pilih Salah Satu",
        route: "Pilih Salah Satu"
    },
    errorPlacement: function(error, element) {
        if (element.hasClass("select2-hidden-accessible")) {
            error.insertAfter(element.next('.select2'));
        } else {
            error.insertAfter(element);
        }
    },
    highlight: function(element) {
        $(element).next('.select2').find('.select2-selection')
            .addClass('is-invalid');
    },
    unhighlight: function(element) {
        $(element).next('.select2').find('.select2-selection')
            .removeClass('is-invalid');
    },
    submitHandler: function(form) {

        $.ajax({
            type: "POST",
            url: location.origin+"/warehouse/tambah",
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

                    refreshTable(table,location.origin+"/warehouse/table?filterTanggal=&filterWarehouse=&filterCountry=&filterRoute=&filterCustType=","table_info");

                } else {

                    //NOTIF GAGAL
                    Swal.fire(json.status, json.text, 'error');

                }
            }

        });

    }

});

$("#formEditWarehouse").validate({
    errorClass: "error fail-alert is-invalid",
    rules: {
        warehouseID: {
            required: true,
            minlength: 4,
            remote: {
                url: location.origin+"/warehouse/inputchecking",
                type: "GET",
                data: {
                    warehouseID: function() {
                        return $("#formEditWarehouse #warehouseID").val();
                    },
                    warehouseIDOld: function() {
                        return $("#formEditWarehouse #warehouseIDOld").val();
                    },
                }
            },
        },
        warehouseLoc: "required",
    },
    messages: {
        warehouseID: {
            required: "Tidak Boleh Kosong",
            minlength: "Kurang Dari 4 Karakter",
            remote: "ID sudah ada",
        },
        warehouseLoc: "Tidak Boleh Kosong",
        country: "Pilih Salah Satu",
        route: "Pilih Salah Satu"
    },
    errorPlacement: function(error, element) {
        if (element.hasClass("select2-hidden-accessible")) {
            error.insertAfter(element.next('.select2'));
        } else {
            error.insertAfter(element);
        }
    },
    highlight: function(element) {
        $(element).next('.select2').find('.select2-selection')
            .addClass('is-invalid');
    },
    unhighlight: function(element) {
        $(element).next('.select2').find('.select2-selection')
            .removeClass('is-invalid');
    },
    submitHandler: function(form) {

        $.ajax({
            type: "POST",
            url: location.origin+"/warehouse/edit",
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

                    refreshTable(table,location.origin+"/warehouse/table?filterTanggal=&filterWarehouse=&filterCountry=&filterRoute=&filterCustType=","table_info");

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
        "url": location.origin+"/warehouse/table?filterTanggal=&filterWarehouse=&filterCountry=&filterRoute=",
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