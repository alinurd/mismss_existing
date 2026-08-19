<style>
    .inputPos{
        height:150px;
        overflow:auto;
        overflow-x:hidden;
    }

    .font-20{
        font-size:20px;
    }
</style>

<section class="content">
    <div class="container-fluid">
        <form id="formBulkyData">
            <div class="row row-middle">
                <div class="col-md-6 col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Bulky Data</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="inputPos mb-2"></div>
                            <div class="row">
                                <div class="col">
                                    <div class="form-group">
                                        <label for="customer">Customer</label>
                                        <select id="customer" class="form-control">
                                            <option value="">Pilih Customer</option>
                                            @foreach($customer as $c)
                                                <option value="{{$c->id}}" data-id="{{$c->id}}" data-cust-type="{{$c->cust_type_id}}" data-name="{{$c->first_name}} {{$c->middle_name}} {{$c->last_name}}">{{$c->id}} - {{$c->cust_type_id}} - {{$c->first_name}} {{$c->middle_name}} {{$c->last_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col text-right">
                                    <div class="totalBulky"></div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col text-right">
                                    <button type="button" id="add" class="btn bg-primary">Add Data</button>
                                    <button type="button" id="clear" class="btn bg-danger">Clear All</button>
                                    <button type="submit" class="btn bg-success">Bulky Data</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
reset();

$("#customer").select2({
    width:"100%"
});

$(".inputPos").on("click",".remove",function(){
    $(this).closest(".row-input").remove();

    let rowInputLength = $(".row-input").length;
    $(".totalBulky").text("Total Data "+(rowInputLength));
});

$("#add").on("click",function(){
    let custVal = $("#customer").val(),
        custId = $("#customer option[value='"+custVal+"']").attr("data-id"),
        custType = $("#customer option[value='"+custVal+"']").attr("data-cust-type"),
        custName = $("#customer option[value='"+custVal+"']").attr("data-name"),
        rowInputLength = $(".row-input").length,
        data = "<div class='row row-input font-20'>"+
                    "<div class='col'>"+
                        "<div>"+custId+" - "+custType+" - "+custName+"</div>"+
                    "</div>"+
                    "<div class='col-1'>"+
                        "<a class='pointlink text-danger fw-bold remove'>X</a>"+
                    "</div>"+
                    "<input type='hidden' name='custId[]' value='"+custVal+"'>"+
                "</div>";

    $(".inputPos").append(data);
    $(".totalBulky").text("");
    $(".totalBulky").text("Total Data "+(rowInputLength+1));
})


$("#clear").on("click",function(){
    reset();
})

$("#formBulkyData").validate({
    errorClass: "error fail-alert is-invalid",
    rules: {
        custId: {
            required: true,
        },
    },
    messages: {
        custId: {
            required: "Tidak Boleh Kosong",
        },
    },
    submitHandler: function(form) {

        let rowInputLength = $(".row-input").length;

        if(rowInputLength==0){
            Swal.fire('Gagal', 'Data Tidak Ada !!', 'error');
            return false;
        }

        Swal.fire({
            icon: 'question',
            title: 'Apakah Anda Yakin ?',
            text: 'Bulky Total Data '+rowInputLength,
            showCancelButton: true,
            reverseButtons:true,
            cancelButtonColor:"#dc3545",
            confirmButtonText: "Yakin",
            cancelButtonText: "Batal",
        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    type: "GET",
                    url: location.origin+"/tool/bulky/input",
                    data: $(form).serialize(),
                    beforeSend: function() {
                        loading(form);
                    },
                    success: function(msg) {
                        let json = JSON.parse(msg);
                        unLoading(form);
                        if (json.status == 200) {
                            Swal.fire(json.title, json.text, 'success');
                            reset();
                            return true;
                        }
                        Swal.fire(json.title, json.text, 'error');
                    }

                });

            }
        });

    }
});

function reset(){
    $(".inputPos").html("");
    $(".totalBulky").text("");
    $(".totalBulky").text("Total Data 0");
}
</script>