<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Tracking;

class ResiService
{
    protected $endpointModel,$controller,$trackingModel;

    public function __construct()
    {
        $this->controller = new Controller;
        $this->trackingModel = new Tracking;
    }

    public function editResiButton($array)
    {
        $this->controller->roleAccess();

        if(!Auth::user()->shiplist_edit_resi){
            return "";
        }

        $checkVendorWithApi = ResiService::checkVendorWithApi($array[2]->forwarder_name);
        if($checkVendorWithApi){
            if($array[2]->shipping_status!="Paket dalam proses pengiriman"&&$array[2]->shipping_status!="Paket dalam proses pengiriman [Paket telah dialihkan ke MISMASS]"){
                return "";
            }
        }

        if($array[5]=="INV"&&$array[4]=="COR"){
            return "";
        }

        return "<a class='dropdown-item pointlink' id='editResiBtn' data-mstracks='".$array[0]."' data-getData='".$array[1]."' data-senderAddress='" . $array[2]->sender_address . ", " . $array[2]->sender_city . ", " . $array[2]->sender_prov . ", " . $array[2]->sender_postal_code . "' data-senderPhone='" . $array[2]->sender_phone . "' data-senderName='" . $array[2]->sender_first_name . " " . $array[2]->sender_middle_name . " " . $array[2]->sender_last_name . "' data-consAddress='" . $array[2]->cons_address . ", " . $array[2]->cons_city . ", " . $array[2]->cons_prov . ", " . $array[2]->cons_postal_code . "' data-consPhone='" . $array[2]->cons_phone . "' data-consName='" . $array[2]->cons_first_name . " " . $array[2]->cons_middle_name . " " . $array[2]->cons_last_name . "' data-custTypeId='".$array[2]->cust_type_id."' data-countRow='".$array[3]."' data-totalPrice='" . $this->controller->rupiah($array[2]->totalPrice) . "' data-totalItem='" . $array[2]->totalItem . "' data-totalWeight='" . $array[2]->totalWeight . "' data-custTypeName='" . $array[2]->custTypeName . "' data-mismassInvoiceDate='" . $this->controller->dateFormatIndo($array[2]->created_at,1) . "' data-createdAt='" . $this->controller->dateFormatIndo($array[2]->created_at,1) . "' data-dokuInvoiceId='" . $array[2]->doku_invoice_id . "' data-mismassInvoiceId='" . $array[2]->mismass_invoice_id . "'>Edit Resi</a>";
    }

    private function checkVendorWithApi($name){
        if($name==="SENTRAL CARGO"||$name==="JNE"){
            return true;
        }

        return false;
    }

    public function checkDataEditResi($request){
        $diff = 0;

        for($i=0;$i<=count($request->input('id'))-1;$i++){
            $oldData = DB::table("data_list")->where("id",$request->input("id")[$i])->first();

            $shippingNumberStatsNew = ($request->input("shippingNumberStatsValue")[$i] ?? "") == "checked" ? 1 : 0;

            $request->input("consFirstName")[$i]!=$oldData->cons_first_name?$diff++:'';
            $request->input("consMiddleName")[$i]!=$oldData->cons_middle_name?$diff++:'';
            $request->input("consLastName")[$i]!=$oldData->cons_last_name?$diff++:'';
            $request->input("consPhone")[$i]!=$oldData->cons_phone?$diff++:'';
            $request->input("consAddress")[$i]!=$oldData->cons_address?$diff++:'';
            $request->input("consSubDistrict")[$i]!=$oldData->cons_sub_district?$diff++:'';
            $request->input("consDistrict")[$i]!=$oldData->cons_district?$diff++:'';
            $request->input("consCity")[$i]!=$oldData->cons_city?$diff++:'';
            $request->input("consProv")[$i]!=$oldData->cons_prov?$diff++:'';
            $request->input("consPostalCode")[$i]!=$oldData->cons_postal_code?$diff++:'';
            $request->input("tipeForwarder")[$i]!=$oldData->forwarder_id?$diff++:'';
            ($request->input("namaForwarder")[$i]??"")!=$oldData->forwarder_name?$diff++:'';
            $shippingNumberStatsNew!=$oldData->shipping_number_stats?$diff++:'';
            $request->input("noResi")[$i]!=$oldData->shipping_number?$diff++:'';
        }

        $value = [
            "diff" => $diff
        ];

        $this->stepLog(1, 'Checking Data', $value);

        return $value;

    }

