var fileArr = [];

//Add Secondary Resi LN
$(".card-resiLN").on("click",".minResiLN",function(event){

    let rowResiId = $(this).closest(".row-resi-ln").attr("id"),
        resiLNValue = $("#"+rowResiId+" input[name='resiln[]']").val(),
        trackId = $("#formEditShipment input[name='msTrackId']").val(),
        title = "Resi LN "+resiLNValue+" akan dimasukkan resi secondary. Apakah anda yakin? <br><br>"+
                "<div style='font-size:15px'>Pastikan cek dulu data apakah sudah benar atau belum. Berikutnya cek pada tabel Missed Packet untuk membuat tanggal shipment baru.</div>";

    if(resiLNValue==""){
        return Swal.fire("Gagal", "Resi LN tidak memiliki value!", 'error');
    }

    Swal.fire({
        title: title,
        icon: 'question',
        showCancelButton: true,
        showConfirmButton: true,
        confirmButtonText: `Yakin`,
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
                    trackId:trackId,
                    resiLn:resiLNValue
                },
                url: location.origin+"/shiptrip/create/shipment/secondary",
                success:function(msg){    
                    let json = JSON.parse(msg);                
                    if(json.status==200){

                        $("#"+rowResiId).remove();
                        Swal.fire(json.title, json.message, 'success');
                        refreshTable(table,location.origin+"/shiptrip/table/"+$("#navChoose li .active").attr("id")+"/"+$("#navType li .active").attr("id")+"/"+$("#navChooseCust li .active").attr("id"),"table_info");
                        // refreshTable(tableCi,location.origin+"/shiplist/table/order/"+custTypeId+"?filterTanggal=","table-ci_info");
                        // let custTypeId = $('.btn-select').attr('id');
                        // CheckFilterTable();

                        // $("#updateInvoice input[name='invoiceId']").val("");
                        // $("#updateInvoice input[name='tanggalJamInvoice']").val("");
                        // $("#updateInvoice").modal("hide");
                        let parent = "#editShipmentModal";
                        updateNomorUrutResiLN(parent);

                        return true;

                    }
                    
                    Swal.fire(json.title, json.message, 'error');
                }
            });
        }
    })

    event.stopPropagation();
});

//Create Resi LN    
$(".card-resiLN").on("click",".addResiLN",function(event){
    let id = $(this).closest(".row-resi-ln").attr("id"),
        parent = ".card-resiLN",
        dataEditBtn = $(this).attr("data-edit-btn"),
        minResiLNEl = "",
        makeIdRow = makeId(5);
        
    if(dataEditBtn!=""){
        minResiLNEl = "<button type='button' class='btn minResiLN text-primary' style='font-size:25px!important;padding:0px 5px 0px 10px'><i class='fas fa-backward'></i></button>";
    }

    margin = $(".card-resiLN .card-body .row-resi-ln").length<1?"":"mt-1",
    element = "<div class='row row-resi-ln "+margin+"' id='"+makeIdRow+"'>"+
                    "<div class='col' style='padding-top:6px'>"+
                        "<div style='display:flex;' class='resi-item'>"+
                            "<div class='noUrutResiLN'></div>"+
                            "<div style='width: -webkit-fill-available'>"+
                                "<input type='text' name='resiln[]' data-edit-form='"+dataEditBtn+"' id='"+makeId(10)+"' onkeyup='this.value=this.value.toUpperCase()' placeholder='Input Resi LN ...' class='form-control resiln' required>"+
                            "</div>"+
                            minResiLNEl+
                            "<button type='button' data-edit-btn='"+dataEditBtn+"' class='btn addResiLN text-success' style='font-size:25px!important;padding:0px 5px 0px 10px'><i class='fas fa-plus-square'></i></button>"+
                            "<button type='button' class='btn deleteResiLN text-danger' style='font-size:25px!important;padding:0px 5px 0px 5px'><i class='fas fa-trash'></i></div>"+
                        "</div>"+
                    "</div>"+
                "</div>";

    $(element).insertAfter($("#"+id));

    updateNomorUrutResiLN(parent);

    event.stopPropagation();
});

//Delete Resi LN
$(".card-resiLN").on("click",".deleteResiLN",function(){
    let id = $(this).closest(".row-resi-ln").attr("id"),
        parent = ".card-resiLN";
    $("#"+id).remove();

    updateNomorUrutResiLN(parent);
});

function updateNomorUrutResiLN(parent){
    $(parent+" .resi-item").each(function(i){
        $(this).find(".noUrutResiLN").text(i+1+".");
    });
}

