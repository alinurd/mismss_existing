$(document).on("shown.bs.tab","#navChooseCust .nav-item a", function () {
    let custTypeId = $(this).attr("id"),
        filterPayment = $("#filterPayment").val(),
        filterTanggal = $("#filterTanggal").val(),
        url = location.origin+"/void/table/"+custTypeId+"?filterPayment="+filterPayment+"&filterTanggal="+filterTanggal;

        table.ajax.url(url).load(function(json){
            $("#totalAllVoid").html("Total Void : <b>Rp."+masking(json.totalAll)+"</b>");
        });
});

$("#view").on("click",function(){
    let custTypeId = $("#navChooseCust .nav-item .active").attr("id"),
        filterPayment = $("#filterPayment").val(),
        filterTanggal = $("#filterTanggal").val(),
        url = location.origin+"/void/table/"+custTypeId+"?filterPayment="+filterPayment+"&filterTanggal="+filterTanggal;

        table.ajax.url(url).load(function(json){
            $("#totalAllVoid").html("Total Void : <b>Rp."+masking(json.totalAll)+"</b>");
        });
});

$("#reset").on("click",function(){
    let custTypeId = $("#navChooseCust .nav-item .active").attr("id"),
        url = location.origin+"/void/table/"+custTypeId+"?filterPayment=&filterTanggal=";

        $("#filterPayment").val(""),
        $("#filterTanggal").val("");

        table.ajax.url(url).load(function(json){
            $("#totalAllVoid").html("Total Void : <b>Rp."+masking(json.totalAll)+"</b>");
        });
});

var table = $('#table-void').DataTable({
    "paging": true,
    "searching": true,
    "processing": true,
    "serverSide": true,
    "processing": true,
    "serverSide": true,
    "order": [],
    "ajax": {
        "url": location.origin+"/void/table/IND?filterPayment=&filterTanggal=",
        "type": "GET",
        "dataSrc": function(json){
            $("#table-void").parent().css("overflow-x","auto");
            $("#totalAllVoid").html("Total Void : <b>Rp."+masking(json.totalAll)+"</b>");
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