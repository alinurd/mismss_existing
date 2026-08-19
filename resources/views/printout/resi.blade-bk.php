<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/x-icon" href="https://app-mismass.com/assets/dist/pic/favicon.ico">
    <title>Resi Cetak</title>
    <link rel="stylesheet" href="{{ url('assets/customs/css/styleku.css?v='.date('YmdHis')) }}">
    <link rel="stylesheet" href="{{ url('assets/customs/css/custom.css?v='.date('YmdHis')) }}">
    <link rel="stylesheet" href="{{ url('assets/plugins/fontawesome-free/css/all.min.css') }}">
</head>
<style>

    html {
        background-color: grey;
    }
    
    body {
        margin: 0;
        padding: 0;
        font-family: "Source Sans Pro",-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol";
    }

    .container {
        display: flex;
        height: 100vh;
        justify-content: center;
        background-color: grey;
        margin-top:25px;
    }

    .wrapper {}

    .printout {
        width: 500px;
        height: auto;
        background-color: white;
        padding: 10px;
    }

    table,
    tr,
    td {
        border: 1px solid black;
        border-collapse: collapse;
        padding: 20px;
        width: 100%;
    }

    .fixed-bottom {
        background-color: white;
        width: 100%;
        position: fixed;
        bottom: 0;
    }

    .btn-group {
        display: flex;
        justify-content: end;
    }

    .btn-share {
        color: #fff;
        background: #6c757d linear-gradient(180deg, #828a91, #6c757d) repeat-x !important;
        border-color: none !important;
        border: none !important;
        width: 110px;
        border-radius: 7px;
        line-height: 35px;
        margin: 10px;
        cursor: pointer;
    }

    .btn-cetak {
        background: #ffc107 linear-gradient(180deg, #ffd665, #ffc107) repeat-x !important;
        color: black;
        border: none !important;
        line-height: 35px;
        width: 110px;
        border-radius: 7px;
        margin: 10px;
        cursor: pointer;
    }

    @page {
        size: auto;
        margin: 0.3cm;
    }

    @media print {

        html,
        body {
            margin: 0;
            padding: 0;
            background-color: white;
        }
        
        .container {
            margin:0px;
            background: #fff;
            align-items: start;
        }
        
        .printout {
            padding:0px;
            width:100%;
        }

        .wrapper {
            padding: 0;
            margin-top: 0px;
        }

        .fixed-bottom {
            display: none;
        }
    }
</style>

