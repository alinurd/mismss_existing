<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Invoice;
use DOKU;
use DOKU\Common;
use DOKU\Common\Config;
use DOKU\Common\Utils;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DokuSystem extends Model
{
    use HasFactory;

    private $dokuClient,$invoiceModel,$poolInvoiceModel,$controller;

    public function __construct()
    {
        $this->dokuClient = new Doku\Client;
        $this->invoiceModel = new Invoice;
        $this->controller = new Controller;
    }

    public function generate($params){
        $header = array();
        $data = array();
        $result = array();
        $payment_methods = [
            "VIRTUAL_ACCOUNT_BCA",
            "VIRTUAL_ACCOUNT_BANK_MANDIRI",
            "VIRTUAL_ACCOUNT_BANK_SYARIAH_MANDIRI",
            "VIRTUAL_ACCOUNT_DOKU",
            "VIRTUAL_ACCOUNT_BRI",
            "VIRTUAL_ACCOUNT_BNI",
            "VIRTUAL_ACCOUNT_BANK_PERMATA",
            "VIRTUAL_ACCOUNT_BANK_CIMB",
            "VIRTUAL_ACCOUNT_BANK_DANAMON",
        ];
        // $payment_methods = [
        //     "VIRTUAL_ACCOUNT_BCA",
        //     "VIRTUAL_ACCOUNT_BANK_MANDIRI",
        //     "VIRTUAL_ACCOUNT_BANK_SYARIAH_MANDIRI",
        //     "VIRTUAL_ACCOUNT_DOKU",
        //     "VIRTUAL_ACCOUNT_BRI",
        //     "VIRTUAL_ACCOUNT_BNI",
        //     "VIRTUAL_ACCOUNT_BANK_PERMATA",
        //     "VIRTUAL_ACCOUNT_BANK_CIMB",
        //     "VIRTUAL_ACCOUNT_BANK_DANAMON",
        //     "ONLINE_TO_OFFLINE_ALFA",
        //     "CREDIT_CARD",
        //     "DIRECT_DEBIT_BRI",
        //     "EMONEY_SHOPEEPAY",
        //     "EMONEY_OVO",
        //     "EMONEY_DANA",
        //     "QRIS",
        //     "PEER_TO_PEER_AKULAKU",
        //     "PEER_TO_PEER_KREDIVO",
        //     "PEER_TO_PEER_INDODANA"
        // ];

        $data['order']["amount"] = $params['order']['price'];
        $data['order']["invoice_number"] = $params['order']['invoice_number'];
        $data['payment']["payment_due_date"] = $params['payment']['payment_due_date'];
        $data['payment']["payment_method_types"] = $payment_methods;
        $data['customer']["id"] = $params['customer']['id'];
        $data['customer']["name"] = $params['customer']['name'];
        $data['customer']["phone"] = $params['customer']['phone'];
        $data['customer']["email"] = $params['customer']['email'];
        $data['customer']["address"] = $params['customer']['address'];

        $regId = $this->controller->generateRandomString(25);
        $dateTime = gmdate("Y-m-d H:i:s");
        $dateTime = date(DATE_ISO8601, strtotime($dateTime));
        $dateTimeFinal = substr($dateTime, 0, 19) . "Z";

        $getUrl = Config::getBaseUrl(env('DOKU_PRODUCTION'));

        $targetPath = '/checkout/v1/payment';
        $url = $getUrl.$targetPath;

        $header['Client-Id'] = env('DOKU_CLIENT_ID');
        $header['Request-Id'] = $regId;
        $header['Request-Timestamp'] = $dateTimeFinal;
        $header['Request-Target'] = $targetPath;
        $signature = "";
        if (!isset($params['sigver'])) {
            $signature = Utils::generateSignature($header, json_encode($data), env('DOKU_SECRET_KEY'));
        } else {
            $signature = Utils::generateSignatureV1_3($header, json_encode($data), env('DOKU_SECRET_KEY'));
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 25);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Signature:' . $signature,
            'Request-Id:' . $regId,
            'Client-Id:' . env('DOKU_CLIENT_ID'),
            'Request-Timestamp:' . $dateTimeFinal,
            'Request-Target:' . $targetPath,

        ));

        $start = microtime(true);
        $responseJson = curl_exec($ch);
        $end = microtime(true);
        $executionTime = $end - $start;
        $info = curl_getinfo($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if($responseJson===false){
            $errorCode = curl_errno($ch);
            $errorMsg = curl_error($ch);
            $result = [
                "status" => $errorCode,
                "message" => "Doku Lama Memberikan Respon. Silahkan Coba Lagi."
            ];

            Log::warning('Response Timeout Doku',[
                "status" => $errorCode,
                "errormsg" => $errorMsg
            ]);

            return $result;
        }

        curl_close($ch);

        Log::info('Response Doku',[
            'response' => json_decode($responseJson, true),
            'response_time_seconds' => $executionTime,
            'total_time' => $info['total_time'],
            'connect_time' => $info['connect_time'],
            'namelookup_time' => $info['namelookup_time'],
            'pretransfer_time' => $info['pretransfer_time']
        ]);

        if (is_string($responseJson) && $httpcode == 200) {
            $json=json_decode($responseJson, true);
            $result = [
                "token_id" => $json['response']['payment']['token_id'],
                "url" => $json['response']['payment']['url'],
                "expired_date" =>$json['response']['payment']['expired_date'],
                "status" => 200
            ];
            return $result;
        } else {
            $theResponse = json_decode($responseJson);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Storage::disk('local')->put(
                    "doku/error_" . now()->format('Ymd_His') . ".log",
                    $responseJson
                );
            
                return null;
            }
            
            Storage::disk('local')->put(
                "doku/error_" . now()->format('Ymd_His') . ".log",
                json_encode($theResponse, JSON_PRETTY_PRINT)
            );
            
            if (
                isset($theResponse->message) &&
                (
                    (is_array($theResponse->message) && $theResponse->message[0] === "INVOICE ALREADY USED")
                    || (is_string($theResponse->message) && str_contains($theResponse->message, "INVOICE ALREADY USED"))
                )
            ) {
                return [
                    "status" => 503,
                    "message" => "Invoice terdeteksi telah terbayar. Mohon informasikan ke Tim IT"
                ];
            }
            
            return $theResponse;
        }
    }

    public function checkStatusInvoiceDoku(string $reqId){
        $dateTime = gmdate("Y-m-d H:i:s");
        $dateTime = date(DATE_ISO8601, strtotime($dateTime));
        $dateTimeFinal = substr($dateTime, 0, 19) . "Z";

        $getUrl = Config::getBaseUrl(env('DOKU_PRODUCTION'));

        $targetPath = '/orders/v1/status/'.$reqId;
        $url = $getUrl.$targetPath;

        $header['Client-Id'] = env('DOKU_CLIENT_ID');
        $header['Request-Id'] = $reqId;
        $header['Request-Timestamp'] = $dateTimeFinal;
        $header['Request-Target'] = $targetPath;

        $rawSignature = "Client-Id:" . $header['Client-Id'] . "\n"
            . "Request-Id:" . $header['Request-Id'] . "\n"
            . "Request-Timestamp:" . $header['Request-Timestamp'] . "\n"
            . "Request-Target:" . $header['Request-Target'];

        $sig = base64_encode(hash_hmac('sha256', $rawSignature, env('DOKU_SECRET_KEY'), true));
        $signature = 'HMACSHA256=' . $sig;

        $ch = curl_init($url);  
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Signature:' . $signature,
            'Request-Id:' . $reqId,
            'Client-Id:' . env('DOKU_CLIENT_ID'),
            'Request-Timestamp:' . $dateTimeFinal,
            'Request-Target:' . $targetPath,

        ));
        $responseJson = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (is_string($responseJson) && $httpcode == 200) {
            $json=json_decode($responseJson, true);

            if($json['transaction']['status']=="SUCCESS"){
                
                // $sendToSuccess = DokuController::sendToSuccess([
                //     $reqId,
                //     $json['virtual_account_inquiry']['date']
                // ]);

                // return $sendToSuccess;
                // return true;
                $result = [
                    $reqId,
                    $json['virtual_account_payment']['date'],
                    $json
                ];
                return $result;
            }

            return null;
        } else {
            // echo $responseJson;
            return null;
        }
    }

    public function sendToSuccessDoku(array $data){

        //Variable
        $get = DB::table("data_list")->where("mismass_invoice_id",$data[0])->first();
        $tanggalJam = date("Y-m-d H:i:s", strtotime($data[1]));
        $dateNow = Carbon::now()->setTimezone('Asia/Jakarta');

        //Check Data Exist
        if($get==null){
            $data = [
                "status" => false,
                "title" => "Invoice Tidak Ditemukan."
            ];
            return $data;
        }

        //Check If Already Success
        if($get->payment_status=="SUCCESS"){
            $data = [
                "status" => false,
                "title" => "Invoice Telah Sukses."
            ];
            return $data;
        }

        //Update To Success
        $pindahStatus = DB::table("data_list")->where("mismass_invoice_id",$data[0])->update(['payment_status'=>'SUCCESS','invoice_status'=>'PAID','payment_success_auto_at'=>$tanggalJam]);
        
        if(!$pindahStatus){
            $data = [
                "status" => false,
                "title" => "Invoice Tidak Bisa Diupdate Ke Sukses."
            ];
            return $data;
        }

        //Create Tracking
        $getMsTrack = DB::table("order_list")
                    ->select("ms_track_id")
                    ->where("invoice_id",$data[0])
                    ->get();

        foreach($getMsTrack as $gm){
            if($gm->ms_track_id!=""){
                $dataTracking = [
                    "created_at" => $dateNow,
                    "created_by" => "DOKU & MISMASS",
                    "ms_track_id" => $gm->ms_track_id,
                    "track_status_id" => 11,
                    "track_status_manual_id" => "A",
                    "text" => DB::table("shiptrip_track_status")->where("id","11")->value("value")
                ];
                $updateTracking = DB::table("shiptrip_track_list")->insert($dataTracking);
            }
        }

        //Fill History
        // $dataHistory = [
        //     "codename" => "PS",
        //     "created_at" => date("Y-m-d H:i:s"),
        //     "created_by" => "DOKU & MISMASS",
        //     "description" => $this->controller->createDescForPaymentSuccess($data[0]),
        // ];
        // $insertHistory = DB::table("history_list")->insert($dataHistory);
        // if(!$insertHistory){
        //     return false;
            // $encode = array("status" => "Gagal", "text" => "Gagal Buat History");
            // return json_encode($encode);
        // }

        //Send WA
        // $data = array(
        //     "phone" => $get->cust_type_id=="IND" ? $get->cons_phone : $get->sender_phone,
        //     "fullName" => $get->cust_type_id=="IND" ? $get->cons_first_name." ".$get->cons_middle_name." ".$get->cons_last_name : $get->sender_first_name." ".$get->sender_middle_name." ".$get->sender_last_name,
        //     "invoiceDate" => $this->dateFormatIndo($get->mismass_invoice_date,1),
        //     "invoiceLink" => url('/p')."/".$get->mismass_invoice_link,
        //     "invoice" => $get->mismass_invoice_id,
        //     "paymentLink" => $get->doku_link!=""?url('/payment')."/".$get->doku_link:"", //Link Payment Untuk Doku
        //     "statusPay" => "PAID",
        //     "custType" => DB::table("cust_type_list")->where("id",$get->cust_type_id)->value("name")
        // );
        // $this->invoicePaidSendWA($data);

        //All Process Success
        // $encode = array("status" => 200, "header" => "Berhasil", "text" => "Invoice Berhasil Diupdate");
        // return json_encode($encode);
        $data = [
            "status" => true,
            "title" => "Invoice Berhasil Diupdate."
        ];
        return $data;
    }
    
     // public function generate($params)
    // {
    //     $this->dokuClient->setClientID('DOKU_CLIENT_ID');
    //     $this->dokuClient->setSharedKey(env('DOKU_SECRET_KEY'));
    //     $this->dokuClient->isProduction(env('DOKU_PRODUCTION'));

    //     $this->doku_log("Params Request ", 'PHP-Library Request : ' . json_encode($params, JSON_PRETTY_PRINT), $params['channel']);
    //     if($params['channel']=="dokuva"){
    //         $obj_response = $this->dokuClient->generateDokuVa($params);
    //     }elseif($params['channel']=="bcava"){
    //         $obj_response = $this->dokuClient->generateBcaVa($params);
    //     }elseif($params['channel']=="bniva"){
    //         $obj_response = $this->dokuClient->generateBniVa($params);
    //     }elseif($params['channel']=="briva"){
    //         $obj_response = $this->dokuClient->generateBriVa($params);
    //     }elseif($params['channel']=="bsiva"){
    //         $obj_response = $this->dokuClient->generateBsiVa($params);
    //     }elseif($params['channel']=="cimbva"){
    //         $obj_response = $this->dokuClient->generateCimbVa($params);
    //     }elseif($params['channel']=="danamonva"){
    //         $obj_response = $this->dokuClient->generateDanamonVa($params);
    //     }elseif($params['channel']=="mandiriva"){
    //         $obj_response = $this->dokuClient->generateMandiriVa($params);
    //     }elseif($params['channel']=="permatava"){
    //         $obj_response = $this->dokuClient->generatePermataVa($params);
    //     }

    //     if (isset($obj_response) && !isset($obj_response['error'])) {
    //         echo json_encode($obj_response);
    //         die;
    //     } else {
    //         http_response_code(404);
    //         die;
    //     }
    // }
}