//Tanggal Drop
$('input[name="tanggalDrop"]').daterangepicker({
    singleDatePicker: true,
    autoApply:true,
    autoUpdateInput: false,
    forceParse: false,
    timePicker:false,
    timePicker24Hour: false,
    locale: {
        format: 'DD/MM/YYYY'
    },
});
$('input[name="tanggalDrop"]').on('cancel.daterangepicker', function (ev, picker) {
    $(this).val('');
});
$('input[name="tanggalDrop"]').on('apply.daterangepicker', function (ev, picker) {
    let startDate = picker.startDate;
    $(this).val(startDate.format('DD/MM/YYYY'));
});

//Get Service When Warehouse Choosed
$("select[name='warehouse']").on("change",function(){
    let id = $(this).val(),
        noServ = $(this).attr("data-noserv");
    $.ajax({
        type: "GET",
        url: location.origin+"/check/getservlist?inv=true",
        data: {
            warehouseId:id,
        },
        success: function(msg) {
            let json = JSON.parse(msg);
            $("select[name='service']").html("");
            $("select[name='service']").html(json.data);
            if(noServ==undefined){
                $(".col-service").show();
                $(".col-warehouse").addClass("col-md-6");
            }else{
                $(".col-service").hide();
                $("select[name='service']").val($("select[name='service'] option").eq(1).val());
                createTrackId(id);
            }
            $(".row-track-id").hide();
            $("input[name='trackId']").val("");
        }
    });
});

//Get Track Id When Service Choosed
$("select[name='service']").on("change",function(){
    let wareId = $("select[name='warehouse']").val(),
        formatResi = $("select[name='formatResi']").val();
    // if(formatResi=="PRM"){
        createTrackId(wareId);
    // }
});

//Upload Image
$(".uploadImage").on("click",function(){
    $("input[name='file[]']").click();
});

//Add Uploaded Images
$("input[name='file[]']").on("change",function(e){
    let divFile = document.getElementById('file[]').files;
        length = divFile.length;
        editImg = $(this).attr("data-edit-img");

    if(editImg){
        let ImgEditViewlength = $(".img-preview[data-edit-img-view='true']").length;
        length+=ImgEditViewlength;
    }

    if(length>5){
        swal.fire('Gagal !','Maksimal upload 5 foto','error');
        return false;
    }

    if(length==5){
        $(".uploadImage").hide();
    }

    $(".img-preview[data-edit-img-view='false']").remove();

    if(length>0){
        for(let i = 0; i < length; i++){
            imgURL = URL.createObjectURL(divFile[i]);
            fileArr[i] = divFile[i];
            $(".col-preview").append("<div class='img-preview' data-edit-img-view='false' id='img-view-"+i+"'><a href='"+imgURL+"' target='_blank'><img src='"+imgURL+"' title='"+e.target.files[i].name+"' /></a><button type='button' class='close deleteFoto' data-name='"+e.target.files[i].name+"'><span aria-hidden='true'>&times;</span></button></div>");
        }
    }

    document.getElementById('file[]').files = FileListItem(fileArr);
});

//Delete Images
$(".col-preview").on("click",".deleteFoto",function(){
    let div = $(this).closest(".img-preview").attr("id"),
        fileName = $(this).attr("data-name"),
        imgStatus = $(this).attr("data-status")

    $("#"+div).remove();

    if($(".img-preview").length<=5){
        $(".uploadImage").show();
    }

    if(imgStatus!="imgold"){
        for(let i = 0; i < fileArr.length; i++){
            if(fileArr[i].name === fileName){
                fileArr.splice(i,1);
            }
        }

        document.getElementById('file[]').files = FileListItem(fileArr);
    }
});

function FileListItem(file) {
    file = [].slice.call(Array.isArray(file) ? file : arguments)
    for (var c, b = c = file.length, d = !0; b-- && d;) d = file[b] instanceof File
    if (!d) throw new TypeError("expected argument to FileList is File or array of File objects")
    for (b = (new ClipboardEvent("")).clipboardData || new DataTransfer; c--;) b.items.add(file[c])
    return b.files
}

function createTrackId(id){
    $.ajax({
        type: "GET",
        url: location.origin+"/newship/createtrackid",
        data: {
            id : id,
        },
        success: function(msg) {
            var json = JSON.parse(msg);
            $(".row-track-id").show();
            $("input[name='trackId']").attr("required","true").attr("readonly","false");
            $("input[name='trackId']").val(json.id);
            return true;
        }
    });
}
