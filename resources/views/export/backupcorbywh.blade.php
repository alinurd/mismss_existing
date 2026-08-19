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
$totalProfit=0;

foreach($list as $l){
    $totalPendapatan += Dev8th::getPendapatanByInvoice($l->mismass_invoice_id);
    $totalSGD += $l->fc_symbol=="SGD" ? Dev8th::getPendapatanByInvoice($l->mismass_invoice_id)/$l->fc_value : 0;
    $totalRp += $l->fc_symbol=="" ? Dev8th::getPendapatanByInvoice($l->mismass_invoice_id) : 0;
    $totalKomisiPacker += Dev8th::getKomisiBeratPackerByInvoice($l->mismass_invoice_id);
    $totalKomisiDriver += Dev8th::getKomisiBeratDriverByInvoice($l->mismass_invoice_id);
    $totalBerat += Dev8th::getBeratByInvoice($l->mismass_invoice_id);
    $totalItem += Dev8th::getItemByInvoice($l->mismass_invoice_id);
    $totalCbm += Dev8th::getCbmByInvoice($l->mismass_invoice_id);
    $totalDiskon += Dev8th::getDiskonByInvoice($l->mismass_invoice_id);
    $totalPaid += Dev8th::getTotalPaidByInvoice($l->mismass_invoice_id);
    $totalUnpaid += Dev8th::getTotalUnpaidByInvoice($l->mismass_invoice_id);
}

