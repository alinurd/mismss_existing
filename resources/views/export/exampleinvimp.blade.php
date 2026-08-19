@php

$num=1;

@endphp
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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
    table tr th{
        border:1px solid black;
    }
</style>
<table cellspacing="0" border="0">
    <tr>
        <td colspan=12 height="17" align="center" valign=middle class="middle text-align-center"><font size="5">MISMASS IMPORT INVOICE CORPORATE</font></td>
    </tr>
    <tr>
        <th align="center" bgcolor="#b6f26d" valign=middle class="middle text-align-center bg-head">No</th>
        <th align="center" bgcolor="#b6f26d" valign=middle class="middle text-align-center bg-head">Nama Depan</th>
        <th align="center" bgcolor="#b6f26d" valign=middle class="middle text-align-center bg-head">Nama Tengah</th>
        <th align="center" bgcolor="#b6f26d" valign=middle class="middle text-align-center bg-head">Nama Terakhir</th>
        <th align="center" bgcolor="#b6f26d" valign=middle class="middle text-align-center bg-head">Email</th>
        <th align="center" bgcolor="#b6f26d" valign=middle class="middle text-align-center bg-head">No. Telpon</th>
        <th align="center" bgcolor="#b6f26d" valign=middle class="middle text-align-center bg-head">Alamat</th>
        <th align="center" bgcolor="#b6f26d" valign=middle class="middle text-align-center bg-head">Kelurahan</th>
        <th align="center" bgcolor="#b6f26d" valign=middle class="middle text-align-center bg-head">Kecamatan</th>
        <th align="center" bgcolor="#b6f26d" valign=middle class="middle text-align-center bg-head">Kab/Kota</th>
        <th align="center" bgcolor="#b6f26d" valign=middle class="middle text-align-center bg-head">Provinsi</th>
        <th align="center" bgcolor="#b6f26d" valign=middle class="middle text-align-center bg-head">Kode Pos</th>
    </tr>
    @for($i=0;$i<=0;$i++)
    <tr>
        <td align="center" valign=middle class="middle text-align-center">{{$i+1}}</td>
        <td align="center" valign=middle class="middle text-align-center"></td>
        <td align="center" valign=middle class="middle text-align-center"></td>
        <td align="center" valign=middle class="middle text-align-center"></td>
        <td align="center" valign=middle class="middle text-align-center"></td>
        <td align="center" valign=middle class="middle text-align-center"></td>
        <td align="center" valign=middle class="middle text-align-center"></td>
        <td align="center" valign=middle class="middle text-align-center"></td>
        <td align="center" valign=middle class="middle text-align-center"></td>
        <td align="center" valign=middle class="middle text-align-center"></td>
        <td align="center" valign=middle class="middle text-align-center"></td>
        <td align="center" valign=middle class="middle text-align-center"></td>
    </tr>
    @endfor
</table>