$("#formBackup #customer").select2();

$(".row-driver,.row-packer,.row-export,.row-warehouse,.row-customer,.row-paymentStatus,.row-reference,.row-filterCustomer,.row-corporate-type").hide();

$("#formBackup #tipeCustomer").on("change",function(){
    let type = $(this).val();

    $(".row-export").show();
    $(".row-warehouse,.row-customer").hide();
    $("#jenisExport").val();
    $("#jenisExport").attr("required",true);

    $(".row-corporate-type").hide();
    $("#corType").attr("required",false);
    if(type=="COR"){
        $(".row-corporate-type").show();
        $("#corType").attr("required",true);
    }


});

$("#formBackup #jenisExport").on("change",function(){
    let value = $(this).val();
    if(value=="BW"){
        $(".row-warehouse").show();
        $(".row-customer,.row-reference,.row-packer,.row-driver,.row-paymentStatus,.row-filterCustomer").hide();
        $("#warehouse").attr("required",true);
        $("#customer,#reference,#driver,#packer,#paymentStatus,#filterCustomer").attr("required",false);
        $("#warehouse,#customer,#driver,#packer,#reference,#filterCustomer").val("");
    }else if(value=="BR"){
        $(".row-reference,.row-paymentStatus").show();
        $(".row-packer,.row-customer,.row-warehouse,.row-packer,.row-driver,.row-filterCustomer").hide();
        $("#reference,#paymentStatus").attr("required",true);
        $("#customer,#warehouse,#packer,#driver,#filterCustomer").attr("required",false);
        $("#warehouse,#customer,#driver,#packer,#reference,#filterCustomer").val("");
    }else if(value=="BP"){
        $(".row-packer,.row-paymentStatus,.row-filterCustomer").show();
        $(".row-customer,.row-warehouse,.row-reference,.row-driver").hide();
        $("#paymentStatus,#filterCustomer,#packer").attr("required",true);
        $("#reference,#customer,#warehouse,#driver").attr("required",false);
        $("#warehouse,#customer,#driver,#packer,#reference,#filterCustomer").val("");
    }else if(value=="BD"){
        $(".row-driver").show();
        $(".row-customer,.row-warehouse,.row-reference,.row-packer,.row-paymentStatus,.row-filterCustomer").hide();
        $("#driver").attr("required",true);
        $("#reference,#customer,#warehouse,#paymentStatus,#filterCustomer,#packer").attr("required",false);
        $("#warehouse,#customer,#driver,#packer,#reference,#filterCustomer").val("");
    }else{
        let custTypeId = $("#tipeCustomer").val();
        $.ajax({
            type: "GET",
            url: location.origin+"/check/getcustlist",
            data: {
                custTypeId: custTypeId,
            },
            success: function(msg) {
                var json = JSON.parse(msg);
                $(".row-customer").show();
                $(".row-warehouse,.row-reference,.row-paymentStatus,.row-filterCustomer").hide();

                $("#customer").html(json.data);

                $("#warehouse,#customer,#reference").val("");
                
                $("#warehouse,#reference,#paymentStatus,#filterCustomer").attr("required",false);
                $("#customer").attr("required",true);
            }
        });
    }
});

start = moment().startOf('month');
end = moment();

updateDisplay(start,end);

$('#filterTanggal').daterangepicker({
    startDate: start,
    endDate: end,
    maxSpan: { days: 30 },
    locale: {
        format: 'DD MMM YYYY',
        applyLabel: 'Filter',
        cancelLabel: 'Reset',
        customRangeLabel: 'Rentang Tanggal'
    },
}, updateDisplay);

function updateDisplay(s,e) {
    $('#filterTanggal').val(s.format('DD MMM YYYY') + ' - ' + e.format('DD MMM YYYY'));
    $('#tanggalAwal').val(s.format('DD-MM-YYYY'));
    $('#tanggalAkhir').val(e.format('DD-MM-YYYY'));
}

$("input[name='filterTanggal']").on('cancel.daterangepicker', function(ev, picker) {
    const s = moment().startOf('month'),
          e = moment();
    $('#filterTanggal').data('daterangepicker').setStartDate(s);
    $('#filterTanggal').data('daterangepicker').setEndDate(e);
    updateDisplay(s,e);
});

// var start = moment().startOf('month'),
//     end = moment();

// function updateInputs(picker) {
//     $('#tanggalAwal').val(picker.startDate.format('DD-MM-YYYY'));
//     $('#tanggalAkhir').val(picker.endDate.format('DD-MM-YYYY'));
// }

// $('#tanggalAwal, #tanggalAkhir').daterangepicker({
//     startDate: start,
//     endDate: end,
//     autoUpdateInput: false,
//     locale: {
//     format: 'DD-MM-YYYY',
//     applyLabel: 'Apply',
//     cancelLabel: 'Cancel'
//     }
// }).on('apply.daterangepicker', function(ev, picker) {
//     const diffDays = picker.endDate.diff(picker.startDate, 'days');

//     // Batasi maksimal 31 hari
//     if (diffDays > 30) {
//         Swal.fire("Error!", "Rentang tanggal maksimal 31 hari!", 'error');
//         return; // tidak update input
//     }

//     updateInputs(picker);
// });
  

// $('form #tanggalAwal').daterangepicker({
//     drops:'up',
//     singleDatePicker: true,
//     showDropdowns: true,
//     autoApply:true,
//     locale: {
//         format: 'DD-MM-YYYY'
//     }
// });

// $('form #tanggalAkhir').daterangepicker({
//     drops:'up',
//     singleDatePicker: true,
//     showDropdowns: true,
//     autoApply:true,
//     locale: {
//         format: 'DD-MM-YYYY'
//     }
// }).on('apply.daterangepicker', function(ev, picker) {
//     console.log(picker);
//     // const diffDays = picker.endDate.diff(picker.startDate, 'days');

