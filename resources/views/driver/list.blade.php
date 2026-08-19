<style>
    ul .nav-item .nav-link:not(.active){
        border
    }
    ul .nav-item .active{
        font-weight:700;
        color:var(--driver-primary-textcolor)!important;
        background:var(--driver-primary-bgcolor)!important;
    }

    .form-list .list-data{
        padding:10px;
        background-color:white;
        border-radius:10px;
        box-shadow: rgba(0, 0, 0, 0.15) 1.95px 1.95px 2.6px;
        cursor:pointer;
    }

    .form-list .list-data .header{
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .form-list .list-data .header label[for="individual"],
    .form-list .list-data .header label[for="corporate"]{
        font-size:12px;
        border-radius: 10px;
        padding:5px 10px;
    }

    label{
        margin:.5rem!important;
    }

    .form-list .list-data .header label[for="individual"]{
        background-color: var(--driver-primary-bgcolor);
    }

    .form-list .list-data .header label[for="corporate"]{
        background-color: var(--driver-secondary-bgcolor);
        color:var(--driver-light-textcolor);
    }

    .form-list .list-data .header label[for='resi']{
        font-size: 25px;
        color: var(--driver-secondary-textcolor);
    }

    .form-list .list-data .date{
        margin-top:-25px;
    }

    .form-list .list-data .date label[for='date']{
        font-size: 15px;
    }

    .form-list .list-data .cons{
        display:grid;
        font-size:20px;
    }

    .form-list .list-data .cons label[for='alamatpenerima'],
    .form-list .list-data .cons label[for='telponpenerima']{
        font-weight:500;
        margin-top:-10px;
    }

    .form-list .list-data .footer{
        display:flex;
        justify-content:space-between;
        font-size:15px;
    }

    .form-list .img-wrapper{
        padding:10px;
        display:flex;
        align-items:center;
        justify-content:center;
        height:calc(100vh - 300px);
    }

    .infinite-notif{
        display:none;
        text-align: center;
        font-weight: bold;
        font-size: large;
    }


.colored-toast.swal2-icon-success {
  background-color: #a5dc86 !important;
}

.colored-toast.swal2-icon-error {
  background-color: #f27474 !important;
}

.colored-toast.swal2-icon-warning {
  background-color: #f8bb86 !important;
}

.colored-toast.swal2-icon-info {
  background-color: #3fc3ee !important;
}

.colored-toast.swal2-icon-question {
  background-color: #87adbd !important;
}

.colored-toast .swal2-title {
  color: white;
}

.colored-toast .swal2-close {
  color: white;
}

.colored-toast .swal2-html-container {
  color: white;
}

/*/////// styleku ///////*/
button.swal2-confirm.swal2-styled {
    background: #dc3741 !important;
}

.logout.my-3.p-2, .gantipass.my-3.p-2 {
    background: #ffc107 !important;
}

ul .nav-item .nav-link:not(.active) {
    border: 1px solid #ffc107;
}

.nav-pills .nav-link.active, .nav-pills .show>.nav-link {
    border: 1px solid #ffc107 !important;
}

ul.nav.nav-pills.nav-fill.py-2 {
    gap: 5px;
}

button.btn.btn-primary {
    border: none !important;
    background: #ffc107 linear-gradient(180deg, #ffd665, #ffc107) repeat-x !important;
    border-radius: 10px;
    line-height: 35px;
    color:black;
}

button.btn.btn-secondary {
    border-radius: 10px !important;
    line-height: 35px !important;
    border: none !important;
}

.form-list .list-data .cons label[for='alamatpenerima'], .form-list .list-data .cons label[for='telponpenerima'] {
    font-weight: 500;
    margin-top: 0px !important;
    line-height: 22px;
}
</style>

<section class="content">
    <div class="container-fluid">
        <ul class="nav nav-pills nav-fill py-2">
            <li class="nav-item">
                <a class="nav-link pointlink active" id="shipment">Shipment</a>
            </li>
            <li class="nav-item">
                <a class="nav-link pointlink" id="ongoing">On Proses</a>
            </li>
            <li class="nav-item">
                <a class="nav-link pointlink" id="done">Selesai</a>
            </li>
        </ul>
        <div class="form-search">
            <input type="text" name="search" id="search" class="form-control" placeholder="Ketik Untuk Mencari ...">
        </div>
        <div class="form-list">
            <div class="infinite-notif">Loading ...</div>
        </div>
    </div>
    <div class="modal fade" style="padding-right:0px!important" data-backdrop="static" id="successModal" tabindex="-1" role="dialog" aria-labelledby="endUpdateLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mt-1">
                        <div class="col-sm-12">
                            <div class="preview-bukti-foto"></div>
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="col-sm-12">
                            <div class="alert alert-success" role="alert">
                                <strong>Status Terakhir</strong>
                                <div class="waktuStatus">10 Agustus 2025 10:00:00</div>
                                <div class="descStatus">REJECT. Pelanggan Tidak Dirumah</div>
                                <div class="byStatus">By Admin</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" style="padding-right:0px!important" data-backdrop="static" id="endUpdateModal" tabindex="-1" role="dialog" aria-labelledby="endUpdateLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form id="formEndUpdate">
                        <input type="hidden" name="endId">
                        <input type="hidden" name="endStatus">
                        <input type="hidden" name="endResi">
                        <div class="modal-header">
                            <h5 class="modal-title"></h5>
                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row mt-1">
                                <div class="col-sm-12">
                                    <div class="alert alert-success" role="alert">
                                        <strong>Status Terakhir</strong>
                                        <div class="waktuStatus">10 Agustus 2025 10:00:00</div>
                                        <div class="descStatus">REJECT. Pelanggan Tidak Dirumah</div>
                                        <div class="byStatus">By Admin</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-1">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="trackStatusId">Status Tracking</label>
                                        <select class="form-control" name="trackStatusId" id="trackStatusId" required>
                                            <option value='' hidden>Pilih Status Tracking</option>
                                            <option value='20'>PENDING</option>
                                            <option value='21'>REJECT</option>
                                            <option value='22'>SELESAI</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-1 row-receiver" style="display:none">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="receiver">Nama Penerima</label>
                                        <input type="text" class="form-control" onkeyup="this.value = this.value.toUpperCase()" name="receiver" id="receiver">
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-1 row-reason" style="display:none">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="reason">Alasan</label>
                                        <input type="text" class="form-control" onkeyup="this.value = this.value.toUpperCase()" name="reason" id="reason">
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-1 row-bukti-foto" style="display:none">
                                <div class="col-sm-12 col-md-3">
                                    <div class="preview-bukti-foto">
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-9 col-bukti-foto">
                                    <div class="form-group">
                                        <label for="buktiFoto">Bukti Foto</label>
                                        <input type="file" class="form-control" accept="image/*" name="buktiFoto[]" id="buktiFoto" placeholder="buktiFoto">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
</section>

<script>
    var scrollToEnd = false;
    
    //QR-CODE BUTTON
    $(".qrcode").show();

    //LOAD DATA
    loadTableData({
        'offset':offset,
        'perload':perLoad,
        'status':status,
        'searchText':searchText,
        'infinite': false
    });

    $(document).on("click",".form-list .list-data",function(){

        status = $('.nav .nav-item .active').attr('id');

        $(".row-reason,.row-receiver").hide();
        $("#endUpdateModal input, #endUpdateModal select").val("");

        let id = $(this).attr('id'),
            title = getTitleUpdateDriver(status),
            resi = $("#"+id+" label[for='resi']").text(),
            icon = getIconUpdateDriver(status),
            lastTrackingStatus = $(this).attr('data-last-tracking'),
            lastTrackingDate = $(this).attr('data-last-tracking-date'),
            lastTrackingBy = $(this).attr('data-last-tracking-by'),
            podImage = $(this).attr('data-pod-image'),
            imgEl = podImage!="" ? "<img src='"+location.origin+"/assets/pod/"+podImage+"' width='100%'>" : "";
            filter = {
                id : id,
                icon : icon,
                title : title,
                resi : resi,
                status : status
            };

        if(status=="done"){
            $("#successModal .waktuStatus").text(lastTrackingDate);
            $("#successModal .descStatus").text(lastTrackingStatus);
            $("#successModal .byStatus").text("User : "+lastTrackingBy);
            $("#successModal .preview-bukti-foto").html(imgEl);
            $("#successModal .preview-bukti-foto").hide();
            if(podImage!=""){
                $("#successModal .preview-bukti-foto").show();
            }
            $("#successModal h5").text("Resi "+resi);
            $("#successModal").modal("show");
            return true;
        }

        if(status=="ongoing"){
            $("#endUpdateModal input[name='endId']").val(id);
            $("#endUpdateModal input[name='endStatus']").val(status);
            $("#endUpdateModal input[name='endResi']").val(resi);
            $("#endUpdateModal .waktuStatus").text(lastTrackingDate);
            $("#endUpdateModal .descStatus").text(lastTrackingStatus);
            $("#endUpdateModal .byStatus").text(lastTrackingBy);
            $("#endUpdateModal .preview-bukti-foto").html("<img src='"+location.origin+"/assets/dist/pic/none.png' style='width:100%;aspect-ratio:1;object-fit:cover'/>");
            $("#endUpdateModal h5").text("Update Status Resi "+resi);
            $("#endUpdateModal").modal("show");
            return true;
        }

        notificationConfirm(filter);
    });

    function notificationConfirm(filter){
        Swal.fire({
            title: filter.title+" <div style='color:green'>"+filter.resi+"</div>",
            icon: filter.icon,
            showCancelButton: true,
            showConfirmButton: true,
            confirmButtonText: `Yakin`,
            customClass: {
                cancelButton: 'order-1',
                denyButton: 'order-2',
            },
        }).then((result) => {
            if (result.isConfirmed) {
                goUpdateResiDriver({
                    endId:filter.id,
                    endResi:filter.resi,
                    endStatus:filter.status
                });
            }
        });
    }

    //CHANGE TAB
    $(".nav .nav-item .nav-link").on("click",function(){
        offset = 0;
        status = $(this).attr("id");
        searchText = $("#search").val();

        //TAB ACTIVE
        $(".nav .nav-item .nav-link").removeClass("active");
        $("#"+status).addClass("active");
        
        //RESET CHILD
        resetFormList();

        //LOAD NEW DATA
        loadTableData({
            'offset':offset,
            'perload':perLoad,
            'status':status,
            'searchText':searchText,
            'infinite': false
        });
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
        }

        $("#endUpdateModal input[name='reason'],#endUpdateModal input[name='receiver']").val("");
    });

    //SEARCHING
    $("#search").on("keyup",function(){
        offset = 0;
        status = $(".nav .nav-item .active").attr("id");
        searchText = $("#search").val();

        //RESET CHILD
        resetFormList();

        //LOAD NEW DATA
        loadTableData({
            'offset':offset,
            'perload':perLoad,
            'status':status,
            'searchText':searchText,
            'infinite': false
        });
    });   

    //INFINITE SCROLL
    $(document).on('scroll', function(){
            scrollToEnd = true;          
    });

    setInterval(() => {
        if(scrollToEnd){
            scrollToEnd = false;

            if(Math.ceil($(window).scrollTop() + $(window).height()) + 50 >= $(document).height()) {
                offset = $(".list-data").length;
                status = $(".nav .nav-item .active").attr("id");
                searchText = $("#search").val();

                loadTableData({
                    'offset':offset,
                    'perload':perLoad,
                    'status':status,
                    'searchText':searchText,
                    'infinite': true
                });
                console.log("pause");
            }
        }
    }, 500);

    $("#buktiFoto").on("change", function(e){
        let divFile = document.getElementById('buktiFoto').files;

        $(".preview-bukti-foto").html("");

        imgURL = URL.createObjectURL(divFile[0]);
        $(".preview-bukti-foto").append("<img src='"+imgURL+"' title='"+e.target.files[0].name+"' style='width:100%;aspect-ratio:1;object-fit:cover'/>");
    });

    $("#formEndUpdate").validate({
        errorClass: "error fail-alert is-invalid",
        messages: {
            "receiver": "Tidak Boleh Kosong",
            "reason": "Tidak Boleh Kosong",
        },
        submitHandler: function(form,event) {

            event.preventDefault();

            let id = $("#endUpdateModal input[name='endId']").val(),
                status = $("#endUpdateModal input[name='endStatus']").val(),
                resi = $("#endUpdateModal input[name='endResi']").val(),
                icon = getIconUpdateDriver(status),
                title = getTitleUpdateDriver(status),
                filter = {
                    id : id,
                    icon : icon,
                    title : title,
                    resi : resi,
                    status : status
                };

            Swal.fire({
                title: filter.title+" <div style='color:green'>"+filter.resi+"</div>",
                icon: filter.icon,
                showCancelButton: true,
                showConfirmButton: true,
                confirmButtonText: `Yakin`,
                customClass: {
                    cancelButton: 'order-1',
                    denyButton: 'order-2',
                },
            }).then((result) => {
                if (result.isConfirmed) {

                    let form_data = new FormData($("#formEndUpdate")[0]);

                    $.ajax({
                        type: 'POST',
                        // data: $(form).serialize(),
                        data: form_data,
                        contentType: false,
                        processData:false,
                        url: location.origin+"/d/m/l/u",
                        success: function(msg){
                            let json = JSON.parse(msg);

                            loadTableData({
                                'offset':0,
                                'perload':10,
                                'status':'ongoing',
                                'searchText':'',
                                'infinite': false
                            });

                            if(json.status==200){
                                Swal.fire({
                                    title: "Berhasil",
                                    text: "Resi Berhasil Diupdate!!!",
                                    icon: "success"
                                });
                            }else{
                                Swal.fire({
                                    title: "Gagal",
                                    text: json.text,
                                    icon: "error"
                                });
                            }

                            $("#endUpdateModal").modal('hide');

                        }
                    });
                }
            });            
            
        }
    });

    function loadTableData(data){
        $.ajax({
            type: 'GET',
            data: data,
            url: location.origin+"/d/m/l/t",
            beforeSend: function() {
                beforeSendDriver(data['infinite']);
            },
            success: function(msg){
                let json = JSON.parse(msg);

                if(json.totalData==0){
                    notFoundDataDriver(data['infinite']);
                    return false;
                }

                foundDataDriver(json.data,data['infinite']);

            }
        });
    }

    function beforeSendDriver(a){
        if(!a){
            spinner = "<div id='spinner'><div class='loader'><div></div><div></div><div></div></div></div>";
            $(".infinite-notif").before(spinner);
        }
    }

    function notFoundDataDriver(a){
        if(!a){
            notfound = "<div class='img-wrapper'><img src='{{url("assets/dist/pic/not-found.png")}}' width='50%'></div>";    
            resetFormList();
            $(".infinite-notif").before(notfound);
            $(".form-list #spinner").remove();
            return false;
        }

        Toast.fire({
            icon: 'error',
            title: 'Data Sudah Tampil Semua!!',
        });
    }

    function foundDataDriver(data,stats){
        datalist = "";
        data.forEach(value => {
            let resi = value.cust_type_id=="IND" ? ( value.ms_track_id!="" ? value.ms_track_id : value.shipping_number ) : value.shipping_number;
                shippingStatus = value.shipping_status.split("<br>");
            datalist += "<div class='list-data my-3' id='"+value.id+"' data-cust-type='' data-pod-image='"+value.shipping_success_pod+"' data-last-tracking='"+shippingStatus[0]+"' data-last-tracking-date='"+moment(value.shipping_updated_at).format("DD/MM/YYYY HH:MM")+"' data-last-tracking-by='"+value.shipping_updated_by+"'>"+
                            "<div class='header py-1'>"+
                                "<label for='resi'>"+resi+"</label>"+
                                "<label for='"+(value.cust_type_id=='IND'?"individual":"corporate")+"'>"+(value.cust_type_id=='IND'?"Individual":"Corporate")+"</label>"+
                            "</div>"+
                            "<div class='date py-1'>"+
                                "<label for='date'>"+moment(value.shipping_created_at).format("DD/MM/YYYY HH:MM")+"</label>"+
                            "</div>"+
                            "<div class='cons py-1'>"+
                                "<label for='namapenerima'>"+value.cons_first_name+" "+value.cons_middle_name+" "+value.cons_last_name+"</label>"+
                                "<label for='alamatpenerima'>"+value.cons_address+" ,"+value.cons_sub_district+" ,"+value.cons_district+" ,"+value.cons_city+" ,"+value.cons_prov+" ,"+value.cons_postal_code+"</label>"+
                                "<label for='telponpenerima'>"+value.cons_phone+"</label>"+
                            "</div>"+
                            "<div class='footer py-1'>"+
                                "<label for='totalberat'><i class='fas fa-weight-hanging'></i> "+value.weight+" KG</label>"+
                                "<label for='totalitem'><i class='fas fa-box'></i> "+value.item+" Item</label>"+
                                "<label for='totalcbm'><i class='fas fa-archive'></i> "+value.cbm+" CBM</label>"+
                            "</div>"+
                        "</div>";
        });
        $(".infinite-notif").before(datalist);
        $(".form-list #spinner").remove();

        if(stats){
            Toast.fire({
                icon: 'success',
                title: 'Berhasil Load Data !!',
            });
        }
    }

    function goUpdateResiDriver(data){
        
        $.ajax({
            type: 'POST',
            data: data,
            url: location.origin+"/d/m/l/u",
            success: function(msg){
                let json = JSON.parse(msg);

                loadTableData({
                    'offset':0,
                    'perload':10,
                    'status':'shipment',
                    'searchText':'',
                    'infinite': false
                });

                if(json.status==200){
                    Swal.fire({
                        title: "Berhasil",
                        text: "Resi Berhasil Diupdate!!!",
                        icon: "success"
                    });
                }else{
                    Swal.fire({
                        title: "Gagal",
                        text: json.text,
                        icon: "error"
                    });
                }

            }
        });
    }

        function resetFormList(){
            $(".form-list .list-data,.form-list .img-wrapper").remove();
        }

        function getTitleUpdateDriver(a){
            return a=="shipment"?"Apakah Anda Yakin Akan Memproses Resi Ini?":"Apakah Anda Yakin Update Status Resi Ini?";
        }

        function getIconUpdateDriver(a){
            return a=="shipment"?"question":"warning";
        }
</script>