<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Http\Controllers\KomisiController;

class Afunction extends Model
{
    use HasFactory;

    //################################################ < GET DATA BY INVOICE > ###################################################
    //Hitung Total Berat Pada Suatu Invoice
    public static function getBeratByInvoice($id){
        $totalBerat = DB::table('data_list')
                        ->selectRaw(
                            "(SELECT SUM(data_list.weight) FROM data_list WHERE data_list.mismass_invoice_id=order_list.invoice_id) as total"
                        )
                        ->join("order_list","order_list.id","=","data_list.mismass_order_id")
                        ->where("mismass_invoice_id",$id)
                        ->value("total");
        return $totalBerat;
    }

    //Hitung Total Berat Actual Pada Suatu Invoice
    public static function getBeratActualByInvoice($id){
        $totalBerat = DB::table('data_list')
                        ->selectRaw(
                            "(SELECT SUM(data_list.actual_weight) FROM data_list WHERE data_list.mismass_invoice_id=order_list.invoice_id) as total"
                        )
                        ->join("order_list","order_list.id","=","data_list.mismass_order_id")
                        ->where("mismass_invoice_id",$id)
                        ->value("total");
        return $totalBerat;
    }

    //Hitung Total Item Pada Suatu Invoice
    public static function getItemByInvoice($id){
        $totalItem = DB::table('data_list')
                        ->selectRaw(
                            "(SELECT SUM(data_list.item) FROM data_list WHERE data_list.mismass_invoice_id=order_list.invoice_id) as total"
                        )
                        ->join("order_list","order_list.id","=","data_list.mismass_order_id")
                        ->where("mismass_invoice_id",$id)
                        ->value("total");
        return $totalItem;
    }

    //Hitung Total CBM Pada Suatu Invoice
    public static function getCbmByInvoice($id){
        $totalCbm = DB::table('data_list')
                        ->selectRaw(
                            "(SELECT SUM(data_list.cbm) FROM data_list WHERE data_list.mismass_invoice_id=order_list.invoice_id) as total"
                        )
                        ->join("order_list","order_list.id","=","data_list.mismass_order_id")
                        ->where("mismass_invoice_id",$id)
                        ->value("total");
        return $totalCbm;
    }

    //Hitung Total Diskon Pada Suatu Invoice
    public static function getDiskonByInvoice($id){
        $totalDiskon = DB::table('data_list')
                        ->selectRaw(
                            "(SELECT SUM(data_list.discount) FROM data_list WHERE data_list.mismass_invoice_id=order_list.invoice_id) as total"
                        )
                        ->join("order_list","order_list.id","=","data_list.mismass_order_id")
                        ->where("mismass_invoice_id",$id)
                        ->value("total");
        return $totalDiskon;
    }

    //Hitung Total Sub Total Pada Suatu Invoice
    public static function getSubTotalByInvoice($id){
        $totalSubTotal = DB::table('data_list')
                        ->selectRaw(
                            "(SELECT SUM(data_list.sub_total) FROM data_list WHERE data_list.mismass_invoice_id=order_list.invoice_id) as total"
                        )
                        ->join("order_list","order_list.id","=","data_list.mismass_order_id")
                        ->where("mismass_invoice_id",$id)
                        ->value("total");
        return $totalSubTotal;
    }

    //Hitung Total Additional Pada Suatu Invoice
    public static function getAdditionalByInvoice($id){
        $total = DB::table('data_list')
                        ->selectRaw(
                            "(SELECT SUM(data_list.additional_nom) FROM data_list WHERE data_list.mismass_invoice_id=order_list.invoice_id) as additionalNom,
                            (SELECT SUM(data_list.packing_total) FROM data_list WHERE data_list.mismass_invoice_id=order_list.invoice_id) as packingTotal,
                            (SELECT SUM(data_list.import_permit_total) FROM data_list WHERE data_list.mismass_invoice_id=order_list.invoice_id) as importPermit,
                            (SELECT SUM(data_list.document_total) FROM data_list WHERE data_list.mismass_invoice_id=order_list.invoice_id) as documentTotal,
                            (SELECT SUM(data_list.dr_medicine_total) FROM data_list WHERE data_list.mismass_invoice_id=order_list.invoice_id) as medicineTotal,
                            (SELECT SUM(data_list.insurance_total) FROM data_list WHERE data_list.mismass_invoice_id=order_list.invoice_id) as insuranceTotal,
                            (SELECT SUM(data_list.fee_total) FROM data_list WHERE data_list.mismass_invoice_id=order_list.invoice_id) as feeTotal,
                            (SELECT SUM(data_list.tax_total) FROM data_list WHERE data_list.mismass_invoice_id=order_list.invoice_id) as taxTotal,
                            (SELECT SUM(data_list.extra_cost_price) FROM data_list WHERE data_list.mismass_invoice_id=order_list.invoice_id) as extraCost,
                            (SELECT SUM(data_list.pickup_charge) FROM data_list WHERE data_list.mismass_invoice_id=order_list.invoice_id) as pickupCharge"
                        )
                        ->join("order_list","order_list.id","=","data_list.mismass_order_id")
                        ->where("mismass_invoice_id",$id)
                        ->first();
        $totalAll = $total->additionalNom+$total->packingTotal+$total->importPermit+$total->documentTotal+$total->medicineTotal+$total->insuranceTotal+$total->feeTotal+$total->taxTotal+$total->extraCost+$total->pickupCharge;
        return $totalAll;
    }

