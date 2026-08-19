<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Diskon extends Model
{
    use HasFactory;

    protected $table = 'data_list';
    protected $primarykey = 'id';
    public $incrementing = true;
    protected $keyType = 'string';
    public $timestamps = true;
    protected $guarded = [];
    public $test = "oke";

    protected $order = ['data_list.updated_at' => 'DESC'];
    protected $column_order = [
        'id',
        'mismass_invoice_id',
        'doku_invoice_id',
        'shipping_number',
        'created_at',
        'cons_first_name',
        'address',
        'address',
        'diskon',
    ];

    public function getDTQuery(Request $request, $katakunci = '', $filter)
    {

        $column_search = Diskon::columnSearch($filter[3]);
        $select = "data_list.*,sum(data_list.weight) as totalWeight,sum(data_list.item) as totalItem,sum(data_list.cbm) as totalCbm,sum(data_list.discount) as totalDisc,sum(data_list.sub_total) as totalPrice";

        $filterTanggal = $filter[0][0] != "" && $filter[0][0] != null ? "AND created_at BETWEEN '" . date("Y-m-d", strtotime($filter[0][0])) . " 00:00:00' AND '" . date("Y-m-d", strtotime($filter[0][1])) . " 23:59:59'" : "";
        $filterCustomer = $filter[1] != null && $filter[1] != "" ? "AND data_list.cust_id='" . $filter[1] . "'" : "";
        $query = $filter[2] == "IND" ? "data_list.cust_type_id='IND' $filterCustomer $filterTanggal AND data_list.discount!=0 " : "data_list.cust_type_id='COR' $filterCustomer $filterTanggal AND data_list.discount!=0 ";
        $groupBy = "data_list.mismass_invoice_id";
        if($filter[3]!=""){
            $query = "data_list.mismass_order_id='$filter[3]'";
            $groupBy = "data_list.id";
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
        $query = Diskon::getDTQuery($request, $katakunci, $filter);

        if ($request->input('length') != -1) {
            $offset = $request->input('start');
            $limit = $request->input('length');
            return Diskon::selectRaw($query['select'])
                // ->join("cust_type_list", "cust_type_list.id", "=", "data_list.cust_type_id")
                ->whereRaw($query['where'])
                ->groupBy($query['groupBy'])
                ->skip($offset)
                ->take($limit)
                ->orderBy($query['orderByA'], $query['orderByB'])
                ->get();
        }

        return Diskon::selectRaw($query['select'])
            // ->join("cust_type_list", "cust_type_list.id", "=", "data_list.cust_type_id")
            ->whereRaw($query['where'])
            ->groupBy($query['groupBy'])
            ->orderBy($query['orderByA'], $query['orderByB'])
            ->get();
    }

    public function countFiltered(Request $request, $katakunci, $filter)
    {
        $query = Diskon::getDTQuery($request, $katakunci, $filter);
        return count(Diskon::selectRaw($query['select'])
            // ->join("cust_type_list", "cust_type_list.id", "=", "data_list.cust_type_id")
            ->whereRaw($query['where'])
            ->groupBy($query['groupBy'])
            ->get());
    }

    public function countAll(Request $request, $katakunci, $filter)
    {
        $query = Diskon::getDTQuery($request, $katakunci, $filter);
        return Diskon::groupBy($query['groupBy'])->count();
    }

    public function columnSearch($mismassOrderId){

        if($mismassOrderId==""){
            $column_search = [
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
}
