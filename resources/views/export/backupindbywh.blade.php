@php

use App\Http\Controllers\Controller as Con;
use App\Http\Controllers\KomisiController as Komisi;
use App\Models\Afunction as Dev8th;

header("Content-Disposition: attachment; filename=".$title.".xls");
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");

$num=1;
$totalPendapatan=0;
$totalSGD=0;
$totalRp=0;
$totalKomisiPacker=0;
$totalBerat=0;
$totalItem=0;
$totalCbm=0;
$totalPaid=0;
$totalUnpaid=0;
$totalKomisiDriver=0;
$totalDiskon=0;

foreach($list as $l){
	$berat = Dev8th::getBeratByInvoice($l->mismass_invoice_id);
	$item = Dev8th::getItemByInvoice($l->mismass_invoice_id);
	$cbm = Dev8th::getCbmByInvoice($l->mismass_invoice_id);
	$additional = Dev8th::getAdditionalByInvoice($l->mismass_invoice_id);
	$diskon = Dev8th::getDiskonByInvoice($l->mismass_invoice_id);
	$subTotal = Dev8th::getSubTotalByInvoice($l->mismass_invoice_id);
	$jumlah = $subTotal-$additional+$diskon;

	$komisiPacker = Komisi::getKomisiPacker($berat);
	$komisiDriver = Komisi::getKomisiDriver($berat,$l->cust_type_id);

	$totalPendapatan+=$subTotal;
	$totalSGD += $l->fc_symbol=="SGD" ? ($subTotal/$l->fc_value) : 0;
	$totalRp += $l->fc_symbol=="" ? $subTotal : 0 ;
	$totalPaid+=$l->invoice_status=="PAID"?$subTotal:0;
	$totalUnpaid+=$l->invoice_status=="UNPAID"?$subTotal:0;
	$totalKomisiPacker+=$l->packing_created_by!=""?$komisiPacker:0;
	$totalKomisiDriver+=$l->forwarder_id=="MISMASS"?($l->shipping_status=="SUKSES"?$komisiDriver:0):0;
	$totalDiskon+=$diskon;
	$totalItem+=$item;
	$totalBerat+=$berat;
	$totalCbm+=$cbm;
}

$totalProfit=$totalPaid-$totalKomisiDriver-$totalKomisiPacker;

