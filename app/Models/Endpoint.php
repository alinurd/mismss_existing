<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ShipTrip;
use App\Models\TrackSystem;

class Endpoint extends Model
{
    use HasFactory;
    protected $table = 'data_list';
    protected $primarykey = 'id';
    protected $column_order = [
        'data_list.id',
        "data_list.shipping_number",
        "data_list.ms_track_id",
        "data_list.warehouse_id",
        "data_list.data_list.service_id"
    ];
    // protected $order = ['data_list.to_sg_man_created_at' => 'DESC'];

    private $controller;
    private $shipTripModel;

    public function __construct()
    {
        $this->controller = new Controller;
        $this->shipTripModel = new ShipTrip;
    }

    public function getDTQuery(Request $request, $katakunci = '', $filter)
    {        

        $column_search = [
            'data_list.id',
            "data_list.shipping_updated_by",
            "data_list.shipping_status",
            "data_list.forwarder_id",
            "data_list.forwarder_name",
            "data_list.shipping_number",
            "data_list.ms_track_id",
            "CONCAT_WS(' ',data_list.cons_first_name,data_list.cons_middle_name,data_list.cons_last_name)",
            "CONCAT_WS(' ',data_list.cons_first_name,data_list.cons_last_name)",
            "data_list.cons_first_name",
            "data_list.cons_middle_name",
            "data_list.cons_last_name",
            "data_list.cons_phone",
            "data_list.cons_address",
            "data_list.cons_city",
            "data_list.cons_prov",
            "data_list.cons_postal_code",
            "data_list.warehouse_id",
            "data_list.service_id",
            "service_list.name",
            "warehouse_list.location",
            "(CASE WHEN forwarder_id='MISMASS' THEN 'KURIR MISMASS' END)",
            "(CASE WHEN forwarder_id='PICK-UP' THEN 'PICKUP SENDIRI' END)",
            '(CASE WHEN data_list.ms_track_id="" THEN "Bulky" ELSE 0 END)',
        ];

        $getQuery = Endpoint::getQuery($filter['navType']);
        $filterCust = $filter['custTypeId'];
        $filterTanggal = $filter['tanggal']!=""?($filter['tanggal'][0] != "" || $filter['tanggal'][0] != null ? "AND ".$this->shipTripModel->setFilterDate($filter['navShip'])." BETWEEN '" . date("Y-m-d", strtotime($filter['tanggal'][0])) . " 00:00:00' AND '" . date("Y-m-d", strtotime($filter['tanggal'][1])) . " 23:59:59'" : ""):"";
        $select = "data_list.*,warehouse_list.location AS warename,service_list.name AS servname";
        $query = "$getQuery AND data_list.cust_type_id='$filterCust' $filterTanggal";
        $where = "";
        $groupBy = "data_list.shipping_number";
        $ordering = ['data_list.shipping_updated_at' => 'DESC'];

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

        $data = Endpoint::getDTQuery($request, $katakunci, $filter);
        if ($request->input('length') != -1) {
            $offset = $request->input('start');
            $limit = $request->input('length');
                return Endpoint::selectRaw($data['select'])
                        ->join("warehouse_list", "warehouse_list.id", "=", "data_list.warehouse_id")
                        ->join("service_list", "service_list.id", "=", "data_list.service_id")
                        ->join("cust_type_list", "cust_type_list.id", "=", "data_list.cust_type_id")
                        ->whereRaw($data['where'])
                        ->skip($offset)
                        ->take($limit)
                        ->orderBy($data['orderByA'], $data['orderByB'])
                        ->groupBy($data['groupBy'])
                        ->get();
        }

        return Endpoint::selectRaw($data['select'])
                ->join("warehouse_list", "warehouse_list.id", "=", "data_list.warehouse_id")
                ->join("service_list", "service_list.id", "=", "data_list.service_id")
                ->join("cust_type_list", "cust_type_list.id", "=", "data_list.cust_type_id")
                ->whereRaw($data['where'])
                ->orderBy($data['orderByA'], $data['orderByB'])
                ->groupBy($data['groupBy'])
                ->get();
    }

