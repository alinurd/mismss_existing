<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class Voids extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'void_list';
    protected $primarykey = 'id';
    public $incrementing = true;
    public $timestamps = true;

    protected $order = ['void_list.void_at' => 'DESC'];
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
            'CONCAT_WS(" ",void_list.sender_first_name,void_list.sender_middle_name,void_list.sender_last_name)',
            'CONCAT_WS(" ",void_list.sender_first_name,void_list.sender_last_name)',
            'CONCAT_WS(" ",void_list.cons_first_name,void_list.cons_middle_name,void_list.cons_last_name)',
            'CONCAT_WS(" ",void_list.cons_first_name,void_list.cons_last_name)',
            'CASE WHEN void_list.bank_name="" THEN "DOKU" ELSE "BANK" END',
            'void_list.created_at',
            'void_list.created_by',
            'void_list.void_at',
            'void_list.void_by',
            'void_list.note',
            'void_list.ms_track_id',
            'void_list.mismass_invoice_id',
            'void_list.mismass_invoice_date',
            'void_list.sender_first_name',
            'void_list.sender_middle_name',
            'void_list.sender_last_name',
            'void_list.sender_address',
            'void_list.sender_district',
            'void_list.sender_sub_district',
            'void_list.sender_city',
            'void_list.sender_prov',
            'void_list.sender_postal_code',
            'void_list.cons_first_name',
            'void_list.cons_middle_name',
            'void_list.cons_last_name',
            'void_list.cons_address',
            'void_list.cons_district',
            'void_list.cons_sub_district',
            'void_list.cons_city',
            'void_list.cons_prov',
            'void_list.cons_postal_code',
            'void_list.mode'
        ];

        $select = "void_list.ms_track_id,
        void_list.inv_add,
        SUM(sub_total) AS totalSubTotal,
        void_list.mismass_invoice_id,
        void_list.mismass_invoice_date,
        void_list.created_at,
        void_list.created_by,
        void_list.void_at,
        void_list.void_by,
        void_list.sender_first_name,
        void_list.sender_middle_name,
        void_list.sender_last_name,
        void_list.sender_phone,
        void_list.sender_city,
        void_list.sender_prov,
        void_list.sender_postal_code,
        void_list.cons_first_name,
        void_list.cons_middle_name,
        void_list.cons_last_name,
        void_list.cons_phone,
        void_list.cons_city,
        void_list.cons_prov,
        void_list.cons_postal_code,
        CASE WHEN void_list.bank_name='' THEN 'DOKU' ELSE 'BANK' END AS payment,
        void_list.note,
        void_list.mode";

        $query = Voids::filterAllInvoice($filter);
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
        $query = Voids::getDTQuery($request, $katakunci, $filter);
        if ($request->input('length') != -1) {
            $offset = $request->input('start');
            $limit = $request->input('length');
            return Voids::selectRaw($query['select'])
                ->whereRaw($query['where'])
                ->skip($offset)
                ->take($limit)
                ->groupBy('void_list.mismass_invoice_id')
                ->orderBy($query['orderByA'], $query['orderByB'])
                ->get();
        }
        return Voids::selectRaw($query['select'])
            ->whereRaw($query['where'])
            ->groupBy('void_list.mismass_invoice_id')
            ->orderBy($query['orderByA'], $query['orderByB'])
            ->get();
    }

    public function countFiltered(Request $request, $katakunci, $filter)
    {
        $data = Voids::getDTQuery($request, $katakunci, $filter);
        return count(Voids::whereRaw($data['where'])->groupBy("mismass_invoice_id")->get());
    }

    public function countAll()
    {
        return count(Voids::groupBy("mismass_invoice_id")->get());
    }

    public function getAllTotal(Request $request, $katakunci, $filter)
    {
        $data = Voids::getDTQuery($request, $katakunci, $filter);
        $get = Voids::selectRaw("SUM(sub_total) AS totalAll")->whereRaw($data['where'])->first();
        return $get->totalAll;
    }

    public function filterAllInvoice($filter){

        $filterType = $filter['custTypeId'] == "IND" ? "void_list.cust_type_id='IND' " : "void_list.cust_type_id='COR' ";
        $filterPayment = "";
        if($filter['filterPayment'] != null && $filter['filterPayment'] != ""){
            $filterPayment = " AND void_list.bank_name!=''";
            if($filter['filterPayment']=="DOKU"){
                $filterPayment = " AND void_list.bank_name=''";
            }
        }
        $filterTanggal = $filter['filterTanggal'][0] != null && $filter['filterTanggal'][0] != "" ? " AND void_list.void_at BETWEEN '" . date("Y-m-d", strtotime($filter['filterTanggal'][0])) . " 00:00:00' AND '" . date("Y-m-d", strtotime($filter['filterTanggal'][1])) . " 23:59:59'" : "";

        return $filterType.$filterPayment.$filterTanggal;

    }

    public function formatLinkTrackingMsTrack($msTrackId,$mode,$invAdd){
        $value = "";
        
        //jika tidak punya mstrackid maka return "-"
        if($msTrackId==""){
            if($invAdd){
                return "Additional";
            }
            return "Bulky";
        }

        if($mode=="INR"){
            $value = "<b>".$msTrackId."</b>";
            return $value;
        }

        $arr = explode(",",$msTrackId);
        for($i=0;$i<count($arr);$i++){
            if($arr[$i]!==" "){
                $value.="<a class='fw-bold loadTracking' href='".url('/shiptrip/tracking')."?id=".$arr[$i]."' target='_blank'>".$arr[$i]."</a>,";
            }
        }
        return $value;

    }

    public function insertDataVoid($array){

        $now = date("Y-m-d H:i:s");
        $getDataList = DB::table("data_list")
                        ->where("mismass_invoice_id",$array['invoiceId'])
                        ->get();

        $getDataOrder = DB::table("order_list")
                        ->select("ms_track_id")
                        ->where("invoice_id",$array['invoiceId'])
                        ->get();
        $msTrackId = "";
        foreach($getDataOrder as $go){
            if($go->ms_track_id!=""){
                $msTrackId.=$go->ms_track_id.", ";
            }
        }

        foreach($getDataList as $gd){
            $data = [
                "void_at" => $now,
                "void_by" => Auth::user()->username,
                "note" => $array['note'],
                "ms_track_id" => $msTrackId,
                "mode" => $array['mode'],

                "created_at" => $gd->created_at,
                "created_by" => $gd->created_by,
                "updated_at" => $gd->updated_at,
                "updated_by" => $gd->updated_by,

                "cust_id" => $gd->cust_id,
                "cust_type_id" => $gd->cust_type_id,
                "mismass_order_id" => $gd->mismass_order_id,
                "mismass_invoice_id" => $array['invoiceId'],
                "warehouse_id" => $gd->warehouse_id,
                "service_id" => $gd->service_id,

                "inv_add" => $gd->inv_add,
                "ind_to_cor" => $gd->ind_to_cor,

                "sender_first_name" => $gd->sender_first_name,
                "sender_middle_name" => $gd->sender_middle_name,
                "sender_last_name" => $gd->sender_last_name,
                "sender_email" => $gd->sender_email,
                "sender_phone" => $gd->sender_phone,
                "sender_address" => $gd->sender_address,
                "sender_sub_district" => $gd->sender_sub_district,
                "sender_district" => $gd->sender_district,
                "sender_city" => $gd->sender_city,
                "sender_prov" => $gd->sender_prov,
                "sender_postal_code" => $gd->sender_postal_code,

                "cons_first_name" => $gd->cons_first_name,
                "cons_middle_name" => $gd->cons_middle_name,
                "cons_last_name" => $gd->cons_last_name,
                "cons_email" => $gd->cons_email,
                "cons_phone" => $gd->cons_phone,
                "cons_address" => $gd->cons_address,
                "cons_sub_district" => $gd->cons_sub_district,
                "cons_district" => $gd->cons_district,
                "cons_city" => $gd->cons_city,
                "cons_prov" => $gd->cons_prov,
                "cons_postal_code" => $gd->cons_postal_code,
                
                "length" => $gd->length,
                "width" => $gd->width,
                "height" => $gd->height,
                "weight" => $gd->weight,
                "cbm" => $gd->cbm,
                "actual_weight" => $gd->actual_weight,
                "item" => $gd->item,
                "service_name" => $gd->service_name,
                "service_price_per" => $gd->service_price_per,
    
                "discount" => $gd->discount,
                "additional_desc" => $gd->additional_desc,
                "additional_nom" => $gd->additional_nom,
                "packing" => $gd->packing,
                "packing_per" => $gd->packing_per,
                "packing_total" => $gd->packing_total,
                "packing_desc" => $gd->packing_desc,
                "import_permit" => $gd->import_permit,
                "import_permit_per" => $gd->import_permit_per,
                "import_permit_total" => $gd->import_permit_total,
                "import_permit_desc" => $gd->import_permit_desc,
                "export_permit" => $gd->export_permit,
                "export_permit_per" => $gd->export_permit_per,
                "export_permit_total" => $gd->export_permit_total,
                "export_permit_desc" => $gd->export_permit_desc,
                "document" => $gd->document,
                "document_per" => $gd->document_per,
                "document_total" => $gd->document_total,
                "document_desc" => $gd->document_desc,
                "dr_medicine" => $gd->dr_medicine,
                "dr_medicine_per" => $gd->dr_medicine_per,
                "dr_medicine_total" => $gd->dr_medicine_total,
                "dr_medicine_desc" => $gd->dr_medicine_desc,
                "insurance_item_price" => $gd->insurance_item_price,
                "insurance_percent" => $gd->insurance_percent,
                "insurance_total" => $gd->insurance_total,
                "fee_item_price" => $gd->fee_item_price,
                "fee_percent" => $gd->fee_percent,
                "fee_total" => $gd->fee_total,
                "tax_item_price" => $gd->tax_item_price,
                "tax_percent" => $gd->tax_percent,
                "tax_total" => $gd->tax_total,
                "extra_cost_price" => $gd->extra_cost_price,
                "extra_cost_dest" => $gd->extra_cost_dest,
                "extra_cost_vendor_name" => $gd->extra_cost_vendor_name,
                "extra_cost_shipping_number" => $gd->extra_cost_shipping_number,
                "pickup_weight" => $gd->pickup_weight,
                "pickup_charge" => $gd->pickup_charge,
                "other_pickup_fee" => $gd->other_pickup_fee,
                "sub_total" => $gd->sub_total,

                "bank_name" => $gd->bank_name,
                "bank_account_name" => $gd->bank_account_name,
                "bank_account_id" => $gd->bank_account_id,

                "doku_token_id" => $gd->doku_token_id,
                "doku_expired_date" => $gd->doku_expired_date,
                "doku_link" => $gd->doku_link,
                "doku_invoice_id" => $gd->doku_invoice_id,

                "payment_status" => $gd->payment_status,
                "mismass_invoice_date" => $gd->mismass_invoice_date,
                "mismass_invoice_link" => $gd->mismass_invoice_link,
                "invoice_status" => $gd->invoice_status,
                "template_id" => $gd->template_id,
                "fc_symbol" => $gd->fc_symbol, 
                "fc_value" => $gd->fc_value, 

                ];
    
                $insert = DB::table("void_list")->insert($data);    
        }
        
    }

    public function voidTracking($array){
            $cekOrder = DB::table("order_list")->where("invoice_id",$array['invoiceId'])->get();
            foreach($cekOrder as $co){
                if($co->ms_track_id!=""){
                    if($array['mode']=="INR"){
                        Voids::deleteTrackingAll($co->ms_track_id);
                    }

                    if($array['mode']=="VI"){
                        Voids::deleteLastTracking($co->ms_track_id);
                    }
                }

                if($array['mode']=="INR"){
                    Voids::deleteOrder($array['invoiceId']);
                }

                if($array['mode']=="VI"){
                    Voids::emptyInvoice($array['invoiceId']);
                }


        }

    }

    private function deleteTrackingAll($trackId){
        if($trackId!=""){
            //hapus data di shiptrip_cust_list
            DB::table("shiptrip_cust_list")->where("id",$trackId)->delete();

            //hapus data di shiptrip_foreign_track_list
            DB::table("shiptrip_foreign_track_list")->where("ms_track_id",$trackId)->delete();

            //hapus data di shiptrip_image_list
            DB::table("shiptrip_image_list")->where("ms_track_id",$trackId)->delete();

            //hapus data di shiptrip_list
            DB::table("shiptrip_list")->where("ms_track_id",$trackId)->delete();

            //hapus data di shiptrip_track_list
            DB::table("shiptrip_track_list")->where("ms_track_id",$trackId)->delete();
        }
    }

    private function deleteLastTracking($trackId){
        if($trackId!=""){
            //hapus data di shiptrip_track_list yang memiliki track_status_id 10
            DB::table("shiptrip_track_list")
                ->where("ms_track_id",$trackId)
                ->where("track_status_id",10)
                ->delete();
        }
    }

    private function deleteOrder($invoiceId){
        if($invoiceId!=""){
            //hapus data di order_list yang memiliki invoice_id
            DB::table("order_list")->where("invoice_id",$invoiceId)->delete();
        }
    }

    private function emptyInvoice($invoiceId){
        if($invoiceId!=""){
            //update invoice_id dirubah menjadi null atau tanpa nilai
            DB::table("order_list")->where("invoice_id",$invoiceId)->update(["invoice_id"=>""]);
        }
    }
}
