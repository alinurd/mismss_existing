<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

class Tracking extends Model
{
    use HasFactory;

    protected $table = 'data_list';
    protected $primarykey = 'id';
    public $incrementing = true;
    public $timestamps = true;

    protected $order = ['data_list.shipping_updated_at' => 'DESC'];
    protected $column_order = [
        'id',
        'mismass_invoice_id',
        'mismass_invoice_date',
        'doku_invoice_id',
        'created_by',
        'created_at',
        'weight',
        'item',
        'sub_total',
        'doku_link',
        'bank_name',
        'bank_account_name',
        'bank_account_id',
    ];

    public function getDTQuery(Request $request, $katakunci = '', $filter)
    {

        $filterOrderId = $filter['filterOrderId'];
        $column_search = Tracking::columnSearch($filter['custTypeId'],$filter['filterOrderId']);

        $select = "data_list.*,
        SUM(weight) as totalWeight,
        COUNT(mismass_invoice_id) as totalInvoice,
        SUM(cbm) as totalCbm,
        SUM(item) as totalItem,
        SUM(discount) as totalDisc,
        SUM(adjust_fee) as totalAdtFee,
        SUM(sub_total+adjust_fee) as totalPrice,
        SUM((data_list.sub_total+data_list.adjust_fee)/data_list.fc_value) as totalPriceForeign,
        cust_type_list.name as custTypeName";

        $filterTanggal = $filter['filterTanggal'][0] != null && $filter['filterTanggal'][0] != "" ? "AND data_list.created_at BETWEEN '" . date("Y-m-d", strtotime($filter['filterTanggal'][0])) . " 00:00:00' AND '" . date("Y-m-d", strtotime($filter['filterTanggal'][1])) . " 23:59:59'" : "";
        $filterWarehouse = $filter['filterWarehouse'] != null && $filter['filterWarehouse'] != "" ? "AND data_list.warehouse_id='" . $filter['filterWarehouse'] . "'" : "";
        $filterService = $filter['filterService'] != null && $filter['filterService'] != "" ? "AND data_list.service_id='" . $filter['filterService'] . "'" : "";
        $filterPay = Tracking::filteringPay(['filterPay'=>$filter['filterPay']]);
        $query = $filter['custTypeId'] == "IND" ? "data_list.cust_type_id='IND' AND shipping_number!='' $filterPay $filterWarehouse $filterService $filterTanggal" : "data_list.cust_type_id='COR' AND shipping_number!='' $filterPay $filterWarehouse $filterService $filterTanggal";
        $groupBy = "data_list.mismass_invoice_id";
        if($filter['filterOrderId']!=""){
            $query = "data_list.mismass_order_id='$filterOrderId'";
            $groupBy = "data_list.shipping_number";
        }

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
        $query = Tracking::getDTQuery($request, $katakunci, $filter);

        if ($request->input('length') != -1) {
            $offset = $request->input('start');
            $limit = $request->input('length');
            return Tracking::selectRaw($query['select'])
                ->join("cust_type_list", "cust_type_list.id", "=", "data_list.cust_type_id")
                ->whereRaw($query['where'])
                ->groupBy($query['groupBy'])
                ->skip($offset)
                ->take($limit)
                ->orderBy($query['orderByA'], $query['orderByB'])
                ->get();
        }
        return Tracking::selectRaw($query['select'])
            ->join("cust_type_list", "cust_type_list.id", "=", "data_list.cust_type_id")
            ->whereRaw($query['where'])
            ->groupBy($query['groupBy'])
            ->orderBy($query['orderByA'], $query['orderByB'])
            ->get();

    }

    public function countFiltered(Request $request, $katakunci, $filter)
    {
        $query = Tracking::getDTQuery($request, $katakunci, $filter);
        return count(Tracking::selectRaw($query['select'])
            ->join("cust_type_list", "cust_type_list.id", "=", "data_list.cust_type_id")
            ->whereRaw($query['where'])
            ->groupBy($query['groupBy'])
            ->get());
    }

    public function countAll(Request $request, $katakunci, $filter)
    {
        $query = Tracking::getDTQuery($request, $katakunci, $filter);
        return Tracking::groupBy($query['groupBy'])->count();
    }

