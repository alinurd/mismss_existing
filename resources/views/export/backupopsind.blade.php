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

<table cellspacing="0" border="1">
    <tr>
        <th colspan=15 height="17" align="center" valign=middle class="middle text-align-center"><font size="5">{{$title}}</font></th>
    </tr>
    <tr>
        <th>No</th>
        <th>Tgl. Shipment</th>
        <th>Nama Customer</th>
        <th>Telepon</th>
        <th>Alamat Lengkap Penerima</th>
        <th>Status</th>
        <th>Tgl Invoice</th>
        <th>No. Invoice</th>
        <th>Item</th>
        <th>Berat</th>
        <th>Tgl. Resi</th>
        <th>Status Payment</th>
        <th>No. Resi</th>
        <th>Ekspedisi</th>
        <th>Catatan</th>
    </tr>
    @foreach($list as $l)

    @php
        $ordercreatedat = $l->ordercreatedat != "0000-00-00 00:00:00" ? date("d/m/Y",strtotime($l->ordercreatedat)) : "-";
        $orderfullname = $l->orderfirstname." ".$l->ordermiddlename." ".$l->orderlastname;
        $orderfulladdress = $l->orderaddress.", ".$l->ordersubdistrict.", ".$l->orderdistrict.", ".$l->ordercity.", ".$l->orderprov.", ".$l->orderpostalcode;
        $tglinvoice = $l->created_at != "0000-00-00 00:00:00" ? date("d/m/Y",strtotime($l->created_at)) : "-";
        $noinvoice = $l->orderinvoiceid != "" ? $l->orderinvoiceid : "-";
        $totalitem = App\Http\Controllers\Controller::hitungTotalItemByInvoice($l->orderinvoiceid);
        $totalberat = round(App\Http\Controllers\Controller::hitungTotalBeratByInvoice($l->orderinvoiceid),2);
        $tglresi = $l->shipping_created_at != "0000-00-00 00:00:00" ? date("d/m/Y",strtotime($l->shipping_created_at)) : "-";
        $noresi = $l->shipping_number != "" ? $l->shipping_number : "-";
        $ekspedisi = $l->forwarder_id != "" ? ($l->forwarder_id == "MISMASS" ? $l->forwarder_id : $l->forwarder_name) : "-";
    @endphp

    <tr>
        <td align="center" valign=middle class="middle text-align-center">{{$num}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{$ordercreatedat}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{$orderfullname}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{$l->orderphone}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{$orderfulladdress}}</td>
        <td align="center" valign=middle class="middle text-align-center"><?php echo $l->orderstatusid == "READY" ? "<font color=green class='green'>READY</font>" : "<font color=red class='red'>HOLD</font>" ?></td>
        <td align="center" valign=middle class="middle text-align-center">{{$tglinvoice}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{$noinvoice}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{$totalitem}}</td>
        <td align="right" valign=middle class="middle text-align-center">{{$totalberat}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{$tglresi}}</td>
        <td align="center" valign=middle class="middle text-align-center"><?php echo $l->orderinvoiceid != "" ? ($l->invoice_status == "PAID" ? "<font color=green class='green'>PAID</font>" : "<font color=red class='red'>UNPAID</font>") : "-"; ?></td>
        <td align="center" valign=middle class="middle text-align-center">{{$noresi}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{$ekspedisi}}</td>
        <td align="center" valign=middle class="middle text-align-center"></td>
    </tr>
    <!-- <tr></tr>
    <tr></tr> -->
    
    @php
    $num++;
    @endphp
    
    @endforeach
</table>