    //Hitung Total Komisi Berat Packer Pada Suatu Invoice
    public static function getKomisiBeratPackerByInvoice($id){
        $totalKomisi = 0;
        $get= DB::table('data_list')
                        ->select("weight","packing_created_by")
                        ->where("mismass_invoice_id",$id)
                        ->get();
        foreach($get as $g){
            if($g->packing_created_by!=""){
                $totalKomisi += komisiController::getKomisiPacker($g->weight);
            }
        }
        return $totalKomisi;
    }

    //Hitung Total Komisi Berat Driver Pada Suatu Invoice
    public static function getKomisiBeratDriverByInvoice($id){
        $totalKomisi = 0;
        $get= DB::table('data_list')
                        ->select("weight","shipping_status","cust_type_id")
                        ->where("mismass_invoice_id",$id)
                        ->get();
        foreach($get as $g){
            if($g->shipping_status=="SUKSES"){
                $totalKomisi += KomisiController::getKomisiDriver($g->weight,$g->cust_type_id);
            }
        }
        return $totalKomisi;
    }

    public static function getPendapatanByInvoice($id){
        $total = 0;
        $get= DB::table('data_list')
                        ->select("sub_total")
                        ->where("mismass_invoice_id",$id)
                        ->get();
        foreach($get as $g){
            $total += $g->sub_total;
        }
        return $total;
    }

    public static function getTotalPaidByInvoice($id){
        $total= DB::table('data_list')
                        ->selectRaw("SUM(sub_total) as total")
                        ->whereRaw("mismass_invoice_id='$id' AND invoice_status='PAID'")
                        ->value("total");
        return $total;
    }

    public static function getTotalUnpaidByInvoice($id){
        $total= DB::table('data_list')
                        ->selectRaw("SUM(sub_total) as total")
                        ->whereRaw("mismass_invoice_id='$id' AND invoice_status='UNPAID'")
                        ->value("total");
        return $total;
    }

    public static function getTotalResiByInvoice($id){
        $total= DB::table('data_list')
                        ->selectRaw("COUNT(id) as total")
                        ->whereRaw("mismass_invoice_id='$id'")
                        ->value("total");
        return $total;
    }

    public static function getTotalServiceByInvoice($id){
        $total= DB::table('data_list')
                        ->selectRaw("COUNT(id) as total")
                        ->whereRaw("mismass_invoice_id='$id'")
                        ->value("total");
        return $total;
    }

    //################################################ </ GET DATA BY INVOICE > ###################################################

    //################################################ < GET DATA BY WAREHOUSE > ##################################################

    public static function getBeratByWare($id,$ft){
        $dateQuery = $ft[0]!=""||$ft[0]!=null?"AND mismass_invoice_date BETWEEN '" . date("Y-m-d", strtotime($ft[0])) . " 00:00:00' AND '" . date("Y-m-d", strtotime($ft[1])) . " 23:59:59'":"";
        $where1 = "warehouse_id='$id' $dateQuery AND doku_link!=''";
        $where2 = "warehouse_id='$id' $dateQuery AND bank_name!=''";
        $where = $where1." OR ".$where2;
        $total=DB::table("data_list")
            ->selectRaw("SUM(weight) as total")
            ->whereRaw($where)
            ->value("total");
        return round($total,2);
    }

    public static function getItemByWare($id,$ft){
        $dateQuery = $ft[0]!=""||$ft[0]!=null?"AND mismass_invoice_date BETWEEN '" . date("Y-m-d", strtotime($ft[0])) . " 00:00:00' AND '" . date("Y-m-d", strtotime($ft[1])) . " 23:59:59'":"";
        $where1 = "warehouse_id='$id' $dateQuery AND doku_link!=''";
        $where2 = "warehouse_id='$id' $dateQuery AND bank_name!=''";
        $where = $where1." OR ".$where2;
        $total=DB::table("data_list")
            ->selectRaw("SUM(item) as total")
            ->whereRaw($where)
            ->value("total");
        return round($total,2);
    }