    public function countFiltered(Request $request, $katakunci, $filter)
    {
        $data = Endpoint::getDTQuery($request, $katakunci, $filter);
        return count(Endpoint::selectRaw($data['select'])
                ->join("warehouse_list", "warehouse_list.id", "=", "data_list.warehouse_id")
                ->join("service_list", "service_list.id", "=", "data_list.service_id")
                ->join("cust_type_list", "cust_type_list.id", "=", "data_list.cust_type_id")
                ->whereRaw($data['where'])
                ->groupBy($data['groupBy'])
                ->get());
    }

    public function countAll()
    {
        return Endpoint::count();
    }

    public function getQuery($navType)
    {
        if($navType=="deliver"){
            return "ms_track_id!='' AND shipping_success_by!='' AND data_list.updated_at>'2025-08-26 00:00:00'";
        }

        if($navType=="courier"){
            return "forwarder_id='MISMASS' AND shipping_success_by='' AND shipping_status!='SUKSES' AND data_list.updated_at>'2025-08-26 00:00:00'";
        }

        if($navType=="pickup"){
            return "forwarder_id='PICK-UP' AND shipping_success_by='' AND data_list.updated_at>'2025-08-26 00:00:00'";
        }

        if($navType=="vendor"){
            return "forwarder_id='VENDOR' AND shipping_success_by='' AND data_list.updated_at>'2025-08-26 00:00:00'";
        }
    }

    public function callTableEndPoint($request,$filter)
    {
        
        if($filter['navType']=="vendor"){
            $data = Endpoint::vendorTable($request,$filter);
            return $data;
        }

        if($filter['navType']=="courier"){
            $data = Endpoint::courierTable($request,$filter);
            return $data;
        }

        if($filter['navType']=="pickup"){
            $data = Endpoint::pickupTable($request,$filter);
            return $data;
        }

        if($filter['navType']=="deliver"){
            $data = Endpoint::deliverTable($request,$filter);
            return $data;
        }

    }

    public function vendorTable($request,$filter)
    {
        $no = $request->input('start');
        $search = $request->input('search')['value'];
        $data = [];
        $lists = Endpoint::getDT($request, $search, $filter);

        foreach ($lists as $list) {
            $shippingNumber = $list->shipping_number_stats==1?"<div style='color:red'>".$list->shipping_number."</div>":$list->shipping_number;

            $no++;
            $row = [];
            $row[] = "<div class='orderNum'>".$no."</div>";
            $row[] = Endpoint::checkMsTrackEndpoint($list);
            $row[] = "<div class='fw-bold'>".$list->forwarder_name."</div>".$shippingNumber."<div>".$this->controller->dateFormatIndo($list->shipping_created_at,1)."</div>";
            $row[] = "<div class='fw-bold'>".$list->warename."</div><div>".$list->servname."</div>";
            $row[] = Endpoint::shippingUpdateData($list);
            $row[] = "<div class='fw-bold'>".$list->cons_first_name." ".$list->cons_middle_name." ".$list->cons_last_name."</div><div>".$list->cons_phone."</div><div>".$list->cons_city.", ".$list->cons_prov.", ".$list->cons_postal_code."</div>";
            // $row[] = Endpoint::lastTrackingStatus($list,$filter);
            $row[] = $list->shipping_status!="" ? "<div class='text-primary'>".TrackSystem::checkTimelineText($list->shipping_status,$list->shipping_number,1)."</div>" : "-";
            $row[] = Endpoint::wholeButton($list,$filter);

            $data[] = $row;
        }
        return $data;
    }