//     // // Batasi maksimal 31 hari
//     // if (diffDays > 31) {
//     //     alert('Rentang tanggal maksimal 31 hari!');
//     //     return; // tidak update input
//     // }

//     // $('form #tanggalAkhir').val(picker.endDate.format('DD-MM-YYYY'));
// });

// $('form #tanggalAwal').on('apply.daterangepicker', function() {
//     minDate = $('form #tanggalAwal').val();
//     $('form #tanggalAkhir').val(minDate);

//     // $('form #tanggalAkhir').daterangepicker({
//     //     drops:'up',
//     //     singleDatePicker: true,
//     //     showDropdowns: true,
//     //     autoApply:true,
//     //     minDate: minDate,
//     //     locale: {
//     //         format: 'DD-MM-YYYY'
//     //     }
//     // });
// });

// $('form #tanggalAwal').on('hide.daterangepicker', function() {
//     minDate = $('form #tanggalAwal').val();
//     $('form #tanggalAkhir').val(minDate);

//     // $('form #tanggalAkhir').daterangepicker({
//     //     drops:'up',
//     //     singleDatePicker: true,
//     //     showDropdowns: true,
//     //     autoApply:true,
//     //     minDate: minDate,
//     //     locale: {
//     //         format: 'DD-MM-YYYY'
//     //     }
//     // });
// });

$("#formBackup").validate({
    errorClass: "error fail-alert is-invalid",
    messages: {
        tipeCustomer: "Pilih Salah Satu",
        jenisExport: "Pilih Salah Satu",
        warehouse: "Pilih Salah Satu",
        corType: "Pilih Salah Satu",
        packer: "Pilih Salah Satu",
        driver: "Pilih Salah Satu",
        reference: "Pilih Salah Satu",
        paymentStatus: "Pilih Salah Satu",
        filterCustomer: "Pilih Salah Satu",
        customer: "Pilih Salah Satu"
    },
    submitHandler: function(form) {

        let tipeCustomer = $("#"+form.id+" #tipeCustomer").val(),
            jenisExport = $("#"+form.id+" #jenisExport").val(),
            idWarehouse = $("#"+form.id+" #warehouse").val(),
            corType = $("#"+form.id+" #corType").val(),
            idCustomer = $("#"+form.id+" #customer").val(),
            packer = $("#"+form.id+" #packer").val(),
            driver = $("#"+form.id+" #driver").val(),
            reference = $("#"+form.id+" #reference").val(),
            filterCustomer = $("#"+form.id+" #filterCustomer").val(),
            paymentStatus = $("#"+form.id+" #paymentStatus").val(),
            tanggalAwal = $("#"+form.id+" #tanggalAwal").val(),
            tanggalAkhir = $("#"+form.id+" #tanggalAkhir").val();

        if(jenisExport=="BW"){
            window.open(
                location.origin+'/backup/new/create/export?tipecustomer='+tipeCustomer+'&driver='+driver+'&packer='+packer+'&reference='+reference+'&paymentStatus='+paymentStatus+'&jenisexport='+jenisExport+'&idwarehouse='+idWarehouse+'&idcustomer='+idCustomer+'&filtercustomer='+filterCustomer+'&tanggalawal='+tanggalAwal+'&tanggalakhir='+tanggalAkhir+'&cortype='+corType,
                '_blank'
            );
        }else{
            window.open(
                location.origin+'/backup/export?tipecustomer='+tipeCustomer+'&driver='+driver+'&packer='+packer+'&reference='+reference+'&paymentStatus='+paymentStatus+'&jenisexport='+jenisExport+'&idwarehouse='+idWarehouse+'&idcustomer='+idCustomer+'&filtercustomer='+filterCustomer+'&tanggalawal='+tanggalAwal+'&tanggalakhir='+tanggalAkhir+'&cortype='+corType,
                '_blank'
            );  
        }  

        // $("#"+form.id).reset();
        $(".row-export,.row-warehouse,.row-corporate-type,.row-customer,.row-packer,.row-driver,.row-reference,.row-paymentStatus,.row-filterCustomer").hide();
        $("#tipeCustomer,#warehouse,#corType,#customer,#reference,#packer,#driver,#paymentStatus,#filterCustomer").val("");

    }

});

$("#formBackupPacker").validate({
    errorClass: "error fail-alert is-invalid",
    messages: {
        tipeCustomer: "Pilih Salah Satu",
    },
    submitHandler: function(form) {

        let tipeCustomer = $("#"+form.id+" #tipeCustomer").val(),
            tanggalAwal = $("#"+form.id+" #tanggalAwal").val(),
            tanggalAkhir = $("#"+form.id+" #tanggalAkhir").val();

        window.open(
            location.origin+'/backup/exportpacker?tipecustomer='+tipeCustomer+'&tanggalawal='+tanggalAwal+'&tanggalakhir='+tanggalAkhir,
            '_blank'
        );   

        $("#"+form.id+" #tipeCustomer").val("");
    }
});

$("#formBackupOps").validate({
    errorClass: "error fail-alert is-invalid",
    messages: {
        tipeCustomer: "Pilih Salah Satu",
    },
    submitHandler: function(form) {

        let tipeCustomer = $("#"+form.id+" #tipeCustomer").val(),
            tanggalAwal = $("#"+form.id+" #tanggalAwal").val(),
            tanggalAkhir = $("#"+form.id+" #tanggalAkhir").val();

        window.open(
            location.origin+'/backup/exportops?tipecustomer='+tipeCustomer+'&tanggalawal='+tanggalAwal+'&tanggalakhir='+tanggalAkhir,
            '_blank'
        );   

        $("#"+form.id+" #tipeCustomer").val("");
    }
});