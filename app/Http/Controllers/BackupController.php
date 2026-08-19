<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Warehouse;
use App\Models\Backup;

class BackupController extends Controller
{
    private $backupModel;

    public function __construct()
    {
        $this->backupModel = new Backup;
    }

    public function index()
    {
        if(!env('BACKUP_PAGE')){
            if(Auth::user()->username!="dev8th"){
                return view('pages.maintenance');
            }
        }
        $this->roleAccess();
        $wareModel = new Warehouse;
        $data['warehouse'] = $wareModel::all();
        $data['marketing'] = DB::table("users")->whereRaw("role_id='537469' OR role_id='518374'")->get(); 
        $data['packer'] = DB::table("users")->where("role_id","755387")->get();
        $data['driver'] = DB::table("users")->where("role_id","876384")->whereRaw("username!='MMDriver'")->get();
        return view("pages.backup", $data);
    }

    public function export(Request $request)
    {
        $tipeCustomer = $request->input("tipecustomer");
        $jenisExport = $request->input("jenisexport");
        $idWarehouse = $request->input("idwarehouse") ?? "";
        $corType = $request->input("cortype") ?? "";
        $idCustomer = $request->input("idcustomer") ?? "";
        $filterCustomer = $request->input("filtercustomer") ?? "";
        $reference = $request->input("reference") ?? "";
        $packer = $request->input("packer") ?? "";
        $driver = $request->input("driver") ?? "";
        $paymentStatus = $request->input("paymentStatus") ?? "";
        $tanggalAwal = $request->input("tanggalawal");
        $tanggalAkhir = $request->input("tanggalakhir");
        $filterTanggalAwal = date("Y-m-d 00:00:00",strtotime($tanggalAwal));
        $filterTanggalAkhir = date("Y-m-d 23:59:59",strtotime($tanggalAkhir));
        $tanggalTitle = $tanggalAwal==$tanggalAkhir?$this->dateFormatIndo($tanggalAwal,1):$this->dateFormatIndo($tanggalAwal,1)." - ".$this->dateFormatIndo($tanggalAkhir,1);

        if($tipeCustomer=="IND"){

            if($jenisExport=="BW"){
                
                $viewExport = "export.backupindbywh";
                $whereRaw1 = "data_list.warehouse_id='$idWarehouse' AND data_list.cust_type_id='IND' AND data_list.created_at BETWEEN '$filterTanggalAwal' AND '$filterTanggalAkhir' AND doku_link!=''";
                $whereRaw2 = "data_list.warehouse_id='$idWarehouse' AND data_list.cust_type_id='IND' AND data_list.created_at BETWEEN '$filterTanggalAwal' AND '$filterTanggalAkhir' AND bank_name!=''";
                $whereRaw = $whereRaw1." OR ".$whereRaw2;
                $orderBy = ["data_list.created_at","asc"];
                $groupBy = "data_list.mismass_invoice_id";
                $getWh = DB::table("warehouse_list")
                        ->where("id","=",$idWarehouse)
                        ->first();
                // $data['totalFix'] = DB::table('data_list')->selectRaw("SUM(sub_total) as value")->whereRaw($whereRaw)->value("value");
                // $data['totalDiskon'] = DB::table('data_list')->selectRaw("SUM(discount) as value")->whereRaw($whereRaw)->value("value");
                // $data['totalAll'] = $data['totalFix'] + $data['totalDiskon'];
                $data['title'] = "INDIVIDUAL SHIPMENT BY WAREHOUSE | ".$getWh->id." | ".$getWh->name." | ".$getWh->location; 
                $data['tanggalTitle'] = $tanggalTitle;
            
            }elseif($jenisExport=="BC"){

                $viewExport = "export.backupindbycust";
                $whereRaw1 = "data_list.cust_id='$idCustomer' AND data_list.cust_type_id='IND' AND data_list.created_at BETWEEN '$filterTanggalAwal' AND '$filterTanggalAkhir' AND doku_link!=''";
                $whereRaw2 = "data_list.cust_id='$idCustomer' AND data_list.cust_type_id='IND' AND data_list.created_at BETWEEN '$filterTanggalAwal' AND '$filterTanggalAkhir' AND bank_name!=''";
                $whereRaw= $whereRaw1." OR ".$whereRaw2;
                $orderBy = ["data_list.created_at","asc"];
                $groupBy = "data_list.mismass_invoice_id";
                $getCust = DB::table("cust_list")
                            ->where("id","=","$idCustomer")
                            ->first();
                $data['title'] = "INDIVIDUAL SHIPMENT BY CUSTOMER | ".$getCust->first_name." ".$getCust->middle_name." ".$getCust->last_name;
                $data['tanggalTitle'] = $tanggalTitle;
                $data['totalFix'] = DB::table('data_list')->selectRaw("SUM(sub_total) as value")->whereRaw($whereRaw)->value("value");
                $data['totalDiskon'] = DB::table('data_list')->selectRaw("SUM(discount) as value")->whereRaw($whereRaw)->value("value");
                $data['totalAll'] = $data['totalFix'] + $data['totalDiskon'];
                $data['reference'] = DB::table('users')->where("id",$getCust->reference)->value("fullname");
            }

            if($jenisExport=="BR"){
                $viewExport = "export.backupindbyref";
                $payStatusWhere = $paymentStatus=="ALL"? "" : "data_list.invoice_status='$paymentStatus' AND";
                // $filterCustWhere = $filterCustomer=="ALL"? "" : ($filterCustomer=="ADM" ? "order_list.created_by!='WEBFORM' AND" : "order_list.created_by='WEBFORM' AND");
                $filterCustWhere = "";
                $where1 = "cust_list.reference='$reference' AND $payStatusWhere $filterCustWhere data_list.cust_type_id='IND' AND data_list.created_at BETWEEN '$filterTanggalAwal' AND '$filterTanggalAkhir' AND doku_link!=''";
                $where2 = "cust_list.reference='$reference' AND $payStatusWhere $filterCustWhere data_list.cust_type_id='IND' AND data_list.created_at BETWEEN '$filterTanggalAwal' AND '$filterTanggalAkhir' AND bank_name!=''";
                $where = $where1." OR ".$where2;
                
                $data['username'] = DB::table("users")->where("id",$reference)->value("username");
                $data['fullname'] = DB::table("users")->where("id",$reference)->value("fullname");
                $data['totalweight'] = DB::table("data_list")
                                        ->selectRaw("SUM(data_list.weight) as value")
                                        ->join("cust_list","cust_list.id","=","data_list.cust_id")
                                        ->join("order_list","order_list.id","=","data_list.mismass_order_id")
                                        ->whereRaw($where)
                                        ->value("value");
                $data['totalcbm'] = DB::table("data_list")
                                        ->selectRaw("SUM(data_list.cbm) as value")
                                        ->join("cust_list","cust_list.id","=","data_list.cust_id")
                                        ->join("order_list","order_list.id","=","data_list.mismass_order_id")
                                        ->whereRaw($where)
                                        ->value("value");
                $data['totalcustomer'] = count(DB::table("data_list")
                                        ->selectRaw("data_list.cons_first_name")
                                        ->join("cust_list","cust_list.id","=","data_list.cust_id")
                                        ->join("order_list","order_list.id","=","data_list.mismass_order_id")
                                        ->whereRaw($where)
                                        ->groupBy("data_list.cust_id")
                                        ->get());
                $data['totalshipment'] = count(DB::table("data_list")
                                        ->selectRaw("data_list.cons_first_name")
                                        ->join("cust_list","cust_list.id","=","data_list.cust_id")
                                        ->join("order_list","order_list.id","=","data_list.mismass_order_id")
                                        ->whereRaw($where)
                                        ->groupBy("data_list.mismass_invoice_id")
                                        ->get());
                $data['tanggalTitle'] = $tanggalTitle;
                $data['listcustomer'] = DB::table("data_list")
                                        ->selectRaw("(SELECT COUNT(data_list.cust_id) FROM data_list WHERE data_list.cust_id=cust_list.id) as jumlahkirim")
                                        ->join("cust_list","cust_list.id","=","data_list.cust_id")
                                        ->join("order_list","order_list.id","=","data_list.mismass_order_id")
                                        ->whereRaw($where)
                                        ->groupBy("data_list.cust_id")
                                        ->get();
                $data['list'] = DB::table("data_list")
                ->selectRaw(
                    "data_list.*,
                    cust_list.id as custid,
                    cust_list.reference,
                    order_list.created_by as ordercreatedby,
                    order_list.created_at as ordercreatedat,
                    (SELECT COUNT(data_list.cust_id) FROM data_list WHERE data_list.cust_id=cust_list.id) as jumlahkirim,
                    (SELECT SUM(data_list.weight) FROM data_list WHERE data_list.mismass_invoice_id=order_list.invoice_id) as jumlahberat,
                    (SELECT SUM(data_list.item) FROM data_list WHERE data_list.mismass_invoice_id=order_list.invoice_id) as jumlahitem,
                    (SELECT SUM(data_list.cbm) FROM data_list WHERE data_list.mismass_invoice_id=order_list.invoice_id) as jumlahcbm,
                    knowfrom_list.name as knowname",
                    )
                ->join("cust_list","cust_list.id","=","data_list.cust_id")
                ->join("knowfrom_list","knowfrom_list.id","=","cust_list.know_from_id")
                ->join("order_list","order_list.id","=","data_list.mismass_order_id")
                ->whereRaw($where)
                ->orderBy("data_list.created_at","asc")
                ->groupBy("data_list.mismass_invoice_id")
                ->get();
            }elseif($jenisExport=="BP"){
                // $viewExport = "export.backupindbypacker";
                // $where = "data_list.cust_type_id='IND' AND data_list.packing_created_by='$packer' AND data_list.created_at BETWEEN '$filterTanggalAwal' AND '$filterTanggalAkhir'";
                // $data['packer'] = $packer;
                // $data['totalweight'] = DB::table("data_list")
                //                         ->selectRaw("SUM(data_list.weight) as value")
                //                         ->whereRaw($where)
                //                         ->value("value");
                // $data['totalcbm'] = DB::table("data_list")
                //                         ->selectRaw("SUM(data_list.cbm) as value")
                //                         ->whereRaw($where)
                //                         ->value("value");
                // $data['totalkomisiberat'] = DB::table("data_list")
                //                         ->selectRaw("SUM(CASE WHEN weight=0 THEN 0 WHEN weight<=3 THEN 750 WHEN weight>11 THEN 1500 ELSE 1000 END) as value")
                //                         ->whereRaw($where)
                //                         ->value("value");
                // $data['totalresi'] = count(DB::table("data_list")
                //                         ->whereRaw($where)
                //                         ->groupBy("data_list.shipping_number")
                //                         ->get());
                // $data['tanggalTitle'] = $tanggalTitle;
                // $data['list'] = DB::table("data_list")
                // ->whereRaw($where)
                // ->orderBy("data_list.created_at","asc")
                // ->groupBy("data_list.mismass_invoice_id")
                // ->get();
                $this->backupModel->exportPacker($request);
                return true;
            }elseif($jenisExport=="BD"){
                $viewExport = "export.backupindbydriver";
                $where = "data_list.cust_type_id='IND' AND data_list.shipping_updated_by='$driver' AND data_list.created_at BETWEEN '$filterTanggalAwal' AND '$filterTanggalAkhir'";
                $data['driver'] = $driver;
                $data['totalweight'] = DB::table("data_list")
                                        ->selectRaw("SUM(data_list.weight) as value")
                                        ->whereRaw($where)
                                        ->value("value");
                $data['totalcbm'] = DB::table("data_list")
                                        ->selectRaw("SUM(data_list.cbm) as value")
                                        ->whereRaw($where)
                                        ->value("value");
                $data['totalkomisiberat'] = DB::table("data_list")
                                        ->selectRaw("SUM(CASE WHEN weight=0 THEN 0 WHEN weight<11 THEN 2000 WHEN weight>21 THEN 7000 ELSE 5000 END) as value")
                                        ->whereRaw($where."AND data_list.shipping_status='SUKSES'")
                                        ->value("value");
                $data['totalresi'] = count(DB::table("data_list")
                                        ->whereRaw($where)
                                        ->groupBy("data_list.shipping_number")
                                        ->get());
                $data['tanggalTitle'] = $tanggalTitle;
                $data['list'] = DB::table("data_list")
                ->selectRaw(
                    "data_list.*,
                    (SELECT SUM(data_list.weight) FROM data_list WHERE data_list.mismass_invoice_id=order_list.invoice_id) as jumlahberat,
                    (SELECT SUM(data_list.item) FROM data_list WHERE data_list.mismass_invoice_id=order_list.invoice_id) as jumlahitem,
                    (SELECT SUM(data_list.cbm) FROM data_list WHERE data_list.mismass_invoice_id=order_list.invoice_id) as jumlahcbm"
                )
                ->join("order_list","order_list.id","=","data_list.mismass_order_id")
                ->whereRaw($where)
                ->orderBy("data_list.created_at","asc")
                ->groupBy("data_list.mismass_invoice_id")
                ->get();
            }else{
                $data['list'] = DB::table("data_list")
                ->selectRaw(
                    "data_list.*,
                    warehouse_list.id as wareid,
                    warehouse_list.name as warename,
                    warehouse_list.location as wareloc,
                    cust_list.reference,
                    (SELECT COUNT(data_list.cust_id) FROM data_list WHERE data_list.cust_id=cust_list.id) as jumlahkirim"
                    )
                ->join("warehouse_list","warehouse_list.id","=","data_list.warehouse_id")
                ->join("cust_list","cust_list.id","=","data_list.cust_id")
                ->whereRaw($whereRaw)
                ->orderBy($orderBy[0],$orderBy[1])
                ->groupBy($groupBy)
                ->get();
            }

        }elseif($tipeCustomer=="COR"){
            
            if($jenisExport=="BW"){
            
                $viewExport = "export.backupcorbywh";
                $whereRaw1 = "data_list.warehouse_id='$idWarehouse' AND data_list.cust_type_id='COR' AND data_list.created_at BETWEEN '$filterTanggalAwal' AND '$filterTanggalAkhir' AND doku_link!=''";
                $whereRaw2 = "data_list.warehouse_id='$idWarehouse' AND data_list.cust_type_id='COR' AND data_list.created_at BETWEEN '$filterTanggalAwal' AND '$filterTanggalAkhir' AND bank_name!=''";
                $whereRaw = $whereRaw1." OR ".$whereRaw2;
                $orderBy = ["data_list.created_at","asc"];
                $groupBy = "data_list.mismass_invoice_id";
                $getWh = DB::table("warehouse_list")
                        ->where("id","=",$idWarehouse)
                        ->first();
                $data['title'] = "CORPORATE SHIPMENT BY WAREHOUSE | ".$getWh->id." | ".$getWh->name." | ".$getWh->location;
                // $data['totalFix'] = DB::table('data_list')->selectRaw("SUM(sub_total) as value")->whereRaw($whereRaw)->value("value");
                // $data['totalDiskon'] = DB::table('data_list')->selectRaw("SUM(discount) as value")->whereRaw($whereRaw)->value("value");
                // $data['totalAll'] = $data['totalFix'] + $data['totalDiskon'];
                $data['list2'] = DB::table("data_list")
                                    ->select("*")
                                    ->whereRaw($whereRaw)
                                    ->orderBy($orderBy[0],$orderBy[1])
                                    ->get();
                $data['tanggalTitle'] = $tanggalTitle;
            
            }elseif($jenisExport=="BC"){
            
                $viewExport = "export.backupcorbycust";
                $whereRaw1 = "data_list.cust_id='$idCustomer' AND data_list.cust_type_id='COR' AND data_list.created_at BETWEEN '$filterTanggalAwal' AND '$filterTanggalAkhir' AND doku_link!=''";
                $whereRaw2 = "data_list.cust_id='$idCustomer' AND data_list.cust_type_id='COR' AND data_list.created_at BETWEEN '$filterTanggalAwal' AND '$filterTanggalAkhir' AND bank_name!=''";
                $whereRaw= $whereRaw1." OR ".$whereRaw2;
                $orderBy = ["data_list.created_at","asc"];
                $groupBy = "data_list.mismass_invoice_id";
                $getCust = DB::table("cust_list")
                            ->where("id","=","$idCustomer")
                            ->first();
                $data['corType'] = $corType;
                $data['title'] = "CORPORATE SHIPMENT BY CLIENT | ".$getCust->first_name." ".$getCust->middle_name." ".$getCust->last_name;
                $data['tanggalTitle'] = $tanggalTitle;
                $data['totalFix'] = DB::table('data_list')->selectRaw("SUM(sub_total) as value")->whereRaw($whereRaw)->value("value");
                $data['totalDiskon'] = DB::table('data_list')->selectRaw("SUM(discount) as value")->whereRaw($whereRaw)->value("value");
                $data['totalAll'] = $data['totalFix'] + $data['totalDiskon'];
                $data['reference'] = DB::table('users')->where("id",$getCust->reference)->value("fullname");
                $data['list2'] = DB::table("data_list")
                                    ->select(
                                        "data_list.*",
                                        "warehouse_list.id as wareid",
                                        "warehouse_list.name as warename",
                                        "warehouse_list.location as wareloc"
                                        )
                                    ->join("warehouse_list","warehouse_list.id","=","data_list.warehouse_id")
                                    ->whereRaw($whereRaw)
                                    ->orderBy($orderBy[0],$orderBy[1])
                                    ->get();
            }

            if($jenisExport=="BR"){
                $viewExport = "export.backupcorbyref";
                $payStatusWhere = $paymentStatus=="ALL"? "" : "data_list.invoice_status='$paymentStatus' AND";
                $filterCustWhere = "";
                // $filterCustWhere = $filterCustomer=="ALL"? "" : ($filterCustomer=="ADM" ? "order_list.created_by!='WEBFORM' AND" : "order_list.created_by='WEBFORM' AND");
                
                $where1 = "cust_list.reference='$reference' AND $payStatusWhere $filterCustWhere data_list.cust_type_id='COR' AND data_list.created_at BETWEEN '$filterTanggalAwal' AND '$filterTanggalAkhir' AND doku_link!=''";
                $where2 = "cust_list.reference='$reference' AND $payStatusWhere $filterCustWhere data_list.cust_type_id='COR' AND data_list.created_at BETWEEN '$filterTanggalAwal' AND '$filterTanggalAkhir' AND bank_name!=''";
                $where = $where1." OR ".$where2;

                $data['corType'] = $corType;
                $data['username'] = DB::table("users")->where("id",$reference)->value("username");
                $data['fullname'] = DB::table("users")->where("id",$reference)->value("fullname");
                $data['totalweight'] = DB::table("data_list")
                                        ->selectRaw("SUM(data_list.weight) as value")
                                        ->join("cust_list","cust_list.id","=","data_list.cust_id")
                                        ->join("order_list","order_list.id","=","data_list.mismass_order_id")
                                        ->whereRaw($where)
                                        ->value("value");
                $data['totalcbm'] = DB::table("data_list")
                                        ->selectRaw("SUM(data_list.cbm) as value")
                                        ->join("cust_list","cust_list.id","=","data_list.cust_id")
                                        ->join("order_list","order_list.id","=","data_list.mismass_order_id")
                                        ->whereRaw($where)
                                        ->value("value");
                $data['totalcustomer'] = count(DB::table("data_list")
                                        ->selectRaw("data_list.cons_first_name")
                                        ->join("cust_list","cust_list.id","=","data_list.cust_id")
                                        ->join("order_list","order_list.id","=","data_list.mismass_order_id")
                                        ->whereRaw($where)
                                        ->groupBy("data_list.cust_id")
                                        ->get());
                $data['totalshipment'] = count(DB::table("data_list")
                                        ->selectRaw("data_list.cons_first_name")
                                        ->join("cust_list","cust_list.id","=","data_list.cust_id")
                                        ->join("order_list","order_list.id","=","data_list.mismass_order_id")
                                        ->whereRaw($where)
                                        ->groupBy("data_list.mismass_invoice_id")
                                        ->get());
                $data['listcustomer'] = DB::table("data_list")
                                        ->selectRaw("(SELECT COUNT(data_list.cust_id) FROM data_list WHERE data_list.cust_id=cust_list.id) as jumlahkirim")
                                        ->join("cust_list","cust_list.id","=","data_list.cust_id")
                                        ->join("order_list","order_list.id","=","data_list.mismass_order_id")
                                        ->whereRaw($where)
                                        ->groupBy("data_list.cust_id")
                                        ->get();
                $data['tanggalTitle'] = $tanggalTitle;
                $data['list'] = DB::table("data_list")
                ->selectRaw(
                    "data_list.*,
                    cust_list.id as custid,
                    cust_list.reference,
                    order_list.created_by as ordercreatedby,
                    order_list.created_at as ordercreatedat,
                    (SELECT COUNT(data_list.cust_id) FROM data_list WHERE data_list.cust_id=cust_list.id) as jumlahkirim,
                    knowfrom_list.name as knowname",
                    )
                ->join("cust_list","cust_list.id","=","data_list.cust_id")
                ->join("knowfrom_list","knowfrom_list.id","=","cust_list.know_from_id")
                ->join("order_list","order_list.id","=","data_list.mismass_order_id")
                ->whereRaw($where)
                ->orderBy("data_list.created_at","asc")
                ->groupBy("data_list.mismass_invoice_id")
                ->get();

                $data['list2'] = DB::table("data_list")
                ->selectRaw(
                    "data_list.*,
                    cust_list.id as custid,
                    cust_list.reference,
                    order_list.created_by as ordercreatedby,
                    order_list.created_at as ordercreatedat,
                    (SELECT COUNT(data_list.cust_id) FROM data_list WHERE data_list.cust_id=cust_list.id) as jumlahkirim,
                    knowfrom_list.name as knowname",
                    )
                ->join("cust_list","cust_list.id","=","data_list.cust_id")
                ->join("knowfrom_list","knowfrom_list.id","=","cust_list.know_from_id")
                ->join("order_list","order_list.id","=","data_list.mismass_order_id")
                ->whereRaw($where)
                ->orderBy("data_list.created_at","asc")
                ->get();

                // dd($data['list']);
            }elseif($jenisExport=="BP"){
                // $viewExport = "export.backupcorbypacker";
                // $where = "data_list.cust_type_id='COR' AND data_list.packing_created_by='$packer' AND data_list.created_at BETWEEN '$filterTanggalAwal' AND '$filterTanggalAkhir'";
                // $data['packer'] = $packer;
                // $data['totalweight'] = DB::table("data_list")
                //                         ->selectRaw("SUM(data_list.weight) as value")
                //                         ->whereRaw($where)
                //                         ->value("value");
                // $data['totalcbm'] = DB::table("data_list")
                //                         ->selectRaw("SUM(data_list.cbm) as value")
                //                         ->whereRaw($where)
                //                         ->value("value");
                // $data['totalkomisiberat'] = DB::table("data_list")
                //                         ->selectRaw("SUM(CASE WHEN weight=0 THEN 0 WHEN weight<=3 THEN 750 WHEN weight>11 THEN 1500 ELSE 1000 END) as value")
                //                         ->whereRaw($where)
                //                         ->value("value");
                // $data['totalresi'] = count(DB::table("data_list")
                //                         ->whereRaw($where)
                //                         ->groupBy("data_list.shipping_number")
                //                         ->get());
                // $data['tanggalTitle'] = $tanggalTitle;
                // $data['list'] = DB::table("data_list")
                //                 ->whereRaw($where)
                //                 ->orderBy("data_list.created_at","asc")
                //                 ->groupBy("data_list.mismass_invoice_id")
                //                 ->get();
                // $data['list2'] = DB::table("data_list")
                //                 ->select("*")
                //                 ->whereRaw($where)
                //                 ->orderBy("data_list.created_at","asc")
                //                 ->groupBy("data_list.id")
                //                 ->get();
                $this->backupModel->exportPacker($request);
                return true;
            }elseif($jenisExport=="BD"){
                $viewExport = "export.backupcorbydriver";
                $where = "data_list.cust_type_id='COR' AND data_list.forwarder_name='$driver' AND data_list.created_at BETWEEN '$filterTanggalAwal' AND '$filterTanggalAkhir'";
                $data['driver'] = $driver;
                $data['totalweight'] = DB::table("data_list")
                                        ->selectRaw("SUM(data_list.weight) as value")
                                        ->whereRaw($where)
                                        ->value("value");
                $data['totalcbm'] = DB::table("data_list")
                                        ->selectRaw("SUM(data_list.cbm) as value")
                                        ->whereRaw($where)
                                        ->value("value");
                $data['totalkomisiberat'] = DB::table("data_list")
                                        ->selectRaw("SUM(CASE WHEN weight=0 THEN 0 WHEN weight<11 THEN 500 WHEN weight>21 THEN 1000 ELSE 750 END) as value")
                                        ->whereRaw($where)
                                        ->value("value");
                $data['totalresi'] = count(DB::table("data_list")
                                        ->whereRaw($where)
                                        ->groupBy("data_list.shipping_number")
                                        ->get());

                $data['corType'] = $corType;
                $data['tanggalTitle'] = $tanggalTitle;

                $data['list'] = DB::table("data_list")
                                ->selectRaw(
                                    "data_list.*,
                                    (SELECT COUNT(data_list.id) FROM data_list WHERE data_list.mismass_invoice_id=order_list.invoice_id) as totalresi"
                                )
                                ->whereRaw($where)
                                ->join("order_list","order_list.id","=","data_list.mismass_order_id")
                                ->orderBy("data_list.created_at","asc")
                                ->groupBy("data_list.mismass_invoice_id")
                                ->get();

                $data['list2'] = DB::table("data_list")
                                ->select("*")
                                ->whereRaw($where)
                                ->orderBy("data_list.created_at","asc")
                                ->groupBy("data_list.id")
                                ->get();
            }else{
                $data['list'] = DB::table("data_list")
                            ->selectRaw(
                                "data_list.*,
                                warehouse_list.id as wareid,
                                warehouse_list.name as warename,
                                warehouse_list.location as wareloc,
                                cust_list.reference,
                                (SELECT COUNT(data_list.cust_id) FROM data_list WHERE data_list.cust_id=cust_list.id) as jumlahkirim"
                                )
                            ->join("warehouse_list","warehouse_list.id","=","data_list.warehouse_id")
                            ->join("cust_list","cust_list.id","=","data_list.cust_id")
                            ->whereRaw($whereRaw)
                            ->orderBy($orderBy[0],$orderBy[1])
                            ->groupBy($groupBy)
                            ->get();
            }
        }

        // dd($data['title']);
        return view($viewExport,$data);
    }

