// $(".dev8thFilterDate").html("<div class='row' style='justify-content:end'>"+
//                     "<div class='col-5' style='padding-left:2px;padding-right:2px'>"+
//                         "<input type='text' class='form-control' name='filterTanggal' id='filterTanggal' placeholder='Filter Tanggal' autocomplete='off'>"+
//                     "</div>"+
//                     "<div class='col fit-btn' style='padding-left:2px;padding-right:2px'>"+
//                         "<div class='dropdown'>"+
//                             "<button class='btn dropdown-toggle btn-view' type='button' id='dropdownMenu1' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Action</button>"+
//                             "<div class='dropdown-menu' aria-labelledby='dropdownMenu1'>"+
//                                 "<a id='view' class='dropdown-item' href='#!'><i class='fas fa-search'></i> Cari</a>"+
//                                 "<a id='reset' class='dropdown-item' href='#!'><i class='fas fa-sync-alt'> Reset</i></a>"+
//                             "</div>"+
//                         "</div>"+
//                     "</div>"+
//                 "</div>");
moment.locale("id");
callFilterTanggal();

function callFilterTanggal(){
    if($("input[name='filterTanggal']").length>0){

        $("input[name='filterTanggal']").daterangepicker({
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear'
            }
        });

        $("input[name='filterTanggal']").on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY') + '-' + picker.endDate.format('DD/MM/YYYY'));
        });
        
        $("input[name='filterTanggal']").on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

    }
}