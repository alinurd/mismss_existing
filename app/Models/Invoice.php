<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Invoice extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'data_list';
    protected $primarykey = 'id';
    public $incrementing = true;
    public $timestamps = true;

    protected $order = ['data_list.updated_at' => 'DESC'];
    protected $column_order = [
        'id',
        'mismass_invoice_id',
        'doku_invoice_id',
        'updated_at',
        'sender_first_name',
        'sender_address',
        'weight',
        'doku_link',
        'id'
    ];

    public function getDTQuery(Request $request, $katakunci = '', $filter)
    {

        $column_search = [
            '(SELECT order_list.ms_track_id FROM order_list WHERE order_list.invoice_id=data_list.mismass_invoice_id GROUP BY order_list.ms_track_id ORDER BY order_list.ms_track_id LIMIT 1)',
            '(CASE WHEN data_list.fc_symbol!="" THEN "SGD" ELSE 0 END)',
            '(CASE WHEN data_list.ms_track_id="" THEN "Bulky" ELSE 0 END)',
            'CONCAT_WS(" ",data_list.sender_first_name,data_list.sender_middle_name,data_list.sender_last_name)',
            'CONCAT_WS(" ",data_list.sender_first_name,data_list.sender_last_name)',
            'CONCAT_WS(" ",data_list.cons_first_name,data_list.cons_middle_name,data_list.cons_last_name)',
            'CONCAT_WS(" ",data_list.cons_first_name,data_list.cons_last_name)',
            'data_list.ms_track_id',
            'data_list.mismass_invoice_id',
            'data_list.mismass_invoice_date',
            'data_list.doku_invoice_id',
            'data_list.doku_link',
            'data_list.updated_at',
            'data_list.updated_by',
            'data_list.sender_first_name',
            'data_list.sender_middle_name',
            'data_list.sender_last_name',
            'data_list.sender_address',
            'data_list.sender_district',
            'data_list.sender_sub_district',
            'data_list.sender_city',
            'data_list.sender_prov',
            'data_list.sender_postal_code',
            'data_list.cons_first_name',
            'data_list.cons_middle_name',
            'data_list.cons_last_name',
            'data_list.cons_address',
            'data_list.cons_district',
            'data_list.cons_sub_district',
            'data_list.cons_city',
            'data_list.cons_prov',
            'data_list.cons_postal_code',
            'data_list.bank_name',
            'data_list.bank_account_name',
            'data_list.bank_account_id',
        ];

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

        $query = Invoice::filterAllInvoice($filter);
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
        $query = Invoice::getDTQuery($request, $katakunci, $filter);
        if ($request->input('length') != -1) {
            $offset = $request->input('start');
            $limit = $request->input('length');
            return Invoice::selectRaw($query['select'])
                ->join("cust_type_list", "cust_type_list.id", "=", "data_list.cust_type_id")
                ->join("order_list", "order_list.id", "=", "data_list.mismass_order_id")
                ->whereRaw($query['where'])
                ->skip($offset)
                ->take($limit)
                ->groupBy('data_list.mismass_invoice_id')
                ->orderBy($query['orderByA'], $query['orderByB'])
                ->get();
        }
        return Invoice::selectRaw($query['select'])
            ->join("cust_type_list", "cust_type_list.id", "=", "data_list.cust_type_id")
            ->join("order_list", "order_list.id", "=", "data_list.mismass_order_id")
            ->whereRaw($query['where'])
            ->groupBy('data_list.mismass_invoice_id')
            ->orderBy($query['orderByA'], $query['orderByB'])
            ->get();
    }

    public function countFiltered(Request $request, $katakunci, $filter)
    {
        // $query = Invoice::getDTQuery($request, $katakunci, $filter);
        // return count(Invoice::selectRaw($query['select'])
        //     ->join("cust_type_list", "cust_type_list.id", "=", "data_list.cust_type_id")
        //     ->whereRaw($query['where'])
        //     ->groupBy('data_list.mismass_invoice_id')
        //     ->get());
        $data = Invoice::getDTQuery($request, $katakunci, $filter);
        return count(Invoice::whereRaw($data['where'])->groupBy("mismass_invoice_id")->get());
    }

    public function countAll()
    {
        return count(Invoice::groupBy("mismass_invoice_id")->get());
    }

    public function filterAllInvoice($filter){

        $filterTanggal = $filter['filterTanggal'][0] != null && $filter['filterTanggal'][0] != "" ? "AND data_list.created_at BETWEEN '" . date("Y-m-d", strtotime($filter['filterTanggal'][0])) . " 00:00:00' AND '" . date("Y-m-d", strtotime($filter['filterTanggal'][1])) . " 23:59:59'" : "";
        $filterWarehouse = $filter['filterWarehouse'] != null && $filter['filterWarehouse'] != ""  ? "AND data_list.warehouse_id='" . $filter['filterWarehouse'] . "'" : "";
        $filterService = $filter['filterService'] != null && $filter['filterService'] != "" ? "AND data_list.service_id='" . $filter['filterService'] . "'" : "";
        $filterType = $filter['custTypeId'] == "IND" ? "data_list.cust_type_id='IND' AND data_list.shipping_number=''" : "data_list.cust_type_id='COR' AND data_list.shipping_number=''";

        $filterPayStatus = "";
        if($filter['filterPay'] == null || $filter['filterPay'] == ""){
            return $filterType." ".$filterTanggal." ".$filterWarehouse." ".$filterService." ".$filterPayStatus;
        }

        if($filter['filterPay']=="DOKU"){
            if($filter['filterPayStatus']=="PENDING"){
                $filterPayStatus = "AND doku_link!='' AND payment_status='PENDING'";
                return $filterType." ".$filterTanggal." ".$filterWarehouse." ".$filterService." ".$filterPayStatus;
            }

            if($filter['filterPayStatus']=="FAILED"){
                $filterPayStatus = "AND doku_link!='' AND payment_status='FAILED'";
                return $filterType." ".$filterTanggal." ".$filterWarehouse." ".$filterService." ".$filterPayStatus;
            }

            if($filter['filterPayStatus']=="SUCCESS"){
                $filterPayStatus = "AND doku_link!='' AND payment_status='SUCCESS'";
                return $filterType." ".$filterTanggal." ".$filterWarehouse." ".$filterService." ".$filterPayStatus;
            }
        }

        if($filter['filterPay']=="BANK"){

            if($filter['filterPayStatus']=="PENDING"){
                //$filterPayStatus = $filterType." ".$filterTanggal." ".$filterWarehouse." ".$filterService." AND bank_name!='' AND payment_status='PENDING' AND invoice_status='UNPAID' OR ".$filterType." ".$filterTanggal." ".$filterWarehouse." ".$filterService." AND bank_name!='' AND payment_status='' AND invoice_status='UNPAID'";
                $filterPayStatus = $filterType." ".$filterTanggal." ".$filterWarehouse." ".$filterService." AND bank_name!='' AND invoice_status='UNPAID'";
                return $filterPayStatus;
            }

            if($filter['filterPayStatus']=="SUCCESS"){
                //$filterPayStatus = $filterType." ".$filterTanggal." ".$filterWarehouse." ".$filterService." AND bank_name!='' AND payment_status='SUCCESS' AND invoice_status='PAID' AND forwarder_id='' OR ".$filterType." ".$filterTanggal." ".$filterWarehouse." ".$filterService." AND bank_name!='' AND payment_status='' AND invoice_status='PAID' AND forwarder_id=''";
                $filterPayStatus = $filterType." ".$filterTanggal." ".$filterWarehouse." ".$filterService." AND bank_name!='' AND invoice_status='PAID' AND forwarder_id=''";
                return $filterPayStatus;
            }
        }
    }

}