<body>
    <div class="container">
        <div class="wrapper">
        @for($i=0;$i<=count($get)-1;$i++)
        @php
        foreach($get[$i] as $g){
        $mismassInvoiceId = $g->mismass_invoice_id;
        $dokuInvoiceId = $g->doku_invoice_id;
        $forwarderId = $g->forwarder_id;
        $shippingNumberStats = $g->shipping_number_stats;
        $msTrackId = $g->ms_track_id;
        $shippingNumber = $g->shipping_number;
        $shippingNumberNoPrefix = preg_replace('/TR\/ALY\//i', '', $g->shipping_number);
        $forwarderName = $g->forwarder_name;
        $custTypeId = $g->cust_type_id;
        $custTypeName = $g->custTypeName;
        $tanggalResi = $g->updated_at;
        $penerima = $g->cons_first_name." ".$g->cons_middle_name." ".$g->cons_last_name;
        $alamat = $g->cons_address.", ".$g->cons_sub_district.", ".$g->cons_district.", ".$g->cons_city.", ".$g->cons_prov.", ".$g->cons_postal_code;
        $phone = $g->cons_phone;
        $pengirim = $g->sender_first_name." ".$g->sender_middle_name." ".$g->sender_last_name;
        $secPhone = $g->sender_phone;
        $payment = $g->doku_invoice_id!=""?$g->doku_invoice_id:$g->mismass_invoice_id;
        $invoice = $forwarderId=="MISMASS"?$mismassInvoiceId:$payment;
        }

        $getForwarder = function ($array) {
            if ($array['forwarderId'] === "MISMASS" || $array['forwarderId'] === "PICK-UP") {
                return $array['forwarderId'];
            }
            if ($array['forwarderId'] === "VENDOR") {
                if($array['forwarderName'] === "SENTRAL CARGO" || $array['forwarderName'] === "JNE"){
                    return "MISMASS";
                }
            }
            return $array['forwarderName'];
        };

        $forwarderShow = $getForwarder([
            'forwarderId' => $forwarderId,
            'forwarderName' => $forwarderName
        ]);

        $getShippingNumber = function ($array) {
            if($array['custTypeId']=="IND"){
                if($array['forwarderId']=="VENDOR"){
                    if($array['forwarderName']==="SENTRAL CARGO" || $array['forwarderName'] === "JNE"){
                        return $array['msTrackId'];
                    }
                    return $array['shippingNumber'];
                }
                return $array['msTrackId'];
            }
            return $array['shippingNumber'];
        };

        $shippingNumberShow = $getShippingNumber([
            'custTypeId' => $custTypeId,
            'forwarderId' => $forwarderId,
            'forwarderName' => $forwarderName,
            'msTrackId' => $msTrackId,
            'shippingNumber' => $shippingNumber
        ]);
        @endphp
            <div class="printout">
                <div class="element">
                    <table style="border-bottom:none;">
                        <tr style="text-align: center;border-bottom:none;">
                            <td style="padding:10px 20px;width:40%;border-bottom:none;"><img src="{{url('assets/dist/pic/logo-circle.png')}}" width="70%"></td>
                            <td style="padding:10px 20px;border-bottom:none;"><img class="barcode" style="width:260px"/></td>
                            <!-- <td style="padding:10px 20px;border-bottom:none;"><img src="https://barcode.tec-it.com/barcode.ashx?data={{$shippingNumberShow}}&code=Code128&multiplebarcodes=false&translate-esc=true&unit=Fit&dpi=200&imagetype=jpg&rotation=0&color=%23212529&bgcolor=%23ffffff&codepage=Default&qunit=Mm&quiet=0" width="100%"></td> -->
                        </tr>
                    </table>
                    <table style="border-bottom:none;">
                        <tr style="text-align: center;border-bottom:none;">
                            <td style="width:40%;font-size:25px;font-weight:700;border-bottom:none;">{{$forwarderShow}}</td>
                            <td style="width:60%;font-size:20px;border-bottom:none;"><?php echo $shippingNumberStats ? "ID" : "Resi" ?> : <div style="display: inline;font-weight:700">{{$shippingNumberShow}}</div>
                            </td>
                        </tr>
                    </table>
                    <table style="border-bottom:none;">
                        <tr style="border-bottom:none;">
                            <td style="border-bottom:none;">
                                <div style="display: flex;">
                                    <div style="width:20%">Tanggal Resi</div>
                                    <div style="width:5%">:</div>
                                    <div>{{App\Http\Controllers\Controller::dateFormatIndo($tanggalResi)}}</div>
                                </div>
                                <div style="display: flex;font-weight:700">
                                    <div style="width:20%">Penerima</div>
                                    <div style="width:5%">:</div>
                                    <div>{{$penerima}}</div>
                                </div>
                                <div style="display: flex;">
                                    <div style="width:20%">No. Telpon</div>
                                    <div style="width:5%">:</div>
                                    <div>{{$phone}}</div>
                                </div>
                                <div style="display: flex;">
                                    <div style="width:20%">Alamat</div>
                                    <div style="width:5%">:</div>
                                    <div style="width:75%">{{$alamat}}</div>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <table>
                        <tr>
                            <td style="width:40%;padding:10px 20px;">
                                <div class="qrcode" style="text-align:center;text-align:-webkit-center">
                                <!--<img src="https://barcode.tec-it.com/barcode.ashx?data={{$shippingNumberNoPrefix}}&code=MobileQRCode&multiplebarcodes=false&translate-esc=false&unit=Fit&dpi=200&imagetype=jpg&rotation=0&color=%23000000&bgcolor=%23ffffff&codepage=Default&qunit=Mm&quiet=0&eclevel=L" style="width:80%">-->
                                </div>
                            </td>
                            <td style="width:60%;padding:20px 10px;">
                                <div style="display:flex">
                                    <div style="width:40%">No. Referensi</div>
                                    <div style="width:5%">:</div>
                                    <div style="width:55%">{{$invoice}}</div>
                                </div>
                                <div style="display:flex">
                                    <div style="width:40%">Pengirim</div>
                                    <div style="width:5%">:</div>
                                    <div style="width:55%">{{$pengirim}}</div>
                                </div>
                                <div style="display:flex">
                                    <div style="width:40%">No. Telp</div>
                                    <div style="width:5%">:</div>
                                    <div style="width:55%">{{$secPhone}}</div>
                                </div>
                                <div style="margin-top:10px;text-align:center;font-size:20px;font-weight:bold">{{$custTypeName}}</div>
                                <div style="margin-top:10px;text-align:center;">www.mismasslogistic.com</div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        @endfor
        </div>
    </div>
    <div class="fixed-bottom">
        <div class="btn-group">
            <button class="btn-share"><i class="fas fa-share-alt"></i> Bagikan</button>
            <button class="btn-cetak" onclick="window.print()"><i class="fas fa-print"></i> Cetak</button>
        </div>
    </div>
</body>
<script src="{{ url('assets/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ url('assets/plugins/barcode.js') }}"></script>
<script src="{{ url('assets/plugins/qrcode-maker/qrcode.js') }}"></script>
<script>
    $(".barcode").JsBarcode("{{$shippingNumberShow}}");

    var length = $(".qrcode").length;
    for(let i = 0;i<length;i++){
    var qrcode = new QRCode(document.getElementsByClassName("qrcode")[i], {
        text: "{{$shippingNumberNoPrefix}}",
        width: 130,
        height: 130,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
    });
    }
    
</script>
</html>