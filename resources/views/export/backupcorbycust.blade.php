@php

$num=1;
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
            <th align=left colspan=14><font size="4"><b>Data Shipment Periode {{$tanggalTitle}} <font color=green>(Berdasarkan Tanggal Create Invoice)</font></b></th>
        </tr>
    </table>
    <table cellspacing="0" border="1">
		<tr>
			<th>No</th>
			<th>No.Invoice</th>
			<th colspan=3>Kontak & Alamat Pengirim</th>
			<th>Admin</th>
			<th>Status</th>
			<th colspan=2>Pembayaran</th>
            <th bgcolor="#ffff00" class="bg-yellow">Total Diskon</th>
            <th bgcolor="#ffff00" class="bg-yellow">Total</th>
            <th bgcolor="#ffff00" class="bg-yellow">Total Pembayaran</th>
		</tr>

        @foreach($list as $l)

        <tr>
            <td align="center" valign=middle class="middle text-align-center">{{$num}}</td>
            <td align="center" class="text-align-center">{{$l->mismass_invoice_id}}<br>{{App\Http\Controllers\Controller::dateFormatIndo($l->created_at,1)}}</td>
            <td colspan=3 align="center" class="text-align-center">{{$l->sender_phone}}<br>{{$l->sender_address.", ".$l->sender_sub_district.", ".$l->sender_district.", ".$l->sender_city.", ".$l->sender_prov.", ".$l->sender_postal_code}}</td>
            <td align="center" valign=middle class="middle text-align-center">{{$l->created_by}}</td>
            <td align="center" valign=middle class="middle text-align-center"><?php echo $l->invoice_status=="PAID" ? App\Http\Controllers\Controller::getSuccessTime($l->mismass_invoice_id,1) : "<font color=red class='red'>UNPAID</font>"  ?></td>
            <td align="center" colspan=2 class="text-align-center"><?php echo $l->doku_invoice_id!="" ? 'DOKU' : $l->bank_name?><br><?php echo $l->doku_link!=""? $l->doku_link : $l->bank_account_id.'<br>'.$l->bank_account_name ?></td>
            <td align="center" valign=middle class="middle text-align-center">{{App\Http\Controllers\Controller::rupiah(App\Http\Controllers\Controller::hitungDiskonCust($l->cust_id,2,$l->mismass_invoice_id))}}</td>
            <td align="center" valign=middle class="middle text-align-center">{{App\Http\Controllers\Controller::hitungBeratCust($l->cust_id,2,$l->mismass_invoice_id)}} Kg<br>{{App\Http\Controllers\Controller::hitungItemCust($l->cust_id,2,$l->mismass_invoice_id)}} Item<br>{{App\Http\Controllers\Controller::hitungCbmCust($l->cust_id,2,$l->mismass_invoice_id)}} CBM</td>
            <td align="center" valign=middle class="middle text-align-center">{{App\Http\Controllers\Controller::rupiah(App\Http\Controllers\Controller::hitungLabaCust($l->cust_id,2,$l->mismass_invoice_id))}}</td>
        </tr>
        @if($corType=="ALL")
		<tr>
			<th>No</th>
			<th>Penerima</th>
			<th>Kontak & Alamat Penerima</th>
			<th>No. Resi</th>
			<th>Warehouse</th>
			<th colspan="5">Service</th>
			<th>Berat</th>
			<th>Jumlah</th>
		</tr>

        @php
            $num2=1;
        @endphp

        @foreach($list2 as $dl)
        @if($l->mismass_invoice_id==$dl->mismass_invoice_id)
        @php
            $forwarder = $dl->forwarder_id!="" ? ($dl->forwarder_id=="PICK-UP"?"PICK-UP SENDIRI":$dl->forwarder_name) : "-" ;
            $totalPricePer = $dl->item > 0 ? $dl->item * $dl->service_price_per : ($dl->cbm > 0 ? $dl->cbm * $dl->service_price_per : App\Http\Controllers\Controller::pembulatan($dl->weight) * $dl->service_price_per);
        @endphp
        <tr>
            <td rowspan="6" align="center" valign=middle class="middle text-align-center">{{$num2}}</td>
            <td rowspan="3" align="center" valign=middle class="middle text-align-center">{{$dl->cons_first_name." ".$dl->cons_middle_name." ".$dl->cons_last_name}}</td>
            <td rowspan="3" align="center" valign=middle class="middle text-align-center">{{$dl->cons_phone}}<br>{{$dl->cons_address.", ".$dl->cons_sub_district.", ".$dl->cons_district.", ".$dl->cons_city.", ".$dl->cons_prov.", ".$dl->cons_postal_code}}</td>
            <td rowspan="3" align="center" valign=middle class="text-align-center">{{$forwarder}}<br>{{$dl->shipping_number}}<br>{{App\Http\Controllers\Controller::dateFormatIndo($dl->shipping_created_at,2)}}</td>
            <td rowspan="3" align="center" valign=middle class="text-align-center">{{$dl->wareid}}<br>{{$dl->warename}}<br>{{$dl->wareloc}}</td>
            <td rowspan="3" colspan="5" align="center" valign=middle class="middle text-align-center">{{$dl->service_name}}</td>
            <td rowspan="3" align="center" valign=middle class="middle text-align-center">{{round($dl->weight,2)}} Kg<br>{{$dl->item}} Item<br>{{round($dl->cbm,2)}} CBM</td>
            <td rowspan="3" align="right" valign=middle class="middle text-align-right">{{App\Http\Controllers\Controller::rupiah($totalPricePer)}}</td>
        </tr>
        <tr></tr>
        <tr></tr>
        <tr>
            <th colspan="10" align="center" align=middle class="middle text-align-center">Discount</th>
            <td align="right" align=middle class="middle text-align-right"><?php echo App\Http\Controllers\Controller::rupiah($dl->discount)?></td>
        </tr>
        <tr>
            <th colspan="10" align="center" class="text-align-center">Total Additional Service</th>
            <td align="right" class="text-align-right"><?php echo App\Http\Controllers\Controller::rupiah($dl->packing_total+$dl->import_permit_total+$dl->document_total+$dl->dr_medicine_total+$dl->insurance_total+$dl->fee_total+$dl->tax_total+$dl->extra_cost_price)?></td>
        </tr>
        <tr bgcolor="#efefef" class="bg-grey">
            <th colspan="10" align="center" class="text-align-center">Sub Total</th>
            <th align="right" class="text-align-right">{{App\Http\Controllers\Controller::rupiah($dl->sub_total)}}</th>
        </tr>
        @php
            $num2++;
        @endphp
        @endif
        @endforeach

        @endif

        <tr>
            <td colspan="12" align="center" class="text-align-center"><a href="{{'https://print.'.env('APP_URL').'/p/'.$l->mismass_invoice_link}}">Link Invoice : {{'https://print.'.env('APP_URL').'/p/'.$l->mismass_invoice_link}}</a></td>
        </tr>

        <tr bgcolor="#34a853" class="bg-green">
            <td colspan="12"></td>
        </tr>

        @php
            $num++;
        @endphp
        @endforeach

    </table>    