    public function columnSearch($typeId,$mismassOrderId){

        if($mismassOrderId==""){
            $column_search = [
                '(CASE WHEN data_list.fc_symbol!="" THEN "SGD" ELSE 0 END)',
                'CONCAT_WS(" ",data_list.sender_first_name,data_list.sender_middle_name,data_list.sender_last_name)',
                'CONCAT_WS(" ",data_list.sender_first_name,data_list.sender_last_name)',
                'CONCAT_WS(" ",data_list.cons_first_name,data_list.cons_middle_name,data_list.cons_last_name)',
                'CONCAT_WS(" ",data_list.cons_first_name,data_list.cons_last_name)',
                '(CASE WHEN data_list.ms_track_id="" THEN "Bulky" ELSE 0 END)',
                'data_list.ms_track_id',
                'data_list.mismass_invoice_id',
                'data_list.mismass_invoice_date',
                'data_list.doku_invoice_id',
                'data_list.doku_link',
                'data_list.created_at',
                'data_list.created_by',
                'data_list.forwarder_id',
                'data_list.forwarder_name',
                'data_list.shipping_number',
                'data_list.bank_name',
                'data_list.bank_account_id',
                'data_list.bank_account_name',
                'data_list.sender_first_name',
                'data_list.sender_middle_name',
                'data_list.sender_last_name',
                'data_list.sender_address',
                'data_list.sender_sub_district',
                'data_list.sender_district',
                'data_list.sender_city',
                'data_list.sender_prov',
                'data_list.sender_postal_code',
                'data_list.sender_phone',
                'data_list.cons_first_name',
                'data_list.cons_middle_name',
                'data_list.cons_last_name',
                'data_list.cons_address',
                'data_list.cons_sub_district',
                'data_list.cons_district',
                'data_list.cons_city',
                'data_list.cons_prov',
                'data_list.cons_postal_code',
                'data_list.cons_phone'
            ];
        }else{
            $column_search = [
                'CONCAT_WS(" ",data_list.cons_first_name,data_list.cons_middle_name,data_list.cons_last_name)',
                'CONCAT_WS(" ",data_list.cons_first_name,data_list.cons_last_name)',
                'data_list.forwarder_id',
                'data_list.forwarder_name',
                'data_list.shipping_number',
                'data_list.cons_first_name',
                'data_list.cons_middle_name',
                'data_list.cons_last_name',
                'data_list.cons_address',
                'data_list.cons_sub_district',
                'data_list.cons_district',
                'data_list.cons_city',
                'data_list.cons_prov',
                'data_list.cons_postal_code',
                'data_list.weight',
                'data_list.item'
            ];
        }

        return $column_search;
    }

    public function filteringPay($filter){
        $query = "";
        if($filter['filterPay'] == null || $filter['filterPay'] == ""){
            return $query;
        }

        if($filter['filterPay']=="DOKU"){
            $query = "AND doku_link!=''";
            return $query;
        }

        if($filter['filterPay']=="BANK"){
            $query = "AND data_list.bank_name!=''";
            return $query;
        }
    }

    public function getStatusIdTracking($id){
        if($id=="MISMASS"){
            return 14;
        }
        
        if($id=="PICK-UP"){
            return 12;
        }

        if($id=="VENDOR"){
            return 17;
        }
    }

    public function getTextTracking($id,$name){
        if($id=="MISMASS"){
            return "Siap dikirim ke alamat tujuan";
        }
        
        if($id=="PICK-UP"){
            return "Paket akan dipickup sendiri oleh customer";
        }

        if($id=="VENDOR"){
            // return "Siap dikirim ke alamat tujuan";
            if($name=="SENTRAL CARGO" || $name=="JNE"){
                return "Paket dalam proses pengiriman";
            }
            return "PROCESSN";
        }
    }

    public function getTextTrackingEdit($array){

        $newForwarderId = $array["forwarder_id"];
        $oldForwarderId = $array["forwarder_id_old"];

        if($newForwarderId=="PICK-UP"){
            return "Paket akan dipickup sendiri oleh customer. [Paket telah dialihkan ke ".$newForwarderId."]";
        }

        if($newForwarderId=="MISMASS"){
            return "Siap dikirim ke alamat tujuan. [Paket telah dialihkan ke ".$newForwarderId."]";
        }

        if($newForwarderId=="VENDOR"){
            if($array["forwarder_name"]=="JNE" || $array["forwarder_name"]=="SENTRAL CARGO"){
                return "Paket dalam proses pengiriman [Paket telah dialihkan ke MISMASS]";
            }

            return "PROCESSN";

        }
    }
    
    public static function updateTrackingForCorOnly($array){
        //Array
        //0 Invoice
        //1 Username
        //2 Track Status Id

        if($array[0]==""){
            return false;
        }

        $getMsTracks = DB::table("order_list")
                        ->select("ms_track_id")
                        ->where("invoice_id",$array[0])
                        ->get();

        foreach($getMsTracks as $g){

            if($g->ms_track_id==""){
                continue;
            }

            $dataTracking = [
                "created_at" => date("Y-m-d H:i:s"),
                "created_by" => $array[1],
                "ms_track_id" => $g->ms_track_id,
                "track_status_id" => $array[2],
                "track_status_manual_id" => "A", 
                "text" => "Paket telah dikirimkan ke masing-masing alamat, <b>Klik Select Waybill</b> untuk mengetahui tracking paket lainnya!"
            ];
            $updateTracking = DB::table("shiptrip_track_list")->insert($dataTracking);

            DB::table("shiptrip_list")
            ->where("ms_track_id", "=", $g->ms_track_id)
            ->update([
                "track_status_id" => $array[2],
            ]);
        }

        return true;
    }
}
