@php
$totalKgBulat=0;
$totalCbmBulat=0;

foreach($template as $t){
    $companyName = $t->name;
    $companyPhone = $t->phone;
    $companyAddress = $t->address;
}

foreach($full as $f){
$title = "E-Invoice ".$f->mismass_invoice_id;
$custTypeId = $f->cust_type_id;
$firstN = $f->sender_first_name;
$middleN = $f->sender_middle_name!=""?" ".$f->sender_middle_name:"";
$lastN = $f->sender_last_name!=""?" ".$f->sender_last_name:"";
$invoiceToName = $firstN.$middleN.$lastN;
$invoiceToAddress = $f->sender_address;
$invoiceToSubDistrict = $f->sender_sub_district;
$invoiceToDistrict = $f->sender_district;
$invoiceToCity = $f->sender_district;
$invoiceToProv = $f->sender_prov;
$invoiceToPostalCode = $f->sender_postal_code;
$invoiceToEmail = $f->sender_email;
$invoiceToPhone = $f->sender_phone;
if($custTypeId=="IND"){
    $invoiceToName = $f->cons_first_name." ".$f->cons_middle_name." ".$f->cons_last_name;
    $invoiceToAddress = $f->cons_address;
    $invoiceToSubDistrict = $f->cons_sub_district;
    $invoiceToDistrict = $f->cons_district;
    $invoiceToCity = $f->cons_district;
    $invoiceToProv = $f->cons_prov;
    $invoiceToPostalCode = $f->cons_postal_code;
    $invoiceToEmail = $f->cons_email;
    $invoiceToPhone = $f->cons_phone;
}
$dokuInvoiceId = $f->doku_invoice_id!=""?$f->doku_invoice_id:"-";
$mismassInvoiceId = $f->mismass_invoice_id;
$mismassInvoiceDate = $f->mismass_invoice_date;
$invoiceCreatedAt = $f->created_at;
$mismassInvoiceLink = $f->mismass_invoice_link;
$custTypeName = $f->custTypeName;
$invoiceStatus = $f->invoice_status;
$invoiceStatusColor = $f->invoice_status=="UNPAID"?"color:red":"color:green";
$invoicePaymentAt = $f->payment_success_at;
$invoicePaymentAutoAt = $f->payment_success_auto_at;
$dokuLink = $f->doku_link;
$bankName = $f->bank_name;
$bankAccountName = $f->bank_account_name;
$bankAccountId = $f->bank_account_id;
$shippingNumber = $f->shipping_number;
$forwarderId = $f->forwarder_id;
$forwarderName = $f->forwarder_name;
$fc_symbol = $f->fc_symbol;
$fc_value = $f->fc_value;
}


foreach($sum as $s){
$totalWeight = $s->totalWeight;
$totalItem = $s->totalItem;
$totalCbm = $s->totalCbm;
$totalPrice = $s->totalPrice;
$adjustFee = $s->adjustFee;
}
foreach($subTotalWeight as $stw){
$subTotalWeight = $stw->subTotalW;
}
foreach($subTotalItem as $sti){
$subTotalItem = $sti->subTotalI;
}
foreach($subTotalCbm as $cb){
$subTotalCbm = $cb->subTotalC;
}