$totalProfit = $totalPaid-$totalKomisiPacker-$totalKomisiDriver;


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
		    <td colspan=2 rowspan=3><i><font size=2>Profit sudah dikurangi UNPAID, Komisi Packer, Komisi Driver dan Diskon, belum dikurangi Komisi AE dan biaya lainnya.</font></i></td>
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
		    <td width=100px>Total Diskon</td>
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
			<td colspan=21><b><font size="4">Data Shipment Periode {{$tanggalTitle}} <font color=green>(Berdasarkan Tanggal Invoice Otomatis)</font></font></b></td>
		</tr>
	</table>
    <table cellspacing="0" border="1">
		<tr>
            <th>No</th>
			<th>No.Invoice</th>
			<th>Create Invoice By</th>
			<th>Pembayaran</th>
			<th>Create Resi By</th>
			<th>Reference</th>
			<th>Data Client</th>
			<th colspan=2>Sub Total Berat Actual(Kg)</th>
			<th>Sub Total Berat(Kg)</th>
			<th>Sub Total Item/Box</th>
			<th>Sub Total CBM</th>
			<th>Sub Total CBM(kgs)</th>
			<th>Sub Total Additional</th>
			<th>Sub Total Diskon</th>
			<th>Sub Total Jumlah</th>
			<th>Sub Total Biaya</th>
			<th>Sub Total SGD</th>
			<th>Sub Total Komisi Packer</th>
			<th>Sub Total Komisi Driver</th>
			<th>Sub Total Profit</th>
		</tr>
        @foreach($list as $l)
        @php
            $shippingCreatedBy = $l->shipping_created_at!="0000-00-00 00:00:00" ? "<b>".$l->shipping_created_by."</b>" : "-";
			$shippingCreatedAt = $l->shipping_created_at!="0000-00-00 00:00:00" ? Con::dateFormatIndo($l->shipping_created_at,3) : "-"; 
            $reference = $l->reference!=0 ? "<b>".Con::getReferenceFullName($l->reference)."</b>" : "-";
			$custType = "<b>".($l->jumlahkirim>1 ? "<font color=red>Customer : OC</font>" : "<font color=green>Customer : NC</font>")."</b>";
            $totalBerat = Dev8th::getBeratByInvoice($l->mismass_invoice_id);
            $totalItem = Dev8th::getItemByInvoice($l->mismass_invoice_id);
            $totalCbm = Dev8th::getCbmByInvoice($l->mismass_invoice_id);
            $totalAdditional = Dev8th::getAdditionalByInvoice($l->mismass_invoice_id);
            $totalDiskon = Dev8th::getDiskonByInvoice($l->mismass_invoice_id);
            $totalSubTotal = Dev8th::getSubTotalByInvoice($l->mismass_invoice_id);
            $totalSubTotalSGD = $l->fc_symbol=="SGD" ? Con::dollarSG(Dev8th::getSubTotalByInvoice($l->mismass_invoice_id)/$l->fc_value) : "-" ;
            $jumlah = $totalSubTotal-$totalAdditional+$totalDiskon;
            $totalKomisiPacker = Dev8th::getKomisiBeratPackerByInvoice($l->mismass_invoice_id);
            $totalKomisiDriver = Dev8th::getKomisiBeratDriverByInvoice($l->mismass_invoice_id);
            $totalProfit = $totalSubTotal-$totalKomisiPacker-$totalKomisiDriver;
        @endphp
		<tr>
			<td align="center" valign=middle class="middle text-align-center">{{$num}}</td>
            <td align="center" valign=middle class="middle text-align-center"><b>{{$l->mismass_invoice_id}}</b><br>{{Con::dateFormatIndo($l->created_at,3)}}<br><a href="{{url('/p').'/'.$l->mismass_invoice_link}}" target="_blank">Link Invoice</a></td>
            <td align="center" valign=middle class="middle text-align-center"><b>{{$l->created_by}}</b><br>{{Con::dateFormatIndo($l->created_at,3)}}</td>
            <td align="center" valign=middle class="middle text-align-center"><?php echo $l->invoice_status=="PAID" ? "<font color=green class='green'>PAID</font><br>".Con::getSuccessTime($l->mismass_invoice_id) : "<font color=red class='red'>UNPAID</font>" ?></td>
            <td align="center" valign=middle class="middle text-align-center"><?php echo $shippingCreatedBy ?><br>{{$shippingCreatedAt}}</td>
            <td align="center" valign=middle class="middle text-align-center"><?php echo $reference ?><br><?php echo $custType ?></td>
			<td align="center" valign=middle class="middle text-align-center"><b>{{$l->sender_first_name." ".$l->sender_middle_name." ".$l->sender_last_name}}</b><br>'{{$l->sender_phone}}<br>{{$l->sender_district.", ".$l->sender_city}}</td>
            <td colspan=2 align="center" valign=middle class="middle text-align-center">{{round($totalBerat,2)}}</td>
            <td align="center" valign=middle class="middle text-align-center">{{Con::pembulatan($totalBerat)}}</td>
            <td align="center" valign=middle class="middle text-align-center">{{$totalItem}}</td>
            <td align="center" valign=middle class="middle text-align-center">{{round($totalCbm,2)}}</td>
            <td align="center" valign=middle class="middle text-align-center">{{round($totalCbm,2)*100}}</td>
            <td align="center" valign=middle class="middle text-align-center">{{Con::rupiah($totalAdditional)}}</td>
            <td align="center" valign=middle class="middle text-align-center">{{Con::rupiah($totalDiskon)}}</td>
            <td align="center" valign=middle class="middle text-align-center">{{Con::rupiah($jumlah)}}</td>
            <td align="center" valign=middle class="middle text-align-center">{{Con::rupiah($totalSubTotal)}}</td>
            <td align="center" valign=middle class="middle text-align-center">{{$totalSubTotalSGD}}</td>
            <td align="center" valign=middle class="middle text-align-center">{{Con::rupiah($totalKomisiPacker)}}</td>
            <td align="center" valign=middle class="middle text-align-center">{{Con::rupiah($totalKomisiDriver)}}</td>
            <td align="center" valign=middle class="middle text-align-center">{{Con::rupiah($totalProfit)}}</td>
		</tr>
        <tr>
            <td bgcolor="#efefef" class="bg-grey" colspan=21><b>Rincian Detil Penerima | Tanggal Resi : {{Con::dateFormatIndo($l->shipping_created_at,1)}}</b></td>
        </tr>
        <tr>
            <th>No</th>
			<th>No.Resi</th>
			<th colspan=4>Detail Customer</th>
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
        @php
            $num2=1;
        @endphp
        @foreach($list2 as $dl)
            @if($dl->mismass_invoice_id==$l->mismass_invoice_id)
            @php
                $forwarder = $dl->forwarder_id!="" ? ( $dl->forwarder_id=="PICK-UP" ? "PICKUP SENDIRI" : ( $dl->forwarder_id=="MISMASS" ? $dl->forwarder_id : $dl->forwarder_name )) : "-";
                $shippingNumber = $dl->shipping_number!="" ? "<b>".$dl->shipping_number."</b>" : "-";

                $additional = $dl->additional_nom+$dl->packing_total+$dl->import_permit_total+$dl->document_total+$dl->dr_medicine_total+$dl->insurance_total+$dl->fee_total+$dl->tax_total+$dl->extra_cost_price+$dl->pickup_charge;

                $jumlah = $dl->sub_total-$additional+$dl->discount;

                $komisiPacker = $dl->packing_created_by!=""?Komisi::getKomisiPacker($dl->weight):0;
			    $packerBy = $dl->packing_created_by!=""?"<font color=green>Checked By ".$dl->packing_created_by."</font>":"-";
			    $packerAt = $dl->packing_created_by!=""?Con::dateFormatIndo($dl->packing_created_at,3):"-";

                $komisiDriver = $dl->shipping_status=="SUKSES"?Komisi::getKomisiDriver($dl->weight,$dl->cust_type_id):0;
                $mismassDriver = $dl->forwarder_id=="MISMASS"?($dl->shipping_status=="SUKSES"?"<font color=green>Selesai By ".$dl->shipping_success_by."</font>":($dl->forwarder_name!=''?"<font color=red>On Proses By ".$dl->shipping_updated_by."</font>":"-")):"-";
			    $shippingSuccessAt = $dl->shipping_success_at=="0000-00-00 00:00:00"?$dl->shipping_updated_at:$dl->shipping_success_at;
		    	$mismassDriverAt = $dl->forwarder_id=="MISMASS"?($dl->shipping_status=="SUKSES"?Con::dateFormatIndo($shippingSuccessAt,3):Con::dateFormatIndo($dl->shipping_updated_at,3)):"-";
			    $profit = $dl->sub_total-$komisiDriver-$komisiPacker;

                $subTotal = $dl->sub_total;
                $subTotalSGD = $dl->fc_symbol=="SGD" ? Con::dollarSG($subTotal/$dl->fc_value) : "-" ;
		
            @endphp
            <tr>
                <td align="center" valign=middle class="middle text-align-center">{{$num2}}</td>
                <td align="center" valign=middle class="middle text-align-center">{{$forwarder}}<br><?php echo $shippingNumber ?></td>
                <td colspan=4 align="center" valign=middle class="middle text-align-center"><b>{{$dl->cons_first_name." ".$dl->cons_middle_name." ".$dl->cons_last_name}}</b><br>'{{$dl->cons_phone}}<br>{{$dl->cons_district.", ".$dl->cons_city}}</td>
                <td align="center" valign=middle class="middle text-align-center">{{$dl->service_name}}</td>
                <td align="center" valign=middle class="middle text-align-center">{{Con::rupiah($dl->service_price_per)}}</td>
                <td align="center" valign=middle class="middle text-align-center">{{round($dl->weight,2)}}</td>
                <td align="center" valign=middle class="middle text-align-center">{{Con::pembulatan($dl->weight)}}</td>
                <td align="center" valign=middle class="middle text-align-center">{{$dl->item}}</td>
                <td align="center" valign=middle class="middle text-align-center">{{round($dl->cbm,2)}}</td>
                <td align="center" valign=middle class="middle text-align-center">{{round($dl->cbm,2)*100}}</td>
                <td align="center" valign=middle class="middle text-align-center">{{Con::rupiah($additional)}}</td>
                <td align="center" valign=middle class="middle text-align-center">{{Con::rupiah($dl->discount)}}</td>
                <td align="center" valign=middle class="middle text-align-center">{{Con::rupiah($jumlah)}}</td>
                <td align="center" valign=middle class="middle text-align-center">{{Con::rupiah($subTotal)}}</td>
                <td align="center" valign=middle class="middle text-align-center">{{$subTotalSGD}}</td>
                <td align="center" valign=middle class="middle text-align-center">{{Con::rupiah($komisiPacker)}}<br><?php echo $packerBy ?><br>{{$packerAt}}</td>
                <td align="center" valign=middle class="middle text-align-center">{{Con::rupiah($komisiDriver)}}<br><?php echo $mismassDriver ?><br>{{$mismassDriverAt}}</td>
                <td align="center" valign=middle class="middle text-align-center">{{Con::rupiah($profit)}}</td>
            </tr>
            @php
                $num2++;
            @endphp
            @endif
        @endforeach

        <tr>
            <td bgcolor="#ffff00" class="bg-yellow" colspan=21></td>
        </tr>
        @php
            $num++;
        @endphp
        @endforeach
	</table>