
/////////////////////////INITIALIZE/////////////////////////////

//Active Tab Customer Individual
$("#IND").addClass("btn-select");

//Active First Tab Order (Create Invoice)
$("#navTabOrder li a").first().addClass("active");

//Active First Table Element (Order List)
$("#nav-tabContent .tab-pane").first().addClass("show active");

//If Tab Order Is Only One Visible (Permission). Then All Element Tab Order Must Be Hide
$("#navTabOrder li a").length==1?$("#navTabOrder li a").hide():'';

//Get Cust List Individual For Select Input Filter Customer
$("#filterCustomer").html(getCustList("IND"));

//Select 2 Element
$("select[name='filterCustomer']").select2({
        'width':'20%'
});

$('#filterWarehouse').select2({
    width: "40%",
    templateResult: function (data) {
        if (!data.id) return data.text;

        let navTab = $("#navTabOrder li .active").attr("id");

            if(navTab!="nav-ci-tab"){
                return data.text;
            }

            let text = data.text.split(" - "),
                    html;

            if(text[0]=="BULKY"){

                html = `
                    <span>
                        ${text[0]} -
                        <span style="color: blue;">${text[1]}</span>
                    </span>
                `;

            }else{

                html = `
                    <span>
                        ${text[0]} -
                        ${text[1]} -
                        ${text[2]} -
                        <span style="color: blue;">${text[3]}</span>
                    </span>
                `;

            }

            return $(html);
    },

    templateSelection: function (data) {
        if (!data.id) return data.text;

        let navTab = $("#navTabOrder li .active").attr("id");

        if(navTab!="nav-ci-tab"){
            return data.text;
        }

        let text = data.text.split(" - "),
                html;

        if(text[0]=="BULKY"){

            html = `
                <span>
                    ${text[0]} -
                    <span style="color: blue;">${text[1]}</span>
                </span>
            `;

        }else{

            html = `
                <span>
                    ${text[0]} -
                    ${text[1]} -
                    ${text[2]} -
                    <span style="color: blue;">${text[3]}</span>
                </span>
            `;

        }

        return $(html);
    }
});

$("select[name='filterService']").select2({
        'width':'20%'
});

///////////////////////////HIDE//////////////////////////////////////

//Select Input Service
// $("#filterService").hide();

///////////////////////////SHOW//////////////////////////////////////

//////////////////////////FUNCTION///////////////////////////////////

//If Tab Customer Is On Clicked
// $("#navChooseCust .nav-link").on("click",function(){  
//     let custTypeId = $(this).attr("id");
//     $("#navChooseCust .nav-link").removeClass("btn-select");
//     $(this).addClass("btn-select");

//     $("#table-ci th:nth-child(5)").text("Detail Client");
//     $("#table-ct th:nth-child(5)").text("Detail Client");
//     $("#table-status th:nth-child(6)").text("Detail Client");
//     $("#createInvoice #btnAddServiceElement").html("<i class='fas fa-plus'></i> Add Customer");
//     $("#editInvoice #btnAddServiceElement").html("<i class='fas fa-plus'></i> Add Customer");
//     $("input[name='checkAll']").hide();
//     $("#filterService").select2().next().hide();
    
//     //Adjust Width Of Filters
//     $("select[name='filterCustomer']").select2({
//     'width':'40%'
//     });
    
//     $("select[name='filterWarehouse']").select2({
//             'width':'40%'
//     });
    
//     if(custTypeId=="IND"){
//         $("input[name='checkAll']").show();
//         $("#table-ci th:nth-child(5)").text("Detail Customer");
//         $("#table-ct th:nth-child(5)").text("Detail Customer");
//         $("#table-status th:nth-child(6)").text("Detail Customer");
//         $("#createInvoice #btnAddServiceElement").html("<i class='fas fa-plus'></i> Add Service");
//         $("#editInvoice #btnAddServiceElement").html("<i class='fas fa-plus'></i> Add Service");
//         $("#filterService").select2().next().show();
//         //Adjust Width Of Filters
//         $("select[name='filterCustomer']").select2({
//         'width':'20%'
//         });
        
//         $("select[name='filterWarehouse']").select2({
//                 'width':'40%'
//         });
        
