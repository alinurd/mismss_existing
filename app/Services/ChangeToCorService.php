<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Tracking;

class ChangeToCorService
{
    protected $endpointModel,$controller,$trackingModel;

    public function __construct()
    {
        $this->controller = new Controller;
        $this->trackingModel = new Tracking;
    }

    public function changeToCorButton($array)
    {
        $this->controller->roleAccess();

        if(!Auth::user()->shiplist_pindah_cor){
            return "";
        }

        if($array["msTrackId"]==""){
            return "";
        }

        if($array["custTypeId"]=="COR"){
            return "";
        }

        return "<a class='dropdown-item pointlink' data-id='".$array['id']."' data-name='".$array['firstName']."' id='changeToCorBtn'>Pindah Ke Corporate</a>";
    }

    public function execute($orderId){
        
        //Prepare For Checking
        $now = date("Y-m-d H:i:s");
        $checkOrder = DB::table("order_list")
                ->select("id","ms_track_id","cust_id","cust_type_id","invoice_id")
                ->where("id",$orderId)
                ->first();

        $logCheckData = [
            "orderId" => $orderId
        ];
        $this->stepLog(1, 'Checking Data', $logCheckData);

        //Checking Data 
        $checkingPhase = $this->checkDataChangeToCor($checkOrder);
        if($checkingPhase['status']===500){
            return $checkingPhase;
        }

        //Create New COR Data With IND Data. If COR Data Is Unavailable
        $newCustId = $checkingPhase['newCustId'];
        $oldCustId = $checkingPhase['oldCustId'];
        if($checkingPhase['status']===200){
            $arrayCopyData = [
                "now" => $now,
                "oldCustId" => $oldCustId
            ];
            $copyData = $this->copyDataIndToCor($arrayCopyData);
            $newCustId = $copyData['newCustId'];
        }

        $arrayData = [
            "now" => $now,
            "orderId" => $orderId,
            "oldCustId" => $oldCustId,
            "newCustId" => $newCustId,
            "msTrackId" => $checkOrder->ms_track_id
        ];

        //Update Shiptrip Data
        $changeShipTripData = $this->changeShipTripData($arrayData);

        //Update Orderlist Data
        $changeOrderListData = $this->changeOrderListData($arrayData);

        //Create History
        $createHistory = $this->createHistory($arrayData);

        $output = [
            'status' => 200,
            'title' => "Berhasil",
            'msg' => "Data Berhasil Dipindah ke Corporate",
        ];

        $this->endLog();
        
        return $output;
    }

    private function checkDataChangeToCor($checkOrder){
         //cek cust type
         if($checkOrder->cust_type_id!=="IND"){
            $output = [
                'status' => 500,
                'orderId' => $checkOrder->id,
                'title' => "Gagal",
                'msg' => "Data Telah Menjadi Corporate",
            ];
            $this->stepLog(2, 'Data Already Corporate', $output);
            return $output;
        }

        //cek Invoice
        if($checkOrder->invoice_id!==""){
            $output = [
                'status' => 500,
                'orderId' => $checkOrder->id,
                'title' => "Gagal",
                'msg' => "Data Telah Memiliki Invoice",
            ];
            $this->stepLog(2, 'Data Already Has Invoice Id', $output);
            return $output;
        }

        //Cek Apakah Sudah Ada Customer Corporate Dengan Nomor Telpon Yg Sama
        $checkCust = DB::table("cust_list")
                        ->select("phone")
                        ->where("id",$checkOrder->cust_id)
                        ->first();

        $checkCust2 = DB::table("cust_list")
                        ->select("id")
                        ->where("phone",$checkCust->phone)
                        ->where("cust_type_id","COR")
                        ->first();

        if($checkCust2!=null){
            $output = [
                'status' => 201,
                'orderId' => $checkOrder->id,
                'oldCustId' => $checkOrder->cust_id,
                'newCustId' => $checkCust2->id
            ];
            $this->stepLog(2, 'Phone Has Been Registered',$output);
            return $output;
        }

        $output = [
            'status' => 200,
            'orderId' => $checkOrder->id,
            'oldCustId' => $checkOrder->cust_id,
            'newCustId' => ''
        ];
        $this->stepLog(2, 'Phone Not Registered Yet',$output);
        return $output;
    }

    private function copyDataIndToCor($array){

        $username = Auth::user()->username;
        $getData = DB::table("cust_list")
                    ->where("id",$array['oldCustId'])
                    ->first();
        $insertData = [
            "cust_type_id" => "COR",
            "ind_to_cor" => 1,
            "created_at" => $array['now'],
            "created_by" => $username,
            "updated_at" => $array['now'],
            "updated_by" => $username,
            "first_name" => $getData->first_name,
            "middle_name" => $getData->middle_name,
            "last_name" => $getData->last_name,
            "country_id" => $getData->country_id,
            "know_from_id" => $getData->know_from_id,
            "phone" => $getData->phone,
            "address" => $getData->address,
            "sub_district" => $getData->sub_district,
            "district" => $getData->district,
            "city" => $getData->city,
            "prov" => $getData->prov,
            "postal_code" => $getData->postal_code,
            "email" => $getData->email,
            "reference" => $getData->reference,
            "link_ref" => $getData->link_ref
        ];
        $newCustId = DB::table('cust_list')
                    ->insertGetId($insertData);

        $output = [
            'status' => 200,
            'oldCustId' => $array['oldCustId'],
            'newCustId' => $newCustId
        ];
        $this->stepLog(3, 'Copy Data IND To COR',$output);
        return $output;
    }

    private function changeShipTripData($array){
        $update = DB::table("shiptrip_list")
                    ->where("ms_track_id",$array['msTrackId'])
                    ->update([
                        'cust_id' => $array['newCustId'],
                        'ind_to_cor' => 1
                    ]);

        $logArray = [
            'msTrackId' => $array['msTrackId'],
            'cust_id' => $array['newCustId'],
            'ind_to_cor' => 1
        ];
        $this->stepLog(4, 'Update ShipTrip Data', $logArray);
    }

    private function changeOrderListData($array){
        $update = DB::table("order_list")
                    ->where("id",$array['orderId'])
                    ->update([
                        'cust_id' => $array['newCustId'],
                        'cust_type_id' => "COR",
                        'ind_to_cor' => 1
                    ]);

        $logArray = [
            'orderId' => $array['orderId'],
            'cust_id' => $array['newCustId'],
            'cust_type_id' => "COR",
            'ind_to_cor' => 1
        ];
        $this->stepLog(5, 'Update Order List Data', $logArray);
    }

    private function createHistory($array){
        $dataHistory = [
            "codename" => "ITC",
            "created_at" => $array['now'],
            "created_by" => Auth::user()->username,
            "whatsapp_desc" => "",
            "description" => $this->createDesc($array),
        ];
        $insertHistory = DB::table("history_list")->insert($dataHistory);

        $this->stepLog(6, 'Create History', $array);
    }

    private function createDesc($array){
        return "<b>Change IND To COR</b> dengan detail,<br><br>
            Order Id : <b>".$array['orderId']."</b><br>
            resi Tracking : <b>".$array['msTrackId']."</b><br>
            old Cust Id : <b>".$array['oldCustId']."</b><br>
            new Cust Id : <b>".$array['newCustId']."</b><br>";
    }

    private function stepLog($step, $msg, $context = []){
        Log::channel('changetocor')->info("STEP {$step}: {$msg}",$context);
    }

    private function endLog(){
        Log::channel('changetocor')->info("=============================================");
    }
}