$forwarder = $forwarderId!="" ? ($forwarderId=="VENDOR" ? $forwarderName." | ".$shippingNumber : $forwarderId." | ".$shippingNumber ) : "-";
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ url('assets/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ url('assets/plugins/sweetalert2/sweetalert2.min.css') }}">
    <link rel="icon" type="image/x-icon" href="https://app-mismass.com/assets/dist/pic/favicon.ico">
    <title>{{$title}}</title>
</head>
<style>
    html {
        background: grey;
    }

    body {
        margin: 0;
        padding: 0;
        font-family: "Source Sans Pro",-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol";
    }

    .container {
        display: flex;
        height: calc(100vh);
        justify-content: center;
        /*align-items: center;*/
        background-color: grey;
        margin-top: 30px;
        margin-bottom: 200px;
    }

    .wrapper {}

    .printout {
        width: 800px;
        height: auto;
        background-color: white;
        padding: 10px 10px 60px 10px;
        zoom: 80%;
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
        border-top: 1px solid #828a9157;
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

    .bold-text {
        font-weight: bold;
    }
    
    table.total tr th{
        padding:4px 0px;
    }
    table.total {
        /*font-size:14px;*/
        border-collapse: collapse; 
    }
    table.total tr td{
        padding:10px 0px;
        /*border:1px dotted blue;*/
    }

    
    @media (max-width:500px) {
        .printout {
            zoom:45%;
        }
            
        .btn-group {
            display: flex;
            justify-content: center;
        }
        
        .container {
            margin-top: 0px;
            margin-bottom: 0px;
            background: #fff;
        }
        
        .btn-share, .btn-cetak {
            width:100%;
        }
    }

    @page {
        size: auto;
        margin: 0.5cm;
    }

    @media print {

        html,
        body {
            margin: 0;
            padding: 0;
            background:white;
        }

        .wrapper {
            padding: 0;
            margin-top: 0px;
        }

        .fixed-bottom {
            display: none;
        }
        
        .container {
            background-color: white;
            margin-top:0px;
        }
        
        .printout {
            zoom:95%;
        }
    }
</style>

<body>
    <div class="container">
        <div class="wrapper">
            <div class="printout">
                <table>
                    <tr>
                        <td style="width:50%"><img style="width:55%;" src="{{url('assets/dist/pic/logo-circle.png')}}"></td>
                        <td style="width:50%;text-align:right">
                            <div class="barcode">
                                <div style="font-size:40px;margin-right:50px;">E-Invoice</div>
                                <img id="barcode" style="width:265px"/>
                                <!--<img style="width:70%;" src="https://barcode.tec-it.com/barcode.ashx?data={{$mismassInvoiceId}}&code=Code128&multiplebarcodes=false&translate-esc=true&unit=Fit&dpi=200&imagetype=Svg&rotation=0&color=%23212529&bgcolor=%23ffffff&codepage=Default&qunit=Mm&quiet=0" width="100%">-->
                            </div>
                            <div class="detil-pusat" style="margin-top:10px;">
                                <div class="bold-text">{{$companyName}}</div>
                                <div>{{$companyAddress}}</div>
                                <div>Phone : {{$companyPhone}}</div>
                                <div class="bold-text">www.mismasslogistic.com</div>
                                <!-- <div class="bold-text">Mismass Logistic Pte Ltd</div>
                                <div>Eunos Techpark Lobby B 60 Kaki Bukit Place #06-01A Singapore 415979</div>
                                <div>Phone : +62 812-1916-7783</div>
                                <div class="bold-text">www.mismasslogistic.com</div> -->
                                <!-- <div class="bold-text">PT. Mismass Indo Group</div>
                                <div>Jl. Mangga Raya Blok Q No. 447A. Kel. Duri Kepa, Kebon Jeruk, Jakarta Barat 11510, Indonesia</div>
                                <div>Phone : +62 812-1916-7783</div>
                                <div class="bold-text">www.mismasslogistic.com</div> -->
                            </div>
                        </td>
                    </tr>
                </table>
                <table style="width:100%;margin-top:20px;">
                    <tr>
                        <td style="background-color: #f0f3f4;padding:20px 20px;width:100%;">
                            <div class="detil-tagihan" style="display: flex;">
                                <div style="width:50%;margin-right: 20px;">
                                    <div class="bold-text">Bill To:</div>
                                    <div class="bold-text">{{$invoiceToName}}</div>
                                    <div>{{$invoiceToAddress}}, {{$invoiceToSubDistrict}}, {{$invoiceToDistrict}}, {{$invoiceToCity}}, {{$invoiceToProv}}, {{$invoiceToPostalCode}}</div>
                                    <div>{{$invoiceToEmail}}</div>
                                    <div>{{$invoiceToPhone}}</div>
                                </div>
                                <div style="width:50%">
                                    <!-- <div style="display: flex;">
                                        <div style="width:40%">Order Number</div>
                                        <div style="width:5%">:</div>
                                        <div class="bold-text" style="width:55%;text-align: right;">{{$dokuInvoiceId}}</div>
                                    </div> -->
                                    <div style="display: flex;">
                                        <div style="width:40%">No. Invoice</div>
                                        <div style="width:5%">:</div>
                                        <div class="bold-text" style="width:55%;text-align: right;">{{$mismassInvoiceId}}</div>
                                    </div>
                                    <div style="display: flex;">
                                        <div style="width:40%">Invoice Date</div>
                                        <div style="width:5%">:</div>
                                        <div class="bold-text" style="width:55%;text-align: right;">{{App\Http\Controllers\Controller::dateFormatIndo($mismassInvoiceDate,1)}}</div>
                                    </div>
                                    <div style="display: flex;">
                                        <div style="width:40%">Tipe Customer</div>
                                        <div style="width:5%">:</div>
                                        <div class="bold-text" style="width:55%;text-align: right;">{{$custTypeName}}</div>
                                    </div>
                                    <div style="display: flex;">
                                        <div style="width:40%">Payment Status</div>
                                        <div style="width:5%">:</div>
                                        @if($invoiceStatus=="PAID")
                                            @php
                                                $paymentAt = $invoicePaymentAt;
                                                if($invoicePaymentAt=="0000-00-00 00:00:00"){
                                                    $paymentAt = $invoicePaymentAutoAt;
                                                }
                                            @endphp
                                            <div style='width:55%;justify-content: flex-end;gap:2px;display:flex'><div style='font-weight:bold;color:green'>{{$invoiceStatus}} </div><div>|</div><div> {{App\Http\Controllers\Controller::dateFormatIndo($paymentAt,1)}}</div></div>
                                        @else
                                            <div style='width:55%;text-align: right;font-weight:bold;color:red'>{{$invoiceStatus}}</div>
                                        @endif
                                    </div>
                                     <div style="display: flex;">
                                        <div style="width:40%">Create Invoice</div>
                                        <div style="width:5%">:</div>
                                        <div style="width:55%;text-align: right;">{{App\Http\Controllers\Controller::dateFormatIndo($invoiceCreatedAt,1)}}</div>
                                    </div>
                                    <div style="display: flex;">
                                        <div style="width:40%">Tracking Number</div>
                                        <div style="width:5%">:</div>
                                        <div class="bold-text" style="width:55%;text-align: right;">{{$mstrack}}</div>
                                    </div>
                                    
                                    @if($custTypeId=="IND")
                                    <!-- <div style="display: flex;">
                                        <div style="width:40%">No.Resi</div>
                                        <div style="width:5%">:</div>
                                        <div class="bold-text" style="width:55%;text-align: right;">{{$forwarder}}</div>
                                    </div> -->
                                    @endif
                                    
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
                
                <table width="100%" class="total" style="line-height: 22px;margin-top:20px;border-spacing: 0;">
                    <thead>
                        <tr style="text-align:left">
                            @if($custTypeId=="COR")
                            <th style="border-top:1px solid black;border-bottom:1px solid black">Penerima</th>
                            @endif
                            <th style="border-top:1px solid black;border-bottom:1px solid black" <?php echo $custTypeId=="IND" ? "colspan='2'" : "" ?>>Warehouse</th>
                            <th style="border-top:1px solid black;border-bottom:1px solid black">Service</th>
                            <th style="border-top:1px solid black;border-bottom:1px solid black">Price</th>
                            <th style="border-top:1px solid black;border-bottom:1px solid black">Weight</th>
                            <th style="border-top:1px solid black;border-bottom:1px solid black;text-align:right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($full as $g)
                        @php

                        $rowspan=3;
                        $g->discount>0?$rowspan++:"";
                        $g->packing>0?$rowspan++:"";
                        $g->import_permit>0?$rowspan++:"";
                        $g->document>0?$rowspan++:"";
                        $g->dr_medicine>0?$rowspan++:"";
                        $g->insurance_item_price>0?$rowspan++:"";
                        $g->tax_item_price>0?$rowspan++:"";
                        $g->fee_item_price>0?$rowspan++:"";
                        $g->extra_cost_price>0?$rowspan++:"";
                        $g->pickup_weight>0?$rowspan++:"";
                        $totalKgBulat += App\Http\Controllers\Controller::pembulatan($g->weight);
                        $totalCbmBulat += number_format((float)$g->cbm, 2, '.', '');

                        $jumlahDimensi = "";
                        $jumlahActualWeight = "";
                        $jumlahBarang = "-";
                        $totalPricePer = "-";
                        if($g->item>0){
                            if($fc_symbol==""){
                                $totalPricePer = App\Http\Controllers\Controller::rupiah($g->item * $g->service_price_per);
                            }else{
                                $totalPricePer = "S$ ".round($g->item*$g->service_price_per/$fc_value,2);
                            }
                            $jumlahBarang = $g->item . " Item";
                        }elseif($g->weight>0){
                            if($fc_symbol==""){
                                $totalPricePer = App\Http\Controllers\Controller::rupiah(App\Http\Controllers\Controller::pembulatan($g->weight) * $g->service_price_per);
                            }else{
                                $totalPricePer = "S$ ".round(App\Http\Controllers\Controller::pembulatan($g->weight) * $g->service_price_per/$fc_value,2);
                            }
                            $jumlahBarang = App\Http\Controllers\Controller::pembulatan($g->weight) . " Kg";
                            if($g->length>0){
                                $jumlahBarang = App\Http\Controllers\Controller::pembulatan($g->weight) . " Kg";
                                $jumlahDimensi = "Dimensi : " . $g->length . "x" . $g->width . "x" . $g->height;
                                $jumlahActualWeight = "Aktual : ".$g->actual_weight." Kg";
                            }
                        }elseif($g->cbm>0){
                            if($fc_symbol==""){
                                $totalPricePer = App\Http\Controllers\Controller::rupiah($g->cbm * $g->service_price_per);
                            }else{
                                $totalPricePer = "S$ ".round($g->cbm * $g->service_price_per/$fc_value,2);
                            }
                            $jumlahBarang = number_format((float)$g->cbm, 2, ',', '') . " CBM";
                        }

                        $actualWeight = $g->weight>0&&$g->length==0?"Actual Weight : ".number_format((float)$g->weight, 2, ',', '') . " Kg":"";
                        $servName = $g->service_price_per==0?"-":$g->servName;
                        $servicePricePer = $g->service_price_per==0?"-":($fc_symbol==""? App\Http\Controllers\Controller::rupiah($g->service_price_per) : "S$ ".round($g->service_price_per/$fc_value,2));

                        @endphp
                        
                        
                        <tr style="border-top:1px solid #d5d5d5;">
                            @if($custTypeId=="COR")
                            <td style="text-align: left;vertical-align:top;">
                                <div class="bold-text">{{$g->cons_first_name." ".$g->cons_middle_name." ".$g->cons_last_name}}</div>
                                <div>{{$g->cons_city}}</div>
                                <div>{{$g->cons_phone}}</div>
                            </td>
                            @endif

                            <td style="text-align: left;vertical-align:top;" <?php echo $custTypeId=="IND" ? "colspan='2'" : "" ?>>
                                <div class="bold-text">{{$g->warehouse_id}}</div>
                                <div>{{$g->wareName}}</div>
                                <div>{{$g->wareLoc}}</div>
                            </td>
                            <td style="text-align: left;vertical-align:top">
                                <div class="bold-text">{{$servName}}</div>
                                <div>{{$actualWeight}}</div>
                            </td>
                            <td style="text-align: left;vertical-align:top">{{$servicePricePer}}</td>
                            <td style="text-align: left;vertical-align:top"><div>{{$jumlahBarang}}</div><div>{{$jumlahDimensi}}</div><div>{{$jumlahActualWeight}}</div></td>
                            <td style="text-align: right;vertical-align:top">{{$totalPricePer}}</td>
                        </tr>

                        @if($g->discount>0)
                        <tr style='border-top:1px solid #d5d5d5;border-bottom:1px solid #d5d5d5;'>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style='color:red'>Discount</td>
                            <td></td>
                            <td style="text-align:right"><?php echo $fc_symbol==""? App\Http\Controllers\Controller::rupiah($g->discount) : "S$ ".round($g->discount/$fc_value,2)?></td>
                        </tr>
                        @endif

                        @if($g->packing>0)
                        <tr style='border-top:1px solid #d5d5d5;border-bottom:1px solid #d5d5d5;'>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style='color:blue'>Packing Kayu {{$g->packing}} Item</td>
                            <td></td>
                            <td style="text-align:right"><?php echo $fc_symbol==""? App\Http\Controllers\Controller::rupiah($g->packing_total) : "S$ ".round($g->packing_total/$fc_value,2)?></td>
                        </tr>
                        @endif

                        @if($g->import_permit>0)
                        <tr style='border-top:1px solid #d5d5d5;border-bottom:1px solid #d5d5d5;'>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style='color:blue'>Import Permit {{$g->import_permit}} Item</td>
                            <td></td>
                            <td style="text-align:right"><?php echo $fc_symbol==""? App\Http\Controllers\Controller::rupiah($g->import_permit_total) : "S$ ".round($g->import_permit_total/$fc_value,2)?></td>
                        </tr>
                        @endif

                        @if($g->export_permit>0)
                        <tr style='border-top:1px solid #d5d5d5;border-bottom:1px solid #d5d5d5;'>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style='color:blue'>Export Permit {{$g->export_permit}} Item</td>
                            <td></td>
                            <td style="text-align:right"><?php echo $fc_symbol==""? App\Http\Controllers\Controller::rupiah($g->export_permit_total) : "S$ ".round($g->export_permit_total/$fc_value,2)?></td>
                        </tr>
                        @endif

                        @if($g->document>0)
                        <tr style='border-top:1px solid #d5d5d5;border-bottom:1px solid #d5d5d5;'>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style='color:blue'>Document {{$g->document}} Item</td>
                            <td></td>
                            <td style="text-align:right"><?php echo $fc_symbol==""? App\Http\Controllers\Controller::rupiah($g->document_total) : "S$ ".round($g->document_total/$fc_value,2)?></td>
                        </tr>
                        @endif

                        @if($g->dr_medicine>0)
                        <tr style='border-top:1px solid #d5d5d5;border-bottom:1px solid #d5d5d5;'>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style='color:blue'>DR Medicine {{$g->dr_medicine}} Item</td>
                            <td></td>
                            <td style="text-align:right"><?php echo $fc_symbol==""? App\Http\Controllers\Controller::rupiah($g->dr_medicine_total) : "S$ ".round($g->dr_medicine_total/$fc_value,2)?></td>
                        </tr>
                        @endif

                        @if($g->insurance_item_price>0)
                        <tr style='border-top:1px solid #d5d5d5;border-bottom:1px solid #d5d5d5;'>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style='color:blue'>Asuransi {{"(Harga Barang ".($fc_symbol==""? App\Http\Controllers\Controller::rupiah($g->insurance_item_price) : "S$ ".round($g->insurance_item_price/$fc_value,2)).")"}}</td>
                            <td></td>
                            <td style="text-align:right"><?php echo $fc_symbol==""? App\Http\Controllers\Controller::rupiah($g->insurance_total) : "S$ ".round($g->insurance_total/$fc_value,2)?></td>
                        </tr>
                        @endif

                        @if($g->tax_item_price>0)
                        <tr style='border-top:1px solid #d5d5d5;border-bottom:1px solid #d5d5d5;'>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style='color:blue'>Tax {{"(Harga Barang ".($fc_symbol==""? App\Http\Controllers\Controller::rupiah($g->tax_item_price) : "S$ ".round($g->tax_item_price/$fc_value,2)).")"}}</td>
                            <td></td>
                            <td style="text-align:right"><?php echo $fc_symbol==""? App\Http\Controllers\Controller::rupiah($g->tax_total) : "S$ ".round($g->tax_total/$fc_value,2)?></td>
                        </tr>
                        @endif

                        @if($g->fee_item_price>0)
                        <tr style='border-top:1px solid #d5d5d5;border-bottom:1px solid #d5d5d5;'>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style='color:blue'>Fee {{"(Harga Barang ".($fc_symbol==""? App\Http\Controllers\Controller::rupiah($g->fee_item_price) : "S$ ".round($g->fee_item_price/$fc_value,2)).")"}}</td>
                            <td></td>
                            <td style="text-align:right"><?php echo $fc_symbol==""? App\Http\Controllers\Controller::rupiah($g->fee_total) : "S$ ".round($g->fee_total/$fc_value,2)?></td>
                        </tr>
                        @endif

                        @if($g->extra_cost_price>0)
                        <tr style='border-top:1px solid #d5d5d5;border-bottom:1px solid #d5d5d5;'>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style='color:blue'>Extra Ongkir <?php echo $g->extra_cost_dest!=""?"- ".$g->extra_cost_dest:""?> <?php echo $g->extra_cost_vendor_name!=""?"- ".$g->extra_cost_vendor_name:""?> <?php echo $g->extra_cost_shipping_number!=""?"- ".$g->extra_cost_shipping_number:""?></td>
                            <td></td>
                            <td style="text-align:right"><?php echo $fc_symbol==""? App\Http\Controllers\Controller::rupiah($g->extra_cost_price) : "S$ ".round($g->extra_cost_price/$fc_value,2)?></td>
                        </tr>
                        @endif

                        @if($g->pickup_weight>0)
                        <tr style='border-top:1px solid #d5d5d5;border-bottom:1px solid #d5d5d5;'>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style='color:blue'>Pickup Weight {{$g->pickup_weight}} Kg</td>
                            <td></td>
                            <td style="text-align:right"><?php echo $fc_symbol==""? App\Http\Controllers\Controller::rupiah($g->pickup_charge) : "S$ ".round($g->pickup_charge/$fc_value,2)?></td>
                        </tr>
                        @endif

                        @if($g->other_pickup_fee>0)
                        <tr style='border-top:1px solid #d5d5d5;border-bottom:1px solid #d5d5d5;'>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style='color:blue'>Pickup Fee</td>
                            <td></td>
                            <td style="text-align:right"><?php echo $fc_symbol==""? App\Http\Controllers\Controller::rupiah($g->other_pickup_fee) : "S$ ".round($g->other_pickup_fee/$fc_value,2)?></td>
                        </tr>
                        @endif
                        
                        @if($g->additional_nom>0)
                        <tr style='border-top:1px solid #d5d5d5;border-bottom:1px solid #d5d5d5;'>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style='color:blue'>Additonal : {{$g->additional_desc}}</td>
                            <td></td>
                            <td style="text-align:right"><?php echo $fc_symbol==""? App\Http\Controllers\Controller::rupiah($g->additional_nom) : "S$ ".round($g->additional_nom/$fc_value,2)?></td>
                        </tr>
                        @endif
                        <!--<tr style="border-top:1px solid #d5d5d5;border-bottom:1px solid black;">-->
                        <!--    <td></td>-->
                        <!--    <td></td>-->
                        <!--    <td></td>-->
                        <!--    <td></td>-->
                        <!--    <td class="bold-text">Sub Total</td>-->
                        <!--    <td class="bold-text" style="text-align:right;">{{App\Http\Controllers\Controller::rupiah($g->sub_total)}}</td>-->
                        <!--</tr>-->
                        <tr style='border-top:1px solid #d5d5d5;border-bottom:1px solid black;'></tr>
                        @endforeach
                        <tr>
                            @if($custTypeId=="COR")
                            <td></td>
                            @endif
                            <td <?php echo $custTypeId=="IND" ? "colspan='2'" : "" ?>></td>
                            <td class="bold-text" style="text-align: left;vertical-align:top">Grand Total</td>
                            <td colspan=2 style="vertical-align:middle"> 
                                <div>{{$totalKgBulat}} Kg</div>
                                <div>{{$totalItem}} Item</div>
                                <div>{{str_replace(".",",", (string)$totalCbmBulat)}} CBM</div>
                                @if($adjustFee>0)
                                    <div>Operasional Adjustment Fee</div>
                                @endif
                            </td>
                            <td class="bold-text">
                                <?php
                                    $labelSubTotalWeight = $servicePricePer=="-" ? "-" : ($fc_symbol==""? App\Http\Controllers\Controller::rupiah($subTotalWeight) : "S$ ".round($subTotalWeight/$fc_value,2));
                                    $labelSubTotalItem = $servicePricePer=="-" ? "-" : ($fc_symbol==""? App\Http\Controllers\Controller::rupiah($subTotalItem) : "S$ ".round($subTotalItem/$fc_value,2));
                                    $labelSubTotalCbm = $servicePricePer=="-" ? "-" : ($fc_symbol==""? App\Http\Controllers\Controller::rupiah($subTotalCbm) : "S$ ".round($subTotalCbm/$fc_value,2));
                                ?>
                                <div style="text-align:right"><?php echo $labelSubTotalWeight;?></div>
                                <div style="text-align:right"><?php echo $labelSubTotalItem;?></div>
                                <div style="text-align:right"><?php echo $labelSubTotalCbm;?></div>
                                @if($adjustFee>0)
                                    <div style="text-align:right"><?php echo $fc_symbol==""? App\Http\Controllers\Controller::rupiah($adjustFee) : "S$ ".round($adjustFee/$fc_value,2)?></div>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <table style="margin-top:20px">
                    <tr>
                        <td>
                            <div class="bold-text">Payment Instruction</div>
                            <div>Please make payment at your earliest convenience before we dispatch to your address. We reserves the right to hold any shipment if payment has not been made.</div>
                            
                            @if($dokuLink!="")
                            <div class="bold-text" style="margin-top:10px">Link Payment</div>
                            <div class="bold-text"><a href="{{(preg_match("/doku.com/i", $dokuLink)?$dokuLink:url('payment')."/".$dokuLink)}}" target="_blank">Click Here To Make Payment Process!</a></div>
                            @else
                            <div class="bold-text" style="margin-top:10px">Payment</div>
                            <div style='color:blue;display:flex'><div>{{$bankName}} :</div><div style="font-weight:bold;margin-left:5px">{{$bankAccountName}}</div></div>
                            <div style='color:red;display:flex'><div>No. Rekening / Virtual Account :</div><div style="font-weight:bold;margin-left:5px">{{$bankAccountId}}</div></div>
                            @endif
                            
                        </td>
                        <td style="background:#f0f3f4;padding:30px;">
                            <div style="font-size:20px;text-align: center;font-weight: 600;">Total Bill</div>
                            <div style="font-size:40px;font-weight:500;text-wrap:nowrap"><?php echo $fc_symbol==""? App\Http\Controllers\Controller::rupiah($totalPrice+$adjustFee) : "S$ ".round(($totalPrice+$adjustFee)/$fc_value,2)?></div>
                        </td>
                    </tr>
                </table>
                <table style="margin-top:20px">
                    <tr>
                        <td>
                            <div style="width:70%">Scan here to check the authenticity of the invoice. This document is valid and
effective without a signature because it is issued electronically.</div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div id="qrcode" style="margin-top:10px"></div>
                        </td>
                    </tr>
                </table>
                <!--<div style="width:100%;text-align:center;margin-top:20px">Untuk pertanyaan mengenai invoice, silahkan hubungi Customer Support kami di nomor telpon <br>+62812-8482-2112 / +62882-9579-8912</div>-->
                <div style="text-align: center"><strong>Thank you for your trust with MISMASS.</strong><br>Your satisfaction is really important to us. We look forward to serve you again</div>
            </div>
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
<script src="{{ url('assets/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ url('assets/plugins/barcode.js') }}"></script>
<script src="{{ url('assets/plugins/qrcode-maker/qrcode.js') }}"></script>
<script>
    $("#barcode").JsBarcode("{{$mismassInvoiceId}}");
    
    var qrcode = new QRCode(document.getElementById("qrcode"), {
        // text: "{{(preg_match('/doku.com/i', $dokuLink)?$dokuLink:url('payment')."/".$dokuLink)}}",
        text: "{{'https://print.app-mismass.com/p/'.$mismassInvoiceLink}}",
        width: 160,
        height: 160,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
    });
    
    let err,fullN="{{$invoiceToName}}";
    const shareData = {
        title: 'Mismass Invoice {{$mismassInvoiceId}}',
        text: 'Hello *'+fullN.trim()+'* \n \n Below are the details of your invoice, please click the link below! \n',
        url: "{{'https://print.app-mismass.com/p/'.$mismassInvoiceLink}}",
    },
    btn = document.querySelector('button'),
    resultPara = document.querySelector('.result');

    btn.addEventListener('click', async () => {
        try {
            await navigator.share(shareData);
            resultPara.textContent = shareData['title']+' shared successfully';
        } catch (err) {
            console.log(err);
            resultPara.textContent = `Error: ${err}`;
        }
    });

    showInvoiceAlert("{{$mismassInvoiceId}}");

    function showInvoiceAlert(invoiceId) {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian',
            html: `
                <p>
                    Pembayaran invoice melalui Bank<br>
                    <strong>
                        (WAJIB mencantumkan nomor invoice pada kolom berita acara/keterangan transfer.)
                    </strong>
                    <br><br>
                    Hal ini bertujuan supaya pembayaran dapat dengan cepat terverifikasi sehingga 
                    proses pengiriman bisa dilakukan lebih cepat. Terima kasih.
                    <br><br>
                    <b>Nomor Invoice:</b>
                    <span id="nomorInvoice">${invoiceId}</span>
                </p>
            `,
            showCancelButton: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            backdrop: true,
            confirmButtonText: 'Copy No. Invoice',
            cancelButtonText: 'Tutup',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#aaa'
        }).then((result) => {
            if (result.isConfirmed) {
                const text = document.getElementById('nomorInvoice').innerText;
                navigator.clipboard.writeText(text).then(() => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Nomor Invoice berhasil disalin',
                        timer: 1500,
                        showConfirmButton: false
                    });
                });
            }
        });
    }
</script>
</html>