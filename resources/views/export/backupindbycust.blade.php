@php

$num=1;
$mismassInvoice="";

header("Content-Disposition: attachment; filename=".$title.".xls");
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");

@endphp
<style>
    .top{
        vertical-align: top;
    }
    .middle{
        vertical-align: middle;
    }
    .text-align-center{
        text-align: center;
    }
    .text-align-right{
        text-align: right;
    }
    .bg-grey{
        background-color: #efefef;
    }
    .bg-yellow{
        background-color: #ffff00;
    }
    .bg-green{
        background-color: #34a853;
    }
    .green{
        color:green;
    }
    .red{
        color:red;
    }
</style>

<table>
    <tr>
        <center style='font-weight:bold'><font size=5>{{$title}}</font></center>
    </tr>
    <tr>
        <center style='font-weight:bold'><font size=4>(REFERENCE : <?php echo $reference!=""?App\Http\Controllers\Controller::getReferenceFullName($reference):"-"?>)</font></center>
    </tr>
    <tr>
        <td height=17></td>
    </tr>
    <tr>
        <th align=left>Total Biaya</th>
        <th>:</th>
        <th align=left>{{App\Http\Controllers\Controller::rupiah($totalAll)}}</th>
    </tr>
    <tr>
        <th align=left>Total Diskon</th>
        <th>:</th>
        <th align=left>{{App\Http\Controllers\Controller::rupiah($totalDiskon)}}</th>
    </tr>
    <tr>
        <th align=left>Total Profit</th>
        <th>:</th>
        <th align=left>{{App\Http\Controllers\Controller::rupiah($totalFix)}}</th>
    </tr>
    <tr>
        <td height=17></td>
    </tr>
    <tr>
        <th align=left colspan=7><font size="4"><b>Data Shipment Periode {{$tanggalTitle}} <font color=green>(Berdasarkan Tanggal Create Invoice)</font></b></th>
    </tr>
</table>
<table cellspacing="0" border="1">
    <tr>
        <th>No</th>
        <th>No.Invoice</th>
        <th>No.Resi</th>
        <th colspan=2>Kontak & Alamat Penerima</th>
        <th>Admin</th>
        <th>Status</th>
        <th>Pembayaran</th>
    </tr>

    @foreach($list as $l)
    @if($l->mismass_invoice_id!=$mismassInvoice)
    @php
        $forwarder = $l->forwarder_id!="" ? ($l->forwarder_id=="PICK-UP"? "PICKUP SENDIRI" : $l->forwarder_id." (".$l->forwarder_name.")") : "-";
        $shipping_number = $l->shipping_number!="" ? $l->shipping_number : "-";
        $shipping_created_at = $l->shipping_number!="" ? date("d-m-Y H:i:s",strtotime($l->shipping_created_at)) : "-";
    @endphp
    <tr>
        <td align="center" valign=middle class="middle text-align-center">{{$num}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{$l->mismass_invoice_id}}<br>{{date("d-m-Y",strtotime($l->created_at))}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{$forwarder}}<br>{{$shipping_number}}<br>{{date("d-m-Y H:i:s",strtotime($shipping_created_at))}}</td>
        <td colspan=2 align="center" valign=middle class="middle text-align-center">'{{$l->cons_phone}}<br>{{$l->cons_address.", ".$l->cons_sub_district.", ".$l->cons_district.", ".$l->cons_city.", ".$l->cons_prov.", ".$l->cons_postal_code}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{$l->created_by}}</td>
        <td align="center" valign=middle class="middle text-align-center"><?php echo $l->invoice_status=="PAID" ? App\Http\Controllers\Controller::getSuccessTime($l->mismass_invoice_id,1) : "<font color=red class='red'>UNPAID</font>" ?></td>
        <td align="center" valign=middle class="middle text-align-center"><?php echo $l->doku_invoice_id!="" ? 'DOKU' : $l->bank_name?> <br> <?php echo $l->doku_link!=""? $l->doku_link : $l->bank_account_id.'<br>'.$l->bank_account_name ?></td>
    </tr>

    <tr>
        <th>No</th>
        <th>Warehouse</th>
        <th colspan="3">Service</th>
        <th colspan="2">Berat</th>
        <th>Jumlah (Rp)</th>
    </tr>

    @php
        $num2 = 1;
        $subTotal = 0;
    @endphp
    @foreach($list as $a)
        @if($l->mismass_invoice_id==$a->mismass_invoice_id)
            @php
                $totalPricePer = $a->item > 0 ? $a->item * $a->service_price_per : ($a->cbm > 0 ? $a->cbm * $a->service_price_per : App\Http\Controllers\Controller::pembulatan($a->weight) * $a->service_price_per);
            @endphp
        <tr>
            <td rowspan="6" align="center" valign=middle class="middle text-align-center">{{$num2}}</td>
            <td rowspan="3" align="center" align=middle class="middle text-align-center">{{$a->wareid}}<br>{{$a->warename}}<br>{{$a->wareloc}}</td>
            <td rowspan="3" colspan="3" align="center" valign=middle class="middle text-align-center">{{$a->service_name}}</td>
            <td rowspan="3" colspan="2" align="center" valign=middle class="middle text-align-center">{{round($a->weight,2)}} Kg<br>{{$a->item}} Item<br>{{round($a->cbm,2)}} CBM</td>
            <td rowspan="3" align="right" valign=middle class="middle text-align-right">{{App\Http\Controllers\Controller::rupiah($totalPricePer)}}</td>
        </tr>
        <tr></tr>
        <tr></tr>
        <tr>
            <th colspan="6" align="center" align=middle class="middle text-align-center">Discount</th>
            <td align="right" align=middle class="middle text-align-right"><?php echo App\Http\Controllers\Controller::rupiah($a->discount)?></td>
        </tr>
        <tr>
            <th colspan="6" align="center" align=middle class="middle text-align-center">Total Additional Service</th>
            <td align="right" align=middle class="middle text-align-right"><?php echo App\Http\Controllers\Controller::rupiah($a->packing_total+$a->import_permit_total+$a->document_total+$a->dr_medicine_total+$a->insurance_total+$a->fee_total+$a->tax_total+$a->extra_cost_price)?></td>
        </tr>
        <tr>
            <th bgcolor="#efefef" class="bg-grey" colspan="6" align="center" align=middle class="middle text-align-center">Sub Total</th>
            <th bgcolor="#efefef" class="bg-grey" align="right" align=middle class="middle text-align-right">{{App\Http\Controllers\Controller::rupiah($a->sub_total)}}</th>
        </tr>
        @php
            $num2++;
            $subTotal+=$a->sub_total;
        @endphp
        @endif
    @endforeach

    <tr>
        <th bgcolor="#ffff00" class="bg-yellow"></th>
        <th bgcolor="#ffff00" class="bg-yellow" colspan="6" align="center" align=middle class="middle text-align-center">Total Biaya</th>
        <th bgcolor="#ffff00" class="bg-yellow" align="right" align=middle class="middle text-align-right">{{App\Http\Controllers\Controller::rupiah($subTotal)}}</th>
    </tr>

    <tr>
        <td colspan="7" align="center" align=middle class="middle text-align-center"><a href="{{'https://print.'.env('APP_URL').'/p/'.$l->mismass_invoice_link}}">Link Invoice : {{'https://print.'.env('APP_URL').'/p/'.$l->mismass_invoice_link}}</a></td>
    </tr>

    <tr>
        <td bgcolor="#34a853" class="bg-green" colspan="8"></td>
    </tr>

    @php
        $num++;
        $mismassInvoice=$l->mismass_invoice_id;
    @endphp
    @endif
    @endforeach

</table>    