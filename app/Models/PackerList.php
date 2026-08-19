<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class PackerList extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'order_list';
    protected $primarykey = 'id';
    public $incrementing = true;
    public $timestamps = true;

    protected $order = ['order_list.updated_at' => 'DESC'];
    protected $column_order = [
        'id',
        'invoice_id'
    ];

    private $controller;

    public function __construct()
    {
        $this->controller = new Controller;
    }

    public function getDTQuery(Request $request, $katakunci = '', $filter)
    {

        $column_search = [
            'order_list.invoice_id',
            'order_list.first_name',
            'order_list.middle_name',
            'order_list.last_name',
        ];

        $packquery = PackerList::getQueryFromMode($filter['mode']);
        
        $select = "order_list.*,
                CONCAT_WS(' ',order_list.first_name,order_list.middle_name,order_list.last_name) AS full_name,
                CONCAT_WS(' ',(SELECT COUNT(shiptrip_foreign_track_list.id) FROM shiptrip_foreign_track_list WHERE shiptrip_foreign_track_list.ms_track_id=order_list.ms_track_id),'Resi LN') AS total_foreign,
                (SELECT GROUP_CONCAT(shiptrip_foreign_track_list.id SEPARATOR ', ') FROM shiptrip_foreign_track_list WHERE shiptrip_foreign_track_list.ms_track_id=order_list.ms_track_id GROUP BY ms_track_id) AS foreign_tracks";
        
        $filterTanggal = $filter['filterTanggal'][0]!=null||$filter['filterTanggal'][0]!=""?"AND order_list.to_sg_man_created_at BETWEEN '" . date("Y-m-d", strtotime($filter['filterTanggal'][0])) . " 00:00:00' AND '" . date("Y-m-d", strtotime($filter['filterTanggal'][1])) . " 23:59:59'":"";
        $query = $filter['custTypeId'] == "IND" ? "order_list.cust_type_id='IND' $packquery $filterTanggal" : "order_list.cust_type_id='COR' $packquery $filterTanggal";
        $where = "";

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
        } else if (isset($this->order)) {
            $orderByA = key($this->order);
            $orderByB = $this->order[key($this->order)];
        }

        $data = [
            'select' => $select,
            'where' => $where,
            'orderByA' => $orderByA,
            'orderByB' => $orderByB
        ];

        return $data;
    }

    public function getDT(Request $request, $katakunci, $filter)
    {
        $query = PackerList::getDTQuery($request, $katakunci, $filter);

        if ($request->input('length') != -1) {
            $offset = $request->input('start');
            $limit = $request->input('length');
            return PackerList::selectRaw($query['select'])
                ->join("cust_type_list", "cust_type_list.id", "=", "order_list.cust_type_id")
                ->whereRaw($query['where'])
                ->skip($offset)
                ->take($limit)
                ->orderBy($query['orderByA'], $query['orderByB'])
                ->get();
        }
        return PackerList::selectRaw($query['select'])
            ->join("cust_type_list", "cust_type_list.id", "=", "order_list.cust_type_id")
            ->whereRaw($query['where'])
            ->orderBy($query['orderByA'], $query['orderByB'])
            ->get();
    }

    public function countFiltered(Request $request, $katakunci, $filter)
    {
        $data = PackerList::getDTQuery($request, $katakunci, $filter);
        return count(PackerList::whereRaw($data['where'])->get());
    }

    public function countAll()
    {
        return count(PackerList::get());
    }

    private function getQueryFromMode($mode)
    {
        $username = Auth::user()->username;
        if($mode=="packlist"){
            return "AND order_list.ms_track_id!='' AND packing_created_by=''";
        }

        if($mode=="packchecked"){
            return "AND order_list.ms_track_id!='' AND packing_created_by='$username'";
        }
    }

    public function getTotalByInvoice($invoiceId)
    {
        if($invoiceId==""){
            return "-";
        }

        $get = DB::table("data_list")
                ->selectRaw(
                    "SUM(CASE when mismass_invoice_id='$invoiceId' THEN weight ELSE 0 END) as totalWeight,
                    SUM(CASE when mismass_invoice_id='$invoiceId' THEN item ELSE 0 END) as totalItem,
                    SUM(CASE when mismass_invoice_id='$invoiceId' THEN cbm ELSE 0 END) as totalCbm"
                )
                ->where("mismass_invoice_id",$invoiceId)
                ->first();

        return "<div>" . round($get->totalWeight,2) . " KG</div><div>" . $get->totalItem . " Item</div><div>".round($get->totalCbm,2)." CBM</div>";
    }

    public function getInvoiceIdData($invoiceId)
    {
        if($invoiceId==""){
            return "-";
        }

        $invoiceDate = DB::table("data_list")
                ->where("mismass_invoice_id",$invoiceId)
                ->value("created_at");

        return "<div class='fw-bold'>" . $invoiceId . "</div><div>" . $this->controller->dateFormatIndo($invoiceDate,1) . "</div>";
    }
}
