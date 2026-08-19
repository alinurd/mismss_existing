@php

$num=1;
header("Content-Disposition: attachment; filename=Rekap Data Driver ".$driver." | Individual | ".$tanggalTitle.".xls");
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
        <td colspan=15><b><font size=5>Rekap Data Driver {{$driver}}</font></b></td>
    </tr>
    <tr>
        <td height=17></td>
    </tr>
    <tr>
        <th align=left>User</th>
        <th align=left>: <b>{{$driver}}</b></th>
        <th></th>
        <th align=left>Total Berat</th>
        <th align=left>: <b>{{round($totalweight,2)}}</b></th>
        <th align=left>Total Shipment</th>
        <th align=left>: <b>{{$totalresi}}</b></th>
    </tr>
    <tr>
        <th align=left>Tipe Customer</th>
        <th align=left>: <b>Individual</b></th>
        <th></th>
        <th align=left>Total CBM</th>
        <th align=left>: <b>{{round($totalcbm,2)}}</b></th>
        <th align=left>Total Komisi</th>
        <th align=left>: <b>{{App\Http\Controllers\Controller::rupiah($totalkomisiberat)}}</b></th>
    </tr>
    <tr>
        <td height=17></td>
    </tr>
    <tr>
        <th align=left colspan=15><font size="4"><b>Data Shipment Periode {{$tanggalTitle}} <font color=green>(Berdasarkan Tanggal Create Invoice)</font></b></th>
    </tr>
</table>

<table cellspacing="0" border="1">
    <tr>
        <th>Tanggal Shipment</th>
        <th>Tanggal Pickup</th>
        <th >Data Penerima</th>
        <th>Created Resi By</th>
        <th>No.Invoice</th>
        <th>No.Resi</th>
        <th>Pembayaran</th>
        <th>Status Kirim</th>
        <th>Berat(Kg)</th>
        <th>Item/Box</th>
        <th>CBM</th>
        <th>Berat CBM (Kgs)</th>
        <th>Komisi Berat (Kg)</th>
        <th>Komisi CBM (Kgs)</th>
        <th>Komisi By Jarak</th>
    </tr>

    @foreach($list as $l)
    @php
        $komisiDriver = $l->shipping_status=="SUKSES"?App\Http\Controllers\KomisiController::getKomisiDriver($l->jumlahberat,$l->cust_type_id):0;
        $shippingSuccessAt = $l->shipping_success_at=="0000-00-00 00:00:00"? $l->shipping_updated_at : $l->shipping_success_at;
        $statusKirim = $l->shipping_status=="SUKSES"?"<b><font color=green>SELESAI</font></b><br>".App\Http\Controllers\Controller::dateFormatIndo($shippingSuccessAt,2):"<b><font color=red>ON PROSES</font></b>";
    @endphp
    <tr>
        <td align="center" valign=middle class="middle text-align-center">{{App\Http\Controllers\Controller::dateFormatIndo($l->created_at,3)}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{App\Http\Controllers\Controller::dateFormatIndo($l->shipping_updated_at,3)}}</td>
        <td valign=middle class="middle"><b>{{$l->cons_first_name." ".$l->cons_middle_name." ".$l->cons_last_name}}</b><br>'{{$l->cons_phone}}<br>{{$l->cons_address.", ".$l->cons_sub_district.", ".$l->cons_district.", ".$l->cons_city.", ".$l->cons_prov.", ".$l->cons_postal_code}}</td>
        <td align="center" valign=middle class="middle text-align-center"><b>{{$l->shipping_created_by}}</b><br>Created At :<br>{{App\Http\Controllers\Controller::dateFormatIndo($l->shipping_created_at,3)}}</td>
        <td align="center" valign=middle class="middle text-align-center"><b>{{$l->mismass_invoice_id}}</b></td>
        <td align="center" valign=middle class="middle text-align-center"><?php echo $l->forwarder_id!="VENDOR"?$l->forwarder_id:$l->forwarder_name ?><br><b>{{$l->shipping_number}}</b></td> 
        <td align="center" valign=middle class="middle text-align-center"><?php echo $l->invoice_status=="PAID" ? "<b><font color=green class='green'>PAID</font></b><br>".App\Http\Controllers\Controller::getSuccessTime($l->mismass_invoice_id) : "<b><font color=red class='red'>UNPAID</font></b>" ?></td>
        <td align="center" valign=middle class="middle text-align-center"><?php echo $statusKirim?></td>
        <td align="center" valign=middle class="middle text-align-center">{{round($l->jumlahberat, 2)}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{$l->jumlahitem}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{round($l->jumlahcbm,2)}}</td>
        <td align="center" valign=middle class="middle text-align-center"><?php echo round($l->jumlahcbm,2)*100 ?></td>
        <td align="center" valign=middle class="middle text-align-center">{{App\Http\Controllers\Controller::rupiah($komisiDriver)}}</td>
        <td align="center" valign=middle class="middle text-align-center">Rp.</td>
        <td align="center" valign=middle class="middle text-align-center"></td>
    </tr>

    @php
        $num++;
    @endphp
    @endforeach

</table>    