    public function exportPacker(Request $request)
    {
        $this->backupModel->exportPacker($request);
    }

    public function exportDriverStepOne(Request $request){
        if($request->input("tipeCustomer")==""||$request->input("tanggalAwal")==""||$request->input("tanggalAkhir")==""){
            abort(404);
        }

        $tipeCustomer = $request->input("tipeCustomer");
        $tanggalAwal = $request->input("tanggalAwal");
        $tanggalAkhir = $request->input("tanggalAkhir");
        $driver = Auth::user()->username;

        $data = [
            "random" => $this->generateRandomString(50),
            "data" => '{"tipeCustomer":"'.$tipeCustomer.'","tanggalAwal":"'.$tanggalAwal.'","tanggalAkhir":"'.$tanggalAkhir.'","driver":"'.$driver.'"}'
        ];
        $insert = DB::table("backup_list")->insert($data);

        if(!$insert){
            $encode = array("status" => 500, "message" => "Gagal Insert");
            return json_encode($encode);
        }

        $encode = array("status" => 200, "message" => "Berhasil Insert", "random" => $data["random"]);
        return json_encode($encode);
    }

    public function exportDriver(string $random){  
        $sql = DB::table("backup_list")->where("random",$random)->value("data");

        if($sql==null){
            abort(404);          
        }

        $get = json_decode($sql);
        $driver = $get->driver;
        $filterTanggalAwal = date("Y-m-d 00:00:00",strtotime($get->tanggalAwal));
        $filterTanggalAkhir = date("Y-m-d 23:59:59",strtotime($get->tanggalAkhir));
        $tanggalTitle = $get->tanggalAwal==$get->tanggalAkhir?$this->dateFormatIndo($get->tanggalAwal,1):$this->dateFormatIndo($get->tanggalAwal,1)." - ".$this->dateFormatIndo($get->tanggalAkhir,1);

        if($get->tipeCustomer=="IND"){
            $viewExport = "export.backupindbydriver";
            $where = "data_list.cust_type_id='IND' AND data_list.forwarder_id='MISMASS' AND data_list.forwarder_name='$driver' AND data_list.created_at BETWEEN '$filterTanggalAwal' AND '$filterTanggalAkhir'";
        }elseif($get->tipeCustomer=="COR"){
            $viewExport = "export.backupcorbydriver";
            $where = "data_list.cust_type_id='COR' AND data_list.forwarder_id='MISMASS' AND data_list.forwarder_name='$driver' AND data_list.created_at BETWEEN '$filterTanggalAwal' AND '$filterTanggalAkhir'";
            $data['list2'] = DB::table("data_list")
                ->whereRaw($where)
                ->orderBy("data_list.created_at","asc")
                ->get();
        }

        $data['driver'] = $driver;
        $data['totalweight'] = DB::table("data_list")
                                ->selectRaw("SUM(data_list.weight) as value")
                                ->whereRaw($where)
                                ->value("value");
        $data['totalcbm'] = DB::table("data_list")
                                ->selectRaw("SUM(data_list.cbm) as value")
                                ->whereRaw($where)
                                ->value("value");
        $data['totalkomisiberat'] = DB::table("data_list")
                                ->selectRaw("SUM(CASE WHEN weight=0 THEN 0 WHEN weight<11 THEN 2000 WHEN weight>21 THEN 7000 ELSE 5000 END) as value")
                                ->whereRaw($where)
                                ->value("value");
        $data['totalresi'] = count(DB::table("data_list")
                                ->whereRaw($where)
                                ->groupBy("data_list.shipping_number")
                                ->get());
        $data['tanggalTitle'] = $tanggalTitle;
        $data['list'] = DB::table("data_list")
        ->whereRaw($where)
        ->orderBy("data_list.created_at","asc")
        ->groupBy("data_list.id")
        ->get();

        return view($viewExport,$data);
    }

