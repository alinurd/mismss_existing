<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class Whatsapp extends Model
{
    use HasFactory;
    
    private $controller;
    public function __construct()
    {
        $this->controller = new Controller;
    }

    public function createShipmentSendWA($array){

        $message = "Yth, ".$array['fullName']."\n".
                    "\n".
                    "Detail pengiriman anda :\n".
                    "Nomor Pelacakan : *".$array['shipmentNumber']."*\n".
                    "Pengirim : *".$array['fullName']."*\n".
                    "\n".
                    "Klik tautan di bawah ini untuk melacak pengiriman Anda\n".
                    "https://www.mismasslogistic.com/tracking\n".
                    "\n".
                    "Terima kasih\n".
                    "*MISMASS LOGISTIC*\n".
                    "www.mismasslogistic.com";

        if($array['templateId']==0){
            $message = "Halo ".$array['fullName']."\n".
                        "\n".
                        "Your shipping details :\n".
                        "Tracking Number : *".$array['shipmentNumber']."*\n".
                        "Shipper : *".$array['fullName']."*\n".
                        "\n".
                        "Click the link below to track your shipment\n".
                        "https://www.mismasslogistic.com/tracking\n".
                        "\n".
                        "Thank You\n".
                        "*MISMASS LOGISTIC*\n".
                        "www.mismasslogistic.com";
        }

        $data = [
            "ref" => $array['reference'],
            "to" => $array['phone'],
            "message" => $message
        ];

        $get = Whatsapp::sendWA($data);
        $result = json_decode($get);
        $result->phone = Whatsapp::checkFormatPhoneSendWA($array['phone']);
        $result->ref = Whatsapp::checkRef($array['reference']);
        return $result;
    }
    
    public function createInvoiceSendWA($array){

        $message = "Yth, ".$array['fullName']." , tautan di bawah ini adalah salinan invoice Anda :\n".
                    "\n".
                    $array['invoiceLink']."\n".
                    "\n".
                    "Tanggal invoice : *".$array['invoiceDate']."*\n".
                    "No. invoice : *".$array['invoice']."*\n".
                    "Status pembayaran : *".$array['statusPay']."*\n".
                    "Customer : *".$array['custType']."*\n".
                    "Link pembayaran : *".$array['paymentLink']."*\n".
                    "\n".
                    "Harap lakukan pembayaran sesegera mungkin sebelum kami mengirimkan paket Anda.\n".
                    "\n".
                    "Terima kasih\n".
                    "www.mismasslogistic.com";  

        if($array['templateId']==0){
            $message = "Halo ".$array['fullName']." , below link is your copy of invoice :\n".
                        "\n".
                        $array['invoiceLink']."\n".
                        "\n".
                        "Invoice Date : *".$array['invoiceDate']."*\n".
                        "Invoice No. : *".$array['invoice']."*\n".
                        "Payment Status : *".$array['statusPay']."*\n".
                        "Customer : *".$array['custType']."*\n".
                        "Payment Link : *".$array['paymentLink']."*\n".
                        "\n".
                        "Please make payment at your earliest convenience before we deliver your parcel.\n".
                        "\n".
                        "Thank You\n".
                        "www.mismasslogistic.com";  
        }

        $data = [
            "ref" => $array['reference'],
            "to" => $array['phone'],
            "message" => $message
        ];

        $get = Whatsapp::sendWA($data);
        $result = json_decode($get);
        $result->phone = Whatsapp::checkFormatPhoneSendWA($array['phone']);
        $result->ref = Whatsapp::checkRef($array['reference']);
        return $result;
    }

    public function revisiInvoiceSendWA($array){

        $message = "*Revisi Invoice*\n".
                    "\n".
                    "Yth, ".$array['fullName']." , tautan di bawah ini adalah salinan invoice Anda :\n".
                    "\n".
                    $array['invoiceLink']."\n".
                    "\n".
                    "Tanggal invoice : *".$array['invoiceDate']."*\n".
                    "No. invoice : *".$array['invoice']."*\n".
                    "Status pembayaran : *".$array['statusPay']."*\n".
                    "Customer : *".$array['custType']."*\n".
                    "Link pembayaran : *".$array['paymentLink']."*\n".
                    "\n".
                    "Harap lakukan pembayaran sesegera mungkin sebelum kami mengirimkan paket Anda.\n".
                    "\n".
                    "Terima kasih\n".
                    "www.mismasslogistic.com";  

        if($array['templateId']==0){
            $message = "*Revisi Invoice*\n".
                        "\n".
                        "Halo ".$array['fullName']." , below link is your copy of invoice :\n".
                        "\n".
                        $array['invoiceLink']."\n".
                        "\n".
                        "Invoice Date : *".$array['invoiceDate']."*\n".
                        "Invoice No. : *".$array['invoice']."*\n".
                        "Payment Status : *".$array['statusPay']."*\n".
                        "Customer : *".$array['custType']."*\n".
                        "Payment Link : *".$array['paymentLink']."*\n".
                        "\n".
                        "Please make payment at your earliest convenience before we deliver your parcel.\n".
                        "\n".
                        "Thank You\n".
                        "www.mismasslogistic.com";  
        }

        $data = [
            "ref" => $array['reference'],
            "to" => $array['phone'],
            "message" => $message
        ];

        $get = Whatsapp::sendWA($data);
        $result = json_decode($get);
        $result->phone = Whatsapp::checkFormatPhoneSendWA($array['phone']);
        $result->ref = Whatsapp::checkRef($array['reference']);
        if(isset($result->mstimeout)){
            if($result->mstimeout){
                $result->success = false;
                return $result;
            }
        }
        return $result;
    }

    public function sendWA($data){

        $reqUrl = "https://api.wooblazz.com/o1/send";

        $headersList = [
            "Accept: */*",
            "Content-Type: application/json",
            "Authorization: ".Whatsapp::getAuthorization($data['ref'])
        ];
    
        $dt = [
            "to" => Whatsapp::checkFormatPhoneSendWA($data['to']),
            "type" => "chat",
            "message" => $data['message']
        ];
    
        $ch = curl_init($reqUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headersList);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dt));
    
        $start = microtime(true);
        $response = curl_exec($ch);
        $end = microtime(true);
        $executionTime = $end - $start;
        $info = curl_getinfo($ch);
        $error = curl_error($ch);

        if($response === false){
            $errorCode = curl_errno($ch);
            $errorMsg = curl_error($ch);
            $result = json_encode([
                "mstimeout" => true,
                "status" => $errorCode,
                "errormsg" => $errorMsg,
                "message" => "Wooblaz Lama Memberikan Respon."
            ]);

            Log::warning('Response Timeout Wooblaz',[
                "status" => $errorCode,
                "errormsg" => $errorMsg
            ]);

            return $result;
        }
    
        curl_close($ch);

        Log::info('Response Wooblaz',[
            'response' => json_decode($response, true),
            'response_time_seconds' => $executionTime,
            'total_time' => $info['total_time'],
            'connect_time' => $info['connect_time'],
            'namelookup_time' => $info['namelookup_time'],
            'pretransfer_time' => $info['pretransfer_time']
        ]);
        
        return $response;
    }

    private function getAuthorization($code = 0){
        //None
        //Vivi 20
        //Ramaria 18
        //Syarifah 30

        if($code==20){
            return env("WA_AUTH_VIVI");
        }

        if($code==18){
            return env("WA_AUTH_RAMARIA");
        }

        if($code==30){
            return env("WA_AUTH_SYARIFAH");
        }

        return env("WA_AUTH_NONE");
    }

    private function checkRef($code){
        if($code==20){
            return "VIVI";
        }

        if($code==18){
            return "RAMARIA";
        }

        if($code==30){
            return "SYARIFAH";
        }

        return "NOREF";
    }

    private function checkFormatPhoneSendWA($phone){
        $stat = $phone;
        // if(substr($phone,0,1)=="0"){
        //     $stat = "62".substr($phone,1,strlen($phone));
        // }
        
        if(substr($phone,0,1)=="+"){
            $stat = substr($phone,1,strlen($phone));
            // if(substr($phone,1,3)=="620"){
            //     $stat = substr($phone,1,2).substr($phone,4,strlen($phone));   
            // }
        }
        
        // if(substr($phone,0,3)=="620"){
        //     $stat = substr($phone,0,2).substr($phone,3,strlen($phone));
        // }
        return $stat;
    }
    
    public function checkTimeBlast($array)
    {
        //0 uniqId (Resi Tracking / Order Id)
        //1 username
        //2 BR/BI

        $waTemplateId = 0;
        $now = date("Y-m-d H:i:s");

        $getLastTemplate = DB::table("wa_blast_list")
                ->select("template_id")
                ->where("kind_id",$array[2])
                ->orderBy("created_at","DESC")
                ->first();

        $getLastBlast = DB::table("wa_blast_list")
                        ->select("created_at")
                        ->orderBy("created_at","DESC")
                        ->first();
        
        $data = [
            "created_at" => $now,
            "created_by" => $array[1],
            "kind_id" => $array[2],
            "uniq_id" => $array[0],
            "template_id" => $waTemplateId
        ];

        if($getLastBlast==null){

            $insert = DB::table("wa_blast_list")
                        ->insert($data);

            $array = [
                "status" => true,
                "now" => $now,
                "waTemplateId" => $waTemplateId,
                "queueTime" => 0,
            ];
            return $array;
        }

        //Jika waktu sekarang - waktu terakhir kurang dari X menit maka gagal
        $diff = strtotime($now)-strtotime($getLastBlast->created_at);
        if($diff<env('WA_DURATION')){
            $array = [
                "status" => false,
                "waTemplateId" => $waTemplateId,
                "queueTime" => env('WA_DURATION')-$diff,
                "createdTime" => $this->controller->dateFormatIndo($getLastBlast->created_at,2),
                "now" => $this->controller->dateFormatIndo($now,2)
            ];
            return $array;
        }

        //Jika sebelumnya adalah template 0
        if($getLastTemplate!=null){
            if($getLastTemplate->template_id==0){
                $waTemplateId=1;
            }
        }

        $data['template_id'] = $waTemplateId;
        $insert = DB::table("wa_blast_list")
                        ->insert($data);


        $array = [
            "status" => true,
            "now" => $now,
            "waTemplateId" => $waTemplateId,
            "queueTime" => 0,
        ];
        return $array;
    }

    //     public function webFormSendWA($id){
