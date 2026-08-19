<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Warehouse;
use App\Models\BackupNew;

class BackupNewController extends Controller
{
    private $backupNewModel;

    public function __construct()
    {
        $this->backupNewModel = new BackupNew;
    }

    public function newCreateExport(Request $request)
    {
        $data["tipeCustomer"] = $request->input("tipecustomer");
        $data["jenisExport"] = $request->input("jenisexport");
        $data["idWarehouse"] = $request->input("idwarehouse") ?? "";
        $data["corType"] = $request->input("cortype") ?? "";
        $data["idCustomer"] = $request->input("idcustomer") ?? "";
        $data["filterCustomer"] = $request->input("filtercustomer") ?? "";
        $data["reference"] = $request->input("reference") ?? "";
        $data["packer"] = $request->input("packer") ?? "";
        $data["driver"] = $request->input("driver") ?? "";
        $data["paymentStatus"] = $request->input("paymentStatus") ?? "";
        $data["tanggalAwal"] = $request->input("tanggalawal");
        $data["tanggalAkhir"] = $request->input("tanggalakhir");
        $array = array(
            "IND" => array(
                "BW" => array(
                    "loaderTitle" => "Export By Warehouse",
                    "loaderDetail" => "Individual | ".$data["idWarehouse"]
                ),
                "BC" => array(
                    "loaderTitle" => "Export By Customer",
                    "loaderDetail" => "Individual | ".$data["tipeCustomer"]
                ),
                "BR" => array(
                    "loaderTitle" => "Export By Reference",
                    "loaderDetail" => "Individual | ".$data["reference"]
                ),
                "BP" => array(
                    "loaderTitle" => "Export By Packer",
                    "loaderDetail" => "Individual | ".$data["packer"]
                ),
                "BD" => array(
                    "loaderTitle" => "Export By Driver",
                    "loaderDetail" => "Individual | ".$data["driver"]
                ),
            ),
            "COR" => array(
                "BW" => array(
                    "loaderTitle" => "Export By Warehouse",
                    "loaderDetail" => "Corporate | ".$data["idWarehouse"]." | ".$data['corType']
                ),
                "BC" => array(
                    "loaderTitle" => "Export By Customer",
                    "loaderDetail" => "Corporate | ".$data["tipeCustomer"]." | ".$data['corType']
                ),
                "BR" => array(
                    "loaderTitle" => "Export By Reference",
                    "loaderDetail" => "Corporate | ".$data["reference"]." | ".$data['corType']
                ),
                "BP" => array(
                    "loaderTitle" => "Export By Packer",
                    "loaderDetail" => "Corporate | ".$data["packer"]." | ".$data['corType']
                ),
                "BD" => array(
                    "loaderTitle" => "Export By Driver",
                    "loaderDetail" => "Corporate | ".$data["driver"]." | ".$data['corType']
                ),
            ),
        );

        $data["loaderTitle"] = $array[$data["tipeCustomer"]][$data["jenisExport"]]["loaderTitle"];
        $data["loaderDetail"] = $array[$data["tipeCustomer"]][$data["jenisExport"]]["loaderDetail"];
        $data["loaderDateRange"] = $data["tanggalAwal"]." - ".$data["tanggalAkhir"];

        return view("export.newcreateexport", $data);
    }

    public function exportDownload(Request $request)
    {
        $tipeCustomer = $request->input("tipecustomer");
        $jenisExport = $request->input("jenisexport");
        $idWarehouse = $request->input("idwarehouse") ?? "";
        $corType = $request->input("cortype") ?? "";
        $idCustomer = $request->input("idcustomer") ?? "";
        $filterCustomer = $request->input("filtercustomer") ?? "";
        $reference = $request->input("reference") ?? "";
        $packer = $request->input("packer") ?? "";
        $driver = $request->input("driver") ?? "";
        $paymentStatus = $request->input("paymentStatus") ?? "";
        $tanggalAwal = $request->input("tanggalawal");
        $tanggalAkhir = $request->input("tanggalakhir");
        $filterTanggalAwal = date("Y-m-d 00:00:00",strtotime($tanggalAwal));
        $filterTanggalAkhir = date("Y-m-d 23:59:59",strtotime($tanggalAkhir));
        $tanggalTitle = $tanggalAwal==$tanggalAkhir?$this->dateFormatIndo($tanggalAwal,1):$this->dateFormatIndo($tanggalAwal,1)." - ".$this->dateFormatIndo($tanggalAkhir,1);

        //By Warehouse
        if($jenisExport=="BW"){
            //Individual
            if($tipeCustomer=="IND"){
                $dt = array(
                    "idWarehouse" => $idWarehouse,
                    "tanggalTitle" => $tanggalTitle,
                    "filterTanggalAwal" => $filterTanggalAwal,
                    "filterTanggalAkhir" => $filterTanggalAkhir,
                );
                $getData = $this->backupNewModel->backupINDbyWH($dt);  
                return json_encode($getData);          
            }
            //Corporate
            $dt = array(
                "idWarehouse" => $idWarehouse,
                "corType" => $corType,
                "tanggalTitle" => $tanggalTitle,
                "filterTanggalAwal" => $filterTanggalAwal,
                "filterTanggalAkhir" => $filterTanggalAkhir,
            );
            $getData = $this->backupNewModel->backupCORbyWH($dt);  
            return json_encode($getData);  
        }

    }
}