    public function exportops(Request $request){
        $tipeCustomer = $request->input("tipecustomer");
        $tanggalAwal = $request->input("tanggalawal");
        $tanggalAkhir = $request->input("tanggalakhir");
        $filterTanggalAwal = date("Y-m-d 00:00:00",strtotime($tanggalAwal));
        $filterTanggalAkhir = date("Y-m-d 23:59:59",strtotime($tanggalAkhir));

        $tipeCust = DB::table("cust_type_list")
                    ->where("id",$tipeCustomer)
                    ->value("name");

        if($tipeCustomer=="IND"){
            $viewExport = "export.backupopsind";
        }elseif($tipeCustomer=="COR"){
            $viewExport = "export.backupopscor";
            $data['list2'] = DB::table('data_list')
                            ->orderBy("updated_at","desc")
                            ->get();
        }else{
            return json_encode(false);
        }

        $whereRaw = "order_list.cust_type_id='$tipeCustomer' AND order_list.created_at BETWEEN '$filterTanggalAwal' AND '$filterTanggalAkhir'";

        $data['title'] =  "Export Data Operational | ".$tipeCust." | ".$this->dateFormatIndo($tanggalAwal,1)." - ".$this->dateFormatIndo($tanggalAkhir,1);
        $data['list'] = DB::table("order_list")
                        ->select(
                            "order_list.id as orderid",
                            "order_list.order_status_id as orderstatusid",
                            "order_list.created_at as ordercreatedat",
                            "order_list.invoice_id as orderinvoiceid",
                            "order_list.first_name as orderfirstname",
                            "order_list.middle_name as ordermiddlename",
                            "order_list.last_name as orderlastname",
                            "order_list.phone as orderphone",
                            "order_list.address as orderaddress",
                            "order_list.sub_district as ordersubdistrict",
                            "order_list.district as orderdistrict",
                            "order_list.city as ordercity",
                            "order_list.prov as orderprov",
                            "order_list.postal_code as orderpostalcode",
                            "data_list.*",
                            )
                        ->join("data_list","data_list.mismass_order_id","=","order_list.id")
                        ->whereRaw($whereRaw)
                        ->orderBy("order_list.updated_at","desc")
                        ->groupBy("order_list.id")
                        ->get();

        // dd($data['list']);

        return view($viewExport,$data);
    }
}
