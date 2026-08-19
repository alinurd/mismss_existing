<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class Backup extends Model
{
    use HasFactory;

    private $controller;

    public function __construct()
    {
        $this->controller = new Controller;
    }

    public function exportPacker($request)
    {
        $packer = Auth::user()->username;
        $tipeCustomer = $request->input("tipecustomer");
        $tanggalAwal = $request->input("tanggalawal");
        $tanggalAkhir = $request->input("tanggalakhir");
        $filterTanggalAwal = date("Y-m-d 00:00:00",strtotime($tanggalAwal));
        $filterTanggalAkhir = date("Y-m-d 23:59:59",strtotime($tanggalAkhir));
        $tanggalTitle = $tanggalAwal==$tanggalAkhir?$this->controller->dateFormatIndo($tanggalAwal,1):$this->controller->dateFormatIndo($tanggalAwal,1)." - ".$this->controller->dateFormatIndo($tanggalAkhir,1);

        if($tipeCustomer=="IND"){
            $viewExport = "export.backupindbypacker";
            $where = "order_list.cust_type_id='IND' AND order_list.packing_created_by='$packer' AND order_list.to_sg_man_created_at BETWEEN '$filterTanggalAwal' AND '$filterTanggalAkhir'";
            
        }elseif($tipeCustomer=="COR"){
            $viewExport = "export.backupcorbypacker";
            $where = "order_list.cust_type_id='COR' AND order_list.packing_created_by='$packer' AND order_list.to_sg_man_created_at BETWEEN '$filterTanggalAwal' AND '$filterTanggalAkhir'";
            $data['list2'] = DB::table("order_list")
                ->whereRaw($where)
                ->orderBy("order_list.to_sg_man_created_at","asc")
                ->get();
        }

        $data['packer'] = $packer;
        $data['tanggalTitle'] = $tanggalTitle;

        $data['list'] = DB::table("order_list")
                        ->whereRaw($where)
                        ->orderBy("order_list.to_sg_man_created_at","asc")
                        ->get();

        $lst = DB::table("order_list")
                        ->whereRaw($where)
                        ->orderBy("order_list.to_sg_man_created_at","asc")
                        ->groupBy("order_list.invoice_id")
                        ->get();
        $dt = Backup::getTotal($lst,$where);

        $data['totalweight'] = $dt['totalWeight'];
        $data['totalcbm'] = $dt['totalCbm'];
        $data['totalkomisiberat'] = $dt['totalKomisiBerat'];
        $data['totalresi'] = $dt['totalResi'];

        return view($viewExport,$data);
    }

    private function getTotal($list,$where)
    {
        $weight = 0;
        $cbm = 0;
        $komisiBerat = 0;
        $resi = 0;
        foreach($list as $l)
        {
            $valueWeight = DB::table("data_list")
                    ->selectRaw("SUM(data_list.weight) as value")
                    ->whereRaw("mismass_invoice_id",$l->invoice_id)
                    ->value("value");
            $weight += $valueWeight;

            $valueCbm = DB::table("data_list")
                        ->selectRaw("SUM(data_list.cbm) as value")
                        ->whereRaw("mismass_invoice_id",$l->invoice_id)
                        ->value("value");
            $cbm += $valueCbm;

            $valueKomisiBerat = DB::table("data_list")
                                ->selectRaw("SUM(CASE WHEN weight<=3 THEN 750 WHEN weight>11 THEN 1500 ELSE 1000 END) as value")
                                ->whereRaw("mismass_invoice_id",$l->invoice_id)
                                ->value("value");
            $komisiBerat += $valueKomisiBerat;

            $valueResi = count(DB::table("data_list")
                        ->whereRaw("mismass_invoice_id",$l->invoice_id)
                        ->groupBy("data_list.shipping_number")
                        ->get());
            $resi += $valueResi;
        }

        $dt['totalWeight'] = $weight;
        $dt['totalCbm'] = $cbm;
        $dt['totalKomisiBerat'] = $komisiBerat;
        $dt['totalResi'] = $resi;

        return $dt;
    }
}
