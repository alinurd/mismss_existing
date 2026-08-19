$("#navChooseCust .nav-item .nav-link").on("click",function(){
    let id = $(this).attr("id"),
        custTypeId = id=="I"?"IND":"COR";

    refreshTable(tableDisc,location.origin+"/diskonlist/table/"+custTypeId+"?mismassOrderId=&customerId=&filterTanggal=","table_info");
    getCustomerListDisc(custTypeId);
    $('select[name="customerId"]').val("").trigger("change");
    // $('#filterTanggalAwal,#filterTanggalAkhir').val(moment().format('DD-MM-YYYY'));

    $.ajax({
        type: "GET",
        data:{
            "custTypeId":custTypeId,
            "customerId":null,
            "filterTanggal":""
        },
        url: location.origin+"/check/gettotaldisc",
        success: function(data) {
            let a = JSON.parse(data);
            $("h1").html("Total Diskon : <div class='font-weight-bold' style='display:inline'>Rp."+masking(a.data)+"</div>");
        }
    });
});

$("select[name='customerId']").select2();

$("#reset").on("click",function(){
    let id = $("#navChooseCust .nav-item .active").attr("id"),
        custTypeId = id=="I"?"IND":"COR";

    refreshTable(tableDisc,location.origin+"/diskonlist/table/"+custTypeId+"?mismassOrderId=&customerId=&filterTanggal=","table_info");

    $('select[name="customerId"]').val("").trigger("change");
    $('#filterTanggal').val("");

    $.ajax({
        type: "GET",
        data:{
            "custTypeId":custTypeId,
            "customerId":null,
            "filterTanggal":""
        },
        url: location.origin+"/check/gettotaldisc",
        success: function(data) {
            let a = JSON.parse(data);
            $("h1").html("Total Diskon : <div class='font-weight-bold' style='display:inline'>Rp."+masking(a.data)+"</div>");
        }
    });
});

$("#view").on("click",function(){
    let id = $("#navChooseCust .nav-item .active").attr("id"),
        custTypeId = id=="I"?"IND":"COR",
        customerId = $("select[name='customerId']").val(),
        filterTanggal = $("#filterTanggal").val();

        refreshTable(tableDisc,location.origin+"/diskonlist/table/"+custTypeId+"?mismassOrderId=&customerId="+customerId+"&filterTanggal="+filterTanggal,"table_info");    

        $.ajax({
            type: "GET",
            data:{
                "custTypeId":custTypeId,
                "customerId":customerId,
                "filterTanggal":filterTanggal
            },
            url: location.origin+"/check/gettotaldisc",
            success: function(data) {
                let a = JSON.parse(data);
                $("h1").html("Total Diskon : <div class='font-weight-bold' style='display:inline'>Rp."+masking(a.data)+"</div>");
            }
        });
});

