@php

$num=1;
foreach($shipping as $s){
    $mismassInvoiceId = $s->mismass_invoice_id;
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
    .bg-head{
        background-color: #b6f26d;
    }
    .green{
        color:green;
    }
    .red{
        color:red;
    }
    .yellow{
        color:#f7da8b;
    }
    th, td {
        border: 1px solid black;
    }
</style>
<table>
    <tr>
        <th colspan=10 height="17" align="center" valign=middle class="middle text-align-center"><font size="5">MISMASS IMPORT RESI CORPORATE - NO INVOICE {{$mismassInvoiceId}}</font></th>
    </tr>
    <tr>
        <th bgcolor="#b6f26d" rowspan="2" align="center" valign=middle class="middle text-align-center">No</th>
        <th bgcolor="#b6f26d" colspan="3" align="center" valign=middle class="middle text-align-center">Penerima</th>
        <th bgcolor="#b6f26d" rowspan="2" align="center" valign=middle class="middle text-align-center">Berat</th>
        <th bgcolor="#b6f26d" rowspan="2" align="center" valign=middle class="middle text-align-center">Item</th>
        <th bgcolor="#b6f26d" rowspan="2" align="center" valign=middle class="middle text-align-center">Total</th>
        <th bgcolor="#f7da8b" rowspan="2" align="center" valign=middle class="middle text-align-center">Ekspedisi</th>
        <th bgcolor="#f7da8b" rowspan="2" align="center" valign=middle class="middle text-align-center">Nama Kurir</th>
        <th bgcolor="#f7da8b" rowspan="2" align="center" valign=middle class="middle text-align-center">No Resi</th>
    </tr>
    <tr>
        <th bgcolor="#b6f26d" align="center" valign=middle class="middle text-align-center">Nama</th>
        <th bgcolor="#b6f26d" align="center" valign=middle class="middle text-align-center">Telpon</th>
        <th bgcolor="#b6f26d" align="center" valign=middle class="middle text-align-center">Alamat</th>
    </tr>
    @foreach($shipping as $s)
    <tr>
        <td align="center" valign=middle class="middle text-align-center">{{$num}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{$s->cons_first_name." ".$s->cons_middle_name." ".$s->cons_last_name}}</td>
        <td align="center" valign=middle class="middle text-align-center">'{{$s->cons_phone}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{$s->cons_address.", ".$s->cons_sub_district.", ".$s->cons_district.", ".$s->cons_city.", ".$s->cons_prov.", ".$s->cons_postal_code}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{$s->weight}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{$s->item}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{App\Http\Controllers\Controller::rupiah($s->sub_total)}}</td>
        <td align="center" valign=middle class="middle text-align-center"></td>
        <td align="center" valign=middle class="middle text-align-center"></td>
        <td align="center" valign=middle class="middle text-align-center"></td>
    </tr>
    @php
        $num++;
    @endphp
    @endforeach
</table>