//         $("select[name='filterService']").select2({
//                 'width':'20%'
//         });
//     }

//     //Get Data Cust List Based On Cust Type ID For Select Input Filter Customer
//     $("#filterCustomer").html("");
//     $("#filterCustomer").html(getCustList(custTypeId));

//     refreshTable(tableCi,location.origin+"/shiplist/table/order/"+custTypeId+"?filterTanggal=","table-ci_info");
//     CheckFilterTable();
// });

function loadFilterAndTable(){
    let navTabOrder = $("#navTabOrder .nav-item .active").attr('id'),
        custTypeId = $(".btn-select").attr("id");

    $("#table-ci th:nth-child(5)").text("Detail Client");
    $("#table-ct th:nth-child(5)").text("Detail Client");
    $("#table-status th:nth-child(6)").text("Detail Client");
    $("#createInvoice #btnAddServiceElement").html("<i class='fas fa-plus'></i> Add Customer");
    $("#editInvoice #btnAddServiceElement").html("<i class='fas fa-plus'></i> Add Customer");
    $("input[name='checkAll']").hide();
    if(custTypeId=="IND"){
        $("input[name='checkAll']").show();
        $("#table-ci th:nth-child(5)").text("Detail Customer");
        $("#table-ct th:nth-child(5)").text("Detail Customer");
        $("#table-status th:nth-child(6)").text("Detail Customer");
        $("#createInvoice #btnAddServiceElement").html("<i class='fas fa-plus'></i> Add Service");
        $("#editInvoice #btnAddServiceElement").html("<i class='fas fa-plus'></i> Add Service");
    }

    //Get Data Cust List Based On Cust Type ID For Select Input Filter Customer
    $("#filterCustomer").html("");
    $("#filterCustomer").html(getCustList(custTypeId));

    if(navTabOrder=="nav-ci-tab"){
        refreshTable(tableCi,location.origin+"/shiplist/table/order/"+custTypeId+"?filterTanggal=","table-ci_info");   
        
        //Change Placeholder Of Filter Date
        $("input[name='filterTanggal']").attr("placeholder","Filter Tanggal Shipment");

        //Show Filter Customer
        $("#filterCustomer").select2().next().show();

        //Show Select All
        $("#selectMode").show();

        //Show Create Invoice
        $("#createInv").show();

        //Adjust Width Of Filters
        $("select[name='filterCustomer']").select2({
            'width':'20%'
        }).next('.select2-container').show();
    }else{
        //Check Filter Table
        CheckFilterTable();
        
        //Change Placeholder Of Filter Date
        $("input[name='filterTanggal']").attr("placeholder","Filter Tanggal Create Invoice");

        //Hide Filter Customer
        $("select[name='filterCustomer']").select2({
            'width':'20%'
        }).next('.select2-container').hide();

        //Hide Select All
        $("#selectMode").hide();

        //Hide Create Invoice
        $("#createInv").hide();
    }

    $("select[name='filterWarehouse']").val("");
    $("select[name='filterService']").val("");
    $("input[name='filterTanggal']").val("");
}

$(document).on("shown.bs.tab","#navChooseCust .nav-link", function () {
    $("#navChooseCust .nav-link").removeClass("btn-select");
    $(this).addClass("btn-select");

    loadFilterAndTable();
    getWarehouseList();
});

$(document).on("shown.bs.tab","#navTabOrder .nav-item a", function () {
    loadFilterAndTable();
    getWarehouseList();
});

getWarehouseList();

