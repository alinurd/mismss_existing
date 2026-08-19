@php

header("Content-Disposition: attachment; filename=Rekap Data Reference {{$username}} | Individual | {{$tanggalTitle}}.xls");
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
$num=1;
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
        <td colspan=13><font size=5><b>Rekap Data Reference CS/Marketing</b></font></td>
    </tr>
    <tr>
        <th height="17"></th>
    </tr>
    <tr>
        <td>User</td>
        <td>: <b>{{$username}}</b></td>
        <td>Total Shipment</td>
        <td>: <b>{{$totalshipment}}</b></td>
        <td><font color=green>Customer NC</font></td>
        <td>: <b>{{$nc}}</b></td>
        <td align=right>Total Kilogram</td>
        <td>: <b>{{round($totalweight,2)}}</b></td>
    </tr>
    <tr>
        <td>Nama Lengkap</td>
        <td>: <b>{{$fullname}}</b></td>
        <td>Total Customer</td>
        <td>: <b>{{$totalcustomer}}</b></td>
        <td><font color=red>Customer OC</font></td>
        <td>: <b>{{$oc}}</b></td>
        <td align=right>Total CBM</td>
        <td>: <b>{{round($totalcbm,2)}}</b></td>
    </tr>
    <tr>
        <td>Tipe Customer</td>
        <td>: <b>Individual</b></td>
    </tr>
    <tr>
        <th height="17"></th>
    </tr>
    <tr>
        <td colspan=13><font size=4><b>Data Shipment Periode {{$tanggalTitle}} <font color=green>(Berdasarkan Tanggal Create Invoice)</font></b></font></td>
    </tr>
</table>
<table cellspacing="0" border="1">
    <tr>
        <th>Tanggal Shipment</th>
        <th>Tanggal Create Invoice</th>
        <th>Data Customer</th>
        <th>Created By Invoice</th>
        <th>Status Customer</th>
        <th>No.Invoice</th>
        <th>No.Resi</th>
        <th>Status</th>
        <th>Berat(Kg)</th>
        <th>Item/Box</th>
        <th>CBM</th>
        <th>Berat CBM (Kgs)</th>
        <th>Informasi</th>
    </tr>

    @foreach($list as $l)
    <tr>
        <td align="center" valign=middle class="middle text-align-center">{{App\Http\Controllers\Controller::dateFormatIndo($l->mismass_invoice_date,3)}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{App\Http\Controllers\Controller::dateFormatIndo($l->created_at,3)}}</td>
        <td valign=middle class="middle"><b>{{$l->cons_first_name." ".$l->cons_middle_name." ".$l->cons_last_name}}</b><br>'{{$l->cons_phone}}<br>{{$l->cons_district.", ".$l->cons_city.", ".$l->cons_prov}}</td>
        <td align="center" valign=middle class="middle text-align-center"><b>{{$l->created_by}}</b><br>Created At :<br>{{App\Http\Controllers\Controller::dateFormatIndo($l->created_at,3)}}</td>
        <td align="center" valign=middle class="middle text-align-center"><?php echo $l->jumlahkirim>1 ? "<font color=red class='red'>OC</font>" : ( App\Http\Controllers\Controller::oldCustOrNot($l->sender_phone)>1 ? "<font color=red class='red'>OC</font>" : "<font color=green class='green'>NC</font>") ?></td>
        <td align="center" valign=middle class="middle text-align-center"><b>{{$l->mismass_invoice_id}}</b></td>
        <td align="center" valign=middle class="middle text-align-center"><?php echo $l->forwarder_id!="VENDOR"?$l->forwarder_id:$l->forwarder_name ?><br>{{$l->shipping_number}}</td> 
        <td align="center" valign=middle class="middle text-align-center"><?php echo $l->invoice_status=="PAID" ? "<font color=green class='green'>PAID</font><br>".App\Http\Controllers\Controller::getSuccessTime($l->mismass_invoice_id) : "<font color=red class='red'>UNPAID</font>" ?></td>
        <td align="center" valign=middle class="middle text-align-center">{{round($l->jumlahberat,2)}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{round($l->jumlahitem,2)}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{round($l->jumlahcbm,2)}}</td>
        <td align="center" valign=middle class="middle text-align-center"><?php echo round($l->jumlahcbm,2)*100 ?></td>
        <td align="center" valign=middle class="middle text-align-center">{{$l->knowname}}</td>
    </tr>

    @php
        $num++;
    @endphp
    @endforeach

</table>    