<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Order extends Model
{
    use HasFactory;

    protected $table = 'order_list';
    protected $primarykey = 'id';
    public $incrementing = true;
    protected $keyType = 'string';
    public $timestamps = true;

    protected $order = ['updated_at' => 'DESC'];
    protected $column_order = [
        'id',
        'updated_at',
        'first_name',
        'address',
        'order_status_id',
        'id',
        'updated_at',
        'updated_at',
    ];

    private $controller;

    public function __construct()
    {
        $this->controller = new Controller;
    }

    public function getDTQuery(Request $request, $katakunci = '', $filter)
    {

        $column_search = [
            'CONCAT_WS(" ",order_list.first_name,order_list.middle_name,order_list.last_name)',
            'CONCAT_WS(" ",order_list.first_name,order_list.last_name)',
            'order_list.ms_track_id',
            'order_list.id',
            'order_list.order_status_id',
            'order_list.created_at',
            'order_list.created_by',
            'order_list.first_name',
            'order_list.middle_name',
            'order_list.last_name',
            'order_list.address',
            'order_list.city',
            'order_list.prov',
            'order_list.phone',
            'order_list.email',
            'order_list.postal_code',
        ];

        $select = "order_list.*,
                CONCAT_WS(' ',(SELECT COUNT(shiptrip_foreign_track_list.id) FROM shiptrip_foreign_track_list WHERE shiptrip_foreign_track_list.ms_track_id=order_list.ms_track_id),'Resi LN') AS total_foreign,
                (SELECT GROUP_CONCAT(shiptrip_foreign_track_list.id SEPARATOR ', ') FROM shiptrip_foreign_track_list WHERE shiptrip_foreign_track_list.ms_track_id=order_list.ms_track_id GROUP BY ms_track_id) AS foreign_tracks";
        
        $filterTanggal = $filter['tanggal'][0] != null || $filter['tanggal'][0] != "" ? "AND to_sg_man_created_at BETWEEN '" . date("Y-m-d", strtotime($filter['tanggal'][0])) . " 00:00:00' AND '" . date("Y-m-d", strtotime($filter['tanggal'][1])) . " 23:59:59'" : "";
        $filterWarehouse = $filter['warehouse'] != null || $filter['warehouse'] != "" ? ($filter['warehouse']!="BULKY"?"AND warehouse_id='".$filter["warehouse"]."'":"AND warehouse_id=''") : "";
        $filterService = $filter['service'] != null || $filter['service'] != "" ? "AND service_id='".$filter["service"]."'" : "";
        $filterCustomer = $filter['customer'] != null || $filter['customer'] != "" ? "AND cust_id='".$filter['customer']."'" : "";
        $query = $filter['custTypeId'] == "IND" ? "cust_type_id='IND' AND invoice_id='' $filterTanggal $filterCustomer $filterWarehouse $filterService" : "cust_type_id='COR' AND invoice_id='' $filterTanggal $filterCustomer $filterWarehouse $filterService";
        $where = $query;

        if (!empty($katakunci)) {
            $where = "";
            for ($i = 0; $i <= count($column_search) - 1; $i++) {
                if ($i < count($column_search) - 1) {
                    $where .= $query . "AND $column_search[$i] LIKE '%$katakunci%' OR ";
                } else {
                    $where .= $query . "AND $column_search[$i] LIKE '%$katakunci%'";
                }
            }
        }

        if ($request->input('order')) {
            $orderByA = $this->column_order[$request->input('order')['0']['column']];
            $orderByB = $request->input('order')['0']['dir'];
        } else if (isset($this->order)) {
            $orderByA = key($this->order);
            $orderByB = $this->order[key($this->order)];
        }

        $queries = [
            'select' => $select,
            'where' => $where,
            'orderByA' => $orderByA,
            'orderByB' => $orderByB
        ];

        return $queries;
    }

    public function getDT(Request $request, $katakunci, $filter)
    {
        $query = Order::getDTQuery($request, $katakunci, $filter);
        if ($request->input('length') != -1) {
            $offset = $request->input('start');
            $limit = $request->input('length');
            return Order::selectRaw($query['select'])
                ->whereRaw($query['where'])
                ->skip($offset)
                ->take($limit)
                ->orderBy($query['orderByA'], $query['orderByB'])
                ->get();
            
        }
        return Order::selectRaw($query['select'])
            ->whereRaw($query['where'])
            ->orderBy($query['orderByA'], $query['orderByB'])
            ->get();
    }

    public function countFiltered(Request $request, $katakunci, $filter)
    {
        $data = Order::getDTQuery($request, $katakunci, $filter);
        return Order::whereRaw($data['where'])->count();
    }

    public function countAll()
    {
        return Order::count();
    }

    public function checkResiLN($id){
        $value = "<div>-</div>";

        return $value;
    }

    public function checkMsTrackId($id){
        $value = "<div class='fw-bold'>Bulky</div>";
        if($id!=""){
            $getShipTripData = DB::table("shiptrip_list")->select("to_sg_man_created_at","drop_man_created_at")->where("ms_track_id",$id)->first();
            // dd($getShipTripData);
            $getLatest = DB::table("shiptrip_track_list")->selectRaw("created_by,created_at")->where("ms_track_id",$id)->orderBy("created_at","desc")->get();
            $shipmentDate = count($getLatest)>1?"<div>TRACK:".$this->controller->dateFormatIndo($getLatest[0]->created_at,1)."<div>SHIP:".$this->controller->dateFormatIndo($getShipTripData->to_sg_man_created_at,1)."</div>":"";
            $value = "<a class='fw-bold loadTracking' href='".url('/shiptrip/tracking')."?id=".$id."' target='_blank'>".$id."</a>".$shipmentDate."<div>DROP:".$this->controller->dateFormatIndo($getShipTripData->drop_man_created_at,1)."</div>";
        }
        return $value;
    }

    public function checkWareServ($id){
        $value = "<div>-</div>";
        $getWareServ = DB::table("order_list")
                        ->select("warehouse_id","service_id")
                        ->where("id",$id)
                        ->first();
        if($getWareServ->warehouse_id!=""&&$getWareServ->service_id!=0){
            $warehouse = DB::table("warehouse_list")->where("id",$getWareServ->warehouse_id)->value("location");
            $service = DB::table("service_list")->where("id",$getWareServ->service_id)->value("name");
            $value = "<div style='font-weight:700'>".$getWareServ->warehouse_id." - ".$warehouse."</div><div>Service : ".$service."</div>";
        }
        return $value;
    }
}