    public static function getCbmByWare($id,$ft){
        $dateQuery = $ft[0]!=""||$ft[0]!=null?"AND mismass_invoice_date BETWEEN '" . date("Y-m-d", strtotime($ft[0])) . " 00:00:00' AND '" . date("Y-m-d", strtotime($ft[1])) . " 23:59:59'":"";
        $where1 = "warehouse_id='$id' $dateQuery AND doku_link!=''";
        $where2 = "warehouse_id='$id' $dateQuery AND bank_name!=''";
        $where = $where1." OR ".$where2;
        $total=DB::table("data_list")
            ->selectRaw("SUM(cbm) as total")
            ->whereRaw($where)
            ->value("total");
        return round($total,2);
    }

    public static function getInvoiceByWare($id,$ft){
        $dateQuery = $ft[0]!=""||$ft[0]!=null?"AND mismass_invoice_date BETWEEN '" . date("Y-m-d", strtotime($ft[0])) . " 00:00:00' AND '" . date("Y-m-d", strtotime($ft[1])) . " 23:59:59'":"";
        $where1 = "warehouse_id='$id' $dateQuery AND doku_link!=''";
        $where2 = "warehouse_id='$id' $dateQuery AND bank_name!=''";
        $where = $where1." OR ".$where2;
        $total=DB::table("data_list")
            ->select("mismass_invoice_id")
            ->whereRaw($where)
            ->groupBy("mismass_invoice_id")
            ->get();
        return count($total);
    }

    public static function getServiceByWare($id,$ft){
        $dateQuery = $ft[0]!=""||$ft[0]!=null?"AND mismass_invoice_date BETWEEN '" . date("Y-m-d", strtotime($ft[0])) . " 00:00:00' AND '" . date("Y-m-d", strtotime($ft[1])) . " 23:59:59'":"";
        $where1 = "warehouse_id='$id' $dateQuery AND doku_link!=''";
        $where2 = "warehouse_id='$id' $dateQuery AND bank_name!=''";
        $where = $where1." OR ".$where2;
        $total=DB::table("data_list")
            ->select("service_id")
            ->whereRaw($where)
            ->groupBy("service_id")
            ->get();
        return count($total);
    }

    public static function getCustByWare($id,$ft){
        $dateQuery = $ft[0]!=""||$ft[0]!=null?"AND mismass_invoice_date BETWEEN '" . date("Y-m-d", strtotime($ft[0])) . " 00:00:00' AND '" . date("Y-m-d", strtotime($ft[1])) . " 23:59:59'":"";
        $where1 = "warehouse_id='$id' $dateQuery AND doku_link!=''";
        $where2 = "warehouse_id='$id' $dateQuery AND bank_name!=''";
        $where = $where1." OR ".$where2;
        $total=DB::table("data_list")
            ->select("cust_id")
            ->whereRaw($where)
            ->groupBy("cust_id")
            ->get();
        return count($total);
    }

    public static function getPaidByWare($id,$ft){
        $dateQuery = $ft[0]!=""||$ft[0]!=null?"AND mismass_invoice_date BETWEEN '" . date("Y-m-d", strtotime($ft[0])) . " 00:00:00' AND '" . date("Y-m-d", strtotime($ft[1])) . " 23:59:59'":"";
        $where = "fc_symbol='' AND warehouse_id='$id' AND invoice_status='PAID' $dateQuery";
        $total=DB::table("data_list")
            ->selectRaw("SUM(sub_total) as total")
            ->whereRaw($where)
            ->value("total");
        return $total;
    }

    public static function getUnpaidByWare($id,$ft){
        $dateQuery = $ft[0]!=""||$ft[0]!=null?"AND mismass_invoice_date BETWEEN '" . date("Y-m-d", strtotime($ft[0])) . " 00:00:00' AND '" . date("Y-m-d", strtotime($ft[1])) . " 23:59:59'":"";
        $where1 = "fc_symbol='' AND warehouse_id='$id' AND invoice_status='UNPAID' $dateQuery AND doku_link!=''";
        $where2 = "fc_symbol='' AND warehouse_id='$id' AND invoice_status='UNPAID' $dateQuery AND bank_name!=''";
        $where = $where1." OR ".$where2;
        $total=DB::table("data_list")
            ->selectRaw("SUM(sub_total) as total")
            ->whereRaw($where)
            ->value("total");
        return $total;
    }