//         $get = DB::table("order_list")->where("id",$id)->first();
//         $message = "*Auto-Generated Message*

// Halo ".$get->first_name." ".$get->middle_name." ".$get->last_name." We have received your registration with below details : 

// Full Name : *".$get->first_name." ".$get->middle_name." ".$get->last_name."*
// Whatsapp No : *".$get->phone."*
// Email : *".$get->email."*
// Full Address : *".$get->address." ".$get->sub_district." ".$get->district." ".$get->city." ".$get->prov." ".$get->postal_code." * 
            
// Kindly wait for further confirmations. Thank You.

// Send from website https://www.mismasslogistic.com";

//     $this->sendWA($phone,$message);
        
//     }
    
//     public function createOrderSendWA($id){
//         $get = DB::table("order_list")->where("id",$id)->first();
//         $message = "*Auto-Generated Message*

// Halo ".$get->first_name." ".$get->middle_name." ".$get->last_name." We have created your registration with below details : 

// Full Name : *".$get->first_name." ".$get->middle_name." ".$get->last_name."*
// Whatsapp No : *".$get->phone."*
// Email : *".$get->email."*
// Full Address : *".$get->address." ".$get->sub_district." ".$get->district." ".$get->city." ".$get->prov." ".$get->postal_code."* 
            
