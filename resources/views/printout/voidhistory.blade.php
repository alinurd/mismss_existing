<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ url('assets/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="icon" type="image/x-icon" href="https://app-mismass.com/assets/dist/pic/favicon.ico">
    <title>Detail Void History</title>
    
    <style>
        body {
            font-family: "Source Sans Pro",-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol";
            font-size: 14px;
        }
        .primary,
        .warning,
        .danger,
        .success{
            max-width:fit-content;
        }

        .primary{
            background:blue;
            color:white;
        }

        .warning{
            background:yellow;
            color:black;
        }

        .danger{
            background:red;
            color:white;
        }

        .success{
            background:green;
            color:white;
        }
    </style>
</head>
<body>
    <table>
        <tr>
            <td style='vertical-align:top'>Create</td>
            <td style='padding:0px 20px 0px 20px'><b>{{$coreData->created_by}}</b><br><b>{{App\Http\Controllers\Controller::dateFormatIndo($coreData->created_at,2)}}</b></td>
        </tr>
        <tr></tr>
        <tr>
            <td style='vertical-align:top'>Void</td>
            <td style='padding:0px 20px 0px 20px'><b>{{$coreData->void_by}}</b><br><b>{{App\Http\Controllers\Controller::dateFormatIndo($coreData->void_at,2)}}</b></td>
        </tr>
        <tr></tr>
        <tr></tr>
        <tr></tr>
        <tr>
            <td style='vertical-align:top'>Aktivitas</td>
            <td style='padding:0px 20px 0px 20px'>
                <b>Void Invoice</b> dengan detail,<br><br>
                No. invoice Mismass: <b>{{$coreData->mismass_invoice_id}}</b><br>
                Tanggal Invoice : <b>{{App\Http\Controllers\Controller::dateFormatIndo($coreData->mismass_invoice_date,1)}}</b><br>
                Tipe Order : <b><?php echo $coreData->cust_type_id=="IND"? "Individual" : "Corporate"?></b><br>
                Konversi Mata Uang : <b><?php echo $coreData->fc_symbol==""? "-" : $coreData->fc_symbol." (".App\Http\Controllers\Controller::rupiah($coreData->fc_value).")"?></b><br>
                Format Alamat Invoice : <b><?php echo $coreData->template_id==0? "PT. Mismass Indo Group" : "Mismass Logistic Pte Ltd"?></b><br><br>
                <b>Pengirim</b><br>
                Nama : <b>{{$coreData->sender_first_name}} {{$coreData->sender_middle_name}} {{$coreData->sender_last_name}}</b><br>
                Telpon : <b>{{$coreData->sender_phone}}</b><br>
                <?php
                    $fullAddress = "<b>-</b>";
                    if($coreData->cust_type_id=="COR"){
                        $fullAddress = $coreData->sender_address.", ".$coreData->sender_sub_district.", ".$coreData->sender_district.", ".$coreData->sender_city.", ".$coreData->sender_prov.", ".$coreData->sender_postal_code;
                    }
                    echo "Alamat : <b>".$fullAddress."</b><br><br>";
                ?>
                @if($coreData->cust_type_id=="IND")
                    <b>Penerima</b><br>
                    Nama : <b>{{$coreData->cons_first_name}} {{$coreData->cons_middle_name}} {{$coreData->cons_last_name}}</b><br>
                    Telpon : <b>{{$coreData->cons_phone}}</b><br>
                    Alamat : <b>{{$coreData->cons_address}}, {{$coreData->cons_sub_district}}, {{$coreData->cons_district}}, {{$coreData->cons_city}}, {{$coreData->cons_prov}}, {{$coreData->cons_postal_code}}</b><br><br>
                @endif

                @if($coreData->bank_name=="")
                    Pembayaran : <b>DOKU</b><br>
                    Doku Token ID : <b>{{$coreData->doku_token_id}}</b><br>
                    Masa Berlaku : <b>{{App\Http\Controllers\Controller::dateFormatIndo($coreData->doku_expired_date,1)}}</b><br>
                    Link Pembayaran Doku : <b>{{url('/payment')."/".$coreData->doku_link}}</b><br><br>
                @else
                    Pembayaran : <b>BANK</b><br>
                    Nama bank : <b>{{$coreData->bank_name}}</b><br>
                    Bank akun : <b>{{$coreData->bank_account_name}}</b><br>
                    Id akun : <b>{{$coreData->bank_account_id}}</b><br><br>
                @endif

                <?php

                    $num = 1;
                    $totalAll = 0;
                    $label = $coreData->cust_type_id=="IND" ? "Service" : "Customer";

                    foreach($servData as $sd){

                        // $show = "<div style='border:1px solid black;padding:10px;border-radius:10px'>".
                        //     "<b>".$label." #".$num."</b><br><br>".                       
                        //     "Warehouse : <b>".$sd->warehouse_id."</b><br>".
                        //     "Service : <b>".$sd->service_name."</b><br>".
                        //     "Berat : <b>".$sd->weight." Kg</b><br>".
                        //     "Item : <b>".$sd->item."</b><br>".
                        //     "CBM : <b>".$sd->cbm." CBM</b><br>".
                        //     "Harga/satuan : <b>".App\Http\Controllers\Controller::rupiah($sd->service_price_per)."</b><br><br>".
                        //     "Sub Total : <b>".App\Http\Controllers\Controller::rupiah($sd->sub_total)."</b><br>".
                        //     "</div><br>";

                        $show = "<div style='border:1px solid black;padding:10px;border-radius:10px'>
                                <b>".$label." #".$num."</b><br><br>
                                ".($coreData->cust_type_id=="COR"?
                                "<b>Penerima</b><br>
                                Nama : <b>".$sd->cons_first_name." ".$sd->cons_middle_name." ".$sd->cons_last_name."</b><br>
                                Telpon : <b>".$sd->cons_phone."</b><br>
                                Alamat : <b>".$sd->cons_address.", ".$sd->cons_sub_district.", ".$sd->cons_district.", ".$sd->cons_city.", ".$sd->cons_prov.", ".$sd->cons_postal_code."</b><br><br>":
                                "")."
                                Warehouse : <b>".$sd->warehouse_id."</b><br>
                                Service : <b>".$sd->service_name."</b><br>
                                Berat : <b>".($sd->length>0?$sd->weight." Kg (".$sd->length."x".$sd->width."x".$sd->height.")":$sd->weight." Kg")."</b><br>
                                ".(
                                    $sd->length>0?
                                    "Berat Aktual : <b>".$sd->actual_weight." Kg</b><br>":
                                    ""
                                )."
                                Item : <b>".$sd->item."</b><br>
                                CBM : <b>".$sd->cbm." CBM</b><br>
                                Harga/satuan : <b>".App\Http\Controllers\Controller::rupiah($sd->service_price_per)."</b><br>
                                <br>
                                ".($sd->discount>0?
                                "<b>Diskon</b><br>
                                Nominal : <b>".App\Http\Controllers\Controller::rupiah($sd->discount)."</b><br>
                                <br>":
                                "").
                                ($sd->pickup_weight>0?
                                "<b>Pickup</b><br>
                                Berat : <b>".$sd->pickup_weight." Kg</b><br>
                                Total Charge : <b>".App\Http\Controllers\Controller::rupiah($sd->pickup_charge)."</b><br>
                                <br>":
                                "").
                                ($sd->other_pickup_fee>0?
                                "<b>Pickup Fee</b><br>
                                Total Charge : <b>".App\Http\Controllers\Controller::rupiah($sd->other_pickup_fee)."</b><br>
                                <br>":
                                "").
                                ($sd->document>0?
                                "<b>Dokumen</b><br>
                                Item : <b>".$sd->document."</b><br>
                                Harga/item : <b>".App\Http\Controllers\Controller::rupiah($sd->document_per)."</b><br>
                                Total Charge : <b>".App\Http\Controllers\Controller::rupiah($sd->document_total)."</b><br>
                                <br>":
                                "").
                                ($sd->packing>0?
                                "<b>Packing</b><br>
                                Item : <b>".$sd->packing."</b><br>
                                Harga/item : <b>".App\Http\Controllers\Controller::rupiah($sd->packing_per)."</b><br>
                                Total Charge : <b>".App\Http\Controllers\Controller::rupiah($sd->packing_total)."</b><br>
                                <br>":
                                "")
                                .($sd->import_permit>0?
                                "<b>Import Permit</b><br>
                                Item : <b>".$sd->import_permit."</b><br>
                                Harga/item : <b>".App\Http\Controllers\Controller::rupiah($sd->import_permit_per)."</b><br>
                                Total Charge : <b>".App\Http\Controllers\Controller::rupiah($sd->import_permit_total)."</b><br>
                                <br>":
                                "").
                                ($sd->export_permit>0?
                                "<b>Import Permit</b><br>
                                Item : <b>".$sd->export_permit."</b><br>
                                Harga/item : <b>".App\Http\Controllers\Controller::rupiah($sd->export_permit_per)."</b><br>
                                Total Charge : <b>".App\Http\Controllers\Controller::rupiah($sd->export_permit_total)."</b><br>
                                <br>":
                                "").
                                ($sd->dr_medicine>0?
                                "<b>Dr Medicine</b><br>
                                Item : <b>".$sd->dr_medicine."</b><br>
                                Harga/item : <b>".App\Http\Controllers\Controller::rupiah($sd->dr_medicine_per)."</b><br>
                                Total Charge : <b>".App\Http\Controllers\Controller::rupiah($sd->dr_medicine_total)."</b><br>
                                <br>":
                                "").
                                ($sd->insurance_item_price>0?
                                "<b>Asuransi</b><br>
                                Harga Barang : <b>".App\Http\Controllers\Controller::rupiah($sd->insurance_item_price)."</b><br>
                                Persentase : <b>".$sd->insurance_percent." %</b><br>
                                Total Charge : <b>".App\Http\Controllers\Controller::rupiah($sd->insurance_total)."</b><br>
                                <br>":
                                "").
                                ($sd->fee_item_price>0?
                                "<b>Fee</b><br>
                                Harga Barang : <b>".App\Http\Controllers\Controller::rupiah($sd->fee_item_price)."</b><br>
                                Persentase : <b>".$sd->fee_percent." %</b><br>
                                Total Charge : <b>".App\Http\Controllers\Controller::rupiah($sd->fee_total)."</b><br>
                                <br>":
                                "").
                                ($sd->tax_item_price>0?
                                "<b>Tax</b><br>
                                Harga Barang : <b>".App\Http\Controllers\Controller::rupiah($sd->tax_item_price)."</b><br>
                                Persentase : <b>".$sd->tax_percent." %</b><br>
                                Total Charge : <b>".App\Http\Controllers\Controller::rupiah($sd->tax_total)."</b><br>
                                <br>":
                                "").
                                ($sd->extra_cost_price>0?
                                "<b>Extra Ongkir</b><br>
                                Nominal : <b>".App\Http\Controllers\Controller::rupiah($sd->extra_cost_price)."</b><br>
                                Tujuan : <b>".$sd->extra_cost_dest."</b><br>
                                Vendor : <b>".$sd->extra_cost_vendor_name."</b><br>
                                <br>":
                                "").
                                ($sd->additional_nom>0?
                                "<b>Additional</b><br>
                                Deskripsi : <b>".$sd->additional_desc."</b><br>
                                Nominal : <b>".App\Http\Controllers\Controller::rupiah($sd->additional_nom)."</b><br>
                                <br>":
                                "")."
                                Sub Total : <b>".App\Http\Controllers\Controller::rupiah($sd->sub_total)."</b><br>
                                </div>
                                <br>";

                        echo $show;
                    
                        $num++;
                        $totalAll+=$sd->sub_total;
                    }


                echo "Total : <b style='font-size:30px'>".App\Http\Controllers\Controller::rupiah($totalAll)."</b>";


                ?>
            </td>
        </tr>
    </table>
</body>
</html>