$("table").on("click",".printResiAll",function(){
    let type = $("#navChooseCust .nav-item .active").attr("id");
    if(type=="I"){
        let total=0,
            b="";
        $("input[name='checkResi']").each(function(){
            if($(this).is(":checked")){
                total++;
                b += $(this).attr("data-resi").replace(/TR\/ALY\//g, "")+"=";
            }
        });

        if(total==0){
            Swal.fire("Resi Belum Diseleksi", "Belum Ada Resi Yang Diseleksi", 'error');
            return false;
        }

        window.open(
            location.origin+"/printout/resi/"+b,
            "_blank"
        );

    }else if(type=="C"){
        let id = $(this).attr("data-mismassInvoiceId");
        $.ajax({
            type: "GET",
            data:{
                "id":id
            },
            url: location.origin+"/check/getshipnumfrominvoice",
            success: function(a) {
                let z = JSON.parse(a),
                    b = "";
                for(let i=0;i<=z.data.length-1;i++){
                    b += z.data[i]['shipping_number'].replace(/TR\/ALY\//g, "")+"=";
                }
                
                window.open(
                    location.origin+"/printout/resi/"+b,
                    "_blank"
                );
            }
        });
    }

});

$("table").on("click",".detail-control",function(){
    let id = $(this).attr("id"),
        createdAt = $(this).attr("data-createdAt"),
        tr = $(this).closest("tr"),
        row = tableDisc.row(tr),
        printResi = auth==1?"<th>Print</th>":""
        div = "<div style='border: 1px solid rgb(52 58 64 / 34%);padding: 10px;border-radius: 5px;'>"+
        "<table class='table table-striped' id='"+id+"-child'>"+
            "<thead>"+
                "<tr>"+
                    "<th>No</th>"+
                    "<th>No. Resi</th>"+
                    "<th>Nama Penerima</th>"+
                    "<th>Kontak & Alamat</th>"+
                    "<th>Jumlah</th>"+
                    printResi+
                "</tr>"+
            "</thead>"+
        "</table>"+
        "</div>";

        if($(this).hasClass("hidden-child")){
            $(this).removeClass("hidden-child");
            $(this).addClass("shown-child");
            row.child(div).show();

            $("#"+id+"-child").DataTable({
                "paging": true,
                "searching": true,
                // "responsive": true,
                "processing": true,
                "serverSide": true,
                "processing": true,
                "serverSide": true,
                "order": [],
                "ajax": {
                    "url": location.origin+"/diskonlist/table/IND?mismassOrderId="+id+"&customerId=&filterTanggal=",
                    "type": "GET",
                    "dataSrc": function(json){
                        $("#"+id+"-child").parent().css("overflow-x","auto");
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
                    "lengthMenu" : "Rincian Detil Penerima | Tanggal Resi : <div style='color:green;display:inline'>"+createdAt+"</div>"
                }
            });
            
        }else{
            $(this).removeClass("shown-child");
            $(this).addClass("hidden-child");
            row.child.hide();
        }
});

var tableDisc = $('#table').DataTable({
    "paging": true,
    "searching": true,
    // "responsive": true,
    "processing": true,
    "serverSide": true,
    "processing": true,
    "serverSide": true,
    "order": [],
    "ajax": {
        "url": location.origin+"/diskonlist/table/IND?mismassOrderId=&customerId=&filterTanggal=",
        "type": "GET",
        "dataSrc": function(json){
            $("table").parent().css("overflow-x","auto");
            return json.data;
        }
    },
    "fnDrawCallback": function( oSettings ) {
        let custTypeId = $("#navChooseCust .nav-item .active").attr("id");
        custTypeId == "I" ? $("#thResi").show() : $("#thResi").hide();
        custTypeId == "I" ? $("#table tbody tr td:nth-child(4)").show() : $("#table tbody tr td:nth-child(4)").hide();
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

function getCustomerListDisc(custTypeId){
    $.ajax({
        type: "GET",
        data:{
            "custTypeId":custTypeId
        },
        url: location.origin+"/check/getcustlistdisc",
        success: function(data) {
            let a = JSON.parse(data);
            $("select[name='customerId']").html("");
            $("select[name='customerId']").html(a.data);
        }
    });
}

// var tableCor = $('#tableCor').DataTable({
//     "paging": true,
//     "searching": true,
//     // "responsive": true,
//     "processing": true,
//     "serverSide": true,
//     "processing": true,
//     "serverSide": true,
//     "order": [],
//     "ajax": {
//         "url": location.origin+"/diskonlist/table/COR?mismassOrderId=&customerId=&filterTanggalAwal=&filterTanggalAkhir=",
//         "type": "GET",
//         "dataSrc": function(json){
//             $("table").parent().css("overflow-x","auto");
//             return json.data;
//         }
//     },
//     "columnDefs": [{
//         "targets": [],
//         "orderable": true,
//     }],
//     "fixedHeader": false,
//     "ordering": true,
//     "info": true,
//     "autoWidth": true,
//     "lengthChange": true,
//     "pageLength": pageLength,
//     "language": {
//         "info": dt_info,
//         "infoEmpty": dt_info_empty,
//         "infoFiltered": dt_info_filter,
//         "search": dt_search_label,
//         "searchPlaceholder": dt_search_placeholder,
//         "zeroRecords": dt_zero_data,
//         "thousands": dt_thousands,
//         "processing": dt_processing,
//     }
// });