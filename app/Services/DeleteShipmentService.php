<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DeleteShipmentService
{
    protected $endpointModel,$controller;

    public function __construct()
    {
        $this->controller = new Controller;
    }

    public function execute($array)
    {
        $this->stepLog(1, 'Create History', $array);
        $createHistory = $this->createHistory($array);
        if(!$createHistory){
            $output = array("status" => 500, "title" => "Gagal", "text" => "Gagal Buat History!");
            return $output;
        }

        $this->stepLog(2, 'Delete Consignee');
        $deleteConsignee = $this->deleteConsignee($array);
        if(!$deleteConsignee){
            $output = array("status" => 500, "title" => "Gagal", "text" => "Gagal Hapus Cons List!");
            return $output;
        }

        $this->stepLog(3, 'Delete Image');
        $deleteImage = $this->deleteImage($array);
        if(!$deleteImage){
            $output = array("status" => 500, "title" => "Gagal", "text" => "Gagal Hapus Image List!");
            return $output;
        }

        $this->stepLog(4, 'Delete Foreign Track');
        $deleteForeign = $this->deleteForeign($array);
        if(!$deleteForeign){
            $output = array("status" => 500, "title" => "Gagal", "text" => "Gagal Hapus Foreign Track List!");
            return $output;
        }

        $this->stepLog(5, 'Delete Track Status');
        $deleteTrackStats = $this->deleteTrackStats($array);
        if(!$deleteTrackStats){
            $output = array("status" => 500, "title" => "Gagal", "text" => "Gagal Hapus Track Status List!");
            return $output;
        }

        $this->stepLog(6, 'Delete Shipment Trip');
        $deleteShip = $this->deleteShip($array);
        if(!$deleteShip){
            $output = array("status" => 500, "title" => "Gagal", "text" => "Gagal Hapus Shipment List!");
            return $output;
        }

        $this->endLog();
        $output = array("status" => 200, "title" => "Berhasil", "text" => "Hapus Shipment Berhasil!");
        return $output;
    }

    private function createHistory($array)
    {
        $username = Auth::user()->username;
        $get = DB::table("shiptrip_list")
        ->selectRaw("shiptrip_list.ms_track_id,
                shiptrip_list.track_type_id,
                shiptrip_list.track_group_type_id,
                shiptrip_list.track_status_id,
                shiptrip_track_status.next_step,
                shiptrip_track_status.value AS now_step,
                shiptrip_list.drop_man_created_at,
                shiptrip_list.drop_created_by,
                shiptrip_list.drop_updated_at,
                shiptrip_list.drop_updated_by,
                shiptrip_list.to_sg_man_created_at,
                shiptrip_list.to_sg_created_by,
                service_list.name AS serv_name,
                CONCAT_WS(' - ',warehouse_list.id,warehouse_list.location) AS ware_name,
                cust_list.id AS cons_id,
                CONCAT_WS(' ',cust_list.first_name,cust_list.middle_name,cust_list.last_name) AS cons_full_name,
                cust_list.phone AS cons_phone,
                cust_list.address AS cons_address,
                cust_list.city AS cons_city,
                cust_list.prov AS cons_prov,
                cust_list.postal_code AS cons_postal_code,
                CONCAT_WS(' ',shiptrip_cust_list.first_name,shiptrip_cust_list.middle_name,shiptrip_cust_list.last_name) AS send_full_name,
                shiptrip_cust_list.phone AS send_phone,
                CONCAT_WS(' ',(SELECT COUNT(shiptrip_foreign_track_list.id) FROM shiptrip_foreign_track_list WHERE shiptrip_foreign_track_list.ms_track_id=shiptrip_list.ms_track_id),'Resi LN') AS total_foreign,
                (SELECT GROUP_CONCAT(shiptrip_foreign_track_list.id SEPARATOR ', ') FROM shiptrip_foreign_track_list WHERE shiptrip_foreign_track_list.ms_track_id=shiptrip_list.ms_track_id GROUP BY ms_track_id) AS foreign_tracks")
        ->join("service_list", "service_list.id", "=", "shiptrip_list.service_id")
        ->join("cust_list", "cust_list.id", "=", "shiptrip_list.cust_id")
        ->join("warehouse_list", "warehouse_list.id", "=", "service_list.warehouse_id")
        ->join("shiptrip_track_status", "shiptrip_track_status.id", "=", "shiptrip_list.track_status_id")
        ->join("shiptrip_cust_list", "shiptrip_cust_list.id", "=", "shiptrip_list.ms_track_id")
        ->join("shiptrip_foreign_track_list", "shiptrip_foreign_track_list.ms_track_id", "=", "shiptrip_list.ms_track_id")
        ->where("shiptrip_list.ms_track_id",$array['id'])
        ->groupBy("shiptrip_list.ms_track_id")
        ->first();

        $latestTrackStatus = DB::table("shiptrip_track_list")
                                ->select("created_at","text")
                                ->where("ms_track_id",$array['id'])
                                ->orderBy("created_at","DESC")
                                ->first();

        //History Delete
        $dataHistory=[
            "codename" => "HH",
            "created_at" => $array['now'],
            "created_by" => $username,
            "description" => "<b>Hapus Shipment</b> dengan detail,<br><br>
            Resi : <b>".$get->ms_track_id."</b><br>
            Alasan : <b>".$array['note']."</b><br>
            Waktu Tracking Terakhir : <b>".$this->controller->dateFormatIndo($latestTrackStatus->created_at,2)."</b><br>
            Status Tracking Terakhir : <b>".$latestTrackStatus->text."</b><br><br>
            Detail : <br>
            Tgl Drop : <b>".$this->controller->dateFormatIndo($get->drop_man_created_at,2)."</b><br>
            Status Resi : <b>".($get->track_type_id=="PRM"?"Primary":"Secondary")."</b><br>
            Warehouse : <b>".$get->ware_name."</b><br>
            Service : <b>".$get->serv_name."</b><br>
            Sender : <b>".$get->send_full_name."</b><br>
            Sender Phone : <b>".$get->send_phone."</b><br>
            Consignee ID : <b>".$get->cons_id."</b><br>
            Consignee : <b>".$get->cons_full_name."</b><br>
            Consignee Phone : <b>".$get->cons_phone."</b><br>
            Consignee Address : <b>".$get->cons_address.", ".$get->cons_city.", ".$get->cons_prov."</b><br>
            Resi Luar Negeri : <b>".$get->foreign_tracks."</b><br>"
        ];
        $insertHistory = DB::table('history_list')->insert($dataHistory);
        if(!$insertHistory){
            $output = false;
            return $output;
        }

        $output = true;
        return $output;

    }

    private function deleteConsignee($array)
    {
        //Delete Consignee
        $deleteCons = DB::table("shiptrip_cust_list")->where("id",$array['id'])->delete();
        if(!$deleteCons){
            $output = false;
            return $output;
        }

        $output = true;
        return $output;
    }

    private function deleteImage($array)
    {
        //Delete Image
        //Delete File Image
        $getImg = DB::table("shiptrip_image_list")->selectRaw("id,ext")->where("ms_track_id",$array['id'])->get();
        if(count($getImg)>0){
            foreach($getImg as $g){
                unlink(public_path('assets/photos/').$g->id.".".$g->ext);
            }
            //Delete Image DB
            $deleteImg = DB::table("shiptrip_image_list")->where("ms_track_id",$array['id'])->delete();
            if(!$deleteImg){
                $output = false;
                return $output;
            }
        }
        $output = true;
        return $output;
    }

    private function deleteForeign($array)
    {
        //Delete Foreign Track
        $deleteForeign = DB::table("shiptrip_foreign_track_list")->where("ms_track_id",$array['id'])->delete();
        if(!$deleteForeign){
            $output = false;
            return $output;
        }
        $output = true;
        return $output;
    }

    private function deleteTrackStats($array)
    {
        //Delete Tracking Status
        $deleteTrackStats = DB::table("shiptrip_track_list")->where("ms_track_id",$array['id'])->delete();
        if(!$deleteTrackStats){
            $output = false;
            return $output;
        }
        $output = true;
        return $output;
    }

    private function deleteShip($array)
    {
        //Delete Shipment List
        $deleteShip = DB::table("shiptrip_list")->where("ms_track_id",$array['id'])->delete();
        if(!$deleteShip){
            $output = false;
            return $output;
        }
        $output = true;
        return $output;
    }

    private function stepLog($step, $msg, $context = []){
        Log::channel('deleteshipment')->info("STEP {$step}: {$msg}",$context);
    }

    private function endLog(){
        Log::channel('deleteshipment')->info("=============================================");
    }

}