function getWarehouseList(){
    let navCust = $("#navChooseCust li .active").attr("id"),
        navTab = $("#navTabOrder li .active").attr("id");

        $("#filterWarehouse").val("").trigger("change");

        $.ajax({
            type: "GET",
            url: location.origin+"/shiplist/warehouse/list",
            data: {
                navCust : navCust,
                navTab : navTab
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

//If Filter Warehouse Is On Clicked
$("#filterWarehouse").on("change",function(){
    let id = $(this).val();
    $.ajax({
        type: "GET",
        url: location.origin+"/check/getservlist?inv=false",
        data: {
            warehouseId:id,
        },
        success: function(msg) {
            let json = JSON.parse(msg);
            $(".col-filterService").show();
            $("select[name='filterService']").html("");
            $("select[name='filterService']").html(json.data);
        }
    });
});

//If List Tab Order Is On Clicked
// $("#navTabOrder .nav-item a").on("click",function(){

//     //Initialize
//     let id = $(this).attr('id'),
//         custTypeId = $(".btn-select").attr("id");

//     if(id=="nav-ci-tab"){
//         refreshTable(tableCi,location.origin+"/shiplist/table/order/"+custTypeId+"?filterTanggal=","table-ci_info");   
        
//         //Change Placeholder Of Filter Date
//         $("input[name='filterTanggal']").attr("placeholder","Filter Tanggal Shipment");

//         //Show Filter Customer
//         $("#filterCustomer").select2().next().show();

//         //Show Select All
//         $("#selectMode").show();

//         //Show Create Invoice
//         $("#createInv").show();

//         //Adjust Width Of Filters
//         $("select[name='filterCustomer']").select2({
//         'width':'20%'
//         });
        
//         $("select[name='filterWarehouse']").select2({
//                 'width':'40%'
//         });
        
//         $("select[name='filterService']").select2({
//                 'width':'20%'
//         });
//     }else{

//         //Check Filter Table
//         CheckFilterTable();
        
//         //Change Placeholder Of Filter Date
//         $("input[name='filterTanggal']").attr("placeholder","Filter Tanggal Create Invoice");

//         //Adjust Width Of Filters
//         $("select[name='filterCustomer']").select2({
//         'width':'20%'
//         });
        
//         $("select[name='filterWarehouse']").select2({
//                 'width':'40%'
//         });
        
//         $("select[name='filterService']").select2({
//                 'width':'20%'
//         });

//         //Hide Filter Customer
//         $("#filterCustomer").select2().next().hide();

//         //Hide Select All
//         $("#selectMode").hide();

//         //Hide Create Invoice
//         $("#createInv").hide();
//     }

//     //Reset All Values Of Filters
//     $("select[name='filterWarehouse']").val("");
//     $("select[name='filterService']").val("");
//     $("input[name='filterTanggal']").val("");
// });

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$('input[name="tanggalJamInvoice"]').daterangepicker({
    singleDatePicker: true,
    autoApply:true,
    autoUpdateInput: false,
    forceParse: false,
    timePicker:true,
    timePicker24Hour: true,
    locale: {
        format: 'DD/MM/YYYY HH:mm'
    },
});

$('input[name="tanggalJamInvoice"]').on('cancel.daterangepicker', function (ev, picker) {
    $(this).val('');
});

$('input[name="tanggalJamInvoice"]').on('apply.daterangepicker', function (ev, picker) {
    let startDate = picker.startDate;
    console.log(startDate);
    $(this).val(startDate.format('DD/MM/YYYY HH:mm'));
});


$("input[name='tanggalInvoice']").daterangepicker({
    singleDatePicker: true,
    autoApply:false,
    autoUpdateInput: false,
    locale: {
        format: 'DD/MM/YYYY'
    },
    maxDate: moment(),
});

$("input[name='tanggalInvoice']").on('apply.daterangepicker', function (ev, picker) {
    let startDate = picker.startDate;
    $(this).val(startDate.format('DD/MM/YYYY'));
});

//If Select All
$("#selectMode").on("click",function(){
    let id = $(this).attr("data-id");

    if(id==1){
        let filterCustomer = $("#filterCustomer").val(),
        filterWarehouse = $("#filterWarehouse").val(),
        filterService = $("#filterService").val(),
        filterTanggal = $("input[name='filterTanggal']").val();

        if(filterWarehouse==""||filterTanggal==""||filterCustomer==""||filterService==""){
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
    console.log($(this).attr("data-id"));
});

$("#view").on("click",function(){
    let filterTanggal = $("input[name='filterTanggal']").val(),
        filterCustomer = $("#filterCustomer").val(),
        filterWarehouse = $("#filterWarehouse").val(),
        filterService = $("#filterService").val(),
        custTypeId = $(".btn-select").attr("id"),
        navTabId = $("#navTabOrder .nav-item .active").attr("id");

    if(navTabId=="nav-ci-tab"){
        refreshTable(tableCi,location.origin+"/shiplist/table/order/"+custTypeId+"?filterTanggal="+filterTanggal+"&filterCustomer="+filterCustomer+"&filterWarehouse="+filterWarehouse+"&filterService="+filterService,"table-ci_info");
    }else{
        CheckFilterTable();    
    }
});

$("#reset").on("click",function(){
    let custTypeId = $(".btn-select").attr("id"),
        navTabId = $("#navTabOrder .nav-item .active").attr("id");

    resetAllFilter();

    if(navTabId=="nav-ci-tab"){
        refreshTable(tableCi,location.origin+"/shiplist/table/order/"+custTypeId,"table-ci_info");
    }else{
        CheckFilterTable();    
    }

});

function resetAllFilter(){
    if($("input[name='filterTanggal']").length>0){
        $("input[name='filterTanggal']").val("");
    }

    if($("#filterCustomer").length>0){
        $("#filterCustomer").val("").trigger('change');
    }

    if($("#filterWarehouse").length>0){
        $("#filterWarehouse").val("").trigger('change');
    }

    if($("#filterService").length>0){
        $("#filterService").val("").trigger('change');
    }
}

var searchTurn = 0;
$(".modal").on("keyup","#searching",function(e){
    let num=0,
        array=[],
        modalId = $(this).closest(".modal").attr("id");

    $("#"+modalId+" #searchingScore").hide();
    if($(this).val()!==""){
        $("#"+modalId+" #searchingScore").text("0/0").show();
    }

    if(e.key==="enter" || e.keyCode===13){
        let keyword = $(this).val().toLowerCase();

        $("#"+modalId+" select:visible,#"+modalId+" input:not(#searching):visible, #"+modalId+" .searchAble").each(function(index, value) {
            // console.log($(this));
            let val="";
            
            if($(this).is("input")){
                val = $(this).val().toLowerCase();
            }
            
            if($(this).is("label")){
                val = $(this).text().toLowerCase();
            }
            
            if($(this).is("select")){
                val = $("#"+modalId+" select option[value='"+val+"']").text().toLowerCase();
            }

            if($(this).hasClass("searchAble")){
                val = $(this).text().toLowerCase();
            }

            if(val.match(keyword)){
                array[num]=$(this).attr("id");
                num++;
            }
        });

        if(num==0){
            return false;
        }

        searchTurn++;
        searchTurn>num?searchTurn=1:'';

        $("#"+modalId+" #searchingScore").text(searchTurn+"/"+num).show();

        document.querySelectorAll("#"+modalId+" #"+array[searchTurn-1])[0].scrollIntoView({block:'center',behavior:'smooth'});

        return true;
    }

    searchTurn = 0;
});

$("form").on("keypress",function(e){
    if(e.key==="enter" || e.keyCode===13){
        e.preventDefault();
    }
});

$("form").on("click","#importBtn",function(){
    let modalId = $(this).closest(".modal").attr("id");
    if($("#"+modalId+" #import").get(0).files.length === 0){
        console.log("empty");
    }else{
        let formData = new FormData();
        formData.append("_token",$("input[name='_token']").val());
        formData.append("excel",$("#"+modalId+" #import").prop("files")[0]);
        $.ajax({
            url: location.origin+"/import",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                loading(formData);
            },
            success: function(msg) {
                
            let json = JSON.parse(msg),
                a = 0;

            if(modalId!="createResi"){
                console.log("Length : "+$("#"+modalId+" .services input[name='consFirstName']").length);
                $("#"+modalId+" .services input[name='consFirstName[]']").each(function(){
                    if($(this).val()!=""){
                        a++
                    }
                });
            }

            for(let i=0;i<=json[0].length-1;i++){
                if(modalId=="createResi"){
                    if(i>2){
                        // jika cell ekspedisi kosong
                        if(json[0][i][7]==null||json[0][i][7]==""){
                            $(".preloaderz").hide();
                            Swal.fire({
                                title: "Gagal Import!",
                                text: "Nama Ekspedisi Kosong. Cell "+numToAlp(7)+(i+1)+".",
                                icon: "error"
                            });
                            return false;
                        }

                        // jika cell ekspedisi berisi selain mismass dan ekspedisi lain
                        if(json[0][i][7]!="MISMASS"&&json[0][i][7]!="JNE"&&json[0][i][7]!="J&T"){
                            $(".preloaderz").hide();
                            Swal.fire({
                                title: "Gagal Import!",
                                text: "Nama Ekspedisi Tidak Dikenal. Cell "+numToAlp(7)+(i+1)+".",
                                icon: "error"
                            });
                            return false
                        }

                        // jika cell kurir kosong
                        if(json[0][i][7]=="MISMASS"){
                            if(json[0][i][8]==""){
                            $(".preloaderz").hide();
                                Swal.fire({
                                    title: "Gagal Import!",
                                    text: "Jika Ekspedisi MISMASS, maka Cell Kurir "+numToAlp(8)+(i+1)+" Tidak Boleh Kosong",
                                    icon: "error"
                                });
                                return false
                            }
                        }

                        // jika cell no resi
                        if(json[0][i][7]!="MISMASS"){
                            if(json[0][i][9]==""){
                                $(".preloaderz").hide();
                                Swal.fire({
                                    title: "Gagal Import!",
                                    text: "Jika Ekspedisi Lain, maka Cell Resi "+numToAlp(9)+(i+1)+" Tidak Boleh Kosong",
                                    icon: "error"
                                });
                                return false;
                            }

                            // for(let a=3;a<=json[0].length-1;a++){
                            //     if(a!=i){
                            //         if(json[0][i][9]==json[0][a][9]){
                            //             Swal.fire({
                            //                 title: "Gagal Import!",
                            //                 text: "Cell Resi "+numToAlp(9)+(i+1)+" & "+numToAlp(9)+(a+1)+" Sama",
                            //                 icon: "error"
                            //             });
                            //             return false;
                            //         }
                            //     }
                            // }

                        }
                        
                        if(json[0][i][7]=="MISMASS"){
                            $("#"+modalId+" #"+(a)+" select[name='tipeForwarder[]']").val(json[0][i][7]);
                        }else{
                            $("#"+modalId+" #"+(a)+" select[name='tipeForwarder[]']").val("VENDOR");
                        }
                        $("#"+modalId+" #"+(a)+" select[name='tipeForwarder[]']").trigger("change");

                        $("#"+modalId+" #"+(a)+" select[name='namaForwarder[]']").val(json[0][i][7]);
                        if(json[0][i][7]=="MISMASS"){
                            $("#"+modalId+" #"+(a)+" input[name='namaForwarder[]']").val(json[0][i][8]);
                        }
                        
                        if(json[0][i][7]!="MISMASS"){
                            $("#"+modalId+" #"+(a)+" input[name='noResi[]']").val(json[0][i][9]);
                        }

                        a++;

                    }
                }else{
                    // console.log("Ini Adalah A:"+a);
                    if(i>1){
                        if(json[0][i][1]==null||json[0][i][1]==""){
                            $(".preloaderz").hide();
                            Swal.fire({
                                title: "Gagal Import!",
                                text: "Nama Depan Kosong. Cell "+numToAlp(1)+(i+1)+".",
                                icon: "error"
                            });
                            return false;
                        }

                        // if(json[0][i][2]==null||json[0][i][2]==""){
                        //     $(".preloaderz").hide();
                        //     Swal.fire({
                        //         title: "Gagal Import!",
                        //         text: "Nama Tengah Kosong. Cell "+numToAlp(2)+(i+1)+".",
                        //         icon: "error"
                        //     });
                        //     return false;
                        // }

                        // if(json[0][i][3]==null||json[0][i][3]==""){
                        //     $(".preloaderz").hide();
                        //     Swal.fire({
                        //         title: "Gagal Import!",
                        //         text: "Nama Terakhir Kosong. Cell "+numToAlp(3)+(i+1)+".",
                        //         icon: "error"
                        //     });
                        //     return false;
                        // }

                        // if(json[0][i][4]==null||json[0][i][4]==""){
                        //     $(".preloaderz").hide();
                        //     Swal.fire({
                        //         title: "Gagal Import!",
                        //         text: "Email Kosong. Cell "+numToAlp(4)+(i+1)+".",
                        //         icon: "error"
                        //     });
                        //     return false;
                        // }

                        if(json[0][i][5]==null||json[0][i][5]==""){
                            $(".preloaderz").hide();
                            Swal.fire({
                                title: "Gagal Import!",
                                text: "No Telp Kosong. Cell "+numToAlp(5)+(i+1)+".",
                                icon: "error"
                            });
                            return false;
                        }

                        if(json[0][i][6]==null||json[0][i][6]==""){
                            $(".preloaderz").hide();
                            Swal.fire({
                                title: "Gagal Import!",
                                text: "Alamat Kosong. Cell "+numToAlp(6)+(i+1)+".",
                                icon: "error"
                            });
                            return false;
                        }

                        // if(json[0][i][7]==null||json[0][i][7]==""){
                        //     $(".preloaderz").hide();
                        //     Swal.fire({
                        //         title: "Gagal Import!",
                        //         text: "Kelurahan Kosong. Cell "+numToAlp(7)+(i+1)+".",
                        //         icon: "error"
                        //     });
                        //     return false;
                        // }

                        // if(json[0][i][8]==null||json[0][i][8]==""){
                        //     $(".preloaderz").hide();
                        //     Swal.fire({
                        //         title: "Gagal Import!",
                        //         text: "Kecamatan Kosong. Cell "+numToAlp(8)+(i+1)+".",
                        //         icon: "error"
                        //     });
                        //     return false;
                        // }

                        if(json[0][i][9]==null||json[0][i][9]==""){
                            $(".preloaderz").hide();
                            Swal.fire({
                                title: "Gagal Import!",
                                text: "Kab/Kota Kosong. Cell "+numToAlp(9)+(i+1)+".",
                                icon: "error"
                            });
                            return false;
                        }

                        if(json[0][i][10]==null||json[0][i][10]==""){
                            $(".preloaderz").hide();
                            Swal.fire({
                                title: "Gagal Import!",
                                text: "Provinsi Kosong. Cell "+numToAlp(10)+(i+1)+".",
                                icon: "error"
                            });
                            return false;
                        }

                        if(json[0][i][11]==null||json[0][i][11]==""){
                            $(".preloaderz").hide();
                            Swal.fire({
                                title: "Gagal Import!",
                                text: "Kode Pos Kosong. Cell "+numToAlp(11)+(i+1)+".",
                                icon: "error"
                            });
                            return false;
                        }

                        if($("#"+modalId+" #"+a).length==0){
                            $("#"+modalId+" #btnAddServiceElement").trigger("click");
                        }

                        $("#"+modalId+" #"+a+" input[name='consFirstName[]']").val(json[0][i][1]);
                        $("#"+modalId+" #"+a+" input[name='consMiddleName[]']").val(json[0][i][2]);
                        $("#"+modalId+" #"+a+" input[name='consLastName[]']").val(json[0][i][3]);
                        $("#"+modalId+" #"+a+" input[name='consEmail[]']").val(json[0][i][4]);
                        $("#"+modalId+" #"+a+" input[name='consPhone[]']").val(json[0][i][5]);
                        $("#"+modalId+" #"+a+" input[name='consAddress[]']").val(json[0][i][6]);
                        $("#"+modalId+" #"+a+" input[name='consSubDistrict[]']").val(json[0][i][7]);
                        $("#"+modalId+" #"+a+" input[name='consDistrict[]']").val(json[0][i][8]);
                        $("#"+modalId+" #"+a+" input[name='consCity[]']").val(json[0][i][9]);
                        $("#"+modalId+" #"+a+" input[name='consProv[]']").val(json[0][i][10]);
                        $("#"+modalId+" #"+a+" input[name='consPostalCode[]']").val(json[0][i][11]);

                        if($("#"+modalId+" #"+a+" input[name='consFirstName[]']").val()==""){
                            $("#"+modalId+" #"+a).remove();
                        }

                        if(i<json[0].length-1){
                            $("#"+modalId+" #btnAddServiceElement").trigger("click");
                        }

                        a++;
                    }
                }
            }

            unLoading(formData);
            $("input[name='import']").val("");

            Swal.fire({
                title: "Berhasil Import!",
                text: "Data Berhasil Diimport",
                icon: "success"
            });
            }
        });
    }
});

function numToAlp(num){
    const arr = ["A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z"];
    return arr[num];
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

if($('#table-ci').length>0){
var tableCi = $('#table-ci').DataTable({
    "paging": true,
    "fixedHeader": true,
    "searching": true,
    "processing": true,
    "serverSide": true,
    "processing": true,
    "serverSide": true,
    "order": [],
    "ajax": {
        "url": location.origin+"/shiplist/table/order/IND?filterTanggal=",
        "type": "GET",
        "dataSrc": function(json){
            $("#table-ci").parent().css("overflow-x","auto");
            return json.data;
        },
        "timeout": 60000,
        error: function (xhr, error, thrown) {
            Swal.fire({
                icon: 'error',
                title: 'Koneksi bermasalah',
                text: 'Gagal memuat data. Periksa koneksi Anda.'
            }).then((result) => {
                if (result.isConfirmed) {
                    location.reload(true);
                }
            });
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
}

if($('#table-ct').length>0){
var tableCt = $('#table-ct').DataTable({
    "paging": true,
    "searching": true,
    "processing": true,
    "serverSide": true,
    "processing": true,
    "serverSide": true,
    "order": [],
    "ajax": {
        "url": location.origin+"/shiplist/table/invoice/IND?filterTanggal=&filterWarehouse=&filterService=&filterPay=DOKU&filterPayStatus=PENDING",
        "type": "GET",
        "dataSrc": function(json){
            $("#table-ct").parent().css("overflow-x","auto");
            return json.data;
        },
        "timeout": 60000,
        error: function (xhr, error, thrown) {
            Swal.fire({
                icon: 'error',
                title: 'Koneksi bermasalah',
                text: 'Gagal memuat data. Periksa koneksi Anda.'
            }).then((result) => {
                if (result.isConfirmed) {
                    location.reload(true);
                }
            });
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
}

if($('#table-status').length>0){
var tableSt = $('#table-status').DataTable({
    "paging": true,
    "searching": true,
    "processing": true,
    "serverSide": true,
    "processing": true,
    "serverSide": true,
    "order": [],
    "ajax": {
        "url": location.origin+"/shiplist/table/tracking/IND?mismassOrderId=&filterTanggal=&filterWarehouse=&filterService=&filterPay=DOKU",
        "type": "GET",
        "dataSrc": function(json){
            $("#table-status").parent().css("overflow-x","auto");
            return json.data;
        },
        "timeout": 60000,
        error: function (xhr, error, thrown) {
            Swal.fire({
                icon: 'error',
                title: 'Koneksi bermasalah',
                text: 'Gagal memuat data. Periksa koneksi Anda.'
            }).then((result) => {
                if (result.isConfirmed) {
                    location.reload(true);
                }
            });

        }
    },
    "fnDrawCallback": function( oSettings ) {
        let custTypeId = $(".btn-select").attr("id");
        custTypeId == "IND" ? $("#thResi").show() : $("#thResi").hide();
        custTypeId == "IND" ? $("#table-status tbody tr td:nth-child(4)").show() : $("#table-status tbody tr td:nth-child(4)").hide();
    },
    "columnDefs": [{
        "targets": [],
        "orderable": true,
    }],
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

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

tabEl = "<div class='col-sm-12 col-md-3 col-pay-order'>"+
    "<ul class='nav nav-pills' id='navPayOrder'>"+
        "<li class='nav-item'>"+
            "<a class='nav-link active' id='nav-doku-tab' data-toggle='tab' href='#' role='tab' aria-selected='true'>DOKU</a>"+
        "</li>"+
        "<li class='nav-item'>"+
            "<a class='nav-link' id='nav-bank-tab' data-toggle='tab' href='#' role='tab' aria-selected='false'>BANK</a>"+
        "</li>"+
    "</ul>"+
"</div>"+
"<div class='col-sm-12 col-md-4 col-doku-order'>"+
    "<ul class='nav nav-pills' id='navPayStatus'>"+
        "<li class='nav-item'>"+
            "<a class='nav-link active' id='nav-pending-tab' data-toggle='tab' href='#' role='tab' aria-selected='true'>PENDING</a>"+
        "</li>"+
        "<li class='nav-item'>"+
            "<a class='nav-link' id='nav-failed-tab' data-toggle='tab' href='#' role='tab' aria-selected='false'>FAILED</a>"+
        "</li>"+
        "<li class='nav-item'>"+
            "<a class='nav-link' id='nav-success-tab' data-toggle='tab' href='#' role='tab' aria-selected='false'>SUCCESS</a>"+
        "</li>"+
    "</ul>"+
"</div>";

$("#table-ct_length").parent(".col-md-6").removeClass("col-md-6").addClass("col-md-2");
$("#table-ct_filter").parent(".col-md-6").removeClass("col-md-6").addClass("col-md-3");


$(document).on("click","#navPayOrder li a",function(){
    let id = $(this).attr("id");
    
    if(id=="nav-doku-tab"){
        $("#navPayStatus .nav-item #nav-failed-tab").show();
    }else if(id=="nav-bank-tab"){
        $("#navPayStatus .nav-item #nav-failed-tab").hide();    
    }

    CheckFilterTable();
    getWarehouseList();
});

$(document).on("click","#navPayStatus li a, #navPayOrderStatus li a",function(){
    CheckFilterTable();
    getWarehouseList();
});

$(tabEl).insertBefore($("#table-ct_filter").parent(".col-md-3"));

tabEl2 = "<div class='col-sm-12 col-md-7 col-pay-order'>"+
    "<ul class='nav nav-pills' id='navPayOrderStatus'>"+
        "<li class='nav-item'>"+
            "<a class='nav-link active' id='nav-doku-tab' data-toggle='tab' href='#' role='tab' aria-selected='true'>DOKU</a>"+
        "</li>"+
        "<li class='nav-item'>"+
            "<a class='nav-link' id='nav-bank-tab' data-toggle='tab' href='#' role='tab' aria-selected='false'>BANK</a>"+
        "</li>"+
    "</ul>"+
"</div>";

$("#table-status_length").parent(".col-md-6").removeClass("col-md-6").addClass("col-md-2");
$("#table-status_filter").parent(".col-md-6").removeClass("col-md-6").addClass("col-md-3");

$(tabEl2).insertBefore($("#table-status_filter").parent(".col-md-3"));

function CheckFilterTable(){
    let custTypeId = $(".btn-select").attr("id"),
        navTab = $("#navTabOrder .nav-item .active").attr("id"),
        tableType="invoice",
        tableInfo="table-ct_info",
        table=tableCt,
        filterTanggal=$("input[name='filterTanggal']").val(),
        filterWarehouse=$("#filterWarehouse").val(),
        filterService=$("#filterService").val(),
        filterPayId=$("#navPayOrder .nav-item .active").attr("id"),
        filterPay = filterPayId=="nav-doku-tab"?"DOKU":"BANK",
        filterPayStatusId=$("#navPayStatus .nav-item .active").attr("id"),
        filterPayStatus = filterPayStatusId=="nav-pending-tab"?"PENDING":(filterPayStatusId=="nav-failed-tab"?(filterPayId=="nav-doku-tab"?"FAILED":"PENDING"):"SUCCESS");

        if(navTab!="nav-status-tab"&&filterPayId!="nav-doku-tab"&&filterPayStatusId=="nav-failed-tab"){
            $("#navPayStatus .nav-item .nav-link").removeClass("active");
            $("#nav-pending-tab").addClass("active");
        }

        if(navTab=="nav-status-tab"){
            tableType="tracking";
            tableInfo="table-status_info";
            table=tableSt;
            filterPayId=$("#navPayOrderStatus .nav-item .active").attr("id");
            filterPay = filterPayId=="nav-doku-tab"?"DOKU":"BANK";
            filterPayStatus="";
        }

        refreshTable(table,location.origin+"/shiplist/table/"+tableType+"/"+custTypeId+"?mismassOrderId=&filterTanggal="+filterTanggal+"&filterWarehouse="+filterWarehouse+"&filterService="+filterService+"&filterPay="+filterPay+"&filterPayStatus="+filterPayStatus,tableInfo);

}