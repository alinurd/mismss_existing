@php

$num=1;
header("Content-Disposition: attachment; filename=".$title." | ".$titleTanggal.".xls");
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");

$colspan=9;
if($custTypeId=="COR"){
    $colspan=10;
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
<table cellspacing="0" border="1">
    <tr>
        <th colspan={{$colspan}} height="17" align="center" valign=middle class="middle text-align-center"><font size="5">{{$title}} | {{$titleTanggal}}</font></th>
    </tr>
    <tr>
        <th align="center" valign=middle class="middle text-align-center">No.</th>
        <th align="center" valign=middle class="middle text-align-center">Terdaftar</th>
        <th align="center" valign=middle class="middle text-align-center">Admin</th>
        <th align="center" valign=middle class="middle text-align-center">Nama & ID</th>
        <th align="center" valign=middle class="middle text-align-center">Kontak & Alamat</th>
        <th align="center" valign=middle class="middle text-align-center">Total Invoice</th>
        @if($custTypeId=="COR")
        <th align="center" valign=middle class="middle text-align-center">Total Resi</th>
        @endif
        <th align="center" valign=middle class="middle text-align-center">Total Berat</th>
        <th align="center" valign=middle class="middle text-align-center">Total Item</th>
        @if(Auth::user()->custlist_nom)
        <th align="center" valign=middle class="middle text-align-center">Total Pembayaran</th>
        @endif
        <th align="center" valign=middle class="middle text-align-center">Informasi</th>
        <th align="center" valign=middle class="middle text-align-center">Reference</th>
    </tr>
    @foreach ($customer as $c)
    <tr>
        <td align="center" valign=middle class="middle text-align-center">{{$num}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{App\Http\Controllers\Controller::dateFormatIndo($c->created_at)}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{$c->created_by}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{$c->first_name." ".$c->middle_name." ".$c->last_name}}<br>ID{{strval($c->id)}}</td>
        <td align="center" valign=middle class="middle text-align-center">'{{$c->phone}}<br>{{$c->email}}<br>{{$c->address}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{App\Http\Controllers\Controller::hitungInvoiceCust($c->id)}}</td>
        @if($custTypeId=="COR")
        <td align="center" valign=middle class="middle text-align-center">{{App\Http\Controllers\Controller::hitungResiCust($c->id)}}</td>
        @endif
        <td align="center" valign=middle class="middle text-align-center">{{number_format((float)App\Http\Controllers\Controller::hitungBeratCust($c->id,0), 2, '.', '')}}</td>
        <td align="center" valign=middle class="middle text-align-center">{{App\Http\Controllers\Controller::hitungItemCust($c->id,0)}}</td>
        @if(Auth::user()->custlist_nom)
        <td align="center" valign=middle class="middle text-align-center">{{App\Http\Controllers\Controller::rupiah(App\Http\Controllers\Controller::hitungLabaCust($c->id,0))}}</td>
        @endif
        <td align="center" valign=middle class="middle text-align-center">{{$c->knowname}}</td>
        <td align="center" valign=middle class="middle text-align-center"><?php echo $c->reference!=""?$c->fullname:"-" ?></td>
    </tr>
    @php
    $num++;
    @endphp
    @endforeach
</table>