    public function pickupTable($request,$filter)
    {
        $no = $request->input('start');
        $search = $request->input('search')['value'];
        $data = [];
        $lists = Endpoint::getDT($request, $search, $filter);

        foreach ($lists as $list) {
            $shippingNumberEl = $list->cust_type_id=="COR" ? "<div>Resi Lokal</div><div class='text-primary fw-bold'>".$list->shipping_number."</div><div>".$this->controller->dateFormatIndo($list->shipping_created_at,2)."</div><div style='border:1px solid grey;margin:5px 0px'></div>" : "" ;
            $shippingStatusEl = $shippingNumberEl."<div class='text-primary'>".$list->shipping_status."</div>";
            $shippingStatus = $list->shipping_status!="" ? $shippingStatusEl : "-";

            $no++;
            $row = [];
            $row[] = "<div class='orderNum'>".$no."</div>";
            $row[] = Endpoint::checkMsTrackEndpoint($list);
            // $row[] = "<div class='fw-bold'>".$list->shipping_number."</div><div>".$this->controller->dateFormatIndo($list->shipping_created_at,1)."</div>";
            $row[] = "<div class='fw-bold'>".$list->warename."</div><div>".$list->servname."</div>";
            $row[] = Endpoint::shippingUpdateData($list);
            $row[] = "<div class='fw-bold'>".$list->cons_first_name." ".$list->cons_middle_name." ".$list->cons_last_name."</div><div>".$list->cons_phone."</div><div>".$list->cons_city.", ".$list->cons_prov.", ".$list->cons_postal_code."</div>";
            $row[] = $shippingStatus;
            $row[] = Endpoint::wholeButton($list,$filter);
            $row[] = "";

            $data[] = $row;
        }
        return $data;
    }

    public function deliverTable($request,$filter)
    {
        $no = $request->input('start');
        $search = $request->input('search')['value'];
        $data = [];
        $lists = Endpoint::getDT($request, $search, $filter);

        foreach ($lists as $list) {
            $vendorShippingNumber = $list->shipping_number_stats==1?"<div style='color:red'>".$list->shipping_number."</div>":$list->shipping_number;
            $shippingNumber = $list->forwarder_id=="VENDOR"?"<div class='fw-bold'>".$list->forwarder_name."</div>".$vendorShippingNumber."<div>".$this->controller->dateFormatIndo($list->shipping_created_at,1)."</div>":($list->forwarder_id=="MISMASS"?"<div class='fw-bold'>KURIR MISMASS</div><div>".$this->controller->dateFormatIndo($list->shipping_created_at,1)."</div>":"<div class='fw-bold'>PICKUP SENDIRI</div><div>".$this->controller->dateFormatIndo($list->shipping_created_at,1)."</div>");

            $no++;
            $row = [];
            $row[] = "<div class='orderNum'>".$no."</div>";
            $row[] = Endpoint::checkMsTrackEndpoint($list);
            $row[] = $shippingNumber;
            $row[] = "<div class='fw-bold'>".$list->warename."</div><div>".$list->servname."</div>";
            $row[] = Endpoint::shippingUpdateData($list);
            $row[] = "<div class='fw-bold'>".$list->cons_first_name." ".$list->cons_middle_name." ".$list->cons_last_name."</div><div>".$list->cons_phone."</div><div>".$list->cons_city.", ".$list->cons_prov.", ".$list->cons_postal_code."</div>";
            $row[] = "";
            $row[] = "";

            $data[] = $row;
        }
        return $data;
    }

    public function courierTable($request,$filter)
    {
        $no = $request->input('start');
        $search = $request->input('search')['value'];
        $data = [];
        $lists = Endpoint::getDT($request, $search, $filter);

        foreach ($lists as $list) {
            $shippingNumberEl = $list->cust_type_id=="COR" ? "<div>Resi Lokal</div><div class='text-primary fw-bold'>".$list->shipping_number."</div><div>".$this->controller->dateFormatIndo($list->shipping_created_at,2)."</div><div style='border:1px solid grey;margin:5px 0px'></div>" : "" ;
            $shippingStatusEl = $shippingNumberEl."<div class='text-primary'>".$list->shipping_status."</div>";
            $shippingStatus = $list->shipping_status!="" ? $shippingStatusEl : "-";

            $no++;
            $row = [];
            $row[] = "<div class='orderNum'>".$no."</div>";
            $row[] = Endpoint::checkMsTrackEndpoint($list);
            // $row[] = "<div class='fw-bold'>".$list->shipping_number."</div><div>".$this->controller->dateFormatIndo($list->shipping_created_at,1)."</div>";
            $row[] = "<div class='fw-bold'>".$list->warename."</div><div>".$list->servname."</div>";
            $row[] = Endpoint::shippingUpdateData($list);
            $row[] = "<div class='fw-bold'>".$list->cons_first_name." ".$list->cons_middle_name." ".$list->cons_last_name."</div><div>".$list->cons_phone."</div><div>".$list->cons_city.", ".$list->cons_prov.", ".$list->cons_postal_code."</div>";
            $row[] = Endpoint::shippingCourierData($list);
            // $row[] = Endpoint::lastTrackingStatus($list,$filter);
            $row[] = $shippingStatus;
            $row[] = Endpoint::wholeButton($list,$filter);

            $data[] = $row;
        }
        return $data;
    }

