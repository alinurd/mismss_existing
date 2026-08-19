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
			<th colspan=12 height="17" align="center" valign=middle class="middle text-align-center"><font size="5">{{$title}}</font></th>
		</tr>
		<tr>
            <th>No</th>
            <th>Tgl. Shipment</th>
            <th>Nama Client</th>
            <th>Telepon</th>
            <th>Alamat Lengkap Client</th>
            <th>Status</th>
            <th>Tgl Invoice</th>
            <th>No. Invoice</th>
            <th>Status Payment</th>
            <th>Total Item</th>
            <th>Total Berat</th>
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
        @endphp
		<tr>
			<td align="center" valign=middle class="middle text-align-center">{{$num}}</td>
			<td align="center" valign=middle class="middle text-align-center">{{$ordercreatedat}}</td>
			<td align="center" valign=middle class="middle text-align-center">{{$orderfullname}}</td>
			<td align="center" valign=middle class="middle text-align-center">{{$l->orderphone}}</td>
			<td align="center" valign=middle class="middle text-align-center">{{$orderfulladdress}}</td>
			<td align="center" valign=middle class="middle text-align-center"><?php echo $l->orderstatusid == "READY" ? "<font color=green class='green'>READY</font>" : "<font color=red class='red'>HOLD</font>"; ?></td>
			<td align="center" valign=middle class="middle text-align-center">{{$tglinvoice}}</td>
			<td align="center" valign=middle class="middle text-align-center">{{$noinvoice}}</td>
			<td align="center" valign=middle class="middle text-align-center"><?php echo $l->orderinvoiceid != "" ? ($l->invoice_status == "PAID" ? "<font color=green class='green'>PAID</font><br>".App\Http\Controllers\Controller::dateFormatIndo(App\Http\Controllers\Controller::getSuccessTime($l->mismass_invoice_id),2) : "<font color=red class='red'>UNPAID</font>") : "-"; ?></td>
			<td align="center" valign=middle class="middle text-align-center">{{$totalitem}}</td>
			<td align="center" valign=middle class="middle text-align-center">{{$totalberat}}</td>
			<td align="center" valign=middle class="middle text-align-center"></td>
		</tr>

        @if($l->orderinvoiceid != "")
        <tr>
            <th>No</th>
			<th>Tgl. Resi</th>
			<th>No. Resi</th>
			<th>Ekspedisi</th>
			<th>Nama Penerima & Telepon</th>
			<th colspan="4">Alamat Lengkap Penerima</th>
            <th>Item</th>
            <th>Berat</th>
            <th></th>
        </tr>
        @php
            $num2=1;
        @endphp
        @foreach($list2 as $l2)
            @if($l->orderinvoiceid==$l2->mismass_invoice_id)
            @php
                $tglresi = $l2->shipping_created_at != "0000-00-00 00:00:00" ? date("d/m/Y",strtotime($l2->shipping_created_at)) : "-";
                $ekspedisi = $l2->forwarder_id != "" ? ($l2->forwarder_id != "MISMASS" ? $l2->forwarder_id : $l2->forwarder_name) : "-";
                $fullname = $l2->cons_first_name." ".$l2->cons_middle_name." ".$l2->cons_last_name;
                $fulladdress = $l2->cons_address.", ".$l2->cons_sub_district.", ".$l2->cons_district.", ".$l2->cons_city.", ".$l2->cons_prov.", ".$l2->cons_postal_code;
                $noresi = $l2->shipping_number != "" ? $l2->shipping_number : "-";
            @endphp
            <tr>
                <td align="center" valign=middle class="middle text-align-center">{{$num2}}</td>
                <td align="center" valign=middle class="middle text-align-center">{{$tglresi}}</td>
                <td align="center" valign=middle class="middle text-align-center">{{$noresi}}</td>
                <td align="center" valign=middle class="middle text-align-center">{{$ekspedisi}}</td>
                <td align="center" valign=middle class="middle text-align-center">{{$fullname."<br>".$l2->cons_phone}}</td>
                <td align="center" colspan="4" valign=middle class="middle text-align-center">{{$fulladdress}}</td>
                <td align="center" valign=middle class="middle text-align-center">{{round($l2->weight,2)}}</td>
                <td align="center" valign=middle class="middle text-align-center">{{$l2->item}}</td>
                <td align="center" valign=middle class="middle text-align-center"></td>
            </tr>
            @php
                $num2++;
            @endphp
            @endif
        @endforeach
        <tr>
            <td bgcolor="#34a853" class="bg-green" colspan="12"></td>
        </tr>
        @endif

        @php
            $num++;
        @endphp
        @endforeach
	</table>