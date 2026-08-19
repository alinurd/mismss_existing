@php

$num=1;
header("Content-Disposition: attachment; filename=DATA SERVICE LIST | ".$tanggal.".xls");
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
        <th colspan=<?php echo Auth::user()->servlist_nom ? "11" : "7" ?> height="17" align="center" valign=middle class="middle text-align-center"><font size="5">DATA SERVICE LIST | {{$tanggal}}</font></th>
    </tr>
    <tr>
        <th align="center" valign=middle class="middle text-align-center">No.</th>
        <th align="center" valign=middle class="middle text-align-center">Terdaftar</th>
        <th align="center" valign=middle class="middle text-align-center">Admin</th>
        <th align="center" valign=middle class="middle text-align-center">Nama Service</th>
        <th align="center" valign=middle class="middle text-align-center">ID & Warehouse</th>
        <th align="center" valign=middle class="middle text-align-center">Lokasi</th>
        @if(Auth::user()->servlist_nom)
        <th align="center" valign=middle class="middle text-align-center">Harga / KG</th>
        <th align="center" valign=middle class="middle text-align-center">Harga / Item</th>
        <th align="center" valign=middle class="middle text-align-center">Harga / Vol</th>
        <th align="center" valign=middle class="middle text-align-center">Harga / CBM</th>
        @endif
        <th align="center" valign=middle class="middle text-align-center">Deskripsi</th>
    </tr>
    @foreach ($service as $s)
    <tr>
        <td align="center" valign=middle class="middle text-align-center">{{$num}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{App\Http\Controllers\Controller::dateFormatIndo($s->created_at)}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{$s->created_by}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{$s->name}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{$s->warehouse_id}}<br>{{$s->warename}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{$s->location}}</td>
        @if(Auth::user()->servlist_nom)
        <td align="center" valign=middle class="middle text-align-center">{{App\Http\Controllers\Controller::rupiah($s->pricekg)}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{App\Http\Controllers\Controller::rupiah($s->priceitem)}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{App\Http\Controllers\Controller::rupiah($s->pricevol)}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{App\Http\Controllers\Controller::rupiah($s->pricecbm)}}</td>
        @endif
        <td align="center" valign=middle class="middle text-align-center">{{$s->description}}</td>
    </tr>
    @php
    $num++;
    @endphp
    @endforeach
</table>