<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class KomisiController extends Controller
{

    public static function getKomisiDriver($berat,$jenis){

        if($jenis=="IND"){
            if($berat==0){
                return 0;
            }
    
            if($berat<11){
                return 2000;
            }
    
            if($berat>21){
                return 7000;
            }
    
            return 5000;
        }

        if($berat==0){
            return 0;
        }

        if($berat<11){
            return 500;
        }

        if($berat>21){
            return 1000;
        }

        return 750;

    }

    public static function getKomisiPacker($berat){

        if($berat==0){
            return 0;
        }

        if($berat<=3){
            return 750;
        }

        if($berat>11){
            return 1500;
        }

        return 1000;

    }

    public static function totalKomisiBeratPackerByInvoice($invoice){
       return DB::table("data_list")
        ->selectRaw("SUM(CASE WHEN weight=0 THEN 0 WHEN weight<=3 THEN 750 WHEN weight>11 THEN 1500 ELSE 1000 END) as value")
        ->whereRaw("mismass_invoice_id='$invoice'")
        ->value("value");
    }

    public static function totalKomisiBeratDriverByInvoice($invoice,$jenis){
        $query = "SUM(CASE WHEN weight=0 THEN 0 WHEN weight<11 THEN 2000 WHEN weight>21 THEN 7000 ELSE 5000 END) as value";
        if($jenis=="COR"){
            $query = "SUM(CASE WHEN weight=0 THEN 0 WHEN weight<11 THEN 500 WHEN weight>21 THEN 1000 ELSE 750 END) as value";
        }

        return DB::table("data_list")
         ->selectRaw($query)
         ->whereRaw("mismass_invoice_id='$invoice'")
         ->value("value");
     }
}
