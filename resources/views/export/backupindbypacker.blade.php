@php

use App\Http\Controllers\Controller as Con;
use App\Http\Controllers\KomisiController as Komisi;
use App\Models\Afunction as Dev8th;

$num=1;
header("Content-Disposition: attachment; filename=Rekap Data Packer ".$packer." | Individual | ".$tanggalTitle.".xls");
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
        <td colspan=18><font size=5><b>Rekap Data Packer {{$packer}}</b></font></td>
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
        <td>: <b>Individual</b></td>
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
        <td colspan=18><font size="3"><b>Data Shipment Periode {{$tanggalTitle}} <font color=green>(Berdasarkan Tanggal Create Invoice)</font></b></font></td>
    </tr>
</table>

<table cellspacing="0" border="1">
    <tr>
        <th>Tanggal Shipment</th>
        <th>Tanggal Check</th>
        <th>Data Customer</th>
        <th>Created Invoice By</th>
        <th>No.Invoice</th>
        <th>Create Tracking</th>
        <th>No.Resi</th>
        <th>Status</th>
        <th>Berat(Kg)</th>
        <th>Item/Box</th>
        <th>CBM</th>
        <th>Berat CBM (Kgs)</th>
        <th>Komisi Berat (Kg)</th>
        <th>Komisi CBM (Kgs)</th>
    </tr>

    @foreach($list as $l)
    @php
        $komisiPacker = $l->invoice_status=="PAID"?Komisi::getKomisiPacker($l->weight):0;
    @endphp
    <tr>
        <td align="center" valign=middle class="middle text-align-center">{{Con::dateFormatIndo($l->created_at,3)}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{Con::dateFormatIndo($l->packing_created_at,3)}}</td>
        <td valign=middle class="middle"><b>{{$l->cons_first_name." ".$l->cons_middle_name." ".$l->cons_last_name}}</b><br>'{{$l->cons_phone}}<br>{{$l->cons_address.", ".$l->cons_sub_district.", ".$l->cons_district.", ".$l->cons_city.", ".$l->cons_prov.", ".$l->cons_postal_code}}</td>
        <td align="center" valign=middle class="middle text-align-center"><b>{{$l->created_by}}</b><br>Created At :<br>{{Con::dateFormatIndo($l->created_at,3)}}</td>
        <td align="center" valign=middle class="middle text-align-center"><b>{{$l->mismass_invoice_id}}</b></td>
        <td align="center" valign=middle class="middle text-align-center"><b>{{$l->shipping_created_by}}</b><br>{{Con::dateFormatIndo($l->shipping_created_at,3)}}</td>
        <td align="center" valign=middle class="middle text-align-center"><?php echo $l->forwarder_id!="VENDOR"?$l->forwarder_id:$l->forwarder_name ?><br>{{$l->shipping_number}}</td> 
        <td align="center" valign=middle class="middle text-align-center"><?php echo $l->invoice_status=="PAID" ? "<font color=green class='green'>PAID</font><br>".Con::getSuccessTime($l->mismass_invoice_id) : "<font color=red class='red'>UNPAID</font>" ?></td>
        <td align="center" valign=middle class="middle text-align-center">{{round($l->weight, 2)}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{$l->item}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{round($l->cbm,2)}}</td>
        <td align="center" valign=middle class="middle text-align-center"><?php echo round($l->cbm,2)*100 ?></td>
        <td align="center" valign=middle class="middle text-align-center">{{Con::rupiah($komisiPacker)}}</td>
        <td align="center" valign=middle class="middle text-align-center">Rp.</td>
    </tr>

    @php
        $num++;
    @endphp
    @endforeach

</table>    