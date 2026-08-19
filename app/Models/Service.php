<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Service extends Model
{
    use HasFactory;

    protected $table = 'service_list';
    protected $primarykey = 'id';
    public $incrementing = true;
    public $timestamps = true;

    protected $order = ['updated_at' => 'DESC'];
    protected $column_order = [
        'id',
        'updated_at',
        'name',
        'id',
        'location',
        'pricekg',
        'priceitem',
        'pricevol',
        'pricecbm',
        'description',
        'id'
    ];

    public function getDTQuery(Request $request, $katakunci = '', $filter)
    {

        $column_search = [
            'service_list.id',
            'service_list.created_at',
            'service_list.created_by',
            'service_list.name',
            'service_list.warehouse_id',
            'warehouse_list.name',
            'warehouse_list.location',
            'service_list.pricekg',
            'service_list.priceitem',
            'service_list.pricevol',
            'service_list.pricecbm',
            'service_list.description',
        ];

        $select = [
            "service_list.*",
            "warehouse_list.country_id",
            "warehouse_list.name as warename",
            "warehouse_list.location"
        ];


        $filterTanggal = $filter[0]!=""?($filter[0][0] != "" || $filter[0][0] != null ? "AND service_list.created_at BETWEEN '" . date("Y-m-d", strtotime($filter[0][0])) . " 00:00:00' AND '" . date("Y-m-d", strtotime($filter[0][1])) . " 23:59:59'" : ""):"";
        $filterCountry = $filter[2] != "" || $filter[2] != null ? "AND warehouse_list.country_id='".$filter[2]."'" : "" ;
        $filterRoute = $filter[3] != "" || $filter[3] != null ? "AND warehouse_list.route_id='".$filter[3]."'" : "" ;
        $filterWarehouse = $filter[1] != "" || $filter[1] != null ? "AND service_list.warehouse_id='".$filter[1]."'" : "" ;
        $query = "service_list.id!='' $filterCountry $filterRoute $filterWarehouse $filterTanggal";
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
        $servModel = new Service;
        $query = $servModel->getDTQuery($request, $katakunci, $filter);
        if ($request->input('length') != -1) {
            $offset = $request->input('start');
            $limit = $request->input('length');
            return Service::select($query['select'])
                ->join("warehouse_list", "warehouse_list.id", "=", "service_list.warehouse_id")
                ->join("country_list", "country_list.id", "=", "warehouse_list.country_id")
                ->join("route_list", "route_list.id", "=", "warehouse_list.route_id")
                ->whereRaw($query['where'])
                ->skip($offset)
                ->take($limit)
                ->orderBy($query['orderByA'], $query['orderByB'])
                ->get();
        }
        return Service::select($query['select'])
            ->join("warehouse_list", "warehouse_list.id", "=", "service_list.warehouse_id")
            ->join("country_list", "country_list.id", "=", "warehouse_list.country_id")
            ->join("route_list", "route_list.id", "=", "warehouse_list.route_id")
            ->whereRaw($query['where'])
            ->orderBy($query['orderByA'], $query['orderByB'])
            ->get();
    }

    public function countFiltered(Request $request, $katakunci, $filter)
    {
        $servModel = new Service;
        $query = $servModel->getDTQuery($request, $katakunci, $filter);
        return count(Service::select($query['select'])
        ->join("warehouse_list", "warehouse_list.id", "=", "service_list.warehouse_id")
        ->join("country_list", "country_list.id", "=", "warehouse_list.country_id")
        ->join("route_list", "route_list.id", "=", "warehouse_list.route_id")
        ->whereRaw($query['where'])
        ->get());
    }

    public function countAll()
    {
        return Service::count();
    }
}
