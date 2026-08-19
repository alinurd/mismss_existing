$("#IND").addClass("btn-select");

//Look Resi LN & Notes
$("table").on('click','.lookresiln',function(){
    let id = $(this).attr("data-id"),
        resiln = $(this).attr("data-resi-ln"),
        resilnArr = resiln.split(", "),
        resilnElm = "",
        catatan = $(this).attr("data-catatan"),
        catatanElm = isValidUrl(catatan) ? "<a href='"+catatan+"' target='_blank'>"+catatan+"</a>" : catatan,
        modalId = "#resiLnModal";
        
    $(modalId+" .row-resiln .card-body").html("");
    $(modalId+" .row-catatan .text").text("");

    for(let i = 0;i<resilnArr.length;i++){
        resilnElm += "<div class='row'>"+
                        "<div class='col' style='padding-top:6px'>"+
                            "<div style='display:flex;'>"+
                                "<div style='width: -webkit-fill-available'>"+
                                    "<input type='text' name='resiln' class='form-control' value='"+resilnArr[i]+"' readonly>"+
                                "</div>"+
                            "</div>"+
                        "</div>"+
                    "</div>";
    }


    $(modalId+" .row-resiln .card-body").append(resilnElm);
    $(modalId+" .row-catatan .text").html(catatanElm);
    $(modalId+" h5").text("Resi LN Shipment "+id);
    $(modalId).modal("show");
});

$("table").on("click","#checkBtn",function(){
    let id = $(this).attr("data-id");
    console.log(id);
    Swal.fire({
        title: 'Apakah Anda Yakin Akan Check Resi Tracking '+id+'?',
        icon: 'question',
        showCancelButton: true,
        showConfirmButton: true,
        confirmButtonText: `Yakin`,
        customClass: {
            cancelButton: 'order-1',
            denyButton: 'order-2',
          },
    }).then((result) => {
        if (result.isConfirmed) {
            
            $.ajax({
                url: location.origin+'/shiplist/input/packlist',
                type: 'GET',
                data: {'id':id},
                success: function(msg) {
                    var json = JSON.parse(msg);
                    
                    if(json.status==200){
                        Swal.fire(json.header, json.text, 'success');
                        loadTable();
                        return true;
                    }

                    Swal.fire(json.status, json.text, 'error');
                }
            });

        }
    })
});

$("#navChooseCust .nav-link").on("click",function(){  
    let custTypeId = $(this).attr("id");

    $("#navChooseCust .nav-link").removeClass("btn-select");
    $(this).addClass("btn-select");
    $(".row-filter,.card-table").show();

    $("#table-list th:nth-child(4)").text("Client");
    $("#table-checked th:nth-child(5)").text("Client");
    if(custTypeId=="IND"){
        $("#table-list th:nth-child(4)").text("Customer");
        $("#table-checked th:nth-child(5)").text("Customer");
    }

    loadTable();
});

$("#navTabOrder .nav-item .nav-link").on("click",function(){
    loadTable();
});

$("#view").on("click",function(){
    loadTable();
});

$("#reset").on("click",function(){
    $('#filterTanggal').val('');
    loadTable();
});

if($('#table-list').length>0){
    tableList = $('#table-list').DataTable({
            "paging": true,
            "searching": true,
            "processing": true,
            "serverSide": true,
            "processing": true,
            "serverSide": true,
            "order": [],
            "columnDefs": [{
                "targets": [],
                "orderable": true,
            }],
            "ajax": {
                    "url": location.origin+"/shiplist/table/packlist/IND?filterTanggal=",
                    "type": "GET",
                    "dataSrc": function(json){
                        $("#table-list").parent().css("overflow-x","auto");
                        return json.data;
                    }
            },
            "fixedHeader": false,
            "ordering": false,
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

}

if($('#table-checked').length>0){
    tableChecked = $('#table-checked').DataTable({
            "paging": true,
            "searching": true,
            "processing": true,
            "serverSide": true,
            "processing": true,
            "serverSide": true,
            "order": [],
            "columnDefs": [{
                "targets": [],
                "orderable": true,
            }],
            "ajax": {
                    "url": location.origin+"/shiplist/table/packcheck/IND?filterTanggal=",
                    "type": "GET",
                    "dataSrc": function(json){
                        $("#table-checked").parent().css("overflow-x","auto");
                        return json.data;
                    }
            },
            // "fnDrawCallback": function( oSettings ) {
            //     let custTypeId = $(".btn-select").attr("id");
            //     custTypeId == "IND" ? $("#thResi").show() : $("#thResi").hide();
            //     custTypeId == "IND" ? $("#table-checked tbody tr td:nth-child(4)").show() : $("#table-checked tbody tr td:nth-child(4)").hide();
            // },
            "fixedHeader": false,
            "ordering": false,
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

}

function loadTable(){
    let custTypeId = $(".btn-select").attr("id"),
        navTabOrder = $("#navTabOrder .nav-item .active").attr("id"),
        filterTanggal = $("#filterTanggal").val();
        table = navTabOrder=="nav-tab-list"?tableList:tableChecked,
        tableInfo = navTabOrder=="nav-tab-list"?"table-list_info":"table-checked_info",
        tableKind = navTabOrder=="nav-tab-list"?"packlist":"packcheck";
    
    // console.log(location.origin+"/shiplist/table/"+tableKind+"/"+custTypeId);

    refreshTable(table,location.origin+"/shiplist/table/"+tableKind+"/"+custTypeId+"?filterTanggal="+filterTanggal,tableInfo);
}