@php

use App\Http\Controllers\Controller as Con;
use App\Http\Controllers\KomisiController as Komisi;
use App\Models\Afunction as Dev8th;

header("Content-Disposition: attachment; filename=Rekap Data Packer ".$packer." | Corporate | ".$tanggalTitle.".xls");
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
        <td colspan=14><font size=5><b>Rekap Data Packer {{$packer}}</b></font></td>
    </tr>
    <tr>
        <th height="17"></th>
    </tr>
    <tr>
        <td>User</td>
        <td>: <b>{{$packer}}</b></td>
        <td></td>
        <td>Total Berat</td>
        <td>: <b>{{round($totalweight,2)}}</b></td>
        <td>Total Resi</td>
        <td>: <b>{{$totalresi}}</b></td>
    </tr>
    <tr>
        <td>Tipe Customer</td>
        <td>: <b>Corporate</b></td>
        <td></td>
        <td>Total CBM</td>
        <td>: <b>{{round($totalcbm,2)}}</b></td>
        <td>Total Komisi</td>
        <td>: <b>{{Con::rupiah($totalkomisiberat)}}</b></td>
    </tr>
    <tr>
        <th height="17"></th>
    </tr>
    <tr>
        <td colspan=14><font size="3"><b>Data Shipment Periode {{$tanggalTitle}} <font color=green>(Berdasarkan Tanggal Create Invoice)</font></b></font></td>
    </tr>
</table>
<table cellspacing="0" border="1">
    <tr>
        <th>Tanggal Shipment</th>
        <th>Tanggal Check</th>
        <th>Data Client</th>
        <th>Created Invoice By</th>
        <th>No.Invoice</th>
        <th>Create Tracking</th>
        <th>Total Resi</th>
        <th>Status</th>
        <th>Total Berat (Kg)</th>
        <th>Total Item/Box</th>
        <th>Total CBM</th>
        <th>Total Berat CBM (Kgs)</th>
        <th>Total Komisi Berat (Kg)</th>
        <th>Total Komisi CBM (Kgs)</th>
    </tr>

    @foreach($list as $l)
    @php
        $totalKomisi = $l->invoice_status=="PAID"?Komisi::totalKomisiBeratPackerByInvoice($l->mismass_invoice_id):0;
    @endphp
    <tr>
        <td align="center" valign=middle class="middle text-align-center">{{Con::dateFormatIndo($l->created_at,3)}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{Con::dateFormatIndo($l->packing_created_at,3)}}</td>
        <td valign=middle class="middle"><b>{{$l->sender_first_name." ".$l->sender_middle_name." ".$l->sender_last_name}}</b><br>'{{$l->sender_phone}}<br>{{$l->sender_address.", ".$l->sender_sub_district.", ".$l->sender_district.", ".$l->sender_city.", ".$l->sender_prov.", ".$l->sender_postal_code}}</td>
        <td align="center" valign=middle class="middle text-align-center"><b>{{$l->created_by}}</b><br>Created At :<br>{{Con::dateFormatIndo($l->created_at,3)}}</td>
        <td align="center" valign=middle class="middle text-align-center"><b>{{$l->mismass_invoice_id}}</b></td>
        <td align="center" valign=middle class="middle text-align-center"><b>{{$l->shipping_created_by}}</b><br>{{Con::dateFormatIndo($l->shipping_created_at,3)}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{Dev8th::getTotalResiByInvoice($l->mismass_invoice_id)}}</td>
        <td align="center" valign=middle class="middle text-align-center"><?php echo $l->invoice_status=="PAID" ? "<font color=green class='green'>PAID</font><br>".Con::getSuccessTime($l->mismass_invoice_id) : "<font color=red class='red'>UNPAID</font>" ?></td>
        <td align="center" valign=middle class="middle text-align-center">{{round(Con::hitungTotalBeratByInvoice($l->mismass_invoice_id),2)}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{Con::hitungTotalItemByInvoice($l->mismass_invoice_id)}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{round(Con::hitungTotalCbmByInvoice($l->mismass_invoice_id),2)}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{round(Con::hitungTotalCbmByInvoice($l->mismass_invoice_id),2)*100}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{Con::rupiah($totalKomisi)}}</td>
        <td align="center" valign=middle class="middle text-align-center">Rp.</td>
    </tr>
    @if($corType=="ALL")
        <tr>
            <td colspan=14 height="17" valign=middle class="middle"><font size="2"><b>Rincian Detil Penerima | Tanggal Resi : {{Con::dateFormatIndo($l->shipping_created_at,1)}}</b></font></td>
        </tr>
        @php
            $num=1;
        @endphp
        @foreach($list2 as $m)
            @if($l->mismass_invoice_id==$m->mismass_invoice_id)
                @php
                    $komisi = $m->invoice_status=="PAID"?Komisi::getKomisiPacker($m->weight):0;
                @endphp
                <tr>
                    <td align="center" valign=middle class="middle text-align-center">{{$num}}</td>
                    <td colspan=4 valign=middle class="middle"><b>{{$m->cons_first_name." ".$m->cons_middle_name." ".$m->cons_last_name}}</b><br>'{{$m->cons_phone}}<br>{{$m->cons_address.", ".$m->cons_sub_district.", ".$m->cons_district.", ".$m->cons_city.", ".$m->cons_prov.", ".$m->cons_postal_code}}</td>
                    <td colspan=3 align="center" valign=middle class="middle text-align-center"><?php echo $m->forwarder_id!="VENDOR"?$m->forwarder_id:$m->forwarder_name ?><br>{{$m->shipping_number}}</td>
                    <td align="center" valign=middle class="middle text-align-center">{{round($m->weight,2)}}</td>
                    <td align="center" valign=middle class="middle text-align-center">{{$m->item}}</td>
                    <td align="center" valign=middle class="middle text-align-center">{{round($m->cbm,2)}}</td>
                    <td align="center" valign=middle class="middle text-align-center"><?php echo round($m->cbm,2)*100 ?></td>
                    <td align="center" valign=middle class="middle text-align-center">{{Con::rupiah($komisi)}}</td>
                    <td align="center" valign=middle class="middle text-align-center">Rp.</td>
                </tr>
            @php
                $num++;
            @endphp
            @endif
        @endforeach
        <tr>
            <td colspan=14 height=17 bgcolor="black"></td>
        </tr>
    @endif
    @endforeach
</table>    