    public function updateDataEditResi($array){
        $urlResi = $array["urlResi"];

        for($i=0;$i<=count($array["request"]->input('id'))-1;$i++){

            //Initialize
            $shippingNumberStatsNew = ($array["request"]->input("shippingNumberStatsValue")[$i] ?? "") == "checked" ? 1 : 0;
            $oldData = DB::table("data_list")->where("id",$array["request"]->input("id")[$i])->first();

            //Shipping Number
            $arrayCheckShippingNumberEdit = [
                "noResi" => $array["request"]->input("noResi")[$i],
                "tipeForwarder" => $array["request"]->input("tipeForwarder")[$i],
                "forwarderIdOld" => $array["request"]->input("forwarderIdOld")[$i],
            ];
            $shipping_number = $this->checkShippingNumberEditResi($arrayCheckShippingNumberEdit);

            //URL Resi
            $urlResi .= $this->controller->encodeURLCustom($shipping_number).'=';

            //Update Data List
            $arrayUpdateDataList = [
                "now" => $array["now"],
                "shippingNumber" => $shipping_number,
                "shippingNumberStatsNew" => $shippingNumberStatsNew,
                "id" => $array["request"]->input("id")[$i],
                "custTypeId" => $array["request"]->input("custTypeId"),
                "shippingNumberOld" => $array["request"]->input("shippingNumberOld") ?: "X-X-X",
                "tipeForwarder" => $array["request"]->input("tipeForwarder")[$i],
                "namaForwarder" => $array["request"]->input("namaForwarder")[$i],
                "tipeForwarderOld" => $oldData->forwarder_id,
                "namaForwarderOld" => $oldData->forwarder_name,
                "consFirstName" => $this->controller->noSingleQuo($array["request"]->input("consFirstName")[$i]),
                "consMiddleName" => $this->controller->noSingleQuo($array["request"]->input("consMiddleName")[$i]) ?? "",
                "consLastName" => $this->controller->noSingleQuo($array["request"]->input("consLastName")[$i]) ?? "",
                "consPhone" => $array["request"]->input("consPhone")[$i],
                "consAddress" => $this->controller->noSingleQuo($array["request"]->input("consAddress")[$i]),
                "consSubDistrict" => $this->controller->noSingleQuo($array["request"]->input("consSubDistrict")[$i]) ?? "",
                "consDistrict" => $this->controller->noSingleQuo($array["request"]->input("consDistrict")[$i]),
                "consCity" => $this->controller->noSingleQuo($array["request"]->input("consCity")[$i]),
                "consProv" => $this->controller->noSingleQuo($array["request"]->input("consProv")[$i]),
                "consPostalCode" => $array["request"]->input("consPostalCode")[$i],
            ];
            $updateDataList = $this->updateDataListEditResi($arrayUpdateDataList);

            //Update Shiptrip Track
            $arrayUpdateShipTrip = [
                "custTypeId" => $array["request"]->input("custTypeId"),
                "mismassInvoiceId" => $array["request"]->input("mismassInvoiceId"),
                "noResi" => $shipping_number,
                "noResiOld" => $oldData->shipping_number
            ];
            $updateShiptripTrack = $this->updateShipTripTrackEditResi($arrayUpdateShipTrip); 

            //Insert New Status
            $arrayInsertNewStatus = [
                "now" => $array["now"],
                "custTypeId" => $array["request"]->input("custTypeId"),
                "noResi" => $shipping_number,
                "mismassInvoiceId" => $array["request"]->input("mismassInvoiceId"),
                "tipeForwarder" => $array["request"]->input("tipeForwarder")[$i],
                "namaForwarder" => $array["request"]->input("namaForwarder")[$i],
                "tipeForwarderOld" => $oldData->forwarder_id,
                "namaForwarderOld" => $oldData->forwarder_name,
            ];
            $insertNewStatus = $this->insertNewStatusEditResi($arrayInsertNewStatus);
        }

        $value = [
            "status" => true,
            "name" => "Berhasil",
            "msg" => "Edit Resi Berhasil",
            "urlResi" => $urlResi
        ];
        $this->endLog();

        return $value;
    }

