@php

$num=1;
header("Content-Disposition: attachment; filename=DATA WAREHOUSE | ".$titleTanggal.".xls");
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
        <th colspan=10 height="17" align="center" valign=middle class="middle text-align-center"><font size="5">DATA WAREHOUSE | {{$titleTanggal}}</font></th>
    </tr>
    <tr>
        <th align="center" valign=middle class="middle text-align-center">No</th>
        <th align="center" valign=middle class="middle text-align-center">Terdaftar</th>
        <th align="center" valign=middle class="middle text-align-center">Admin</th>
        <th align="center" valign=middle class="middle text-align-center">Nama & ID Warehouse</th>
        <th align="center" valign=middle class="middle text-align-center">Lokasi</th>
        <th align="center" valign=middle class="middle text-align-center">Customer</th>
        <th align="center" valign=middle class="middle text-align-center">Service</th>
        <th align="center" valign=middle class="middle text-align-center">Total Invoice</th>
        <th align="center" valign=middle class="middle text-align-center">Total Berat (Kg)</th>
        <th align="center" valign=middle class="middle text-align-center">Total Item</th>
        <th align="center" valign=middle class="middle text-align-center">Total CBM</th>
        @if(Auth::user()->warelist_nom)
        <th align="center" valign=middle class="middle text-align-center">Total Pendapatan</th>
        @endif
    </tr>
    @foreach($warehouse as $w)
    <tr>
        <td align="center" valign=middle class="middle text-align-center">{{$num}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{App\Http\Controllers\Controller::dateFormatIndo($w->created_at)}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{$w->created_by}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{$w->name}}<br>{{$w->id}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{$w->location}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{App\Http\Controllers\Controller::hitungCustomerWare($w->id)}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{App\Http\Controllers\Controller::hitungServiceWare($w->id)}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{App\Http\Controllers\Controller::hitungInvoiceWare($w->id)}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{App\Http\Controllers\Controller::hitungBeratWare($w->id,0)}} KG</td>
        <td align="center" valign=middle class="middle text-align-center">{{App\Http\Controllers\Controller::hitungItemWare($w->id,0)}} Item</td>
        <td align="center" valign=middle class="middle text-align-center">{{App\Http\Controllers\Controller::hitungCbmWare($w->id,0)}} CBM</td>
        @if(Auth::user()->warelist_nom)
        <td align="center" valign=middle class="middle text-align-center">{{App\Http\Controllers\Controller::rupiah(App\Http\Controllers\Controller::hitungLabaWare($w->id,0))}}</td>
        @endif
    </tr>
    @php
    $num++;
    @endphp
    @endforeach
</table>