    public static function getDiskonByWare($id,$ft){
        $dateQuery = $ft[0]!=""||$ft[0]!=null?"AND mismass_invoice_date BETWEEN '" . date("Y-m-d", strtotime($ft[0])) . " 00:00:00' AND '" . date("Y-m-d", strtotime($ft[1])) . " 23:59:59'":"";
        $where1 = "fc_symbol='' AND warehouse_id='$id' $dateQuery AND doku_link!=''";
        $where2 = "fc_symbol='' AND warehouse_id='$id' $dateQuery AND bank_name!=''";
        $where = $where1." OR ".$where2;
        $total=DB::table("data_list")
            ->selectRaw("SUM(discount) as total")
            ->whereRaw($where)
            ->value("total");
        return $total;
    }

    public static function getPendapatanByWare($id,$ft){
        $dateQuery = $ft[0]!=""||$ft[0]!=null?"AND mismass_invoice_date BETWEEN '" . date("Y-m-d", strtotime($ft[0])) . " 00:00:00' AND '" . date("Y-m-d", strtotime($ft[1])) . " 23:59:59'":"";
        $where1 = "fc_symbol='' AND warehouse_id='$id' $dateQuery AND doku_link!=''";
        $where2 = "fc_symbol='' AND warehouse_id='$id' $dateQuery AND bank_name!=''";
        $where = $where1." OR ".$where2;
        $total=DB::table("data_list")
            ->selectRaw("SUM(sub_total) as total")
            ->whereRaw($where)
            ->value("total");
        return $total;
    }

    public static function getPaidByWareForeign($id,$ft){
        $dateQuery = $ft[0]!=""||$ft[0]!=null?"AND mismass_invoice_date BETWEEN '" . date("Y-m-d", strtotime($ft[0])) . " 00:00:00' AND '" . date("Y-m-d", strtotime($ft[1])) . " 23:59:59'":"";
        $where = "fc_symbol='SGD' AND warehouse_id='$id' AND invoice_status='PAID' $dateQuery";
        $total=DB::table("data_list")
            ->selectRaw("SUM(sub_total/fc_value) as total")
            ->whereRaw($where)
            ->value("total");
        return $total;
    }

    public static function getUnpaidByWareForeign($id,$ft){
        $dateQuery = $ft[0]!=""||$ft[0]!=null?"AND mismass_invoice_date BETWEEN '" . date("Y-m-d", strtotime($ft[0])) . " 00:00:00' AND '" . date("Y-m-d", strtotime($ft[1])) . " 23:59:59'":"";
        $where1 = "fc_symbol='SGD' AND warehouse_id='$id' AND invoice_status='UNPAID' $dateQuery AND doku_link!=''";
        $where2 = "fc_symbol='SGD' AND warehouse_id='$id' AND invoice_status='UNPAID' $dateQuery AND bank_name!=''";
        $where = $where1." OR ".$where2;
        $total=DB::table("data_list")
            ->selectRaw("SUM(sub_total/fc_value) as total")
            ->whereRaw($where)
            ->value("total");
        return $total;
    }

    public static function getDiskonByWareForeign($id,$ft){
        $dateQuery = $ft[0]!=""||$ft[0]!=null?"AND mismass_invoice_date BETWEEN '" . date("Y-m-d", strtotime($ft[0])) . " 00:00:00' AND '" . date("Y-m-d", strtotime($ft[1])) . " 23:59:59'":"";
        $where1 = "fc_symbol='SGD' AND warehouse_id='$id' $dateQuery AND doku_link!=''";
        $where2 = "fc_symbol='SGD' AND warehouse_id='$id' $dateQuery AND bank_name!=''";
        $where = $where1." OR ".$where2;
        $total=DB::table("data_list")
            ->selectRaw("SUM(discount/fc_value) as total")
            ->whereRaw($where)
            ->value("total");
        return $total;
    }

    public static function getPendapatanByWareForeign($id,$ft){
        $dateQuery = $ft[0]!=""||$ft[0]!=null?"AND mismass_invoice_date BETWEEN '" . date("Y-m-d", strtotime($ft[0])) . " 00:00:00' AND '" . date("Y-m-d", strtotime($ft[1])) . " 23:59:59'":"";
        $where1 = "fc_symbol='SGD' AND warehouse_id='$id' $dateQuery AND doku_link!=''";
        $where2 = "fc_symbol='SGD' AND warehouse_id='$id' $dateQuery AND bank_name!=''";
        $where = $where1." OR ".$where2;
        $total=DB::table("data_list")
            ->selectRaw("SUM(sub_total/fc_value) as total")
            ->whereRaw($where)
            ->value("total");
        return $total;
    }

