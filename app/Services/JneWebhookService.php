<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Models\ApiJne;
use App\Models\Endpoint;

class JneWebhookService
{
    protected $endpointModel,$controller;

    public function __construct()
    {
        $this->endpointModel = new Endpoint;
        $this->controller = new Controller;
    }

    public function checkIp(Request $request): bool
    {
        $ip = $request->header('CF-Connecting-IP')
            ?? $request->header('X-Forwarded-For')
            ?? $request->ip();
            
        if(!env("SANDBOX")){
            if($ip!==env("JNE_IP")){
                $this->logError('unauthorized', $request, 'Unauthorized');
                return false;
            }
        }
        
        return true;
    }

    public function validatePayload(Request $request): bool
    {
        $body = $request->getContent();
        $data = json_decode($body, true);

        //Jika Format JSON Tidak Valid
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data) || empty($data)) {
            $this->logError('invalid_json', $request, json_last_error_msg());
            return false;
        }

        //Jika Tidak Memiliki Array Yang Dibutuhkan
        $required = ['awb', 'status'];
        foreach ($required as $key) {
            if (!array_key_exists($key, $data)) {
                $this->logError('missing_key', $request, $key);
                return false;
            }
        }

        //Jika Status Kosong
        if(empty($data['status'])||$data['status']==null){
            $this->logError('status_null', $request);
            return false;
        }

        //Jika AWB Kosong
        if(empty($data['awb'])||$data['awb']==null){
            $this->logError('awb_null', $request);
            return false;
        }

        //Jika Format AWB Tidak Valid
        if (!preg_match('/^[a-zA-Z0-9]+(\/[a-zA-Z0-9]+)*$/', $data['awb'])) {
            $this->logError('awb_format_not_valid', $request);
            return false;
        }

        //Jika Format History Tidak Valid
        if (
            !isset($data['history']) ||
            !is_array($data['history']) ||
            count($data['history']) === 0
        ) {
            $this->logError('history_not_valid', $request);
            return false;
        }

        //Jika Key History Tidak Valid
        $requiredHistoryKeys = ['date', 'status', 'status_code', 'status_desc', 'location_code'];
        foreach ($data['history'] as $index => $history) {
            foreach ($requiredHistoryKeys as $key) {
        
                if (!array_key_exists($key, $history)) {
                    $this->logError('history_not_has_'.$key, $request);
                    return false;
                }
        
                // kecuali location_code boleh null
                if ($key !== 'location_code' && empty($history[$key])) {
                    $this->logError('history_'.$key.'_empty', $request);
                    return false;
                }

                if ($key === 'date'){
                    if(date("Y-m-d",strtotime($history[$key]))==="0000-00-00" || date("Y-m-d",strtotime($history[$key]))==="1970-01-01"){
                        $this->logError('history_date_not_valid', $request);
                        return false;
                    }
                }
            }
        }

        //Jika Status Delivered
        if($data['status']==="DELIVERED"){
            // $requiredDeliveredKeys = ['photo', 'receiver_name'];
            $requiredDeliveredKeys = ['receiver_name'];
            foreach ($requiredDeliveredKeys as $key) {
                if (!array_key_exists($key, $data)) {
                    $this->logError('missing_key', $request, $key);
                    return false;
                }

                if (empty($data[$key])||$data[$key]===null) {
                    $this->logError($key.'_empty_null', $request);
                    return false;
                }
            }
        }

        return true;
    }

    public function isDuplicate(Request $request): bool
    {
        $hash = hash('sha256', $request->getContent());

        return ApiJne::where('uniq_id', $hash)->exists();
    }

    public function saveLog(Request $request): void
    {
        ApiJne::create([
            'created_at' => now(),
            'uniq_id' => hash('sha256', $request->getContent()),
            'description' => substr($request->getContent(), 0, 10000)
        ]);
    }

    private function logError(string $type, Request $request, string $extra = ''): void
    {
        $ip = $request->header('CF-Connecting-IP')
            ?? $request->header('X-Forwarded-For')
            ?? $request->ip();

        $content = <<<LOG
=== INFO ===
IP: {$ip}
Time: {now()}

=== BODY ===
{$request->getContent()}

=== ERROR ===
{$type} {$extra}
LOG;

        Storage::disk('local')->put(
            "jne/error_{$type}_" . now()->format('Ymd_His') . ".log",
            $content
        );
    }

    private function logErrorSecond(string $type, array $array): void
    {
        $now = now();
        $content = <<<LOG
=== INFO ===
Customer Type: {$array['custType']}
Shipping Number: {$array['shippingNumber']}
Time: {$now}

=== ERROR ===
{$type}
LOG;

        Storage::disk('local')->put(
            "jne/error_{$type}_" . now()->format('Ymd_His') . ".log",
            $content
        );
    }

    public function updateStatusTracking(Request $request)
    {
        $body = $request->getContent();
        $data = json_decode($body, true);

        $shippingNumber = $data['awb'];
        $status = $data['status'];
        usort($data['history'], function ($a, $b) {
            return strtotime($b['date']) <=> strtotime($a['date']);
        });        
        $dateTime = $data['history'][0]['date'];
        
        if($status!="DELIVERED"){
            $statusDesc = ucfirst(strtolower($data['history'][0]['status_desc']));
            $locationCode = $data['history'][0]['location_code']===null||$data['history'][0]['location_code']===""?"":"[".$data['history'][0]['location_code']."]";
            $desc = $statusDesc." ".$locationCode;
            $array = [
                "status" => $status,
                "shippingNumber" => $shippingNumber,
                "dateTime" => $dateTime,
                "desc" => $desc
            ];
            $update = $this->processTracking($array);

            //check multiple Shipping Number Ex: 1111(1)
            $check = $this->checkMultipleShipNum($array);
        }else{
            $image = $data['photo'] ?? "";
            $receiverName = $data['receiver_name'];
            $desc = ucfirst(strtolower($data['history'][0]['status']));
            $array = [
                "status" => $status,
                "shippingNumber" => $shippingNumber,
                "dateTime" => $dateTime,
                "receiverName" => $receiverName,
                "podImage" => $image,
                "desc" => $desc
            ];
            $update = $this->doneTracking($array);

            //check multiple Shipping Number Ex: 1111(1)
            $check = $this->checkMultipleShipNum($array);
        }
        
        return [
            "status" => $update['status'],
            "message" => $update['message']
        ];
        
    }
    
    private function processTracking($array)
    {        
        $shippingNumber = $array['shippingNumber'];
        $desc = $array['desc'];
        $dateTime = date("Y-m-d H:i:s",strtotime($array['dateTime']));
        
        //check data_list
        $checkShipNum = DB::table("data_list")
                    ->select("cust_type_id","ms_track_id")
                    ->where("shipping_number",$shippingNumber)
                    ->first();
                    
        if($checkShipNum==null){
            $errArray = [
                "custType" => "-",
                "shippingNumber" => $shippingNumber
            ];
            $this->logErrorSecond('shipping_number_not_found', $errArray);
            return [
                "status" => false,
                "message" => "Data Shipping Number Kosong"
            ];
        }

        // $content = "custType : ".$checkShipNum->cust_type_id."\n";
        // $content .= "Mstrackid : ".$checkShipNum->ms_track_id."\n";
        // $content .= "ShippingNumber : ".$shippingNumber."\n";
        // $content .= "description : ".$desc."\n";
        // $content .= "date : ".$dateTime."\n";
        // Storage::disk('local')->put(
        //     "sentralkargo/process.log",
        //     $content
        // );

        //update data_list
        $dataListArray = [
            "shipping_status" => $desc,
            "shipping_updated_at" => now(),
            "shipping_updated_by" => "JNE",
            "track_status_id" => 17,
            "track_man_created_at" => $dateTime
        ];
        
        //check jika Individual atau Corporate
        if($checkShipNum->cust_type_id=="IND"&&$checkShipNum->ms_track_id!==""){
            $shippingNumber = $checkShipNum->ms_track_id;
            $updateDataList = $this->endpointModel->where("ms_track_id",$shippingNumber)->update($dataListArray);
        }else{
            $updateDataList = $this->endpointModel->where("shipping_number",$shippingNumber)->update($dataListArray);
        }
        
        if($updateDataList===0){
            $errArray = [
                "custType" => $checkShipNum->cust_type_id,
                "shippingNumber" => $shippingNumber
            ];
            $this->logErrorSecond('failed_update_data_list', $errArray);
            return [
                "status" => false,
                "message" => "Update Data List Error"
            ];
        }
        
        //update tracking
        $arrayUpdateTracking = [
            "msTrackId" => $checkShipNum->ms_track_id,
            "custTypeId" => $checkShipNum->cust_type_id,
            "dateTime" => $dateTime,
            "desc" => $desc,
            "shippingNumber" => $shippingNumber,
            "trackStatusId" => 17
        ];
        $updateTracking = $this->updateTracking($arrayUpdateTracking);
        if(!$updateTracking){
            $errArray = [
                "custType" => $checkShipNum->cust_type_id,
                "shippingNumber" => $shippingNumber
            ];
            $this->logErrorSecond('failed_update_track_list', $errArray);
            return [
                "status" => false,
                "message" => "Update Tracking Error"
            ];
        }
        
        //update history
        $arrayCreateHistory = [
            "dateTime" => $dateTime,
            "desc" => $desc,
            "podImage" => "-",
            "custTypeId" => $checkShipNum->cust_type_id,
            "shippingNumber" => $shippingNumber
        ];
        $createHistory = $this->createHistory($arrayCreateHistory);
        if(!$createHistory){
            $errArray = [
                "custType" => $checkShipNum->cust_type_id,
                "shippingNumber" => $shippingNumber
            ];
            $this->logErrorSecond('failed_update_history', $errArray);
            return [
                "status" => false,
                "message" => "Create History Error"
            ];
        }
        
        return [
            "status" => true,
            "message" => "Ok"
        ];
    }
    
    private function doneTracking($array)
    {        
        $receiverName = $array['receiverName'];
        $image = $array['podImage'];
        $shippingNumber = $array['shippingNumber'];
        $desc = $array['desc'];
        $dateTime = date("Y-m-d H:i:s",strtotime($array['dateTime']));
        
        //check data_list
        $checkShipNum = DB::table("data_list")
                    ->select("cust_type_id","ms_track_id")
                    ->where("shipping_number",$shippingNumber)
                    ->first();
                    
        if($checkShipNum==null){
            $errArray = [
                "custType" => "-",
                "shippingNumber" => $shippingNumber
            ];
            $this->logErrorSecond('shipping_number_not_found', $errArray);
            return [
                "status" => false,
                "message" => "Data Shipping Number Kosong"
            ];
        }
        
        //update data_list
        $desc .= "<br><a target='_blank' href='".$image."'>Lihat bukti penerimaan</a>";
        $dataListArray = [
            "shipping_status" => $desc,
            "shipping_success_at" => now(),
            "shipping_success_by" => "JNE",
            "shipping_success_receiver" => $receiverName,
            "shipping_success_pod" => $image,
            "shipping_updated_at" => now(),
            "shipping_updated_by" => "JNE",
            "track_status_id" => 22,
            "track_man_created_at" => $dateTime
        ];
        
        //check jika Individual atau Corporate
        if($checkShipNum->cust_type_id=="IND"&&$checkShipNum->ms_track_id!==""){
            $shippingNumber = $checkShipNum->ms_track_id;
            $updateDataList = $this->endpointModel->where("ms_track_id",$shippingNumber)->update($dataListArray);
        }else{
            $updateDataList = $this->endpointModel->where("shipping_number",$shippingNumber)->update($dataListArray);
        }
        
        if($updateDataList===0){
            $errArray = [
                "custType" => $checkShipNum->cust_type_id,
                "shippingNumber" => $shippingNumber
            ];
            $this->logErrorSecond('failed_update_data_list', $errArray);
            return [
                "status" => false,
                "message" => "Update Data List Error"
            ];
        }
        
        //update tracking
        $arrayUpdateTracking = [
            "msTrackId" => $checkShipNum->ms_track_id,
            "custTypeId" => $checkShipNum->cust_type_id,
            "dateTime" => $dateTime,
            "desc" => $desc,
            "shippingNumber" => $shippingNumber,
            "trackStatusId" => 22
        ];
        $updateTracking = $this->updateTracking($arrayUpdateTracking);
        if(!$updateTracking){
            $errArray = [
                "custType" => $checkShipNum->cust_type_id,
                "shippingNumber" => $shippingNumber
            ];
            $this->logErrorSecond('failed_update_track_list', $errArray);
            return [
                "status" => false,
                "message" => "Update Tracking Error"
            ];
        }

        //update history
        $linkImg = "<a target='_blank' href='".$image."'>Foto</a>, ";
        $arrayCreateHistory = [
            "dateTime" => $dateTime,
            "desc" => $array['desc'],
            "podImage" => $linkImg,
            "custTypeId" => $checkShipNum->cust_type_id,
            "shippingNumber" => $shippingNumber
        ];
        $createHistory = $this->createHistory($arrayCreateHistory);
        if(!$createHistory){
            $errArray = [
                "custType" => $checkShipNum->cust_type_id,
                "shippingNumber" => $shippingNumber
            ];
            $this->logErrorSecond('failed_update_history', $errArray);
            return [
                "status" => false,
                "message" => "Create History Error"
            ];
        }
        
        return [
            "status" => true,
            "message" => "Ok"
        ];
    }

    private function updateTracking($array)
    {
        if($array['msTrackId']!=""){            
            if($array['custTypeId']=="IND"){
                $invoiceId = $this->endpointModel->where("ms_track_id",$array['msTrackId'])->value("mismass_invoice_id");
                $getMSTrack = DB::table("order_list")->select("ms_track_id")->where("invoice_id",$invoiceId)->get();
                
                foreach($getMSTrack as $g){
                    $insertTrack = DB::table('shiptrip_track_list')->insert([
                        'created_at' => $array['dateTime'],
                        'created_by' => "JNE",
                        'ms_track_id' => $g->ms_track_id,
                        'track_status_id' => $array['trackStatusId'],
                        "track_status_manual_id" => "A", 
                        'text' => $array['desc'],
                    ]);
                }
            }else{
                DB::table('shiptrip_track_list')->insert([
                    'created_at' => $array['dateTime'],
                    'created_by' => "JNE",
                    'ms_track_id' => $array['shippingNumber'],
                    'track_status_id' => $array['trackStatusId'],
                    "track_status_manual_id" => "A", 
                    'text' => $array['desc'],
                ]);
            }
        }

        return true;
    }

    private function createHistory($array)
    {
        $queryHistory = $this->endpointModel->selectRaw(
                            "to_sg_man_created_at,
                            CONCAT_WS(' - ',forwarder_id,forwarder_name) AS full_forwarder,
                            ms_track_id,
                            shipping_number,
                            cons_phone,
                            CONCAT_WS(' ',cons_first_name,cons_middle_name,cons_last_name) AS full_name,
                            CONCAT_WS(', ',cons_address,cons_sub_district,cons_district,cons_city,cons_prov,cons_postal_code) AS full_address"
                        );

        if($array['custTypeId']==="IND"){
            $queryHistory->where("ms_track_id",$array['shippingNumber']);
        }else{
            $queryHistory->where("shipping_number",$array['shippingNumber']);
        }

        $getDataForHistory = $queryHistory->first();
        $dataHistory=[
            "codename" => "UE",
            "created_at" => now(),
            "created_by" => "JNE",
            "description" => "<b>Update Endpoint</b> dengan detail,<br><br>
            Tgl Status Tracking : <b>".$this->controller->dateFormatIndo($array['dateTime'],1)."</b><br>
            Tgl Shipment : <b>".$this->controller->dateFormatIndo($getDataForHistory->to_sg_man_created_at,1)."</b><br>
            Pengiriman : <b>".$getDataForHistory->full_forwarder."</b><br>
            Resi Utama : <b>".($getDataForHistory->ms_track_id ?? "-")."</b><br>
            Resi Lokal : <b>".$getDataForHistory->shipping_number."</b><br>
            Nama : <b>".$getDataForHistory->full_name."</b><br>
            Telpon :<b>".$getDataForHistory->cons_phone."</b><br>
            Alamat : <b>".$getDataForHistory->full_address."</b><br>
            Status Update : <b>".$array['desc']."</b><br>
            Bukti Foto : <b>".$array['podImage']."</b><br>"
        ];
        $insertHistory = DB::table('history_list')->insert($dataHistory);
        if(!$insertHistory){
            return false;
        }

        return true;
    }

    private function checkMultipleShipNum($array)
    {

        $check = DB::table("data_list")
        ->select("cust_type_id","ms_track_id","shipping_number")
        ->where("shipping_number","LIKE",$array['shippingNumber']."(%")
        ->groupBy('shipping_number')
        ->orderBy("shipping_number", "ASC")
        ->get();

        if(count($check)==0){
            return false;
        }

        foreach($check as $c){
            $array['shippingNumber'] = $c->shipping_number;
            if($array['status']!="DELIVERED"){
                $update = $this->processTracking($array);
            }else{
                $update = $this->doneTracking($array);
            }
        }

        return true;
    }
}
?>