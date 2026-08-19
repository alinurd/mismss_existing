@php
$totalKgBulat=0;

foreach($full as $f){
$title = "Invoice ".$f->mismass_invoice_id;
$custTypeId = $f->cust_type_id;
$invoiceToName = $f->sender_first_name." ".$f->sender_middle_name." ".$f->sender_last_name;
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
$custTypeName = $f->custTypeName;
$invoiceStatus = $f->invoice_status;
$invoiceStatusColor = $f->invoice_status=="UNPAID"?"color:red":"color:green";
$dokuLink = $f->doku_link;
$bankName = $f->bank_name;
$bankAccountName = $f->bank_account_name;
$bankAccountId = $f->bank_account_id;
$shippingNumber = $f->shipping_number;
$forwarderId = $f->forwarder_id;
$forwarderName = $f->forwarder_name;
}


foreach($sum as $s){
$totalWeight = $s->totalWeight;
$totalItem = $s->totalItem;
$totalPrice = $s->totalPrice;
}
foreach($subTotalWeight as $stw){
$subTotalWeight = $stw->subTotalW;
}
foreach($subTotalItem as $sti){
$subTotalItem = $sti->subTotalI;
}
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ url('assets/plugins/fontawesome-free/css/all.min.css') }}">
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
                                <div style="font-size:40px;margin-right:70px;">Invoice</div>
                                <img style="width:70%;" src="https://barcode.tec-it.com/barcode.ashx?data={{$mismassInvoiceId}}&code=Code128&multiplebarcodes=false&translate-esc=true&unit=Fit&dpi=200&imagetype=Svg&rotation=0&color=%23212529&bgcolor=%23ffffff&codepage=Default&qunit=Mm&quiet=0" width="100%">
                            </div>
                            <div class="detil-pusat" style="margin-top:10px;">
                                <div class="bold-text">PT. Mismass Indo Group</div>
                                <div>Jl. Mangga Raya Blok Q No. 447A, Kel. Duri Kepa, Kebon Jeruk, Jakarta Barat 11510, Indonesia</div>
                                <div>Phone : +62 812-1916-7783</div>
                                <div class="bold-text">www.mismasslogistic.com</div>
                            </div>
                        </td>
                    </tr>
                </table>
                <table style="width:100%;margin-top:20px;">
                    <tr>
                        <td style="background-color: #f0f3f4;padding:20px 20px;width:100%;">
                            <div class="detil-tagihan" style="display: flex;">
                                <div style="width:50%;margin-right: 20px;">
                                    <div class="bold-text">Tagihan Kepada,</div>
                                    <div class="bold-text">{{$invoiceToName}}</div>
                                    <div>{{$invoiceToAddress}}, {{$invoiceToSubDistrict}}, {{$invoiceToDistrict}}, {{$invoiceToCity}}, {{$invoiceToProv}}, {{$invoiceToPostalCode}}</div>
                                    <div>{{$invoiceToEmail}}</div>
                                    <div>{{$invoiceToPhone}}</div>
                                </div>
                                <div style="width:50%">
                                    <div style="display: flex;">
                                        <div style="width:40%">Order Number</div>
                                        <div style="width:5%">:</div>
                                        <div class="bold-text" style="width:55%;text-align: right;">{{$dokuInvoiceId}}</div>
                                    </div>
                                    <div style="display: flex;">
                                        <div style="width:40%">No. Invoice</div>
                                        <div style="width:5%">:</div>
                                        <div class="bold-text" style="width:55%;text-align: right;">{{$mismassInvoiceId}}</div>
                                    </div>
                                    <div style="display: flex;">
                                        <div style="width:40%">Tanggal Invoice</div>
                                        <div style="width:5%">:</div>
                                        <div class="bold-text" style="width:55%;text-align: right;">{{App\Http\Controllers\Controller::dateFormatIndo($mismassInvoiceDate,1)}}</div>
                                    </div>
                                    <div style="display: flex;">
                                        <div style="width:40%">Tipe Customer</div>
                                        <div style="width:5%">:</div>
                                        <div class="bold-text" style="width:55%;text-align: right;">{{$custTypeName}}</div>
                                    </div>
                                    <div style="display: flex;">
                                        <div style="width:40%">Status</div>
                                        <div style="width:5%">:</div>
                                        <div style="width:55%;text-align: right;font-weight:bold;<?= $invoiceStatusColor ?>">{{$invoiceStatus}}</div>
                                    </div>
                                    
                                    @if($custTypeId=="IND")
                                    <div style="display: flex;">
                                        <div style="width:40%">No.Resi</div>
                                        <div style="width:5%">:</div>
                                        <div class="bold-text" style="width:55%;text-align: right;"><?php echo $forwarderId!=""?($forwarderId=="MISMASS"?"MISMASS | ".$shippingNumber:$forwarderName." | ".$shippingNumber):"-"?></div>
                                    </div>
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
                            <th style="border-top:1px solid black;border-bottom:1px solid black">Harga</th>
                            <th style="border-top:1px solid black;border-bottom:1px solid black">Berat/Item</th>
                            <th style="border-top:1px solid black;border-bottom:1px solid black;text-align:right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($full as $g)
                        @php

                        $rowspan=3;
                        $g->packing_price>0?$rowspan++:"";
                        $g->import_permit_price>0?$rowspan++:"";
                        $g->document_price>0?$rowspan++:"";
                        $g->dr_medicine_price>0?$rowspan++:"";
                        $g->insurance_item_price>0?$rowspan++:"";
                        $g->tax_item_price>0?$rowspan++:"";
                        $g->fee_item_price>0?$rowspan++:"";
                        $g->extra_cost_price>0?$rowspan++:"";
                        $totalKgBulat += App\Http\Controllers\Controller::pembulatan($g->weight);

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
                            <td class="bold-text" style="text-align: left;vertical-align:top">{{$g->service_name}}</td>
                            <td style="text-align: left;vertical-align:top">{{App\Http\Controllers\Controller::rupiah($g->service_price_per)}}</td>
                            <td style="text-align: left;vertical-align:top"><?php echo $g->length > 0 ? "<div>" . App\Http\Controllers\Controller::pembulatan($g->weight) . " Kg</div><div>Dimensi : " . $g->length . "x" . $g->width . "x" . $g->height . "</div><div>Aktual : ".$g->actual_weight." Kg</div>" : ($g->item > 0 ? $g->item . " Item" : ($g->weight > 0 ? App\Http\Controllers\Controller::pembulatan($g->weight) . " Kg" : "")) ?></td>
                            <td style="text-align: right;vertical-align:top"><?php echo $g->item > 0 ? App\Http\Controllers\Controller::rupiah($g->item * $g->service_price_per) : App\Http\Controllers\Controller::rupiah(App\Http\Controllers\Controller::pembulatan($g->weight) * $g->service_price_per) ?></td>
                        </tr>
                       
                        

                        @if($g->packing_price>0)
                        <tr style='border-top:1px solid #d5d5d5;border-bottom:1px solid #d5d5d5;'>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style='color:blue'>Packing Kayu {{empty($g->packing_desc)?"":"(".$g->packing_desc.")"}}</td>
                            <td></td>
                            <td style="text-align:right">{{App\Http\Controllers\Controller::rupiah($g->packing_price)}}</td>
                        </tr>
                        @endif

                        @if($g->import_permit_price>0)
                        <tr style='border-top:1px solid #d5d5d5;border-bottom:1px solid #d5d5d5;'>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style='color:blue'>Import Permit {{empty($g->import_permit_desc)?"":"(".$g->import_permit_desc.")"}}</td>
                            <td></td>
                            <td style="text-align:right">{{App\Http\Controllers\Controller::rupiah($g->import_permit_price)}}</td>
                        </tr>
                        @endif

                        @if($g->document_price>0)
                        <tr style='border-top:1px solid #d5d5d5;border-bottom:1px solid #d5d5d5;'>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style='color:blue'>Document {{empty($g->document_desc)?"":"(".$g->document_desc.")"}}</td>
                            <td></td>
                            <td style="text-align:right">{{App\Http\Controllers\Controller::rupiah($g->document_price)}}</td>
                        </tr>
                        @endif

                        @if($g->dr_medicine_price>0)
                        <tr style='border-top:1px solid #d5d5d5;border-bottom:1px solid #d5d5d5;'>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style='color:blue'>DR Medicine {{empty($g->dr_medicine_desc)?"":"(".$g->dr_medicine_desc.")"}}</td>
                            <td></td>
                            <td style="text-align:right">{{App\Http\Controllers\Controller::rupiah($g->dr_medicine_price)}}</td>
                        </tr>
                        @endif

                        @if($g->insurance_item_price>0)
                        <tr style='border-top:1px solid #d5d5d5;border-bottom:1px solid #d5d5d5;'>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style='color:blue'>Asuransi {{"(Harga Barang ".App\Http\Controllers\Controller::rupiah($g->insurance_item_price).")"}}</td>
                            <td></td>
                            <td style="text-align:right">{{App\Http\Controllers\Controller::rupiah($g->insurance_total)}}</td>
                        </tr>
                        @endif

                        @if($g->tax_item_price>0)
                        <tr style='border-top:1px solid #d5d5d5;border-bottom:1px solid #d5d5d5;'>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style='color:blue'>Tax {{"(Harga Barang ".App\Http\Controllers\Controller::rupiah($g->tax_item_price).")"}}</td>
                            <td></td>
                            <td style="text-align:right">{{App\Http\Controllers\Controller::rupiah($g->tax_total)}}</td>
                        </tr>
                        @endif

                        @if($g->fee_item_price>0)
                        <tr style='border-top:1px solid #d5d5d5;border-bottom:1px solid #d5d5d5;'>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style='color:blue'>Fee {{"(Harga Barang ".App\Http\Controllers\Controller::rupiah($g->fee_item_price).")"}}</td>
                            <td></td>
                            <td style="text-align:right">{{App\Http\Controllers\Controller::rupiah($g->fee_total)}}</td>
                        </tr>
                        @endif

                        @if($g->extra_cost_price>0)
                        <tr style='border-top:1px solid #d5d5d5;border-bottom:1px solid #d5d5d5;'>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style='color:blue'>Extra Ongkir <?php echo $g->extra_cost_dest!=""?"- ".$g->extra_cost_dest:""?> <?php echo $g->extra_cost_vendor_name!=""?"- ".$g->extra_cost_vendor_name:""?></td>
                            <td></td>
                            <td style="text-align:right">{{App\Http\Controllers\Controller::rupiah($g->extra_cost_price)}}</td>
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
                            <td></td>
                            <td class="bold-text" style="text-align: left;vertical-align:top">Grand Total</td>
                            <td>
                                <div>{{$totalKgBulat}} Kg</div>
                                <div>{{$totalItem}} Item</div>
                            </td>
                            <td class="bold-text">
                                <div style="text-align:right">{{App\Http\Controllers\Controller::rupiah($subTotalWeight)}}</div>
                                <div style="text-align:right">{{App\Http\Controllers\Controller::rupiah($subTotalItem)}}</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <table style="margin-top:20px">
                    <tr>
                        <td>
                            <div class="bold-text">Instruksi Pembayaran</div>
                            <div>Silahkan melakukan pembayaran sesuai dengan nominal yang tertera sebelum kami mengirimkan ke alamat Anda. Mismass Logistic Pte Ltd berhak menahan kiriman apapun jika belum ada pembayaran.</div>
                            
                            @if($dokuLink!="")
                            <div class="bold-text" style="margin-top:10px">Link Pembayaran</div>
                            <div><a href="{{$dokuLink}}" target="_blank">{{$dokuLink}}</a></div>
                            @else
                            <div class="bold-text" style="margin-top:10px">Pembayaran</div>
                            <div style='color:blue;display:flex'><div>{{$bankName}} :</div><div style="font-weight:bold;margin-left:5px">{{$bankAccountName}}</div></div>
                            <div style='color:red;display:flex'><div>No. Rekening / Virtual Account :</div><div style="font-weight:bold;margin-left:5px">{{$bankAccountId}}</div></div>
                            @endif
                            
                        </td>
                        <td style="background:#f0f3f4;padding:30px;">
                            <div style="font-size:20px;text-align: center;font-weight: 600;">Total Tagihan</div>
                            <div style="font-size:40px;font-weight:500;">{{App\Http\Controllers\Controller::rupiah($totalPrice)}}</div>
                        </td>
                    </tr>
                </table>
                <div style="width:100%;text-align:center;margin-top:20px">Untuk pertanyaan mengenai invoice, silahkan hubungi Customer Support kami di nomor telpon <br>+62812-8482-2112 / +62882-9579-8912</div>
                <div style="text-align: center;margin-top:10px"><strong>Terima kasih Untuk Bisnis Anda</strong></div>
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
</html>