    //################################################ </ GET DATA BY WAREHOUSE > ##################################################
    //################################################ < GET DATA BY CUSTOMER > ##################################################
    
    public static function getIncomeByCust($id,$ft){
        $dateQuery = $ft[0]!=""||$ft[0]!=null?"AND mismass_invoice_date BETWEEN '" . date("Y-m-d", strtotime($ft[0])) . " 00:00:00' AND '" . date("Y-m-d", strtotime($ft[1])) . " 23:59:59'":"";
        $where1 = "cust_id='$id' $dateQuery AND doku_link!=''";
        $where2 = "cust_id='$id' $dateQuery AND bank_name!=''";
        $where = $where1." OR ".$where2;
        $total=DB::table("data_list")
            ->selectRaw("SUM(sub_total) as total")
            ->whereRaw($where)
            ->value("total");
        return $total;
    }

    public static function getInvoiceByCust($id,$ft){
        $dateQuery = $ft[0]!=""||$ft[0]!=null?"AND mismass_invoice_date BETWEEN '" . date("Y-m-d", strtotime($ft[0])) . " 00:00:00' AND '" . date("Y-m-d", strtotime($ft[1])) . " 23:59:59'":"";
        $where1 = "cust_id='$id' $dateQuery AND doku_link!=''";
        $where2 = "cust_id='$id' $dateQuery AND bank_name!=''";
        $where = $where1." OR ".$where2;
        $total=DB::table("data_list")
            ->select("mismass_invoice_id")
            ->whereRaw($where)
            ->groupBy("mismass_invoice_id")
            ->get();
        return count($total);
    }

    public static function getShipNumByCust($id,$ft){
        $dateQuery = $ft[0]!=""||$ft[0]!=null?"AND mismass_invoice_date BETWEEN '" . date("Y-m-d", strtotime($ft[0])) . " 00:00:00' AND '" . date("Y-m-d", strtotime($ft[1])) . " 23:59:59'":"";
        $where1 = "cust_id='$id' $dateQuery AND doku_link!=''";
        $where2 = "cust_id='$id' $dateQuery AND bank_name!=''";
        $where = $where1." OR ".$where2;
        $total=DB::table("data_list")
            ->select("shipping_number")
            ->whereRaw($where)
            ->groupBy("shipping_number")
            ->get();
        return count($total);
    }

    public static function getWeightByCust($id,$ft){
        $dateQuery = $ft[0]!=""||$ft[0]!=null?"AND mismass_invoice_date BETWEEN '" . date("Y-m-d", strtotime($ft[0])) . " 00:00:00' AND '" . date("Y-m-d", strtotime($ft[1])) . " 23:59:59'":"";
        $where1 = "cust_id='$id' $dateQuery AND doku_link!=''";
        $where2 = "cust_id='$id' $dateQuery AND bank_name!=''";
        $where = $where1." OR ".$where2;
        $total=DB::table("data_list")
            ->selectRaw("SUM(weight) as total")
            ->whereRaw($where)
            ->value("total");
        return round($total,2);
    }

    public static function getItemByCust($id,$ft){
        $dateQuery = $ft[0]!=""||$ft[0]!=null?"AND mismass_invoice_date BETWEEN '" . date("Y-m-d", strtotime($ft[0])) . " 00:00:00' AND '" . date("Y-m-d", strtotime($ft[1])) . " 23:59:59'":"";
        $where1 = "cust_id='$id' $dateQuery AND doku_link!=''";
        $where2 = "cust_id='$id' $dateQuery AND bank_name!=''";
        $where = $where1." OR ".$where2;
        $total=DB::table("data_list")
            ->selectRaw("SUM(item) as total")
            ->whereRaw($where)
            ->value("total");
        return round($total,2);
    }

    public static function getCbmByCust($id,$ft){
        $dateQuery = $ft[0]!=""||$ft[0]!=null?"AND mismass_invoice_date BETWEEN '" . date("Y-m-d", strtotime($ft[0])) . " 00:00:00' AND '" . date("Y-m-d", strtotime($ft[1])) . " 23:59:59'":"";
        $where1 = "cust_id='$id' $dateQuery AND doku_link!=''";
        $where2 = "cust_id='$id' $dateQuery AND bank_name!=''";
        $where = $where1." OR ".$where2;
        $total=DB::table("data_list")
            ->selectRaw("SUM(cbm) as total")
            ->whereRaw($where)
            ->value("total");
        return round($total,2);
    }
    
    //################################################ </ GET DATA BY CUSTOMER > ##################################################

}