    private function checkMsTrackEndpoint($list)
    {
        $element = "<div class='fw-bold'>Bulky</div>";
        if($list->ms_track_id!=""){
            $element = "<a class='fw-bold loadTracking' href='".url('/shiptrip/tracking')."?id=".$list->ms_track_id."' target='_blank'>".$list->ms_track_id."</a>".
                    "<div>TRACK:".$this->controller->dateFormatIndo($list->track_man_created_at,1)."</div>".
                    "<div>SHIP:".$this->controller->dateFormatIndo($list->to_sg_man_created_at,1)."</div>".
                    "<div>DROP:".$this->controller->dateFormatIndo($list->drop_man_created_at,1)."</div>";
        }
        return $element;
    }

    private function shippingUpdateData($list)
    {
        $getRank = DB::table('users')->where("username",$list->shipping_updated_by)->value("rank");
        $rank = $getRank != null ? "<div class='bg-mismass' style='padding:1px 5px'>".$getRank."</div>" : "";
        $element = "<div class='fw-bold'>".$list->shipping_updated_by."</div>".
                $rank.
                "<div>Updated At : </div>".
                "<div>".$this->controller->dateFormatIndo($list->shipping_updated_at,2)."</div>";
        return $element;
    }

    public function shippingCourierData($list)
    {
        $getRank = DB::table('users')->where("username",$list->forwarder_name)->value("rank");
        $rank = $getRank != null ? "<div class='bg-mismass' style='padding:1px 5px'>".$getRank."</div>" : "";
        $element = "-";
        if($list->forwarder_name!=""){
            $element = "<div class='fw-bold'>".$list->forwarder_name."</div>".
                    $rank.
                    "<div>Updated At : </div>".
                    "<div>".$this->controller->dateFormatIndo($list->shipping_created_at,2)."</div>";
        }
        return $element;
    }

    private function lastTrackingStatus($list,$filter)
    {
        if($list->ms_track_id==""){
            $lastStatus = "<div>-</div>";
            return $lastStatus;
        }

        if($filter['custTypeId']=="IND"){
            $lastStatus = DB::table("shiptrip_track_list")
                        ->where("ms_track_id",$list->ms_track_id)
                        ->orderBy("created_at","DESC")
                        ->value("text");
            return $lastStatus;
        }

        if($filter['custTypeId']=="COR"){
            $lastStatus = DB::table("shiptrip_track_list")
                        ->where("ms_track_id",$list->shipping_number)
                        ->orderBy("created_at","DESC")
                        ->value("text");
            return $lastStatus;
        }
    }

    private function wholeButton($list,$filter)
    {
        $this->controller->roleAccess();

        $updateBtn = "";
        $noClick = "pointer-events: none";
        if(Auth::user()->shiptrip_update){
            $noClick = "";
            $updateBtn = "<a class='dropdown-item pointlink' id='endUpdateBtn' data-cust-type='".$list->cust_type_id."' data-track-status='".$list->track_status_id."' data-ms-track-id='".$list->ms_track_id."' data-shipping-number='".$list->shipping_number."'>Update Shipment</a>";
            if($list->forwarder_name=="SENTRAL CARGO"||$list->forwarder_name=="JNE"){
                $updateBtn = "<a class='dropdown-item'>Update Shipment</a>";
                $noClick = "pointer-events: none";
            }        
        }

        $wholeBtn = "<div style='".$noClick."' class='btn-group dropleft'>".
                        "<button type='button' class='btn btn-secondary nobtn' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>".
                            "<i class='fas fa-ellipsis-v'></i>".
                        "</button>".
                        "<div class='dropdown-menu' x-placement='right-start' style='position: absolute; transform: translate3d(111px, 0px, 0px); top: 0px; left: 0px; will-change: transform;'>".
                        $updateBtn.
                        "</div>".
                    "</div>";
        
        return $wholeBtn;
    }
}
