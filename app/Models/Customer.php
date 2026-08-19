<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'cust_list';
    protected $primarykey = 'id';
    public $incrementing = true;
    protected $keyType = 'string';
    public $timestamps = true;
    protected $guarded = [];

    protected $order = ['cust_list.updated_at' => 'DESC'];
    protected $column_order = [
        'id',
        'updated_at',
        'first_name',
        'address',
        'email',
        'phone',
        'city',
        'prov',
        'id',
        'reference',
    ];

    public function getDTQuery(Request $request, $katakunci = '', $filter)
    {

        $column_search = [
            'cust_list.id',
            'cust_list.created_at',
            'cust_list.created_by',
            'cust_list.first_name',
            'cust_list.middle_name',
            'cust_list.last_name',
            'cust_list.address',
            'cust_list.sub_district',
            'cust_list.district',
            'cust_list.city',
            'cust_list.prov',
            'cust_list.postal_code',
            'cust_list.email',
            'cust_list.phone',
            'cust_list.reference',
            '(SELECT fullname FROM users WHERE users.id=cust_list.reference)',
            'CONCAT(cust_list.first_name," ",cust_list.middle_name)',
            'CONCAT(cust_list.first_name," ",cust_list.last_name)',
            'CONCAT(cust_list.first_name," ",cust_list.middle_name," ",cust_list.last_name)',
            'knowfrom_list.name',
            '(CASE WHEN cust_list.link_ref!=0 THEN "By Referral" ELSE "No Referral" END)',
            'CONCAT("ID",cust_list.id)'
        ];
        
        $select = "cust_list.*,country_phone_codes.code";
        $filterTanggal = $filter[0][0] != null || $filter[0][0] != "" ? "AND created_at BETWEEN '" . date("Y-m-d", strtotime($filter[0][0])) . " 00:00:00' AND '" . date("Y-m-d", strtotime($filter[0][1])) . " 23:59:59'" : "";
        $filterCust = $filter[1] == "IND" ? "cust_list.cust_type_id='IND' " : "cust_list.cust_type_id='COR' ";

        $query = $filterCust.$filterTanggal;
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
        $custModel = new Customer;
        $query = $custModel->getDTQuery($request, $katakunci, $filter);
        if ($request->input('length') != -1) {
            $offset = $request->input('start');
            $limit = $request->input('length');
            return Customer::selectRaw($query['select'])
                ->join('country_phone_codes','country_phone_codes.id','=','cust_list.country_id')
                ->join('knowfrom_list','knowfrom_list.id','=','cust_list.know_From_id')
                // ->join('data_list','order_list.id','=','data_list.mismass_order_id')
                // ->groupBy('order_list.cust_id')
                // ->groupBy('cust_list.id')
                ->whereRaw($query['where'])
                ->skip($offset)
                ->take($limit)
                ->orderBy($query['orderByA'], $query['orderByB'])
                ->get();
        }
        return Customer::selectRaw($query['select'])
                ->join('country_phone_codes','country_phone_codes.id','=','cust_list.country_id')
                ->join('knowfrom_list','knowfrom_list.id','=','cust_list.know_From_id')
            // ->join('order_list','cust_list.id','=','order_list.cust_id')
            // ->join('data_list','order_list.id','=','data_list.mismass_order_id')
            // ->groupBy('order_list.cust_id')
            // ->groupBy('cust_list.id')
            ->whereRaw($query['where'])
            ->orderBy($query['orderByA'], $query['orderByB'])
            ->get();
    }

    public function countFiltered(Request $request, $katakunci, $filter)
    {
        $custModel = new Customer;
        $query = $custModel->getDTQuery($request, $katakunci, $filter);
        return count(Customer::selectRaw($query['select'])
                ->join('country_phone_codes','country_phone_codes.id','=','cust_list.country_id')
                ->join('knowfrom_list','knowfrom_list.id','=','cust_list.know_From_id')
            // ->join('order_list','cust_list.id','=','order_list.cust_id')
            // ->join('data_list','order_list.id','=','data_list.mismass_order_id')
            // ->groupBy('cust_list.id')
            ->whereRaw($query['where'])
            ->get());
    }

    public function countAll()
    {
        return Customer::count();
    }
}
