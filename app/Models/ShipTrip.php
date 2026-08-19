<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ShipTrip extends Model
{
    use HasFactory;

    protected $table = 'shiptrip_list';
    protected $primarykey = 'id';

    // protected $order = ['shiptrip_list.drop_updated_at' => 'DESC'];
    
    protected $column_order = [
        'id',
        "ms_track_id",
        "track_type_id",
        "track_group_type_id",
        "drop_updated_at",
        "drop_updated_by",
        "service_list.name",
        "CONCAT_WS(' - ',warehouse_list.id,warehouse_list.location)",
        "CONCAT_WS(' ',cust_list.first_name,cust_list.middle_name,cust_list.last_name)",
        "cust_list.phone",
        "cust_list.city",
        "cust_list.prov",
        "CONCAT_WS(' ',shiptrip_cust_list.first_name,shiptrip_cust_list.middle_name,shiptrip_cust_list.last_name)",
        "shiptrip_cust_list.phone",
        "CONCAT_WS(' ',(SELECT COUNT(shiptrip_foreign_track_list.id) FROM shiptrip_foreign_track_list WHERE shiptrip_foreign_track_list.ms_track_id=shiptrip_list.ms_track_id),'Resi LN')",
        "(SELECT GROUP_CONCAT(shiptrip_foreign_track_list.id SEPARATOR ', ') FROM shiptrip_foreign_track_list WHERE shiptrip_foreign_track_list.ms_track_id=shiptrip_list.ms_track_id GROUP BY ms_track_id)"
    ];

    private $controller;

    public function __construct()
    {
        $this->controller = new Controller;
    }

    public function getDTQuery(Request $request, $katakunci = '', $filter)
    {        

        $column_search = [
            "shiptrip_list.ms_track_id",
            "shiptrip_list.note",
            "shiptrip_list.track_type_id",
            "shiptrip_list.track_group_type_id",
            "drop_updated_at",
            "drop_updated_by",
            "service_list.name",
            "CONCAT_WS(' - ',warehouse_list.id,warehouse_list.location)",
            "CONCAT_WS(' ',cust_list.first_name,cust_list.middle_name,cust_list.last_name)",
            "cust_list.phone",
            "cust_list.address",
            "cust_list.city",
            "cust_list.prov",
            "CONCAT_WS(' ',shiptrip_cust_list.first_name,shiptrip_cust_list.middle_name,shiptrip_cust_list.last_name)",
            "shiptrip_cust_list.phone",
            "(CASE WHEN shiptrip_list.note!='' THEN 'Catatan' END)",
            "CONCAT_WS(' ',(SELECT COUNT(shiptrip_foreign_track_list.id) FROM shiptrip_foreign_track_list WHERE shiptrip_foreign_track_list.ms_track_id=shiptrip_list.ms_track_id),'Resi LN')",
            "(SELECT GROUP_CONCAT(shiptrip_foreign_track_list.id SEPARATOR ', ') FROM shiptrip_foreign_track_list WHERE shiptrip_foreign_track_list.ms_track_id=shiptrip_list.ms_track_id GROUP BY ms_track_id)"
        ];

        // $ordering = ShipTrip::setOrderBy($filter['navShip'],$filter['navType']);
        $ordering = ['shiptrip_list.edited_at' => 'DESC'];
        $custTypeId = $filter['custTypeId'];
        $filterTanggal = $filter['tanggal']!=""?($filter['tanggal'][0] != "" || $filter['tanggal'][0] != null ? "AND ".ShipTrip::setFilterDate($filter['navShip'])." BETWEEN '" . date("Y-m-d", strtotime($filter['tanggal'][0])) . " 00:00:00' AND '" . date("Y-m-d", strtotime($filter['tanggal'][1])) . " 23:59:59'" : ""):"";
        $filterWarehouse = $filter['warehouse'] != "" || $filter['warehouse'] != null ? "AND service_list.warehouse_id='".$filter['warehouse']."'":"";
        $filterTrackStatus = ShipTrip::checkFilterTrackStatus($filter['navShip'],$filter['navType']);
        $select = "shiptrip_list.*,
                shiptrip_track_status.next_step,
                shiptrip_track_status.value AS now_step,
                service_list.name AS serv_name,
                CONCAT_WS(' - ',warehouse_list.id,warehouse_list.location) AS ware_name,
                CONCAT_WS(' ',cust_list.first_name,cust_list.middle_name,cust_list.last_name) AS full_name,
                CONCAT_WS(', ',cust_list.address,cust_list.sub_district,cust_list.district,cust_list.city,cust_list.prov,cust_list.postal_code) AS full_address,
                cust_list.phone AS phone,
                cust_list.address AS address,
                cust_list.city AS city,
                cust_list.prov AS prov,
                cust_list.postal_code AS postal_code,
                CONCAT_WS(' ',shiptrip_cust_list.first_name,shiptrip_cust_list.middle_name,shiptrip_cust_list.last_name) AS second_full_name,
                shiptrip_cust_list.phone AS second_phone,
                (CASE WHEN shiptrip_list.note!='' THEN 'Catatan' END) AS notenote,
                CONCAT_WS(' ',(SELECT COUNT(shiptrip_foreign_track_list.id) FROM shiptrip_foreign_track_list WHERE shiptrip_foreign_track_list.ms_track_id=shiptrip_list.ms_track_id),'Resi LN') AS total_foreign,
                (SELECT GROUP_CONCAT(shiptrip_foreign_track_list.id SEPARATOR ', ') FROM shiptrip_foreign_track_list WHERE shiptrip_foreign_track_list.ms_track_id=shiptrip_list.ms_track_id GROUP BY ms_track_id) AS foreign_tracks,
                (SELECT GROUP_CONCAT(CONCAT_WS('.',shiptrip_image_list.id,shiptrip_image_list.ext) SEPARATOR ', ') FROM shiptrip_image_list WHERE shiptrip_image_list.ms_track_id=shiptrip_list.ms_track_id GROUP BY ms_track_id) AS images";
        
        $query = "shiptrip_track_status.stats='$filterTrackStatus' AND cust_list.cust_type_id='$custTypeId' $filterWarehouse $filterTanggal";
        $where = "";
        $groupBy = "shiptrip_list.ms_track_id";

        if (!empty($katakunci)) {
            for ($i = 0; $i <= count($column_search) - 1; $i++) {
                if ($i < count($column_search) - 1) {
                    $where .= $query . "AND $column_search[$i] LIKE '%$katakunci%' OR ";
                } else {
                    $where .= $query . "AND $column_search[$i] LIKE '%$katakunci%'";
                }
            }
        } else {
            $where = $query;
        }

        if ($request->input('order')) {
            $orderByA = $this->column_order[$request->input('order')['0']['column']];
            $orderByB = $request->input('order')['0']['dir'];
        } else if (isset($ordering)) {
            $orderByA = key($ordering);
            $orderByB = $ordering[key($ordering)];
        }

        $data = [
            'select' => $select,
            'where' => $where,
            'orderByA' => $orderByA,
            'orderByB' => $orderByB,
            'groupBy' => $groupBy
        ];

        return $data;
    }

    public function getDT(Request $request, $katakunci, $filter)
    {

        $data = ShipTrip::getDTQuery($request, $katakunci, $filter);
        if ($request->input('length') != -1) {
            $offset = $request->input('start');
            $limit = $request->input('length');
                return ShipTrip::selectRaw($data['select'])
                                // ->join("shiptrip_foreign_track_list", "shiptrip_foreign_track_list.ms_track_id", "=", "shiptrip_list.ms_track_id")
                                // ->join("shiptrip_image_list", "shiptrip_image_list.ms_track_id", "=", "shiptrip_list.ms_track_id")
                                // ->join("service_list", "service_list.id", "=", "shiptrip_list.service_id")
                                // ->join("cust_list", "cust_list.id", "=", "shiptrip_list.cust_id")
                                // ->join("warehouse_list", "warehouse_list.id", "=", "service_list.warehouse_id")
                                // ->join("shiptrip_track_status", "shiptrip_track_status.id", "=", "shiptrip_list.track_status_id")
                                // ->join("shiptrip_cust_list", "shiptrip_cust_list.id", "=", "shiptrip_list.ms_track_id")
                                // ->join("shiptrip_foreign_track_list", "shiptrip_foreign_track_list.ms_track_id", "=", "shiptrip_list.ms_track_id")
                                ->leftjoin("service_list", "service_list.id", "=", "shiptrip_list.service_id")
                                ->leftjoin("cust_list", "cust_list.id", "=", "shiptrip_list.cust_id")
                                ->leftjoin("shiptrip_track_status", "shiptrip_track_status.id", "=", "shiptrip_list.track_status_id")
                                ->leftjoin("shiptrip_cust_list", "shiptrip_cust_list.id", "=", "shiptrip_list.ms_track_id")
                                ->leftjoin("warehouse_list", "warehouse_list.id", "=", "service_list.warehouse_id")
                                ->whereRaw($data['where'])
                                ->skip($offset)
                                ->take($limit)
                                ->orderBy($data['orderByA'], $data['orderByB'])
                                // ->groupBy($data['groupBy'])
                                ->get();
        }

        return ShipTrip::selectRaw($data['select'])
                        // ->join("shiptrip_foreign_track_list", "shiptrip_foreign_track_list.ms_track_id", "=", "shiptrip_list.ms_track_id")
                        // ->join("shiptrip_image_list", "shiptrip_image_list.ms_track_id", "=", "shiptrip_list.ms_track_id")
                        // ->join("service_list", "service_list.id", "=", "shiptrip_list.service_id")
                        // ->join("cust_list", "cust_list.id", "=", "shiptrip_list.cust_id")
                        // ->join("warehouse_list", "warehouse_list.id", "=", "service_list.warehouse_id")
                        // ->join("shiptrip_track_status", "shiptrip_track_status.id", "=", "shiptrip_list.track_status_id")
                        // ->join("shiptrip_cust_list", "shiptrip_cust_list.id", "=", "shiptrip_list.ms_track_id")
                        // ->join("shiptrip_foreign_track_list", "shiptrip_foreign_track_list.ms_track_id", "=", "shiptrip_list.ms_track_id")
                        ->leftjoin("service_list", "service_list.id", "=", "shiptrip_list.service_id")
                        ->leftjoin("cust_list", "cust_list.id", "=", "shiptrip_list.cust_id")
                        ->leftjoin("shiptrip_track_status", "shiptrip_track_status.id", "=", "shiptrip_list.track_status_id")
                        ->leftjoin("shiptrip_cust_list", "shiptrip_cust_list.id", "=", "shiptrip_list.ms_track_id")
                        ->leftjoin("warehouse_list", "warehouse_list.id", "=", "service_list.warehouse_id")
                        ->whereRaw($data['where'])
                        ->orderBy($data['orderByA'], $data['orderByB'])
                        // ->groupBy($data['groupBy'])
                        ->get();
    }

    public function countFiltered(Request $request, $katakunci, $filter)
    {
        $data = ShipTrip::getDTQuery($request, $katakunci, $filter);
        return count(ShipTrip::selectRaw($data['select'])
                // ->join("shiptrip_foreign_track_list", "shiptrip_foreign_track_list.ms_track_id", "=", "shiptrip_list.ms_track_id")
                // ->join("shiptrip_image_list", "shiptrip_image_list.ms_track_id", "=", "shiptrip_list.ms_track_id")
                // ->join("service_list", "service_list.id", "=", "shiptrip_list.service_id")
                // ->join("cust_list", "cust_list.id", "=", "shiptrip_list.cust_id")
                // ->join("warehouse_list", "warehouse_list.id", "=", "service_list.warehouse_id")
                // ->join("shiptrip_track_status", "shiptrip_track_status.id", "=", "shiptrip_list.track_status_id")
                // ->join("shiptrip_cust_list", "shiptrip_cust_list.id", "=", "shiptrip_list.ms_track_id")
                // ->join("shiptrip_foreign_track_list", "shiptrip_foreign_track_list.ms_track_id", "=", "shiptrip_list.ms_track_id")
                ->leftjoin("service_list", "service_list.id", "=", "shiptrip_list.service_id")
                ->leftjoin("cust_list", "cust_list.id", "=", "shiptrip_list.cust_id")
                ->leftjoin("shiptrip_track_status", "shiptrip_track_status.id", "=", "shiptrip_list.track_status_id")
                ->leftjoin("shiptrip_cust_list", "shiptrip_cust_list.id", "=", "shiptrip_list.ms_track_id")
                ->leftjoin("warehouse_list", "warehouse_list.id", "=", "service_list.warehouse_id")
                ->whereRaw($data['where'])
                // ->groupBy($data['groupBy'])
                ->get());
    }

    public function countAll()
    {
        return ShipTrip::count();
    }

    public function checkFilterTrackStatus($navship,$navtype){
        if($navship=="whabroad"){
            if($navtype=="drop"){
                return 1;
            }
            return 2;
        }

        if($navship=="sgn"){
            if($navtype=="to"){
                return 3;
            }
            return 4;
        }

        if($navship=="btm"){
            if($navtype=="to"){
                return 5;
            }
            return 6;
        }

        if($navship=="jkt"){
            if($navtype=="to"){
                return 7;
            }
            if($navtype=="in"){
                return 8;
            }
            return 9;
        }

        if($navship=="end"){
            if($navtype=="vendor"){
                return 13;
            }
            if($navtype=="courier"){
                return 12;
            }
            return 11;
        }
    }

    public function getDataShipTrip($data){
        if($data['nextStep']==3){
            $array = [
                "edited_at" => $data['tanggalShipmentAuto'],
                "edited_by" => $data['username'],
                "to_sg_created_at" => $data['tanggalShipmentAuto'],
                "to_sg_man_created_at" =>$data['tanggalShipment'],
                "to_sg_created_by" => $data['username'],
                "to_sg_updated_at" => $data['tanggalShipmentAuto'],
                "to_sg_updated_by" => $data['username'],
                "redline_created_by" => "",
            ];
        }

        if($data['nextStep']==4){
            $array = [
                "edited_at" => $data['tanggalShipmentAuto'],
                "edited_by" => $data['username'],
                "in_sg_created_at" => $data['tanggalShipmentAuto'],
                "in_sg_man_created_at" =>$data['tanggalShipment'],
                "in_sg_created_by" => $data['username'],
                "in_sg_updated_at" => $data['tanggalShipmentAuto'],
                "in_sg_updated_by" => $data['username'],
                "redline_created_by" => "",
            ];
        }

        if($data['nextStep']==5){
            $array = [
                "edited_at" => $data['tanggalShipmentAuto'],
                "edited_by" => $data['username'],
                "to_btm_created_at" => $data['tanggalShipmentAuto'],
                "to_btm_man_created_at" =>$data['tanggalShipment'],
                "to_btm_created_by" => $data['username'],
                "to_btm_updated_at" => $data['tanggalShipmentAuto'],
                "to_btm_updated_by" => $data['username'],
                "redline_created_by" => "",
            ];
        }

        if($data['nextStep']==6){
            $array = [
                "edited_at" => $data['tanggalShipmentAuto'],
                "edited_by" => $data['username'],
                "in_btm_created_at" => $data['tanggalShipmentAuto'],
                "in_btm_man_created_at" =>$data['tanggalShipment'],
                "in_btm_created_by" => $data['username'],
                "in_btm_updated_at" => $data['tanggalShipmentAuto'],
                "in_btm_updated_by" => $data['username'],
                "redline_created_by" => "",
            ];
        }

        if($data['nextStep']==7){
            $array = [
                "edited_at" => $data['tanggalShipmentAuto'],
                "edited_by" => $data['username'],
                "to_jkt_created_at" => $data['tanggalShipmentAuto'],
                "to_jkt_man_created_at" =>$data['tanggalShipment'],
                "to_jkt_created_by" => $data['username'],
                "to_jkt_updated_at" => $data['tanggalShipmentAuto'],
                "to_jkt_updated_by" => $data['username'],
                "redline_created_by" => "",
            ];
        }

        if($data['nextStep']==8){
            $array = [
                "edited_at" => $data['tanggalShipmentAuto'],
                "edited_by" => $data['username'],
                "in_jkt_created_at" => $data['tanggalShipmentAuto'],
                "in_jkt_man_created_at" =>$data['tanggalShipment'],
                "in_jkt_created_by" => $data['username'],
                "in_jkt_updated_at" => $data['tanggalShipmentAuto'],
                "in_jkt_updated_by" => $data['username'],
                "redline_created_by" => "",
            ];
        }

        if($data['nextStep']==9){
            $array = [
                "edited_at" => $data['tanggalShipmentAuto'],
                "edited_by" => $data['username'],
                "cr_order_created_at" => $data['tanggalShipmentAuto'],
                "cr_order_man_created_at" =>$data['tanggalShipment'],
                "cr_order_created_by" => $data['username'],
                "cr_order_updated_at" => $data['tanggalShipmentAuto'],
                "cr_order_updated_by" => $data['username'],
                "redline_created_by" => "",
            ];
            
        }

        $array["track_status_id"] = $data['nextStep'];
        return $array;
    }

    public function getDataShipTripManual($data){
        if($data['nextStep']==1){
            $array = [
                "edited_at" => $data['tanggalShipmentAuto'],
                "edited_by" => $data['username'],
                "redline_created_at" => $data['tanggalShipmentAuto'],
                "redline_created_by" => $data['username'],
                "drop_updated_at" => $data['tanggalShipmentAuto'],
                "drop_updated_by" => $data['username']
            ];
        }

        if($data['nextStep']==2){
            $array = [
                "edited_at" => $data['tanggalShipmentAuto'],
                "edited_by" => $data['username'],
                "redline_created_at" => $data['tanggalShipmentAuto'],
                "redline_created_by" => $data['username'],
                "drop_updated_at" => $data['tanggalShipmentAuto'],
                "drop_updated_by" => $data['username']
            ];
        }

        if($data['nextStep']==3){
            $array = [
                "edited_at" => $data['tanggalShipmentAuto'],
                "edited_by" => $data['username'],
                "redline_created_at" => $data['tanggalShipmentAuto'],
                "redline_created_by" => $data['username'],
                "to_sg_updated_at" => $data['tanggalShipmentAuto'],
                "to_sg_updated_by" => $data['username']
            ];
        }

        if($data['nextStep']==4){
            $array = [
                "edited_at" => $data['tanggalShipmentAuto'],
                "edited_by" => $data['username'],
                "redline_created_at" => $data['tanggalShipmentAuto'],
                "redline_created_by" => $data['username'],
                "in_sg_updated_at" => $data['tanggalShipmentAuto'],
                "in_sg_updated_by" => $data['username']
            ];
        }

        if($data['nextStep']==5){
            $array = [
                "edited_at" => $data['tanggalShipmentAuto'],
                "edited_by" => $data['username'],
                "redline_created_at" => $data['tanggalShipmentAuto'],
                "redline_created_by" => $data['username'],
                "to_btm_updated_at" => $data['tanggalShipmentAuto'],
                "to_btm_updated_by" => $data['username']
            ];
        }

        if($data['nextStep']==6){
            $array = [
                "edited_at" => $data['tanggalShipmentAuto'],
                "edited_by" => $data['username'],
                "redline_created_at" => $data['tanggalShipmentAuto'],
                "redline_created_by" => $data['username'],
                "in_btm_updated_at" => $data['tanggalShipmentAuto'],
                "in_btm_updated_by" => $data['username']
            ];
        }

        if($data['nextStep']==7){
            $array = [
                "edited_at" => $data['tanggalShipmentAuto'],
                "edited_by" => $data['username'],
                "redline_created_at" => $data['tanggalShipmentAuto'],
                "redline_created_by" => $data['username'],
                "to_jkt_updated_at" => $data['tanggalShipmentAuto'],
                "to_jkt_updated_by" => $data['username']
            ];
        }

        if($data['nextStep']==8){
            $array = [
                "edited_at" => $data['tanggalShipmentAuto'],
                "edited_by" => $data['username'],
                "redline_created_at" => $data['tanggalShipmentAuto'],
                "redline_created_by" => $data['username'],
                "in_jkt_updated_at" => $data['tanggalShipmentAuto'],
                "in_jkt_updated_by" => $data['username']
            ];
        }

        if($data['nextStep']==9){
            $array = [
                "edited_at" => $data['tanggalShipmentAuto'],
                "edited_by" => $data['username'],
                "redline_created_at" => $data['tanggalShipmentAuto'],
                "redline_created_by" => $data['username'],
                "cr_order_updated_at" => $data['tanggalShipmentAuto'],
                "cr_order_updated_by" => $data['username']
            ];
            
        }

        $array["track_status_id"] = $data['nextStep'];
        return $array;
    }

    public function getDataShipTripLatestUpdated($list){
        $value = "<div>-</div>";
        if($list->track_status_id==1||$list->track_status_id==2){
            $getRank = DB::table('users')->where("username",$list->drop_updated_by)->value("rank");
            $jabatan = $getRank != null ? "<div class='bg-mismass' style='padding:1px 5px'>".$getRank."</div>" : "";
            $value = "<div class='fw-bold'>" . $list->drop_updated_by . "</div>".$jabatan."<div>Updated At : </div><div>" . $this->controller->dateFormatIndo($list->drop_updated_at,2) . "</div>";
            return $value;
        }

        if($list->track_status_id==3){
            $getRank = DB::table('users')->where("username",$list->to_sg_updated_by)->value("rank");
            $jabatan = $getRank != null ? "<div class='bg-mismass' style='padding:1px 5px'>".$getRank."</div>" : "";
            $value = "<div class='fw-bold'>" . $list->to_sg_updated_by . "</div>".$jabatan."<div>Updated At : </div><div>" . $this->controller->dateFormatIndo($list->to_sg_updated_at,2) . "</div>";
            return $value;
        }

        if($list->track_status_id==4){
            $getRank = DB::table('users')->where("username",$list->in_sg_updated_by)->value("rank");
            $jabatan = $getRank != null ? "<div class='bg-mismass' style='padding:1px 5px'>".$getRank."</div>" : "";
            $value = "<div class='fw-bold'>" . $list->in_sg_updated_by . "</div>".$jabatan."<div>Updated At : </div><div>" . $this->controller->dateFormatIndo($list->in_sg_updated_at,2) . "</div>";
            return $value;
        }

        if($list->track_status_id==5){
            $getRank = DB::table('users')->where("username",$list->to_btm_updated_by)->value("rank");
            $jabatan = $getRank != null ? "<div class='bg-mismass' style='padding:1px 5px'>".$getRank."</div>" : "";
            $value = "<div class='fw-bold'>" . $list->to_btm_updated_by . "</div>".$jabatan."<div>Updated At : </div><div>" . $this->controller->dateFormatIndo($list->to_btm_updated_at,2) . "</div>";
            return $value;
        }

        if($list->track_status_id==6){
            $getRank = DB::table('users')->where("username",$list->in_btm_updated_by)->value("rank");
            $jabatan = $getRank != null ? "<div class='bg-mismass' style='padding:1px 5px'>".$getRank."</div>" : "";
            $value = "<div class='fw-bold'>" . $list->in_btm_updated_by . "</div>".$jabatan."<div>Updated At : </div><div>" . $this->controller->dateFormatIndo($list->in_btm_updated_at,2) . "</div>";
            return $value;
        }

        if($list->track_status_id==7){
            $getRank = DB::table('users')->where("username",$list->to_jkt_updated_by)->value("rank");
            $jabatan = $getRank != null ? "<div class='bg-mismass' style='padding:1px 5px'>".$getRank."</div>" : "";
            $value = "<div class='fw-bold'>" . $list->to_jkt_updated_by . "</div>".$jabatan."<div>Updated At : </div><div>" . $this->controller->dateFormatIndo($list->to_jkt_updated_at,2) . "</div>";
            return $value;
        }

        if($list->track_status_id==8){
            $getRank = DB::table('users')->where("username",$list->in_jkt_updated_by)->value("rank");
            $jabatan = $getRank != null ? "<div class='bg-mismass' style='padding:1px 5px'>".$getRank."</div>" : "";
            $value = "<div class='fw-bold'>" . $list->in_jkt_updated_by . "</div>".$jabatan."<div>Updated At : </div><div>" . $this->controller->dateFormatIndo($list->in_jkt_updated_at,2) . "</div>";
            return $value;
        }

        if($list->track_status_id==9){
            $getRank = DB::table('users')->where("username",$list->cr_order_updated_by)->value("rank");
            $jabatan = $getRank != null ? "<div class='bg-mismass' style='padding:1px 5px'>".$getRank."</div>" : "";
            $value = "<div class='fw-bold'>" . $list->cr_order_updated_by . "</div>".$jabatan."<div>Updated At : </div><div>" . $this->controller->dateFormatIndo($list->cr_order_updated_at,2) . "</div>";
            return $value;
        }

        if($list->track_status_id==10){
            $getRank = DB::table('users')->where("username",$list->cr_inv_updated_by)->value("rank");
            $jabatan = $getRank != null ? "<div class='bg-mismass' style='padding:1px 5px'>".$getRank."</div>" : "";
            $value = "<div class='fw-bold'>" . $list->cr_inv_updated_by . "</div>".$jabatan."<div>Updated At : </div><div>" . $this->controller->dateFormatIndo($list->cr_inv_updated_at,2) . "</div>";
            return $value;
        }

        if($list->track_status_id==11){
            $getRank = DB::table('users')->where("username",$list->paid_inv_updated_by)->value("rank");
            $jabatan = $getRank != null ? "<div class='bg-mismass' style='padding:1px 5px'>".$getRank."</div>" : "";
            $value = "<div class='fw-bold'>" . $list->paid_inv_updated_by . "</div>".$jabatan."<div>Updated At : </div><div>" . $this->controller->dateFormatIndo($list->paid_inv_updated_at,2) . "</div>";
            return $value;
        }

    }

    public function getCodename($id){
        if($id==3){
            return "TS";
        }

        if($id==4){
            return "IS";
        }

        if($id==5){
            return "TB";
        }

        if($id==6){
            return "IB";
        }

        if($id==7){
            return "TJ";
        }

        if($id==8){
            return "IJ";
        }

        if($id==9){
            return "BO";
        }
    }

    public function setOrderBy($navShip,$navType){
        if($navShip=="whabroad"){
            return ['shiptrip_list.drop_updated_at' => 'DESC'];
        }
        if($navShip=="sgn"){
            if($navType=="to"){
                return ['shiptrip_list.to_sg_updated_at' => 'DESC'];
            }
            return ['shiptrip_list.in_sg_updated_at' => 'DESC'];
        }
        if($navShip=="btm"){
            if($navType=="to"){
                return ['shiptrip_list.to_btm_updated_at' => 'DESC'];
            }
            return ['shiptrip_list.in_btm_updated_at' => 'DESC'];
        }
        if($navShip=="jkt"){
            if($navType=="to"){
                return ['shiptrip_list.to_jkt_updated_at' => 'DESC'];
            }
            return ['shiptrip_list.in_jkt_updated_at' => 'DESC'];
        }if($navShip=="end"){
            return ['shiptrip_list.drop_updated_at' => 'DESC'];
        }
    }

    public function setFilterDate($navShip){
        if($navShip=="whabroad"){
            return "drop_man_created_at";
        }
        return "to_sg_man_created_at";
    }

    public function getPrimaryTrack($id){
        $getPrimaryTrack = DB::table("shiptrip_secondary_list")->where("id",$id)->value("ms_track_id");
        return $getPrimaryTrack;
    }

    public function getSecondaryList($id){
        $result = "-";
        $get = DB::table("shiptrip_secondary_list")->select("id")->where("ms_track_id",$id)->get();
        if(count($get)>0){
            return count($get);
        }

        return $result;
    }

    public function callTable($request,$lists,$filter){
        $this->controller->roleAccess();
        $no = $request->input('start');
        $data = [];
        foreach ($lists as $list) {
            $getSecondaryList = $list->ms_track_id;
            $getPrimaryTrack = ShipTrip::getPrimaryTrack($list->ms_track_id);
            if($list->track_type_id=="PRM"){
                $getSecondaryList = ShipTrip::getSecondaryList($list->ms_track_id);
                $getPrimaryTrack = $list->ms_track_id;
            }

            $getRank = DB::table('users')->where("username",$list->edited_by)->value("rank");
            $jabatan = $getRank != null ? "<div class='bg-mismass' style='padding:1px 5px'>".$getRank."</div>" : "";
            $wareId = DB::table("service_list")->where("id",$list->service_id)->value("warehouse_id");
            $servId = $list->service_id;
            $getLatest = DB::table("shiptrip_track_list")->selectRaw("created_by,created_at,text")->where("ms_track_id",$list->ms_track_id)->orderBy("created_at","desc")->get();
            
            $getDataRank = DB::table('users')->where("username",$getLatest[0]->created_by)->value("rank");
            $getRank = $getDataRank!=null?$getDataRank:"";
            // $noteEl = $list->note!=""?"<div class='bg-secondary p-1' style='display:inline-block;border:none;border-radius:5px'>Catatan</div>":"";
            $noteEl = $list->note!=""?"<div class='bg-catatan'>Catatan</div>":"";
            $shipmentDate = count($getLatest)>1?"<div>TRACK:".$this->controller->dateFormatIndo($getLatest[0]->created_at,1)."<div>SHIP:".$this->controller->dateFormatIndo($list->to_sg_man_created_at,1)."</div>":"";
            $nowStep = DB::table("shiptrip_track_list")->where("ms_track_id",$list->ms_track_id)->orderBy("created_at","DESC")->value("text");
            $nextStep = DB::table("shiptrip_track_status")->where("id",$list->next_step)->value("value");

            $detailControl = "";
            $editable = 0;
            if($filter["navType"]!="pay"){
                $editable = 1;
                $detailControl = "<input type='checkbox' style='margin-left:4px' name='checkShipment' data-now-step='".$nowStep."'".
                                "data-next-step='".$nextStep."' data-next-step-id='".$list->next_step."' data-date-hour-now='".$this->controller->dateFormatIndo(date("Y-m-d H:i:s"),4)."' data-date-now='".$this->controller->dateFormatIndo(date("Y-m-d"),1)."'".
                                "data-ms-track='".$list->ms_track_id."' data-track-created-by='".$getLatest[0]->created_by."' data-ship-created-at='".$this->controller->dateFormatIndo($list->to_sg_man_created_at,1)."' data-track-created-hour-at='".$this->controller->dateFormatIndo($getLatest[0]->created_at,5)."'".
                                "data-track-created-at='".$this->controller->dateFormatIndo($getLatest[0]->created_at,1)."' data-drop-created-by='".$list->drop_created_by."'".
                                "data-drop-created-at='".$this->controller->dateFormatIndo($list->drop_man_created_at,1)."' data-drop-updated-by='".$list->drop_updated_by."'".
                                "data-drop-updated-at='".$this->controller->dateFormatIndo($list->drop_updated_at,2)."' data-rank='".$getRank."' data-total-foreign='".$list->total_foreign."'".
                                "data-name='".$list->full_name."' data-phone='".$list->phone."' data-second-name='".$list->second_full_name."'".
                                "data-second-phone='".$list->second_phone."' data-city='".$list->city."' data-prov='$list->prov' data-postal-code='".$list->postal_code."'".
                                "data-warehouse='".$list->ware_name."' data-service='".$list->serv_name."'".
                                ">";
            }

            $updateBtn = "";
            if(Auth::user()->shiptrip_update){
                $updateBtn = "<a class='dropdown-item pointlink' id='updateBtn' data-now-step='".$nowStep."'".
                                "data-next-step='".$nextStep."' data-next-step-id='".$list->next_step."' data-date-hour-now='".$this->controller->dateFormatIndo(date("Y-m-d H:i:s"),4)."' data-date-now='".$this->controller->dateFormatIndo(date("Y-m-d"),1)."'".
                                "data-ms-track='".$list->ms_track_id."' data-track-created-by='".$getLatest[0]->created_by."' data-ship-created-at='".$this->controller->dateFormatIndo($list->to_sg_man_created_at,1)."' data-track-created-hour-at='".$this->controller->dateFormatIndo($getLatest[0]->created_at,5)."'".
                                "data-track-created-at='".$this->controller->dateFormatIndo($getLatest[0]->created_at,1)."' data-drop-created-by='".$list->drop_created_by."'".
                                "data-drop-created-at='".$this->controller->dateFormatIndo($list->drop_man_created_at,1)."' data-drop-updated-by='".$list->drop_updated_by."'".
                                "data-drop-updated-at='".$this->controller->dateFormatIndo($list->drop_updated_at,2)."' data-rank='".$getRank."' data-total-foreign='".$list->total_foreign."'".
                                "data-name='".$list->full_name."' data-phone='".$list->phone."' data-second-name='".$list->second_full_name."'".
                                "data-address='".$list->full_address."'".
                                "data-second-phone='".$list->second_phone."' data-city='".$list->city."' data-prov='$list->prov' data-postal-code='".$list->postal_code."'".
                                "data-warehouse='".$list->ware_name."' data-service='".$list->serv_name."'>Update Shipment</a>";
            }
            
            $editBtn = "";
            if(Auth::user()->shiptrip_edit){
                $editBtn = "<a class='dropdown-item pointlink' id='editBtn'".
                            "data-baseurl='".url('/')."'".
                            "data-note='".$list->note."'".
                            "data-images='".$list->images."'".
                            "data-foreign-tracks='".$list->foreign_tracks."'".
                            "data-type-mstrack='".$list->track_type_id."'".
                            "data-primary-mstrack='".$getPrimaryTrack."'".
                            "data-secondary-mstracks='".$getSecondaryList."'".
                            "data-latest-tracking-time='".$this->controller->dateFormatIndo($getLatest[0]->created_at,1)."'".
                            "data-latest-tracking='".$getLatest[0]->text."' data-now-step='".$nowStep."'".
                            "data-next-step='".$nextStep."'".
                            "data-next-step-id='".$list->next_step."'".
                            "data-date-hour-now='".$this->controller->dateFormatIndo(date("Y-m-d H:i:s"),4)."'".
                            "data-date-now='".$this->controller->dateFormatIndo(date("Y-m-d"),1)."'".
                            "data-ms-track='".$list->ms_track_id."'".
                            "data-track-created-by='".$getLatest[0]->created_by."'".
                            "data-ship-created-at='".$this->controller->dateFormatIndo($list->to_sg_man_created_at,1)."'".
                            "data-track-created-hour-at='".$this->controller->dateFormatIndo($getLatest[0]->created_at,5)."'".
                            "data-track-created-at='".$this->controller->dateFormatIndo($getLatest[0]->created_at,1)."'".
                            "data-drop-created-by='".$list->drop_created_by."'".
                            "data-drop-created-at='".$this->controller->dateFormatIndo($list->drop_man_created_at,1)."'".
                            "data-drop-updated-by='".$list->drop_updated_by."'".
                            "data-drop-updated-at='".$this->controller->dateFormatIndo($list->drop_updated_at,2)."'".
                            "data-rank='".$getRank."'".
                            "data-total-foreign='".$list->total_foreign."'".
                            "data-name='".$list->full_name."'".
                            "data-phone='".$list->phone."'".
                            "data-second-name='".$list->second_full_name."'".
                            "data-address='".$list->full_address."'".
                            "data-second-phone='".$list->second_phone."'".
                            "data-city='".$list->city."'".
                            "data-prov='$list->prov'".
                            "data-postal-code='".$list->postal_code."'".
                            "data-warehouse-id='".$wareId."'".
                            "data-service-id='".$servId."'".
                            "data-warehouse='".$list->ware_name."'".
                            "data-service='".$list->serv_name."'".
                            "data-roleid='".Auth::user()->role_id."'>Edit Data</a>";
            }

            $hapusBtn = "";
            if(Auth::user()->shiptrip_hapus){
                // $hapusBtn = $filter["navShip"]=="whabroad"&&$filter["navType"]=="drop"?"<a class='dropdown-item pointlink' id='hapusBtn' onclick=\"konfirm_hapus('" . $list->ms_track_id . "','" . $list->ms_track_id . "','Shipment','" . url('/shiptrip/hapus/shipment') . "','shiptrip','".$filter["navShip"]."','".$filter["navType"]."','".$filter["custTypeId"]."')\"><div style='color:red'>Hapus Data</div></a>":"";
                $hapusBtn = "<a class='dropdown-item pointlink' id='hapusShipmentBtn' data-id=".$list->ms_track_id."><div style='color:red'>Hapus Data</div></a>";
            }

            $noClick = "pointer-events: none";
            if(Auth::user()->shiptrip_update||Auth::user()->shiptrip_edit||Auth::user()->shiptrip_hapus){
                $noClick = "";
            }
            $wholeBtn = "<div style='".$noClick."' class='btn-group dropleft'><button type='button' class='btn btn-secondary nobtn' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'><i class='fas fa-ellipsis-v'></i></button><div class='dropdown-menu' x-placement='right-start' style='position: absolute; transform: translate3d(111px, 0px, 0px); top: 0px; left: 0px; will-change: transform;'>".$updateBtn.$editBtn.$hapusBtn."</div></div>";
        
            $redLine = $list->redline_created_by!=""?"text-danger":"";

            if($filter['custTypeId']=="IND"){
                $sender = "<div class='fw-bold'>".$list->second_full_name."</div><div>".$list->second_phone."</div>";
                $cons = "<div class='fw-bold'>".$list->full_name."</div><div>".$list->phone."</div><div>".$list->city.", ".$list->prov.", ".$list->postal_code."</div>";
            }else{
                $sender = "<div class='fw-bold'>".$list->full_name."</div><div>".$list->phone."</div><div>".$list->city.", ".$list->prov.", ".$list->postal_code."</div>";
                $cons = "-";
            }

            $no++;
            $row = [];
            $row[] = "<div class='orderNum'>".$no."</div>".$detailControl;
            $row[] = "<a class='fw-bold loadTracking ".$redLine."' href='".url('/shiptrip/tracking')."?id=".$list->ms_track_id."' target='_blank'>".$list->ms_track_id."</a>".$shipmentDate."<div>DROP:".$this->controller->dateFormatIndo($list->drop_man_created_at,1)."</div>";
            $row[] = "<div class='fw-bold'>".$list->ware_name."</div><div>".$list->serv_name."</div>";
            $row[] = "<div class='fw-bold'>" . $list->edited_by . "</div>".$jabatan."<div>Updated At : </div><div>" . $this->controller->dateFormatIndo($list->edited_at,2) . "</div>";
            $row[] = $sender;
            $row[] = $cons;
            $row[] = "<div style='display:flex'><div style='margin-right:5px'>".$list->total_foreign."</div><a class='pointlink lookresiln' data-id='".$list->ms_track_id."' data-resi-ln='".$list->foreign_tracks."' data-catatan='".$list->note."' data-editable='".$editable."' data-images='".$list->images."' data-baseurl='".url('/')."'><i class='fas fa-eye'></i></a></div>".$noteEl;

            if($filter["navType"]!="pay"){
                $row[] = $wholeBtn;
            }else{
                $row[] = "";
            }

            $row[] = "";

            $data[] = $row;
        }

        return $data;
    }
}