    private function checkShippingNumberEditResi($array){
        $shipping_number = $array["noResi"];
        if($array["tipeForwarder"] != "VENDOR"){
            if($array["forwarderIdOld"] == "VENDOR"){
                $uniqId = str::random(30);
                DB::table('pool_shipping_id')->insert(
                    ['uniq_id' => $uniqId]
                );
                $shipping_id = DB::table('pool_shipping_id')->where("uniq_id", $uniqId)->value("id");
                $prefix_shipping_id = "TR/ALY/" . date("y");
                $shipping_number = $prefix_shipping_id . $shipping_id;
            }
        }

        $shipNum = [
            "shippingNumber" => $shipping_number
        ];
        $this->stepLog(2, 'Checking Shipping Number', $shipNum);

        return $shipping_number;
    }

    private function updateDataListEditResi($array){
        $anchor = "id";
        $anchorValue = $array["id"];
        if($array["custTypeId"]=="IND"){
            $anchor = "shipping_number";
            $anchorValue = $array["shippingNumberOld"];
        }

        $arrayTextTrackingEdit = [];
        $arrayTextTrackingEdit = [
            "forwarder_id" => $array["tipeForwarder"],
            "forwarder_id_old" => $array["tipeForwarderOld"],
            "forwarder_name" => $array["namaForwarder"],
            "forwarder_name_old" => $array["namaForwarderOld"]
        ];

        $affected = DB::table("data_list")
        ->where($anchor, "=", $anchorValue)
        ->update([
            "cons_first_name" => $array["consFirstName"],
            "cons_middle_name" => $array["consMiddleName"],
            "cons_last_name" => $array["consLastName"],
            "cons_phone" => $array["consPhone"],
            "cons_address" => $array["consAddress"],
            "cons_sub_district" => $array["consSubDistrict"],
            "cons_district" => $array["consDistrict"],
            "cons_city" => $array["consCity"],
            "cons_prov" => $array["consProv"],
            "cons_postal_code" => $array["consPostalCode"],
            "forwarder_id" => $array["tipeForwarder"],
            "forwarder_name" => $array["namaForwarder"],
            "shipping_number_stats" => $array["shippingNumberStatsNew"],
            "shipping_number" => $array["shippingNumber"],
            "shipping_updated_at" => $array["now"],
            "shipping_updated_by" => Auth::user()->username,
            "shipping_status" => $this->trackingModel->getTextTrackingEdit($arrayTextTrackingEdit),
        ]);

        $affectedArray = [
            "affected" => $affected
        ];
        $this->stepLog(3, 'Update Data List', $affectedArray);
    }

    private function updateShipTripTrackEditResi($array){
        $note = "Update Shiptrip Track List (Tidak Ada Resi Tracking / Individual)";
        if($array["custTypeId"]!="IND"){
            $checkmstrack = DB::table("order_list")->selectRaw("GROUP_CONCAT(ms_track_id SEPARATOR ', ') AS mstracks")->where("invoice_id",$array["mismassInvoiceId"])->get();
            if($checkmstrack[0]->mstracks!=""){
                $data = [
                    "ms_track_id" => $array["noResi"]
                ];
                $update = DB::table("shiptrip_track_list")
                        ->where("ms_track_id","=",$array["noResiOld"])
                        ->update($data);
            }
            $note = "Update Shiptrip Track List";
        }
        $this->stepLog(4, $note, $array);
    }

