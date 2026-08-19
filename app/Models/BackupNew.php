<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class BackupNew extends Model
{
    use HasFactory;

    private $controller;

    public function __construct()
    {
        $this->controller = new Controller;
    }

    public function backupINDbyWH($dt){

        $idWarehouse = $dt['idWarehouse'];
        $filterTanggalAwal = $dt['filterTanggalAwal'];
        $filterTanggalAkhir = $dt['filterTanggalAkhir'];
        $tanggalTitle = $dt['tanggalTitle'];

        // $whereRaw1 = "data_list.warehouse_id='$idWarehouse' AND data_list.cust_type_id='IND' AND data_list.created_at BETWEEN '$filterTanggalAwal' AND '$filterTanggalAkhir' AND doku_link!=''";
        // $whereRaw2 = "data_list.warehouse_id='$idWarehouse' AND data_list.cust_type_id='IND' AND data_list.created_at BETWEEN '$filterTanggalAwal' AND '$filterTanggalAkhir' AND bank_name!=''";
        // $whereRaw = $whereRaw1." OR ".$whereRaw2;
        $arrayGetQuery = [
            "custTypeId" => "IND",
            "wareId" => $idWarehouse,
            "filterTanggalAwal" => $filterTanggalAwal,
            "filterTanggalAkhir" => $filterTanggalAkhir,
        ];
        $whereRaw = $this->getQuery($arrayGetQuery);

        $orderBy = ["data_list.created_at","asc"];
        $groupBy = "data_list.mismass_invoice_id";

        // $getWh = DB::table("warehouse_list")
        //         ->where("id","=",$idWarehouse)
        //         ->first();
        // $data['title'] = "INDIVIDUAL SHIPMENT BY WAREHOUSE | ".$getWh->id." | ".$getWh->name." | ".$getWh->location; 
        $arrayGetTitle = [
            "custTypeId" => "IND",
            "wareId" => $idWarehouse
        ];
        $data['title'] = $this->getTitle($arrayGetTitle);

        $data['tanggalTitle'] = $tanggalTitle;
        $data['idWarehouse'] = $idWarehouse;
        $data['list'] = DB::table("data_list")
                        ->selectRaw(
                            "data_list.*,
                            data_list.mismass_invoice_date as invoicedate,
                            warehouse_list.id as wareid,
                            warehouse_list.name as warename,
                            warehouse_list.location as wareloc,
                            cust_list.reference,
                            COUNT(data_list.id) as totalinvoice,
                            SUM(data_list.weight) as invoiceweight,
                            SUM(data_list.item) as invoiceitem,
                            SUM(data_list.cbm) as invoicecbm,
                            SUM(data_list.discount) as invoicediscount,
                            SUM(data_list.adjust_fee) as invoiceadtfee,
                            SUM(data_list.sub_total+data_list.adjust_fee) as invoicesubtotal,
                            SUM(CASE WHEN data_list.invoice_status='PAID' THEN data_list.sub_total+data_list.adjust_fee ELSE 0 END) AS invoicepaid,
                            SUM(CASE WHEN data_list.invoice_status='UNPAID' THEN data_list.sub_total+data_list.adjust_fee ELSE 0 END) AS invoiceunpaid,
                            SUM(data_list.additional_nom+data_list.packing_total+data_list.import_permit_total+data_list.export_permit_total+data_list.document_total+data_list.dr_medicine_total+data_list.insurance_total+data_list.fee_total+data_list.tax_total+data_list.extra_cost_price+data_list.pickup_charge+data_list.other_pickup_fee) as invoiceadditional,
                            SUM(CASE WHEN data_list.fc_symbol='' THEN data_list.sub_total+data_list.adjust_fee ELSE 0 END) AS invoicesubtotalrupiah,
                            SUM(CASE WHEN data_list.fc_symbol='SGD' THEN (data_list.sub_total+data_list.adjust_fee)/fc_value ELSE 0 END) AS invoicesubtotalsgd,
                            SUM(CASE WHEN weight=0 THEN 0 WHEN weight<=3 THEN 750 WHEN weight>11 THEN 1500 ELSE 1000 END) as komisipackerbyberat,
                            SUM(CASE WHEN weight=0 THEN 0 WHEN weight<11 THEN 2000 WHEN weight>21 THEN 7000 ELSE 5000 END) as komisidriverbyberat,
                            (SELECT fullname FROM users WHERE cust_list.reference=users.id) as reffullname,
                            (SELECT COUNT(data_list.cust_id) FROM data_list WHERE data_list.cust_id=cust_list.id) as jumlahkirim"
                            )
                        ->join("warehouse_list","warehouse_list.id","=","data_list.warehouse_id")
                        ->join("cust_list","cust_list.id","=","data_list.cust_id")
                        ->whereRaw($whereRaw)
                        ->orderBy($orderBy[0],$orderBy[1])
                        ->groupBy($groupBy)
                        ->get();

        return $data;
    }

    public function backupCORbyWH($dt){

        $idWarehouse = $dt['idWarehouse'];
        $corType = $dt['corType'];
        $filterTanggalAwal = $dt['filterTanggalAwal'];
        $filterTanggalAkhir = $dt['filterTanggalAkhir'];
        $tanggalTitle = $dt['tanggalTitle'];

        // $whereRaw1 = "data_list.warehouse_id='$idWarehouse' AND data_list.cust_type_id='COR' AND data_list.created_at BETWEEN '$filterTanggalAwal' AND '$filterTanggalAkhir' AND doku_link!=''";
        // $whereRaw2 = "data_list.warehouse_id='$idWarehouse' AND data_list.cust_type_id='COR' AND data_list.created_at BETWEEN '$filterTanggalAwal' AND '$filterTanggalAkhir' AND bank_name!=''";
        // $whereRaw = $whereRaw1." OR ".$whereRaw2;
        $arrayGetQuery = [
            "custTypeId" => "COR",
            "wareId" => $idWarehouse,
            "filterTanggalAwal" => $filterTanggalAwal,
            "filterTanggalAkhir" => $filterTanggalAkhir,
        ];
        $whereRaw = $this->getQuery($arrayGetQuery);

        $orderBy = ["data_list.created_at","asc"];
        $groupBy = "data_list.mismass_invoice_id";

        // $getWh = DB::table("warehouse_list")
        //         ->where("id","=",$idWarehouse)
        //         ->first();
        // $data['title'] = "CORPORATE SHIPMENT BY WAREHOUSE | ".$getWh->id." | ".$getWh->name." | ".$getWh->location;
        $arrayGetTitle = [
            "custTypeId" => "COR",
            "wareId" => $idWarehouse,
            "corType" => $corType
        ];
        $data['title'] = $this->getTitle($arrayGetTitle);

        $data['corType'] = $corType;

        $data['list2'] = DB::table("data_list")
                            ->selectRaw(
                                "data_list.*,
                                SUM(data_list.additional_nom+data_list.packing_total+data_list.import_permit_total+data_list.export_permit_total+data_list.document_total+data_list.dr_medicine_total+data_list.insurance_total+data_list.fee_total+data_list.tax_total+data_list.extra_cost_price+data_list.pickup_charge+data_list.other_pickup_fee) AS additional"
                            )
                            ->whereRaw($whereRaw)
                            ->groupBy("data_list.id")
                            ->orderBy($orderBy[0],$orderBy[1])
                            ->get();
                            
        $data['tanggalTitle'] = $tanggalTitle;
        $data['idWarehouse'] = $idWarehouse;
        $data['list'] = DB::table("data_list")
        ->selectRaw(
            "data_list.*,
            data_list.mismass_invoice_date as invoicedate,
            warehouse_list.id as wareid,
            warehouse_list.name as warename,
            warehouse_list.location as wareloc,
            cust_list.reference,
            COUNT(data_list.id) as totalinvoice,
            SUM(data_list.weight) as invoiceweight,
            SUM(data_list.item) as invoiceitem,
            SUM(data_list.cbm) as invoicecbm,
            SUM(data_list.discount) as invoicediscount,
            SUM(data_list.adjust_fee) as invoiceadtfee,
            SUM(data_list.sub_total+data_list.adjust_fee) as invoicesubtotal,
            SUM(CASE WHEN data_list.invoice_status='PAID' THEN data_list.sub_total+data_list.adjust_fee ELSE 0 END) AS invoicepaid,
            SUM(CASE WHEN data_list.invoice_status='UNPAID' THEN data_list.sub_total+data_list.adjust_fee ELSE 0 END) AS invoiceunpaid,
            SUM(data_list.additional_nom+data_list.packing_total+data_list.import_permit_total+data_list.export_permit_total+data_list.document_total+data_list.dr_medicine_total+data_list.insurance_total+data_list.fee_total+data_list.tax_total+data_list.extra_cost_price+data_list.pickup_charge+data_list.other_pickup_fee) as invoiceadditional,
            SUM(CASE WHEN data_list.fc_symbol='' THEN data_list.sub_total+data_list.adjust_fee ELSE 0 END) AS invoicesubtotalrupiah,
            SUM(CASE WHEN data_list.fc_symbol='SGD' THEN (data_list.sub_total+data_list.adjust_fee)/fc_value ELSE 0 END) AS invoicesubtotalsgd,
            SUM(CASE WHEN weight=0 THEN 0 WHEN weight<=3 THEN 750 WHEN weight>11 THEN 1500 ELSE 1000 END) as komisipackerbyberat,
            SUM(CASE WHEN weight=0 THEN 0 WHEN weight<11 THEN 2000 WHEN weight>21 THEN 7000 ELSE 5000 END) as komisidriverbyberat,
            (SELECT fullname FROM users WHERE cust_list.reference=users.id) as reffullname,
            (SELECT COUNT(data_list.cust_id) FROM data_list WHERE data_list.cust_id=cust_list.id) as jumlahkirim"
            )
        ->join("warehouse_list","warehouse_list.id","=","data_list.warehouse_id")
        ->join("cust_list","cust_list.id","=","data_list.cust_id")
        ->whereRaw($whereRaw)
        ->orderBy($orderBy[0],$orderBy[1])
        ->groupBy($groupBy)
        ->get();

        return $data;

    }

    private function getQuery($array){
        $idWarehouse = $array['wareId'];
        $custTypeId = $array['custTypeId'];
        $filterTanggalAwal = $array['filterTanggalAwal'];
        $filterTanggalAkhir = $array['filterTanggalAkhir'];

        if($idWarehouse=="ALL WAREHOUSE"){
            $whereRaw1 = "data_list.cust_type_id='$custTypeId' AND data_list.created_at BETWEEN '$filterTanggalAwal' AND '$filterTanggalAkhir' AND doku_link!=''";
            $whereRaw2 = "data_list.cust_type_id='$custTypeId' AND data_list.created_at BETWEEN '$filterTanggalAwal' AND '$filterTanggalAkhir' AND bank_name!=''";
            $whereRaw = $whereRaw1." OR ".$whereRaw2;
            return $whereRaw;
        }

        $whereRaw1 = "data_list.warehouse_id='$idWarehouse' AND data_list.cust_type_id='$custTypeId' AND data_list.created_at BETWEEN '$filterTanggalAwal' AND '$filterTanggalAkhir' AND doku_link!=''";
        $whereRaw2 = "data_list.warehouse_id='$idWarehouse' AND data_list.cust_type_id='$custTypeId' AND data_list.created_at BETWEEN '$filterTanggalAwal' AND '$filterTanggalAkhir' AND bank_name!=''";
        $whereRaw = $whereRaw1." OR ".$whereRaw2;
        return $whereRaw;
    }

    private function getTitle($array){

        $custTypeId = "INDIVIDUAL";
        $corTypeTitle = "";
        if($array['custTypeId']=="COR"){
            $custTypeId = "CORPORATE";
            $corTypeTitle = " | Only Primary";
            if($array['corType']=="ALL"){
                $corTypeTitle = " | All Data";
            }
        }

        if($array['wareId']=="ALL WAREHOUSE"){
            return $custTypeId." SHIPMENT BY WAREHOUSE | ALL WAREHOUSE";
        }

        $getWh = DB::table("warehouse_list")
                ->where("id","=",$array['wareId'])
                ->first();
                
        return $custTypeId." SHIPMENT BY WAREHOUSE | ".$getWh->id." | ".$getWh->name." | ".$getWh->location.$corTypeTitle ;
    }
}
