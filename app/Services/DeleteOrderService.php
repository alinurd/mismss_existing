<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Tracking;

class DeleteOrderService
{
    protected $endpointModel,$controller,$trackingModel;

    public function __construct()
    {
        $this->controller = new Controller;
        $this->trackingModel = new Tracking;
    }

    public function execute($array){
        $username = Auth::user()->username;
        $lawas = DB::table("order_list")->where("id",$array['id'])->first();

        $arrayLog = [
            "id" => $array['id'],
            "custId" => $lawas->cust_id,
            "note" => $array['note']
        ];
        $this->stepLog(1, 'Create History', $arrayLog);

        $dataHistory=[
            "codename" => "HO",
            "created_at" => $array['now'],
            "created_by" => $username,
            "description" => "<b>Hapus Order</b> dengan detail,<br><br>
            ID Sistem : <b>".$lawas->id."</b><br>
            Alasan : <b>".$array['note']."</b><br>
            Tipe : <b>".DB::table("cust_type_list")->where("id",$lawas->cust_type_id)->value("name")."</b><br>
            First Name : <b>".$lawas->first_name."</b><br>
            Middle Name : <b>".$lawas->middle_name."</b><br>
            Last Name : <b>".$lawas->last_name."</b><br>
            Telpon : <b>".$lawas->phone."</b><br>
            Email : <b>".$lawas->email."</b><br>
            Alamat : <b>".$lawas->address.", ".$lawas->sub_district.", ".$lawas->district.", ".$lawas->city.", ".$lawas->prov.", ".$lawas->postal_code."</b>",
        ];
        $insertHistory = DB::table('history_list')->insert($dataHistory);
        if(!$insertHistory){
            $output = array("status" => 500, "title" => "Gagal", "text" => "Gagal Buat History");
            return $output;
        }

        $this->stepLog(2, 'Delete Data');
        $delete = DB::table("order_list")->where('id', '=', $array['id'])->delete();

        if (!$delete) {
            $output = array("status" => 500, "title" => "Gagal", "text" => "Gagal Hapus Order");
            return $output;
        }

        $this->endLog();
        $output = array("status" => 200);
        return $output;
    }

    private function stepLog($step, $msg, $context = []){
        Log::channel('deleteorder')->info("STEP {$step}: {$msg}",$context);
    }

    private function endLog(){
        Log::channel('deleteorder')->info("=============================================");
    }
}