    private function insertNewStatusEditResi($array){

        $arrayTextTrackingEdit = [
            "forwarder_id" => $array["tipeForwarder"],
            "forwarder_id_old" => $array["tipeForwarderOld"],
            "forwarder_name" => $array["namaForwarder"],
            "forwarder_name_old" => $array["namaForwarderOld"]
        ];

        $this->stepLog(5, 'Mulai Insert New Status Edit Resi', $arrayTextTrackingEdit);

        $newType = $array['tipeForwarder'];
        $oldType = $array['tipeForwarderOld'];
        $newName = $array['namaForwarder'];
        $oldName = $array['namaForwarderOld'];
        $isVendor = $newType === 'VENDOR' && $oldType === 'VENDOR';
        $sameGroup = in_array($newName, ['JNE', 'SENTRAL CARGO']) &&
                    in_array($oldName, ['JNE', 'SENTRAL CARGO']);

        if (
            $newType !== $oldType ||
            ($isVendor && !$sameGroup)
        ) {

            if($newName!==$oldName){

            //Cek Individual Atau Corporate
            if($array["custTypeId"]=="IND"){

                //Update Status Per Resi Tracking
                $arrayInsertNewStatus = [
                    "now" => $array["now"],
                    "mismassInvoiceId" => $array["mismassInvoiceId"],
                    "tipeForwarder" => $array["tipeForwarder"],
                    "textTrackingEdit" => $arrayTextTrackingEdit
                ];

                $insert = $this->insertNewStatusEditResiInd($arrayInsertNewStatus);
                $this->stepLog(6, 'Update Status Per Resi Tracking IND',$arrayInsertNewStatus);

            }else{

                //Update Status Per Resi Lokal   
                $arrayInsertNewStatus = [
                    "noResi" => $array["noResi"],
                    "now" => $array["now"],
                    "mismassInvoiceId" => $array["mismassInvoiceId"],
                    "tipeForwarder" => $array["tipeForwarder"],
                    "textTrackingEdit" => $arrayTextTrackingEdit
                ];

                $insert = $this->insertNewStatusEditResiCor($arrayInsertNewStatus);       
                $this->stepLog(6, 'Update Status Per Resi Lokal COR',$arrayInsertNewStatus);      
            }

            }


        }

    }

    private function insertNewStatusEditResiInd($array){

        $trackStatusId = $this->trackingModel->getStatusIdTracking($array["tipeForwarder"]);
        $getMsTrack = DB::table("order_list")
            ->select("ms_track_id")
            ->where("invoice_id",$array["mismassInvoiceId"])
            ->get();

        foreach($getMsTrack as $gm){
            if($gm->ms_track_id!=""){
                $dataTracking = [
                    "created_at" => $array["now"],
                    "created_by" => Auth::user()->username,
                    "ms_track_id" => $gm->ms_track_id,
                    "track_status_id" => $trackStatusId,
                    "track_status_manual_id" => "A", 
                    "text" => $this->trackingModel->getTextTrackingEdit($array["textTrackingEdit"]),
                ];
                $updateTracking = DB::table("shiptrip_track_list")->insert($dataTracking);
                $this->stepLog(7, 'Insert New Status',$dataTracking);

                DB::table("shiptrip_list")
                ->where("ms_track_id", "=", $gm->ms_track_id)
                ->update([
                    "track_status_id" => $trackStatusId,
                ]);
                $this->stepLog(8, 'Change Track Status ShipTrip List');
            }
        }
                    
    }

    private function insertNewStatusEditResiCor($array){
        $trackStatusId = $this->trackingModel->getStatusIdTracking($array["tipeForwarder"]);
        $dataTracking = [
            "created_at" => $array["now"],
            "created_by" => Auth::user()->username,
            "ms_track_id" => $array["noResi"],
            "track_status_id" => $trackStatusId,
            "track_status_manual_id" => "A",
            "text" => $this->trackingModel->getTextTrackingEdit($array["textTrackingEdit"])
        ];
        $updateTracking = DB::table("shiptrip_track_list")->insert($dataTracking);
        $this->stepLog(7, 'Insert New Status',$dataTracking);

        $getMsTrack = DB::table("order_list")
            ->select("ms_track_id")
            ->where("invoice_id",$array["mismassInvoiceId"])
            ->get();

        foreach($getMsTrack as $gm){
            if($gm->ms_track_id!=""){

                DB::table("shiptrip_list")
                ->where("ms_track_id", "=", $gm->ms_track_id)
                ->update([
                    "track_status_id" => $trackStatusId,
                ]);

            }
        }
        $this->stepLog(8, 'Change Track Status ShipTrip List');

    }

    private function stepLog($step, $msg, $context = []){
        Log::channel('editresi')->info("STEP {$step}: {$msg}",$context);
    }

    private function endLog(){
        Log::channel('editresi')->info("=============================================");
    }
}