<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Warehouse extends Model
{
    use HasFactory;

    protected $table = 'warehouse_list';
    protected $primarykey = 'id';
    public $incrementing = false;
    public $timestamps = true;

    protected $order = ['warehouse_list.updated_at' => 'DESC'];
    protected $column_order = [
        'id',
        'updated_at',
        'id',
        'location',
        'name',
        'name',
        'name',
        'name',
        'name',
        'id',
    ];

    public function getDTQuery(Request $request, $katakunci = '', $filter)
    {

        $column_search = [
            "warehouse_list.id",
            "warehouse_list.created_at",
            "warehouse_list.created_by",
            "warehouse_list.updated_at",
            "warehouse_list.updated_by",
            "warehouse_list.name",
            "warehouse_list.location",
            "warehouse_list.description"
        ];

        $select = "warehouse_list.*,
                   COALESCE(SUM(data_list.weight),0) AS weightByWarehouse,
                   COALESCE(SUM(data_list.item),0) AS itemByWarehouse,
                   COALESCE(SUM(data_list.cbm),0) AS cbmByWarehouse,
                   COALESCE(COUNT(DISTINCT data_list.service_id),0) AS serviceByWarehouse,
                   COALESCE(COUNT(DISTINCT data_list.cust_id),0) AS custByWarehouse,
                   COALESCE(COUNT(DISTINCT data_list.mismass_invoice_id),0) AS invoiceByWarehouse,
                   COALESCE(SUM(CASE WHEN data_list.fc_symbol='' THEN data_list.sub_total+data_list.adjust_fee ELSE 0 END),0) AS incomeRpByWarehouse,
                   COALESCE(SUM(CASE WHEN data_list.fc_symbol='' AND data_list.invoice_status='PAID' THEN data_list.sub_total+data_list.adjust_fee ELSE 0 END),0) AS incomePaidRpByWarehouse,
                   COALESCE(SUM(CASE WHEN data_list.fc_symbol='' AND data_list.invoice_status='UNPAID' THEN data_list.sub_total+data_list.adjust_fee ELSE 0 END),0) AS incomeUnpaidRpByWarehouse,
                   COALESCE(SUM(CASE WHEN data_list.fc_symbol='' THEN data_list.discount ELSE 0 END),0) AS discountRpByWarehouse,
                   COALESCE(SUM(CASE WHEN data_list.fc_symbol='SGD' THEN (data_list.sub_total+data_list.adjust_fee)/data_list.fc_value ELSE 0 END),0) AS incomeSGDByWarehouse,
                   COALESCE(SUM(CASE WHEN data_list.fc_symbol='SGD' AND data_list.invoice_status='PAID' THEN (data_list.sub_total+data_list.adjust_fee)/data_list.fc_value ELSE 0 END),0) AS incomePaidSGDByWarehouse,
                   COALESCE(SUM(CASE WHEN data_list.fc_symbol='SGD' AND data_list.invoice_status='UNPAID' THEN (data_list.sub_total+data_list.adjust_fee)/data_list.fc_value ELSE 0 END),0) AS incomeUnpaidSGDByWarehouse,
                   COALESCE(SUM(CASE WHEN data_list.fc_symbol='SGD' THEN data_list.discount/data_list.fc_value ELSE 0 END),0) AS discountSGDByWarehouse";
        
        $filterTanggalAwal = $filter[0]!=""?($filter[0][0] != "" || $filter[0][0] != null ? date("Y-m-d", strtotime($filter[0][0])) . " 00:00:00" : ""):"";
        $filterTanggalAkhir = $filter[0]!=""?($filter[0][0] != "" || $filter[0][0] != null ? date("Y-m-d", strtotime($filter[0][1])) . " 23:59:59" : ""):"";

        $filterCountry = $filter[2] != "" || $filter[2] != null ? "AND warehouse_list.country_id='".$filter[2]."'" : "";
        $filterRoute = $filter[3] != "" || $filter[3] != null ? "AND warehouse_list.route_id='".$filter[3]."'" : "";
        $filterWarehouse = $filter[1] != "" || $filter[1] != null ? "AND warehouse_list.id='".$filter[1]."'" : "" ;
        $filterCustType = $filter[4];
        $query = "warehouse_list.id!='' $filterCountry $filterRoute $filterWarehouse";
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
            'filterWarehouse' => $filterWarehouse,
            'filterTanggalAwal' => $filterTanggalAwal,
            'filterTanggalAkhir' => $filterTanggalAkhir,
            'filterCustType' => $filterCustType,
            'orderByA' => $orderByA,
            'orderByB' => $orderByB
        ];

        return $data;
    }

    public function getDT(Request $request, $katakunci, $filter)
    {

        $wareModel = new Warehouse;
        $query = $wareModel->getDTQuery($request, $katakunci, $filter);
        if ($request->input('length') != -1) {
            $offset = $request->input('start');
            $limit = $request->input('length');
            return Warehouse::selectRaw($query['select'])
                ->leftJoin("data_list", function($join) use ($query){
                    $join->on("warehouse_list.id","=","data_list.warehouse_id");
                    if(!empty($query['filterCustType']) && !empty($query['filterCustType'])){
                        $join->where("data_list.cust_type_id", $query['filterCustType']);
                    }
                    if(!empty($query['filterTanggalAwal']) && !empty($query['filterTanggalAkhir'])){
                        $join->whereBetween("data_list.created_at", [$query['filterTanggalAwal'],$query['filterTanggalAkhir']]);
                    }
                    $join->where(function($q){
                        $q->where("data_list.doku_link","!=","")
                        ->orWhere("data_list.bank_name","!=","");
                    });
                })
                ->groupBy('warehouse_list.id')
                ->when($query['filterWarehouse']=="" && !empty($query['filterTanggalAwal']) && !empty($query['filterTanggalAkhir']),
                    function($q){
                        $q->havingRaw("COUNT(DISTINCT data_list.mismass_invoice_id) != 0");
                    }
                )
                ->whereRaw($query['where'])
                ->skip($offset)
                ->take($limit)
                ->orderBy($query['orderByA'], $query['orderByB'])
                ->get();
        }
        
        return Warehouse::selectRaw($query['select'])
                ->leftJoin("data_list", function($join) use ($query){
                    $join->on("warehouse_list.id","=","data_list.warehouse_id");
                    if(!empty($query['filterCustType']) && !empty($query['filterCustType'])){
                        $join->where("data_list.cust_type_id", $query['filterCustType']);
                    }
                    if(!empty($query['filterTanggalAwal']) && !empty($query['filterTanggalAkhir'])){
                        $join->whereBetween("data_list.created_at", [$query['filterTanggalAwal'],$query['filterTanggalAkhir']]);
                    }
                    $join->where(function($q){
                        $q->where("data_list.doku_link","!=","")
                        ->orWhere("data_list.bank_name","!=","");
                    });
                })
                ->groupBy('warehouse_list.id')
                ->when($query['filterWarehouse']=="" && !empty($query['filterTanggalAwal']) && !empty($query['filterTanggalAkhir']),
                    function($q){
                        $q->havingRaw("COUNT(DISTINCT data_list.mismass_invoice_id) != 0");
                    }
                )
                ->whereRaw($query['where'])
                ->orderBy($query['orderByA'], $query['orderByB'])
                ->get();
    }

    public function countFiltered(Request $request, $katakunci, $filter)
    {
        $wareModel = new Warehouse;
        $query = $wareModel->getDTQuery($request, $katakunci, $filter);
        return count(Warehouse::selectRaw($query['select'])
        ->leftJoin("data_list", function($join) use ($query){
            $join->on("warehouse_list.id","=","data_list.warehouse_id");
            if(!empty($query['filterCustType']) && !empty($query['filterCustType'])){
                $join->where("data_list.cust_type_id", $query['filterCustType']);
            }
            if(!empty($query['filterTanggalAwal']) && !empty($query['filterTanggalAkhir'])){
                $join->whereBetween("data_list.created_at", [$query['filterTanggalAwal'],$query['filterTanggalAkhir']]);
            }
            $join->where(function($q){
                $q->where("data_list.doku_link","!=","")
                ->orWhere("data_list.bank_name","!=","");
            });
        })
        ->groupBy('warehouse_list.id')
        ->when($query['filterWarehouse']=="" && !empty($query['filterTanggalAwal']) && !empty($query['filterTanggalAkhir']),
                    function($q){
                        $q->havingRaw("COUNT(DISTINCT data_list.mismass_invoice_id) != 0");
                    }
                )
        ->whereRaw($query['where'])
        ->get());
    }

    public function countAll()
    {
        return Warehouse::count();
    }
}
