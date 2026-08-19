<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Bootstrap -->
    <link rel="stylesheet" href="{{ url('assets/plugins/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ url('assets/plugins/select2/css/select2.min.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ url('assets/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ url('assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ url('assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ url('assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <!-- daterange picker -->
    <link rel="stylesheet" href="{{ url('assets/plugins/daterangepicker/daterangepicker.css') }}">
    <!-- sweet alert -->
    <link rel="stylesheet" href="{{ url('assets/plugins/sweetalert2/sweetalert2.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ url('assets/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ url('assets/customs/css/styleku.css?v='.date('YmdHis')) }}">
    <link rel="stylesheet" href="{{ url('assets/customs/css/custom.css?v='.date('YmdHis')) }}">
    <link rel="stylesheet" href="{{ url('assets/customs/css/newCustom.css?v='.date('YmdHis')) }}">
    <link rel="stylesheet" href="{{ url('assets/customs/css/newLoader.css?v='.date('YmdHis')) }}">
    <link rel="stylesheet" href="{{ url('assets/customs/css/scrollTop.css?v='.date('YmdHis')) }}">
    <link rel="stylesheet" href="{{ url('assets/customs/css/daterangepicker-custom.css?v='.date('YmdHis')) }}">
</head>
<body>
        <button type="button" class="btn btn-success" id="export">Export</button>
        <table id="table" style="display:none"></table>
        <div class="modal fade" data-backdrop="static" id="modalProgress" tabindex="-1" role="dialog" aria-labelledby="modalProgressLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="progress" style="height:1.7rem">
                            <div id="progressBar" class="progress-bar bg-primary" role="progressbar" style="width:100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <!-- jQuery -->
    <script src="{{ url('assets/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ url('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ url('assets/dist/js/adminlte.min.js') }}"></script>
    <!-- Scriptku -->
    <script src="{{ url('assets/customs/js/script.js?v='.env('APP_VERSION')) }}"></script>
    <script>
        $("#export").on("click",getData);

        function getData(){
            $.ajax({
                type: "GET",
                url: location.origin+"/testing",
                // data: $(form).serialize(),
                beforeSend: function() {
                    $("#modalProgress").modal("show");
                    $("#progressBar").text("Preparing Data ..");
                },
                success: function(msg) {
                    let json = JSON.parse(msg);

                    if(json.length==0){
                        Swal.fire("Gagal", "Data Tidak Ditemukan.", 'error');
                        return false;
                    }

                    json.forEach((row, index)=>{
                        console.log(row.sub_total);
                    });

                    setTable(json);
                }
            });
        }

        function setTable(json){
            element = "<tr>"+
                        "<td colspan=10><b><font size='5'>{{$title}}</font></b></td>"+
                    "</tr>";
            element = "<tr>"+
                        "<th>No</th>"+
                        "<th>No.Invoice</th>"+
                        "<th>Create Invoice By</th>"+
                        "<th>Pembayaran</th>"+
                        "<th>Create Resi By</th>"+
                        "<th>No.Resi</th>"+
                        "<th>Reference</th>"+
                        "<th>Data Customer</th>"+
                        "<th>Service</th>"+
                        "<th>Berat(Kg)</th>"+
                        "<th>Item/Box</th>"+
                        "<th>CBM</th>"+
                        "<th>CBM(kgs)</th>"+
                        "<th>Additional</th>"+
                        "<th>Diskon</th>"+
                        "<th>Jumlah</th>"+
                        "<th>Total Biaya</th>"+
                        "<th>Komisi Packer</th>"+
                        "<th>Komisi Driver</th>"+
                        "<th>Profit</th>"+
                    "</tr>";

            json.forEach(row => {
                element += "<tr>
                    <td align="center" valign=middle class="middle text-align-center">{{$num}}</td>
			        <td align="center" valign=middle class="middle text-align-center"><b>{{$d->mismass_invoice_id}}</b><br>{{Con::dateFormatIndo($d->mismass_invoice_date,3)}}<br><a href="{{url('/p').'/'.$d->mismass_invoice_link}}" target="_blank">Link Invoice</a></td>
                    <td align="center" valign=middle class="middle text-align-center"><b>{{$d->created_by}}</b><br>{{Con::dateFormatIndo($d->created_at,3)}}</td>
                    <td align="center" valign=middle class="middle text-align-center"><?php echo $d->invoice_status=="PAID" ? "<font color=green class='green'>PAID</font><br>".Con::getSuccessTime($d->mismass_invoice_id) : "<font color=red class='red'>UNPAID</font>" ?></td>
                    <td align="center" valign=middle class="middle text-align-center"><?php echo $shippingCreatedBy ?><br>{{$shippingCreatedAt}}</td>
                    <td align="center" valign=middle class="middle text-align-center">{{$forwarder}}<br><?php echo $shippingNumber ?><br>{{$shippingCreatedAt}}</td>
                    <td align="center" valign=middle class="middle text-align-center"><?php echo $reference ?><br><?php echo $custType ?></td>
                    <td align="center" valign=middle class="middle text-align-center"><b>{{$d->cons_first_name." ".$d->cons_middle_name." ".$d->cons_last_name}}</b><br>{{$d->cons_phone}}<br>{{$d->cons_district.", ".$d->cons_city}}</td>
                    <td align="center" valign=middle class="middle text-align-center">{{$d->service_name}}<br>Total {{Dev8th::getTotalServiceByInvoice($d->mismass_invoice_id)}} Invoice</td>
                    <td align="center" valign=middle class="middle text-align-center">{{round($berat,2)}}</td>
                    <td align="center" valign=middle class="middle text-align-center">{{$item}}</td>
                    <td align="center" valign=middle class="middle text-align-center">{{round($cbm,2)}}</td>
                    <td align="center" valign=middle class="middle text-align-center">{{round($cbm,2)*100}}</td>
                    <td align="center" valign=middle class="middle text-align-center">{{Con::rupiah($additional)}}</td>
                    <td align="center" valign=middle class="middle text-align-center">{{Con::rupiah($diskon)}}</td>
                    <td align="center" valign=middle class="middle text-align-center">{{Con::rupiah($jumlah)}}</td>
                    <td align="center" valign=middle class="middle text-align-center">{{Con::rupiah($subTotal)}}</td>
                    <td align="center" valign=middle class="middle text-align-center">{{Con::rupiah($komisiPacker)}}<br><?php echo $packerBy ?><br>{{$packerAt}}</td>
                    <td align="center" valign=middle class="middle text-align-center">{{Con::rupiah($komisiDriver)}}<br><?php echo $mismassDriver ?><br>{{$mismassDriverAt}}</td>
                    <td align="center" valign=middle class="middle text-align-center">{{Con::rupiah($profit)}}</td>
                "</tr>";
            });


        }

        function arrangeTable(row, index){

        }
    </script>
</body>
</html>