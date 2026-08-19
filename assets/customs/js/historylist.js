//Active First Tab
$("#navTab li a").first().addClass("active");

//Active First Table Element
$("#nav-tabContent .tab-pane").first().addClass("show active");

$("#headerText").text("Main Record");

$(document).on("shown.bs.tab","#navTab .nav-item a", function () {
    loadFilterAndTable();
});

function loadFilterAndTable(){
    let navTab = $("#navTab .nav-item .active").attr('id');

    if(navTab=="main"){
        refreshTable(tableHistory,location.origin+"/history/table","table-history_info");
        $("#headerText").text("Main Record");
    }
    
    if(navTab=="jne"){
        refreshTable(tableWebhook,location.origin+"/webhook/table?id=jne","table-webhook_info");
        $("#headerText").text("JNE Log");
    }

    if(navTab=="sentral"){
        refreshTable(tableWebhook,location.origin+"/webhook/table?id=sentral","table-webhook_info");
        $("#headerText").text("SENTRAL Log");
    }
}

var tableHistory = $('#table-history').DataTable({
    "paging": true,
    "searching": true,
    // "responsive": true,
    "processing": true,
    "serverSide": true,
    "processing": true,
    "serverSide": true,
    "order": [],
    "ajax": {
        "url": location.origin+"/history/table",
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

var tableWebhook = $('#table-webhook').DataTable({
    "paging": true,
    "searching": true,
    // "responsive": true,
    "processing": true,
    "serverSide": true,
    "processing": true,
    "serverSide": true,
    "order": [],
    "ajax": {
        "url": location.origin+"/webhook/table?id=jne",
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