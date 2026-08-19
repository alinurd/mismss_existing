<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Models\ApiJne;
use App\Models\ApiSentralCargo;
use App\Models\Endpoint;

class WebhookService
{
    protected $endpointModel,$controller;
    protected $order = ['created_at' => 'DESC'];
    protected $column_order = [
        'id',
        'created_at',
        'description'
    ];

    public function __construct()
    {
        $this->controller = new Controller;
    }

    public function getDT($array){
        $query = $this->getDTQuery($array);

        $vendorQueryArray = [
            "id" => $array["id"],
            "length" => $array["request"]->input('length'),
            "offset" => $array["request"]->input('start'),
            "limit" => $array["request"]->input('length'),
            "query" => $query
        ];
        $data = $this->vendorQuery($vendorQueryArray);

        $getTableArray = [
            "data" => $data,
            "no" => $array["no"],
            "vendor" => $array["id"]
        ];
        return $this->getTable($getTableArray);
    }

    public function getDataById($vendor,$id){
        if($vendor=="jne"){
            return ApiJne::where("id",$id)->get();
        }
        
        if($vendor=="sentral"){
            return ApiSentralCargo::where("id",$id)->get();
        }
    }

    public function countAll($id){
        if($id=="jne"){
            return ApiJne::count();
        }

        if($id=="sentral"){
            return ApiSentralCargo::count();
        }
    }

    public function countFiltered($array){
        $query = $this->getDTQuery($array);

        $vendorQueryArray = [
            "id" => $array["id"],
            "query" => $query
        ];

        return $this->vendorQueryCountFiltered($vendorQueryArray);
    }

    private function getDTQuery($array){
        $column_search = [
            "description"
        ];

        $select = "*";
        $search = $array["search"];
        // $filterTanggal = $filter[0]!=""?($filter[0][0] != "" || $filter[0][0] != null ? "AND data_list.created_at BETWEEN '" . date("Y-m-d", strtotime($filter[0][0])) . " 00:00:00' AND '" . date("Y-m-d", strtotime($filter[0][1])) . " 23:59:59'" : ""):"";
        // $filterWarehouse = $filter[1] != "" || $filter[1] != null ? "AND warehouse_list.id='".$filter[1]."'" : "" ;
        // $query = "warehouse_list.id!='' $filterWarehouse $filterTanggal AND doku_link!='' OR warehouse_list.id!='' $filterWarehouse $filterTanggal AND bank_name!=''";
        $query = "id!=''";
        $where = "";

        if (!empty($search)) {
            for ($i = 0; $i <= count($column_search) - 1; $i++) {
                if ($i < count($column_search) - 1) {
                    $where .= $query . "AND $column_search[$i] LIKE '%$search%' OR ";
                } else {
                    $where .= $query . "AND $column_search[$i] LIKE '%$search%'";
                }
            }
        } else {
            $where = $query;
        }

        if ($array["request"]->input('order')) {
            $orderByA = $this->column_order[$array["request"]->input('order')['0']['column']];
            $orderByB = $array["request"]->input('order')['0']['dir'];
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

    private function getTable($array){
        $data = [];
        $vendor = $array["vendor"];
        foreach ($array["data"] as $list) {
            $array["no"]++;
            $row = [];
            $row[] = $array["no"];
            $row[] = "<div>" . $this->controller->dateFormatIndo($list->created_at,2) . "</div>";
            $row[] = $this->controller->simpleDesc($list->description,$list->id);
            $row[] = "<a href='".url("/webhook/list/".$list->id)."?vendor=".$vendor."' target='_blank'>Lihat Detail</a>";

            $data[] = $row;
        }

        return $data;
    }

    private function vendorQuery($array){
        if($array["id"]=="jne"){
            if($array["length"]!=-1){
                return ApiJne::selectRaw($array["query"]["select"])
                ->whereRaw($array["query"]['where'])
                ->skip($array["offset"])
                ->take($array["limit"])
                ->orderBy($array["query"]['orderByA'], $array["query"]['orderByB'])
                ->get();
            }

            return ApiJne::selectRaw($array["query"]["select"])
                ->whereRaw($array["query"]['where'])
                ->orderBy($array["query"]['orderByA'], $array["query"]['orderByB'])
                ->get();
        }

        if($array["id"]=="sentral"){
            if($array["length"]!=-1){
                return ApiSentralCargo::selectRaw($array["query"]["select"])
                ->whereRaw($array["query"]['where'])
                ->skip($array["offset"])
                ->take($array["limit"])
                ->orderBy($array["query"]['orderByA'], $array["query"]['orderByB'])
                ->get();
            }

            return ApiSentralCargo::selectRaw($array["query"]["select"])
                ->whereRaw($array["query"]['where'])
                ->orderBy($array["query"]['orderByA'], $array["query"]['orderByB'])
                ->get();
        }
    }

    private function vendorQueryCountFiltered($array){

        if($array["id"]=="jne"){
            return count(ApiJne::selectRaw($array["query"]['select'])
                    ->whereRaw($array["query"]['where'])
                    ->get());
        }

        if($array["id"]=="sentral"){
            return count(ApiSentralCargo::selectRaw($array["query"]['select'])
                    ->whereRaw($array["query"]['where'])
                    ->get());
        }

    }
}