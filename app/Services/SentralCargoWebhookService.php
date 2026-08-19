<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Models\ApiSentralCargo;
use App\Models\Endpoint;

class SentralCargoWebhookService
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
            if($ip!==env("SENTRAL_KAR_IP")){
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
        $required = ['ResiNo', 'TrackType', 'TrackNotes', 'InputDt'];
        foreach ($required as $key) {
            if (!array_key_exists($key, $data)) {
                $this->logError('missing_key', $request, $key);
                return false;
            }
        }

        //Jika TrackType Kosong
        if(empty($data['TrackType'])||$data['TrackType']==null){
            $this->logError('tracktype_null', $request);
            return false;
        }

        //Jika TrackNotes Kosong
        if(empty($data['TrackNotes'])||$data['TrackNotes']==null){
            $this->logError('tracknotes_null', $request);
            return false;
        }

        //Jika ResiNo Kosong
        if(empty($data['ResiNo'])||$data['ResiNo']==null){
            $this->logError('resino_null', $request);
            return false;
        }

        //Jika Format Resino Tidak Valid
        if (!preg_match('/^[a-zA-Z0-9]+(\/[a-zA-Z0-9]+)*$/', $data['ResiNo'])) {
            $this->logError('resino_format_not_valid', $request);
            return false;
        }

        //Jika DateTime Kosong
        if(empty($data['InputDt']) || $data['InputDt']==null){
            $this->logError('inputdt_null', $request);
            return false;
        }

        //Jika Format DateTime Tidak Valid
        $dateF = date("Y-m-d",strtotime($data['InputDt']));
        if($dateF==="0000-00-00" || $dateF==="1970-01-01"){
            $this->logError('inputdt_not_valid', $request);
            return false;
        }

        //Jika TrackType = Selesai
        if ($data['TrackType'] === 'DLR' && isset($data['PODImage'])) {
            if (!is_array($data['PODImage'])) {
                $this->logError('pod_not_array', $request);
                return false;
            }

            foreach ($data['PODImage'] as $pod) {
                if (!isset($pod['ReceiverName'], $pod['Image'])) {
                    $this->logError('pod_invalid_structure', $request);
                    return false;
                }
            }
        }

        return true;
    }

    public function isDuplicate(Request $request): bool
    {
        $hash = hash('sha256', $request->getContent());

        return ApiSentralCargo::where('uniq_id', $hash)->exists();
    }

    public function saveLog(Request $request): void
    {
        ApiSentralCargo::create([
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
            "sentralkargo/error_{$type}_" . now()->format('Ymd_His') . ".log",
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
            "sentralkargo/error_{$type}_" . now()->format('Ymd_His') . ".log",
            $content
        );
    }

    public function updateStatusTracking(Request $request)
    {
        $body = $request->getContent();
        $data = json_decode($body, true);
        $shippingNumber = $data['ResiNo'];
        $dateTime = $data['InputDt'];
        $status = $data['TrackType'];
        $desc = $data['TrackNotes'];
        
        if($status!="DLR"){
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
            foreach($data['PODImage'] as $pod){
                $image = $pod['Image'];
                $receiverName = $pod['ReceiverName'];
            }
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

        //update data_list
        $dataListArray = [
            "shipping_status" => $desc,
            "shipping_updated_at" => now(),
            "shipping_updated_by" => "SENTRAL CARGO",
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
            "shipping_success_by" => "SENTRAL CARGO",
            "shipping_success_receiver" => $receiverName,
            "shipping_success_pod" => $image,
            "shipping_updated_at" => now(),
            "shipping_updated_by" => "SENTRAL CARGO",
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
            $this->logErrorSecond('shipping_number_not_found', $errArray);
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
            $this->logErrorSecond('shipping_number_not_found', $errArray);
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
            $this->logErrorSecond('shipping_number_not_found', $errArray);
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
                        'created_by' => "SENTRAL CARGO",
                        'ms_track_id' => $g->ms_track_id,
                        'track_status_id' => $array['trackStatusId'],
                        "track_status_manual_id" => "A", 
                        'text' => $array['desc'],
                    ]);
                }
            }else{
                DB::table('shiptrip_track_list')->insert([
                    'created_at' => $array['dateTime'],
                    'created_by' => "SENTRAL CARGO",
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
            "created_by" => "SENTRAL CARGO",
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
            if($array['status']!="DLR"){
                $update = $this->processTracking($array);
            }else{
                $update = $this->doneTracking($array);
            }
        }

        return true;
    }

//     public function savePodImages(array $data): array
// {
//     $saved = [];

//     if (empty($data['PODImage']) || !is_array($data['PODImage'])) {
//         return [];
//     }

//     foreach ($data['PODImage'] as $i => $pod) {
//         try {
//             $url = $pod['Image'];
//             $receiver = $pod['ReceiverName'] ?? 'unknown';

//             $response = Http::withHeaders([
//                 'User-Agent' => 'Mozilla/5.0',
//                 'Accept' => 'image/*'
//             ])->timeout(20)->get($url);

//             dump(
//                 $response->status(),
//                 $response->header('content-type'),
//                 strlen($response->body())
//             );
            

//             // $response = Http::timeout(10)->get($url);
//             // if (!$response->successful()) {
//             //     continue;
//             // }

//             $ext = $this->detectExtension($response->header('Content-Type'));
//             $filename = 'pod_' . now()->format('Ymd_His') . "_{$i}.{$ext}";
//             $path = "assets/pod/{$filename}";

//             // 🔥 SIMPAN KE PUBLIC
//             // Storage::disk('public')->put($path, $response->body());
//             // file_put_contents(
//             //     public_path("assets/pod/{$filename}"),
//             //     $response->body()
//             // );

//             if ($response->successful() && strlen($response->body()) > 0) {
//                 file_put_contents(
//                     public_path("assets/pod/{$filename}"),
//                     $response->body()
//                 );
//             }
            

//             $saved[] = [
//                 'receiver' => $receiver,
//                 'path' => $path,
//                 'url' => asset("public/{$path}")
//             ];

//         } catch (\Throwable $e) {
//             logger()->error('Save POD failed', [
//                 'error' => $e->getMessage(),
//                 'url' => $pod['Image'] ?? null
//             ]);
//         }
//     }

//     return $saved;
// }

// private function detectExtension(?string $type): string
// {
//     return match ($type) {
//         'image/png' => 'png',
//         'image/webp' => 'webp',
//         default => 'jpg',
//     };
// }


}