// Kindly wait for further confirmations. Thank You.

// Send from website https://www.mismasslogistic.com";

//     $this->sendWA($get->phone,$message);
        
//     }

//     public function invoicePaidSendWA($array){
//         $message = "*Auto-Generated Message*

// Halo ".$array['fullName']." , Thank you for the payment. Below link is your copy of invoice :

// ".$array['invoiceLink']."

// Invoice Date : *".$array['invoiceDate']."*
// Invoice No. : *".$array['invoice']."*
// Payment Status : *".$array['statusPay']."*
// Customer : *".$array['custType']."*
// Payment Link : *".$array['paymentLink']."*

// We will kindly process the deliver. Thank you.

// Thank You
// www.mismasslogistic.com";  

//     $this->sendWA($array['phone'],$message);
//     }
    
//     public function createResiConsSendWA($invoiceId){
//     $get = DB::table("data_list")->select("cons_phone","cons_first_name","cons_middle_name","cons_last_name","shipping_created_at","shipping_number","forwarder_id","forwarder_name","mismass_invoice_id","invoice_status")->where("mismass_invoice_id",$invoiceId)->first();
//     $shippingDate = $this->dateFormatIndo($get->shipping_created_at,1);
//     $shipper = $get->forwarder_id=="MISMASS"?$get->forwarder_id:$get->forwarder_name;
    
//     $message = "*Auto-Generated Message*
    
// Hello *".$get->cons_first_name." ".$get->cons_middle_name." ".$get->cons_first_name."*

// Your shipping details :
// Shipping Date : *".$shippingDate."*
// Shipping Number : *".$get->shipping_number."*
// Shipper : *".$shipper."*

// For tracking your shipment, please kindly check the link below.
// https://www.mismasslogistic.com/tracking

// Thank You
// *MISMASS LOGISTIC*
// www.mismasslogistic.com";
    
//     $this->sendWA($get->cons_phone,$message);
//     }
}
