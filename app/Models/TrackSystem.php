<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TrackSystem extends Model
{
    use HasFactory;

    private $controller;

    public function __construct()
    {
        $this->controller = new Controller;
    }

    // public function getWayBills($id){
    //     //check MSTrackID
    //     $checkOne = DB::table("shiptrip_track_list")->where("ms_track_id",$id)->first();
    //     if($checkOne==null){
    //         $data['wayBill'] = "";
    //         return $data;
    //     }
    //     $waybill[] = $id;

    //     //check customer type id
    //     $custTypeId = DB::table("shiptrip_list")
    //                     ->select("cust_list.cust_type_id")
    //                     ->join("cust_list", "cust_list.id", "=", "shiptrip_list.cust_id")
    //                     ->where("ms_track_id",$id)->value("cust_list.cust_type_id");
    //     $data['custTypeId'] = $custTypeId;

    //     //check MSTrackID Secondary
    //     $checkTwo = DB::table("shiptrip_secondary_list")->select("id")->where("ms_track_id",$id)->get();
    //     if(count($checkTwo)>0){
    //         foreach($checkTwo as $c){
    //             $waybill[] = $c->id;
    //         }
    //     }

    //     // dd($waybill);

    //     //check if MSTrackID is in the consolidation merger MSTrackID and has invoice or not
    //     $checkThree = DB::table("order_list")->select("invoice_id","cust_type_id")->where("ms_track_id",$id)->first();
    //     if($checkThree==null||$checkThree->invoice_id==""){
    //         $data['wayBill'] = $waybill;
    //         return $data;
    //     }

    //     //If has Invoice
    //     $checkFour = DB::table("order_list")->select("ms_track_id")->where("invoice_id",$checkThree->invoice_id)->get();
    //     if(count($checkFour)>1){
    //         foreach($checkFour as $cf){
    //             if($cf->ms_track_id!=$id){
    //                 if(array_search($cf->ms_track_id,$waybill)==""){
    //                     $waybill[] = $cf->ms_track_id;
    //                 }
    //             }
    //         }
    //     }

    //     //check if MSTrackID has shipping Number or not
    //     $checkFive = DB::table("data_list")->select("shipping_number")->where("mismass_invoice_id",$checkThree->invoice_id)->first();
    //     if($checkFive->shipping_number==""){
    //         $data['wayBill'] = $waybill;
    //         return $data;
    //     }

    //     //If has shipping_number
    //     if($custTypeId=="COR"){
    //         $checkSix = DB::table("data_list")->select("shipping_number")->where("mismass_invoice_id",$checkThree->invoice_id)->get();
    //         foreach($checkSix as $cs){
    //             $waybill[] = $cs->shipping_number;
    //         }
    //     }

    //     //Last Return
    //     $data['wayBill'] = $waybill;
    //     return $data;
    // }
    public function getWayBills($id){
        //check MSTrackID
        $checkOne = DB::table("shiptrip_track_list")->where("ms_track_id",$id)->first();
        if($checkOne==null){
            $data['wayBill'] = "";
            return $data;
        }
        $waybill[] = ["id"=>$id,"txt"=>$id];

        //check customer type id
        $custTypeId = DB::table("shiptrip_list")
                        ->select("cust_list.cust_type_id")
                        ->join("cust_list", "cust_list.id", "=", "shiptrip_list.cust_id")
                        ->where("ms_track_id",$id)->value("cust_list.cust_type_id");
        $data['custTypeId'] = $custTypeId;

        //check MSTrackID Secondary
        $checkTwo = DB::table("shiptrip_secondary_list")->select("id")->where("ms_track_id",$id)->get();
        if(count($checkTwo)>0){
            foreach($checkTwo as $c){
                $waybill[] = ["id"=>$c->id,"txt"=>$c->id];
            }
        }

        // dd($waybill);

        //check if MSTrackID is in the consolidation merger MSTrackID and has invoice or not
        $checkThree = DB::table("order_list")->select("invoice_id","cust_type_id")->where("ms_track_id",$id)->first();
        if($checkThree==null||$checkThree->invoice_id==""){
            $data['wayBill'] = $waybill;
            return $data;
        }

        //If has Invoice
        $checkFour = DB::table("order_list")->select("ms_track_id")->where("invoice_id",$checkThree->invoice_id)->get();
        if(count($checkFour)>1){
            foreach($checkFour as $cf){
                if($cf->ms_track_id!=$id){
                    if(array_search($cf->ms_track_id,$waybill)==""){
                        $waybill[] = ["id"=>$cf->ms_track_id,"txt"=>$cf->ms_track_id];
                    }
                }
            }
        }

        //check if MSTrackID has shipping Number or not
        $checkFive = DB::table("data_list")->select("shipping_number")->where("mismass_invoice_id",$checkThree->invoice_id)->first();
        if($checkFive->shipping_number==""){
            $data['wayBill'] = $waybill;
            return $data;
        }

        //If has shipping_number
        if($custTypeId=="COR"){
            $checkSix = DB::table("data_list")->select("shipping_number")->where("mismass_invoice_id",$checkThree->invoice_id)->get();
            foreach($checkSix as $cs){
                $getLastTracking = DB::table("shiptrip_track_list")
                                    ->select("title")
                                    ->join("shiptrip_track_status", "shiptrip_track_list.track_status_id", "=", "shiptrip_track_status.id")
                                    ->where("ms_track_id",$cs->shipping_number)
                                    ->orderBy("created_at","DESC")
                                    ->first();
                $waybill[] = ["id"=>$cs->shipping_number,"txt"=>$cs->shipping_number." (".$getLastTracking->title.")"];
            }
        }

        //Last Return
        $data['wayBill'] = $waybill;
        return $data;
    }

    public function loadTracking($data,$select){
        $msTrackId = $data['wayBill'][0]['id'];
        $dataCust = array();
        $dataCust['shipmentDate'] = "-";
        $dataCust['wareId'] = "-";
        $dataCust['wareLoc'] = "-";
        $dataCust['servName'] = "-";
        $dataCust['fullName'] = "-";
        $dataCust['phone'] = "-";
        $dataCust['fullAddress'] = "-";
        $dataCust['custType'] = "-";
        $dataCust['berat'] = "-";
        $dataCust['item'] = "-";
        $dataCust['cbm'] = "-";

        //check if it shipping number or not
        $isShipNum = DB::table("data_list")->where("shipping_number",$msTrackId)->first();

        if($isShipNum!=null){
            $getDataList = DB::table("data_list")
                        ->selectRaw(
                            "data_list.ms_track_id,
                            data_list.cust_type_id,
                            data_list.shipping_number,
                            service_list.name as servname,
                            data_list.warehouse_id as wareid,
                            warehouse_list.location as wareloc,
                            order_list.to_sg_man_created_at,
                            CONCAT_WS(' ',data_list.sender_first_name,data_list.sender_middle_name,data_list.sender_last_name) AS sender_full_name,
                            CONCAT_WS(' ',data_list.cons_first_name,data_list.cons_middle_name,data_list.cons_last_name) AS cons_full_name,
                            CONCAT_WS(', ',data_list.cons_address,data_list.cons_sub_district,data_list.cons_district,data_list.cons_city,data_list.cons_prov,data_list.cons_postal_code) AS cons_full_address,
                            CONCAT_WS(', ',data_list.sender_address,data_list.sender_sub_district,data_list.sender_district,data_list.sender_city,data_list.sender_prov,data_list.sender_postal_code) AS sender_full_address,
                            SUM(data_list.weight) AS total_kg,
                            SUM('data_list.item') AS total_item,
                            SUM('data_list.cbm') AS total_cbm,
                            data_list.cons_phone,
                            data_list.sender_phone,
                            cust_type_list.name as custtypename"
                        )
                        ->join("order_list", "order_list.id", "=", "data_list.mismass_order_id")
                        ->join("cust_type_list", "cust_type_list.id", "=", "data_list.cust_type_id")
                        ->join("service_list", "service_list.id", "=", "data_list.service_id")
                        ->join("warehouse_list", "warehouse_list.id", "=", "data_list.warehouse_id")
                        ->where("data_list.shipping_number",$msTrackId)
                        ->first();   

            $dataCust['shipmentDate'] = $this->controller->dateFormatIndo($getDataList->to_sg_man_created_at,1);
            $dataCust['wareId'] = $getDataList->wareid;
            $dataCust['wareLoc'] = $getDataList->wareloc;
            $dataCust['servName'] = $getDataList->servname;
            $dataCust['fullName'] = $getDataList->cons_full_name;
            $dataCust['phone'] = $getDataList->cons_phone;
            $dataCust['fullAddress'] = $getDataList->cons_full_address;
            $dataCust['custType'] = $getDataList->custtypename;
            $dataCust['berat'] = $this->controller->pembulatan(round($getDataList->total_kg,2))." Kg (Actual : ".round($getDataList->total_kg,2)." Kg)";
            $dataCust['item'] = $getDataList->total_item;
            $dataCust['cbm'] = round($getDataList->total_cbm,2);
            $dataCust['invoiceNumber'] = "";
            $dataCust['foreignTracks'] = "";

            $getElement = TrackSystem::loadTrackingElement($data,$dataCust,$select);

            return $getElement;
        }

        //check if it has invoice or not
        $invoiceId = DB::table("order_list")->where("ms_track_id",$msTrackId)->value("invoice_id");

        if($invoiceId!=null||$invoiceId!=""){
            $getDataList = DB::table("data_list")
                        ->selectRaw(
                            "data_list.mismass_invoice_link,
                            data_list.mismass_invoice_id,
                            data_list.ms_track_id,
                            data_list.cust_type_id,
                            data_list.shipping_number,
                            service_list.name as servname,
                            data_list.warehouse_id as wareid,
                            warehouse_list.location as wareloc,
                            order_list.to_sg_man_created_at,
                            CONCAT_WS(' ',data_list.sender_first_name,data_list.sender_middle_name,data_list.sender_last_name) AS sender_full_name,
                            CONCAT_WS(' ',data_list.cons_first_name,data_list.cons_middle_name,data_list.cons_last_name) AS cons_full_name,
                            CONCAT_WS(', ',data_list.cons_address,data_list.cons_sub_district,data_list.cons_district,data_list.cons_city,data_list.cons_prov,data_list.cons_postal_code) AS cons_full_address,
                            CONCAT_WS(', ',data_list.sender_address,data_list.sender_sub_district,data_list.sender_district,data_list.sender_city,data_list.sender_prov,data_list.sender_postal_code) AS sender_full_address,
                            SUM(data_list.weight) AS total_kg,
                            SUM(data_list.item) AS total_item,
                            SUM(data_list.cbm) AS total_cbm,
                            data_list.cons_phone,
                            data_list.sender_phone,
                            cust_type_list.name as custtypename"
                        )
                        ->join("order_list", "order_list.id", "=", "data_list.mismass_order_id")
                        ->join("cust_type_list", "cust_type_list.id", "=", "data_list.cust_type_id")
                        ->join("service_list", "service_list.id", "=", "data_list.service_id")
                        ->join("warehouse_list", "warehouse_list.id", "=", "data_list.warehouse_id")
                        ->where("data_list.mismass_invoice_id",$invoiceId)
                        ->first();  
            
            $checkShipDate = DB::table("shiptrip_list")->where("ms_track_id",$msTrackId)->value("to_sg_man_created_at");
            $dataCust['shipmentDate'] = $this->controller->dateFormatIndo($checkShipDate,1);
            $dataCust['wareId'] = $getDataList->wareid;
            $dataCust['wareLoc'] = $getDataList->wareloc;
            $dataCust['servName'] = $getDataList->servname;

            $dataCust['fullName'] = $getDataList->sender_full_name;
            $dataCust['phone'] = $getDataList->sender_phone;
            $dataCust['fullAddress'] = $getDataList->sender_full_address;
            if($getDataList->cust_type_id=="IND"){
                $dataCust['fullName'] = $getDataList->cons_full_name;
                $dataCust['phone'] = $getDataList->cons_phone;
                $dataCust['fullAddress'] = $getDataList->cons_full_address;
            }

            $dataCust['custType'] = $getDataList->custtypename;
            $dataCust['berat'] = $this->controller->pembulatan(round($getDataList->total_kg,2))." Kg (Actual : ".round($getDataList->total_kg,2)." Kg)";
            $dataCust['item'] = $getDataList->total_item;
            $dataCust['cbm'] = round($getDataList->total_cbm,2);
            $dataCust['invoiceNumber'] = "<a class='fw-bold text-inv' href='".url('/p')."/".$getDataList->mismass_invoice_link."' target='_blank'>".$getDataList->mismass_invoice_id."</a><div class='text-view'>Klik nomor invoice untuk melihat</div>";
            $checkResiLN = DB::table("shiptrip_foreign_track_list")
                ->selectRaw("GROUP_CONCAT(shiptrip_foreign_track_list.id SEPARATOR ', ') AS foreign_tracks")
                ->where("ms_track_id",$msTrackId)
                ->get();
            $dataCust['foreignTracks'] = $checkResiLN[0]->foreign_tracks;

            $getElement = TrackSystem::loadTrackingElement($data,$dataCust,$select);

            return $getElement;
        }

        $getDataShipTrip = DB::table("shiptrip_list")
                            ->selectRaw(
                                "shiptrip_list.to_sg_man_created_at,
                                shiptrip_list.track_status_id,
                                service_list.name as servname,
                                service_list.warehouse_id as wareid,
                                warehouse_list.location as wareloc,
                                CONCAT_WS(' ',cust_list.first_name,cust_list.middle_name,cust_list.last_name) AS full_name,
                                CONCAT_WS(', ',cust_list.address,cust_list.sub_district,cust_list.district,cust_list.city,cust_list.prov,cust_list.postal_code) AS full_address,
                                cust_list.cust_type_id as custtypeid,
                                cust_list.phone,
                                cust_type_list.name as custtypename"
                            )
                            ->join("cust_list", "cust_list.id", "=", "shiptrip_list.cust_id")
                            ->join("cust_type_list", "cust_type_list.id", "=", "cust_list.cust_type_id")
                            ->join("service_list", "service_list.id", "=", "shiptrip_list.service_id")
                            ->join("warehouse_list", "warehouse_list.id", "=", "service_list.warehouse_id")
                            ->where("ms_track_id",$msTrackId)
                            ->first();

            // dd($getDataShipTrip);

            $dataCust['shipmentDate'] = $getDataShipTrip->track_status_id==1?"-":$this->controller->dateFormatIndo($getDataShipTrip->to_sg_man_created_at,1);
            $dataCust['wareId'] = $getDataShipTrip->wareid;
            $dataCust['wareLoc'] = $getDataShipTrip->wareloc;
            if($getDataShipTrip->custtypeid=="IND"){
                $dataCust['servName'] = $getDataShipTrip->servname;
            }            
            $dataCust['fullName'] = $getDataShipTrip->full_name;
            $dataCust['phone'] = $getDataShipTrip->phone;
            $dataCust['fullAddress'] = $getDataShipTrip->full_address;
            $dataCust['custType'] = $getDataShipTrip->custtypename;
            $berat = "- Kg";
            $item = "-";
            $cbm = "-";
            $dataCust['invoiceNumber'] = "-";
            $checkResiLN = DB::table("shiptrip_foreign_track_list")
                ->selectRaw("GROUP_CONCAT(shiptrip_foreign_track_list.id SEPARATOR ', ') AS foreign_tracks")
                ->where("ms_track_id",$msTrackId)
                ->get();
            $dataCust['foreignTracks'] = $checkResiLN[0]->foreign_tracks;

            $getElement = TrackSystem::loadTrackingElement($data,$dataCust,$select);

            return $getElement;

    }

    private function getElementOfInvFrgn($dataCust){
        if($dataCust['invoiceNumber']==""){
            return "";
        }

        $array = explode(", ",$dataCust['foreignTracks']);
        $option = "";
        $no = 1;
        foreach($array as $a){
            $option.="<div style='display:flex;gap:2px;'><div style='user-select:none;color:#999999'>".$no.".</div><div>".$a."</div></div>";
            $no++;
        }

        $resiLNEl = "<div class='card collapsed-card'>
                        <div class='card-header' id='lookForeignTracks' style='cursor:pointer;background:#efefef' data-card-widget='collapse'>
                            <div class='card-title'>
                                <div class='fw-bold text-view-resi' style='font-size:14px;color:#555'>Lihat Nomor Resi</div>
                                <div style='font-size:14px;color:#838282' class='text-view'>Klik untuk menampilkan / menyembunyikan daftar nomor resi</div>
                            </div>
                        </div>
                        <div class='card-body' id='foreignTracksData' style='padding-left:1.25rem;padding-right:1.25rem'>"
                            .$option.
                        "</div>
                    </div>";

        $detail = "<hr>
                    <table>
                        <tr><td>No. Invoice</td><td>".$dataCust['invoiceNumber']."</td></tr>
                        <tr><td>Resi Luar Negeri</td><td>".$resiLNEl."</td></tr>
                    </table>";

        return $detail;
    }

    private function loadTrackingElement($data,$dataCust,$select){

        $invForeign = TrackSystem::getElementOfInvFrgn($dataCust);
        $ms_track=$data['wayBill'][0]['id'].TrackSystem::checkResiTemporary($data['wayBill'][0]['id']);

        $getSN = DB::table("data_list")->select("shipping_number","forwarder_id","forwarder_name","ms_track_id")->where("ms_track_id",$ms_track)->first();

        $details = "<table>
                        <tr><td>Tracking Number</td><td class='text-track'>".$ms_track."</td></tr>";
                        if ($getSN && $getSN->forwarder_id == 'VENDOR') {
    $details .= "
        <tr>
            <td>{$getSN->forwarder_name}</td>
            <td>{$getSN->shipping_number}</td>
        </tr>";
}
$details .= "

                        <tr><td>Shipment Date</td><td>".$dataCust['shipmentDate']."</td></tr>
                        <tr><td>WH Origin</td><td>".$dataCust['wareId']." - ".$dataCust['wareLoc']."</td></tr>
                        <tr><td>Service</td><td>".$dataCust['servName']."</td></tr>
                        <tr><td>Weight</td><td>".$dataCust['berat']."</td></tr>
                        <tr><td>Item</td><td>".$dataCust['item']."</td></tr>
                        <tr><td>CBM</td><td>".$dataCust['cbm']."</td></tr>
                    </table>"
                    .$invForeign.
                    "<hr>
                    <table>
                        <tr><td>Tipe Customer</td><td>".$dataCust['custType']."</td></tr>
                        <tr><td>Customer</td><td>".$dataCust['fullName']."</td></tr>
                        <tr><td>Telepon</td><td>".$dataCust['phone']."</td></tr>
                        <tr><td>Alamat Penerima</td><td>".$dataCust['fullAddress']."</td></tr>
                    </table>";

        $timeline = array();
        $queue = 1;
        // for($i=0;$i<count($data['wayBill']);$i++){
        $getTimeLine = DB::table("shiptrip_track_list")
                        ->select("shiptrip_track_list.*","shiptrip_track_status.title as tracktitle","shiptrip_track_status_manual.title as trackmantitle")
                        ->join("shiptrip_track_status","shiptrip_track_list.track_status_id","shiptrip_track_status.id")
                        ->join("shiptrip_track_status_manual","shiptrip_track_list.track_status_manual_id","shiptrip_track_status_manual.id")
                        ->where("ms_track_id",$data['wayBill'][0]['id'])
                        ->orderBy("created_at","desc")
                        ->get();

        foreach($getTimeLine as $g){
            // if($g->track_status_id!=9){
                $statusActive = false;
                if($queue==1){
                    $statusActive=true;
                }

                $location = $g->tracktitle;
                if($g->track_status_manual_id!="A"){
                    $location = $g->trackmantitle;
                }
                $time = date("H:i",strtotime($g->created_at));
                if($time=="00:00"){
                    $time = "";
                }

                $timeline[] = ["date" => $this->controller->dateFormatIndo($g->created_at,1), "time" => $time, "location" => $location, "desc" => TrackSystem::checkTimelineText($g->text,$data['wayBill'][0]['id'],0), "active"=>$statusActive];
                $queue++;
            // }
        }
        // }

        $array = array(
            "status" => 200,
            "id" => $data['wayBill'][0]['id'], 
            "details" => $details,
            "waybills" => $select?$data['wayBill']:'',
            "timeline" => $timeline
            );

        return $array;
    }
    
    public static function checkResiTemporary($waybill){
        $check = DB::table("data_list")
                    ->select("shipping_number_stats")
                    ->where("shipping_number",$waybill)
                    ->first();
        
        if($check==null){
            $checktwo = DB::table("order_list")
                ->select("invoice_id")
                ->where("ms_track_id",$waybill)
                ->first();
                
            if($checktwo==null){
                return "";
            }

            $check = "";
            $check = DB::table("data_list")
                ->select("cust_type_id","shipping_number_stats")
                ->where("mismass_invoice_id",$checktwo->invoice_id)
                ->first();
                
            if($check==null){
                return "";
            }
            
            if($check->cust_type_id=="COR"){
                return "";
            }
        }
        
        if(!$check->shipping_number_stats){
            return "";
        }
        
        return "<div class='btnStatus btnStatusHold'>Temporary</div>";
    }
    
    public static function checkTimelineText($txt,$waybill,$mode){
        
        if($txt=="PROCESSN"){
            $check = DB::table("data_list")
                    ->select("forwarder_name","shipping_number","shipping_number_stats")
                    ->where("shipping_number",$waybill)
                    ->first();
            if($check==null){
                $checktwo = DB::table("order_list")
                    ->select("invoice_id")
                    ->where("ms_track_id",$waybill)
                    ->first();

                $check = "";
                $check = DB::table("data_list")
                    ->select("forwarder_name","shipping_number","shipping_number_stats")
                    ->where("mismass_invoice_id",$checktwo->invoice_id)
                    ->first();
            }
            
            //mode
            //0 Tracking
            //1 Table
            $copyBtn = "";
            if($mode==0){
                $copyBtn = "<a id='copyResi' data-shipnum='".$check->shipping_number."' class='pointlink'>Copy Resi</a>";
            }
            
            //Jika Resi Sementara
            if($check->shipping_number_stats){
                return "Paket diteruskan oleh pihak ".$check->forwarder_name;
            }

            return "Paket diteruskan oleh pihak ".$check->forwarder_name." dengan No.Resi <b>[".$check->shipping_number."]</b> ".$copyBtn;
        }

        return $txt;
    }
}
