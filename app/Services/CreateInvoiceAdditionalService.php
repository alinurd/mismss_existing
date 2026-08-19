<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CreateInvoiceAdditionalService
{
    protected $controller;

    public function __construct()
    {
        $this->controller = new Controller;
    }

    public function createInvoiceAddButton($array)
    {
        if(!Auth::user()->shiplist_buat_invoice_add){
            return "";
        }

        if($array['filterPayStatus']!="SUCCESS"){
            return "";
        }

        if($array['list']->inv_add){
            return "";
        }

        return "<a class='dropdown-item pointlink' id='createInvoiceAddBtn' data-create-inv-add='true' data-tglInv='".date("d-m-Y",strtotime($array['list']->mismass_invoice_date))."'".
                "data-id='" . $array['list']->mismass_order_id . "' data-mstracks='".$array['msTrackId']."'>Buat Invoice Additional</a>";
    }

    public function createInvoiceId($invoiceId){
        $num = 1;
        $status = true;
        while($status){
            $invoiceTest = $invoiceId."X".$num;
            $exist = DB::table("data_list")
                    ->where("mismass_invoice_id",$invoiceTest)
                    ->exists();
            if(!$exist){

                $exist2 = DB::table("void_list")
                    ->where("mismass_invoice_id",$invoiceTest)
                    ->exists();

                if(!$exist2){
                    $newInvoice = $invoiceTest;
                    $status = false;
                }
                
            }
            $num++;
        }

        return $newInvoice;
    }

    public function insertOrderData($array){
        $now = $array['now'];
        $mismassInvoiceId = $array['mismassInvoiceId'];
        $mismassOrderId = $array['mismassOrderId'];

        $getData = DB::table('order_list')
                    ->where('id',$mismassOrderId)
                    ->first();

        $values = array(
            "cust_id" => $getData->cust_id,
            "cust_type_id" => $getData->cust_type_id,
            "order_status_id" => "READY",
            "ms_track_id" => "",
            "invoice_id" => $mismassInvoiceId,
            "warehouse_id" => $getData->warehouse_id,
            "service_id" => $getData->service_id,
            "created_at" => $now,
            "created_by" => Auth::user()->username,
            "updated_at" => $now,
            "updated_by" => Auth::user()->username,
            "drop_created_at" => "0000-00-00 00:00:00",
            "drop_man_created_at" => "0000-00-00 00:00:00",
            "drop_created_by" => "0000-00-00 00:00:00",
            "drop_updated_at" => "0000-00-00 00:00:00",
            "drop_updated_by" => "0000-00-00 00:00:00",
            "to_sg_created_at" => "0000-00-00 00:00:00",
            "to_sg_man_created_at" => "0000-00-00 00:00:00",
            "to_sg_created_by" => "0000-00-00 00:00:00",
            "to_sg_updated_at" => "0000-00-00 00:00:00",
            "to_sg_updated_by" => "0000-00-00 00:00:00",
            "first_name" => $getData->first_name,
            "middle_name" => $getData->middle_name,
            "last_name" => $getData->last_name,
            "phone" => $getData->phone,
            "email" => $getData->email,
            "address" => $getData->address,
            "sub_district" => $getData->sub_district,
            "district" => $getData->district,
            "city" => $getData->city,
            "prov" => $getData->prov,
            "postal_code" => $getData->postal_code,
            "second_name" => $getData->second_name,
            "second_phone" => $getData->second_phone,
        );
        $insert = DB::table("order_list")->insertGetId($values);

        return $insert;
    }

}