@endphp
    <style>
		table{
			font-family: "Times New Roman", Times, serif;
		}
        .middle{
            vertical-align: middle;
        }
        .text-align-left{
            text-align: left;
        }
        .text-align-center{
            text-align: center;
        }
        .text-align-right{
            text-align: right;
        }
    </style>
	<table>
		<tr>
			<td colspan=10><b><font size="5">{{$title}}</font></b></td>
		</tr>
        <tr>
            <td height="17"></td>
        </tr>
        <tr>
		    <td>Total Pendapatan</td>
		    <td>: <b>{{Con::rupiah($totalPendapatan)}}</b></td>
		    <td>Total Komisi Packer</td>
			<td>: <b>{{Con::rupiah($totalKomisiPacker)}}</b></td>
			<td>Total Berat Actual (Kg)</td>
			<td align=left>: <b>{{round($totalBerat,2)}}</b></td>
			<td>Total Profit</td>
			<td>: <b>{{Con::rupiah($totalProfit)}}</b></td>
		</tr>	
		<tr>
		    <td>Total PAID</td>
		    <td>: <b>{{Con::rupiah($totalPaid)}}</b></td>
		    <td>Total Komisi Driver</td>
		    <td>: <b>{{Con::rupiah($totalKomisiDriver)}}</b></td>
		    <td>Total Berat Pembulatan (Kg)</td>
			<td align=left>: <b>{{Con::pembulatan($totalBerat)}}</b></td>
		    <td colspan=2 rowspan=3><i><font size=2>Profit sudah dikurangi UNPAID dan Diskon, belum dikurangi Komisi AE dan biaya lainnya.</font></i></td>
		</tr>
		<tr>
		    <td>Total UNPAID</td>
		    <td>: <b>{{Con::rupiah($totalUnpaid)}}</b></td>
			<td></td>
			<td></td>
		    <td>Total Item/Box</td>
		    <td align=left>: <b>{{$totalItem}}</b></td>
		</tr>
		<tr>
		    <td>Total Diskon</td>
		    <td>: <b>{{Con::rupiah($totalDiskon)}}</b></td>
			<td></td>
			<td></td>
		    <td>Total CBM</td>
		    <td align=left>: <b>{{round($totalCbm,2)}}</b></td>
		</tr>
		<tr>
		    <td></td>
		    <td></td>
			<td></td>
			<td></td>
		    <td>Total CBM (kgs)</td>
		    <td align=left>: <b>{{round($totalCbm,2)*100}}</b></td>
		</tr>
        <tr>
            <td height="17"></td>
        </tr>
        <tr>
            <td colspan=10><b>Total Pendapatan Dalam Mata Uang (Bruto)</b></td>
        </tr>
		<tr>
		    <td>Rupiah</td>
		    <td>: <b>{{Con::rupiah($totalRp)}}</b></td>
		</tr>
		<tr>
		    <td>Dollar Singapore</td>
		    <td>: <b>{{Con::dollarSG($totalSGD)}}</b></td>
		</tr>
        <tr>
            <td height="17"></td>
        </tr>
		<tr>
			<td colspan=10><b><font size="4">Data Shipment Periode {{$tanggalTitle}} <font color=green>(Berdasarkan Tanggal Invoice Otomatis)</font></font></b></td>
		</tr>
	</table>
	<table cellspacing=0 border=1>
		<tr>
			<th>No</th>
			<th>No.Invoice</th>
			<th>Create Invoice By</th>
			<th>Pembayaran</th>
			<th>Create Resi By</th>
			<th>No.Resi</th>
			<th>Reference</th>
			<th>Data Customer</th>
			<th>Service</th>
			<th>Harga Satuan</th>
			<th>Berat Actual (Kg)</th>
			<th>Berat (Kg)</th>
			<th>Item/Box</th>
			<th>CBM</th>
			<th>CBM(kgs)</th>
			<th>Additional</th>
			<th>Diskon</th>
			<th>Jumlah</th>
			<th>Total Biaya</th>
			<th>Total SGD</th>
			<th>Komisi Packer</th>
			<th>Komisi Driver</th>
			<th>Profit</th>
		</tr>
		@foreach($list as $d)
		@php
			$forwarder = $d->forwarder_id!="" ? ( $d->forwarder_id=="PICK-UP" ? "PICKUP SENDIRI" : ( $d->forwarder_id=="MISMASS" ? $d->forwarder_id : $d->forwarder_name )) : "-";
			$shippingNumber = $d->shipping_number!="" ? "<b>".$d->shipping_number."</b>" : "-";
			$shippingCreatedBy = $d->shipping_created_at!="0000-00-00 00:00:00" ? "<b>".$d->shipping_created_by."</b>" : "-";
			$shippingCreatedAt = $d->shipping_created_at!="0000-00-00 00:00:00" ? Con::dateFormatIndo($d->shipping_created_at,3) : "-"; 
			
			$reference = $d->reference!=0 ? "<b>".Con::getReferenceFullName($d->reference)."</b>" : "-";
			$custType = "<b>".($d->jumlahkirim>1 ? "<font color=red>Customer : OC</font>" : "<font color=green>Customer : NC</font>")."</b>";

			$berat = Con::pembulatan(Dev8th::getBeratByInvoice($d->mismass_invoice_id));
			$beratActual = Dev8th::getBeratByInvoice($d->mismass_invoice_id);
			$item = Dev8th::getItemByInvoice($d->mismass_invoice_id);
			$cbm = Dev8th::getCbmByInvoice($d->mismass_invoice_id);
			$additional = Dev8th::getAdditionalByInvoice($d->mismass_invoice_id);
			$diskon = Dev8th::getDiskonByInvoice($d->mismass_invoice_id);
			$subTotal = Dev8th::getSubTotalByInvoice($d->mismass_invoice_id);
			$subTotalSGD = "-";
			if($d->fc_symbol=="SGD"){
				$subTotalSGD = Con::dollarSG($subTotal/$d->fc_value);
			}
			$jumlah = $subTotal-$additional+$diskon;
			
			$komisiPacker = $d->packing_created_by!=""?Komisi::getKomisiPacker($berat):0;
			$packerBy = $d->packing_created_by!=""?"<font color=green>Checked By ".$d->packing_created_by."</font>":"-";
			$packerAt = $d->packing_created_by!=""?Con::dateFormatIndo($d->packing_created_at,3):"-";

			$komisiDriver = $d->shipping_status=="SUKSES"?Komisi::getKomisiDriver($berat,$d->cust_type_id):0;
			
			$mismassDriver = $d->forwarder_id=="MISMASS"?($d->shipping_status=="SUKSES"?"<font color=green>Selesai By ".$d->shipping_success_by."</font>":($d->forwarder_name!=''?"<font color=red>On Proses By ".$d->shipping_updated_by."</font>":"-")):"-";
			$shippingSuccessAt = $d->shipping_success_at=="0000-00-00 00:00:00"?$d->shipping_updated_at:$d->shipping_success_at;
			$mismassDriverAt = $d->forwarder_id=="MISMASS"?($d->shipping_status=="SUKSES"?Con::dateFormatIndo($shippingSuccessAt,3):Con::dateFormatIndo($d->shipping_updated_at,3)):"-";
			$profit = $subTotal-$komisiPacker-$komisiDriver;
		@endphp
		<tr>
			<td align="center" valign=middle class="middle text-align-center">{{$num}}</td>
			<td align="center" valign=middle class="middle text-align-center"><b>{{$d->mismass_invoice_id}}</b><br>{{Con::dateFormatIndo($d->created_at,3)}}<br><a href="{{url('/p').'/'.$d->mismass_invoice_link}}" target="_blank">Link Invoice</a></td>
			<td align="center" valign=middle class="middle text-align-center"><b>{{$d->created_by}}</b><br>{{Con::dateFormatIndo($d->created_at,3)}}</td>
			<td align="center" valign=middle class="middle text-align-center"><?php echo $d->invoice_status=="PAID" ? "<font color=green class='green'>PAID</font><br>".Con::getSuccessTime($d->mismass_invoice_id) : "<font color=red class='red'>UNPAID</font>" ?></td>
			<td align="center" valign=middle class="middle text-align-center"><?php echo $shippingCreatedBy ?><br>{{$shippingCreatedAt}}</td>
			<td align="center" valign=middle class="middle text-align-center">{{$forwarder}}<br><?php echo $shippingNumber ?><br>{{$shippingCreatedAt}}</td>
			<td align="center" valign=middle class="middle text-align-center"><?php echo $reference ?><br><?php echo $custType ?></td>
			<td align="center" valign=middle class="middle text-align-center"><b>{{$d->cons_first_name." ".$d->cons_middle_name." ".$d->cons_last_name}}</b><br>'{{$d->cons_phone}}<br>{{$d->cons_district.", ".$d->cons_city}}</td>
			<td align="center" valign=middle class="middle text-align-center">{{$d->service_name}}<br>Total {{Dev8th::getTotalServiceByInvoice($d->mismass_invoice_id)}} Invoice</td>
			<td align="center" valign=middle class="middle text-align-center">{{Con::rupiah($d->service_price_per)}}</td>
			<td align="center" valign=middle class="middle text-align-center">{{round($beratActual,2)}}</td>
			<td align="center" valign=middle class="middle text-align-center">{{round($berat,2)}}</td>
			<td align="center" valign=middle class="middle text-align-center">{{$item}}</td>
			<td align="center" valign=middle class="middle text-align-center">{{round($cbm,2)}}</td>
			<td align="center" valign=middle class="middle text-align-center">{{round($cbm,2)*100}}</td>
			<td align="center" valign=middle class="middle text-align-center">{{Con::rupiah($additional)}}</td>
			<td align="center" valign=middle class="middle text-align-center">{{Con::rupiah($diskon)}}</td>
			<td align="center" valign=middle class="middle text-align-center">{{Con::rupiah($jumlah)}}</td>
			<td align="center" valign=middle class="middle text-align-center">{{Con::rupiah($subTotal)}}</td>
			<td align="center" valign=middle class="middle text-align-center">{{$subTotalSGD}}</td>
			<td align="center" valign=middle class="middle text-align-center">{{Con::rupiah($komisiPacker)}}<br><?php echo $packerBy ?><br>{{$packerAt}}</td>
			<td align="center" valign=middle class="middle text-align-center">{{Con::rupiah($komisiDriver)}}<br><?php echo $mismassDriver ?><br>{{$mismassDriverAt}}</td>
			<td align="center" valign=middle class="middle text-align-center">{{Con::rupiah($profit)}}</td>
		@php
		$num++;
		@endphp
		@endforeach
	</table>