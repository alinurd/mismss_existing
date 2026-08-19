<style>
    .content{
        height: calc(100vh - 50px);
        display: flex;
        align-items: center;
    }

    /*.btn-primary{
        background-color: var(--driver-primary-bgcolor);
        color: var(--driver-dark-textcolor);
        border-color: var(--driver-dark-textcolor);
    }*/
    
    .logout.my-3.p-2, .gantipass.my-3.p-2 {
    background: #ffc107 !important;
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

.row-middle {
    align-items: flex-start;
}
</style>

<section class="content">
    <div class="container-fluid">
        <div class="row row-middle">
            <div class="col">
                    <form id="formBackupDriver">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Rekap Data</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="tipeCustomer">Tipe Customer</label>
                                            <select name="tipeCustomer" id="tipeCustomer" class="form-control" required>
                                                <option value="" hidden>Pilih Tipe Customer</option>
                                                <option value="IND">Individual</option>
                                                <option value="COR">Corporate</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Tanggal</label>
                                            <div class="form-input" style="display:flex;align-items:center">
                                                <input type="text" class="form-control" id="tanggalAwal" name="tanggalAwal" required>
                                                <span class="date-span"><i class="fas fa-arrow-right"></i></span>
                                                <input type="text" class="form-control" id="tanggalAkhir" name="tanggalAkhir" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col text-right">
                                        <button type="submit" class="btn btn-primary">Download Excel</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
            </div>
        </div>
    </div>
</section>

<script>
$(".qrcode").show();
$(".action").html("<i class='fas fa-home'></i>");
$(".action").attr("data-url","{{url('/d/m/l')}}");

$('form #tanggalAwal').daterangepicker({
    drops:'up',
    singleDatePicker: true,
    showDropdowns: true,
    autoApply:true,
    locale: {
        format: 'DD-MM-YYYY'
    }
});

$('form #tanggalAkhir').daterangepicker({
    drops:'up',
    singleDatePicker: true,
    showDropdowns: true,
    autoApply:true,
    locale: {
        format: 'DD-MM-YYYY'
    }
});

$('form #tanggalAwal').on('apply.daterangepicker', function() {
    minDate = $('form #tanggalAwal').val();
    $('form #tanggalAkhir').val(minDate);

    $('form #tanggalAkhir').daterangepicker({
        drops:'up',
        singleDatePicker: true,
        showDropdowns: true,
        autoApply:true,
        minDate: minDate,
        locale: {
            format: 'DD-MM-YYYY'
        }
    });
});

$('form #tanggalAwal').on('hide.daterangepicker', function() {
    minDate = $('form #tanggalAwal').val();
    $('form #tanggalAkhir').val(minDate);

    $('form #tanggalAkhir').daterangepicker({
        drops:'up',
        singleDatePicker: true,
        showDropdowns: true,
        autoApply:true,
        minDate: minDate,
        locale: {
            format: 'DD-MM-YYYY'
        }
    });
});
$("#formBackupDriver").validate({
    errorClass: "error fail-alert is-invalid",
    messages: {
        tipeCustomer: "Pilih Salah Satu",
    },
    submitHandler: function(form) {

        let tipeCustomer = $("#"+form.id+" #tipeCustomer").val(),
            tanggalAwal = $("#"+form.id+" #tanggalAwal").val(),
            tanggalAkhir = $("#"+form.id+" #tanggalAkhir").val(),
            data = {'_token':'{{csrf_token()}}','tipeCustomer':tipeCustomer,'tanggalAwal':tanggalAwal,'tanggalAkhir':tanggalAkhir}; 

        $.ajax({
            type: 'POST',
            data: data,
            url: location.origin+"/d/m/e/a",
            success: function(msg){
                let json = JSON.parse(msg);

                if(json.status==200){
                    window.open(
                        location.origin+'/d/m/e/a/'+json.random,
                        '_blank'
                    );
                }
            }
        });

        $("#"+form.id+" #tipeCustomer").val("");
    }
});
</script>