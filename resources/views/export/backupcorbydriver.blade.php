@php

$invoiceOld="";
header("Content-Disposition: attachment; filename=Rekap Data Driver ".$driver." | Corporate | ".$tanggalTitle.".xls");
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
    <td colspan=14><b><font size=5>Rekap Data Driver {{$driver}}</font></b></td>
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
        <th align=left>: <b>Corporate</b></th>
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
        <th align=left colspan=14><font size="4"><b>Data Shipment Periode {{$tanggalTitle}} <font color=green>(Berdasarkan Tanggal Create Invoice)</font></b></font></th>
    </tr>
</table>
<table cellspacing="0" border="1">
    <tr>
        <th>Tanggal Shipment</th>
        <th>Tanggal Pickup</th>
        <th>Data Client</th>
        <th>Created Resi By</th>
        <th>No.Invoice</th>
        <th>Total Resi</th>
        <th>Pembayaran</th>
        <th>Status Kirim</th>
        <th>Total Berat (Kg)</th>
        <th>Total Item/Box</th>
        <th>Total CBM</th>
        <th>Total Berat CBM (Kgs)</th>
        <th>Total Komisi Berat (Kg)</th>
        <th>Total Komisi CBM (Kgs)</th>
    </tr>

    @foreach($list as $l)
    @php
        $komisiDriver = $l->shipping_status=="SUKSES"?App\Http\Controllers\KomisiController::totalKomisiBeratDriverByInvoice($l->mismass_invoice_id,$l->cust_type_id):0;
        $shippingSuccessAt = $l->shipping_success_at=="0000-00-00 00:00:00"? $l->shipping_updated_at : $l->shipping_success_at;
        $statusKirim = $l->shipping_status=="SUKSES"?"<b><font color=green>SELESAI</font></b><br>".App\Http\Controllers\Controller::dateFormatIndo($shippingSuccessAt,2):"<b><font color=red>ON PROSES</font></b>";
    @endphp
    <tr>
        <td align="center" valign=middle class="middle text-align-center">{{App\Http\Controllers\Controller::dateFormatIndo($l->created_at,3)}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{App\Http\Controllers\Controller::dateFormatIndo($l->shipping_updated_at,3)}}</td>
        <td valign=middle class="middle"><b>{{$l->sender_first_name." ".$l->sender_middle_name." ".$l->sender_last_name}}</b><br>'{{$l->sender_phone}}<br>{{$l->sender_address.", ".$l->sender_sub_district.", ".$l->sender_district.", ".$l->sender_city.", ".$l->sender_prov.", ".$l->sender_postal_code}}</td>
        <td align="center" valign=middle class="middle text-align-center"><b>{{$l->created_by}}</b><br>Created At :<br>{{App\Http\Controllers\Controller::dateFormatIndo($l->created_at,3)}}</td>
        <td align="center" valign=middle class="middle text-align-center"><b>{{$l->mismass_invoice_id}}</b></td>
        <td align="center" valign=middle class="middle text-align-center">{{$l->totalresi}}</td>
        <td align="center" valign=middle class="middle text-align-center"><?php echo $l->invoice_status=="PAID" ? "<font color=green class='green'>PAID</font><br>".App\Http\Controllers\Controller::getSuccessTime($l->mismass_invoice_id) : "<font color=red class='red'>UNPAID</font>" ?></td>
        <td align="center" valign=middle class="middle text-align-center"><?php echo $statusKirim ?></td>
        <td align="center" valign=middle class="middle text-align-center">{{round(App\Http\Controllers\Controller::hitungTotalBeratByInvoice($l->mismass_invoice_id),2)}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{App\Http\Controllers\Controller::hitungTotalItemByInvoice($l->mismass_invoice_id)}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{round(App\Http\Controllers\Controller::hitungTotalCbmByInvoice($l->mismass_invoice_id),2)}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{round(App\Http\Controllers\Controller::hitungTotalCbmByInvoice($l->mismass_invoice_id),2)*100}}</td>
        <td align="center" valign=middle class="middle text-align-center"><?php echo App\Http\Controllers\Controller::rupiah($komisiDriver) ?></td>
        <td align="center" valign=middle class="middle text-align-center">Rp.</td>
    </tr>

    @if($corType=="ALL")
        <tr>
            <td colspan=14 height="17" valign=middle class="middle"><font size="2"><b>Rincian Detil Penerima | Tanggal Resi : {{App\Http\Controllers\Controller::dateFormatIndo($l->shipping_created_at,1)}}</b></font></td>
        </tr>
        @php
            $num=1;
        @endphp
        @foreach($list2 as $m)
            @if($l->mismass_invoice_id==$m->mismass_invoice_id)
                @php
                    $komisi = $m->shipping_status=="SUKSES"?App\Http\Controllers\KomisiController::getKomisiDriver($m->weight,$m->cust_type_id):0;
                @endphp
                <tr>
                    <td align="center" valign=middle class="middle text-align-center">{{$num}}</td>
                    <td colspan=3 valign=middle class="middle"><b>{{$m->cons_first_name." ".$m->cons_middle_name." ".$m->cons_last_name}}</b><br>'{{$m->cons_phone}}<br>{{$m->cons_address.", ".$m->cons_sub_district.", ".$m->cons_district.", ".$m->cons_city.", ".$m->cons_prov.", ".$m->cons_postal_code}}</td>
                    <td colspan=3 align="center" valign=middle class="middle text-align-center"><?php echo $m->forwarder_id!="VENDOR"?$m->forwarder_id:$m->forwarder_name ?><br>{{$m->shipping_number}}</td>
                    <td align="center" valign=middle class="middle text-align-center">{{round($m->weight,2)}}</td>
                    <td align="center" valign=middle class="middle text-align-center">{{$m->item}}</td>
                    <td align="center" valign=middle class="middle text-align-center">{{round($m->cbm,2)}}</td>
                    <td align="center" valign=middle class="middle text-align-center"><?php echo round($m->cbm,2)*100 ?></td>
                    <td align="center" valign=middle class="middle text-align-center">{{$komisi}}</td>
                    <td align="center" valign=middle class="middle text-align-center">Rp.</td>
                    <td align="center" valign=middle class="middle text-align-center"></td>
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