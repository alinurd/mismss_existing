@php

header("Content-Disposition: attachment; filename=Rekap Data Reference {{$username}} | Corporate | {{$tanggalTitle}}.xls");
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
$oc=0;
$nc=0;
foreach($listcustomer as $lc){
    if($lc->jumlahkirim>1){
        $oc++;
    }else{
        $nc++;
    }
}

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
        <td colspan=12 height="17" valign=middle class="middle"><font size="4"><b>Rekap Data Reference CS/Marketing</b></font></td>
    </tr>
    <tr>
        <th height="17"></th>
    </tr><tr>
        <td>User</td>
        <td>: <b>{{$username}}</b></td>
        <td>Total Shipment</td>
        <td>: <b>{{$totalshipment}}</b></td>
        <td><font color=green>Client NC</font></td>
        <td>: <b>{{$nc}}</b></td>
        <td align=right>Total Kilogram</td>
        <td>: <b>{{round($totalweight,2)}}</b></td>
    </tr>
    <tr>
        <td>Nama Lengkap</td>
        <td>: <b>{{$fullname}}</b></td>
        <td>Total Client</td>
        <td>: <b>{{$totalcustomer}}</b></td>
        <td><font color=red>Client OC</font></td>
        <td>: <b>{{$oc}}</b></td>
        <td align=right>Total CBM</td>
        <td>: <b>{{round($totalcbm,2)}}</b></td>
    </tr>
    <tr>
        <td>Tipe Client</td>
        <td>: <b>Corporate</b></td>
    </tr>
    <tr>
        <th height="17"></th>
    </tr>
    <tr>
        <td colspan=12 height="17"><font size="3"><b>Data Shipment Periode {{$tanggalTitle}} <font color=green>(Berdasarkan Tanggal Create Invoice)</font></b></font></td>
    </tr>
</table>
<table cellspacing="0" border="1">
    <tr>
        <th>Tanggal Shipment</th>
        <th>Tanggal Create Invoice</th>
        <th>Data Client</th>
        <th>Created By Invoice</th>
        <th>Status Client</th>
        <th>No.Invoice</th>
        <th>Status</th>
        <th>Total Berat (Kg)</th>
        <th>Total Item</th>
        <th>Total CBM</th>
        <th>Total Berat CBM (Kgs)</th>
        <th>Informasi</th>
    </tr>

    @foreach($list as $l)
        <tr>
            <td align="center" valign=middle class="middle text-align-center">{{App\Http\Controllers\Controller::dateFormatIndo($l->mismass_invoice_date,3)}}</td>
            <td align="center" valign=middle class="middle text-align-center">{{App\Http\Controllers\Controller::dateFormatIndo($l->created_at,3)}}</td>
            <td valign=middle class="middle"><b>{{$l->sender_first_name." ".$l->sender_middle_name." ".$l->sender_last_name}}</b><br>'{{$l->sender_phone}}<br>{{$l->sender_district.", ".$l->sender_city.", ".$l->sender_prov}}</td>
            <td align="center" valign=middle class="middle text-align-center"><b>{{$l->created_by}}</b><br>Created At :<br>{{App\Http\Controllers\Controller::dateFormatIndo($l->created_at,3)}}</td></td>
            <td align="center" valign=middle class="middle text-align-center"><?php echo $l->jumlahkirim>1 ? "<font color=red class='red'>OC</font>" : "<font color=green class='green'>NC</font>" ?></td>
            <td align="center" valign=middle class="middle text-align-center"><b>{{$l->mismass_invoice_id}}</b></td>
            <td align="center" valign=middle class="middle text-align-center"><?php echo $l->invoice_status=="PAID" ? "<font color=green class='green'>PAID</font><br>".App\Http\Controllers\Controller::getSuccessTime($l->mismass_invoice_id) : "<font color=red class='red'>UNPAID</font>" ?></td>
            <td align="center" valign=middle class="middle text-align-center">{{round(App\Http\Controllers\Controller::hitungTotalBeratByInvoice($l->mismass_invoice_id),2)}}</td>
            <td align="center" valign=middle class="middle text-align-center">{{App\Http\Controllers\Controller::hitungTotalItemByInvoice($l->mismass_invoice_id)}}</td>
            <td align="center" valign=middle class="middle text-align-center">{{round(App\Http\Controllers\Controller::hitungTotalCbmByInvoice($l->mismass_invoice_id),2)}}</td>
            <td align="center" valign=middle class="middle text-align-center">{{round(App\Http\Controllers\Controller::hitungTotalCbmByInvoice($l->mismass_invoice_id),2)*100}}</td>
            <td align="center" valign=middle class="middle text-align-center">{{$l->knowname}}</td>
        </tr>

        @if($corType=="ALL")

            <tr>
                <td colspan=12 height="17" valign=middle class="middle"><font size="2"><b>Rincian Detil Penerima | Tanggal Resi : {{App\Http\Controllers\Controller::dateFormatIndo($l->shipping_created_at,1)}}</b></font></td>
            </tr>
            <tr>
                <th>No</th>
                <th colspan=4>Data Customer</th>
                <th colspan=3>No.Resi</th>
                <th>Berat (Kg)</th>
                <th>Item</th>
                <th>CBM</th>
                <th>Berat CBM (Kgs)</th>
            </tr>
            @php
                $num=1;
            @endphp
            @foreach($list2 as $m)
                @if($l->mismass_invoice_id==$m->mismass_invoice_id)
                    <tr>
                        <td align="center" valign=middle class="middle text-align-center">{{$num}}</td>
                        <td colspan=4 valign=middle class="middle"><b>{{$m->cons_first_name." ".$m->cons_middle_name." ".$m->cons_last_name}}</b><br>'{{$m->cons_phone}}<br>{{$m->cons_district.", ".$m->cons_city.", ".$m->cons_prov}}</td>
                        <td colspan=3 align="center" valign=middle class="middle text-align-center"><?php echo $l->forwarder_id!="VENDOR"?$l->forwarder_id:$l->forwarder_name ?><br>{{$l->shipping_number}}</td>
                        <td align="center" valign=middle class="middle text-align-center">{{round($m->weight,2)}}</td>
                        <td align="center" valign=middle class="middle text-align-center">{{$m->item}}</td>
                        <td align="center" valign=middle class="middle text-align-center">{{round($m->cbm,2)}}</td>
                        <td align="center" valign=middle class="middle text-align-center"><?php echo round($m->cbm,2)*100 ?></td>
                    </tr>
                @php
                    $num++;
                @endphp
                @endif
            @endforeach
            <tr>
                <td colspan=12 height=17 bgcolor="black"></td>
            </tr>

        @endif

    @endforeach

</table>    