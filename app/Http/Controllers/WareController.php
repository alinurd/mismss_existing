<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\RouteList;
use App\Models\Country;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class WareController extends Controller
{
    private $wareModel,$routeModel,$countryModel;

    public function __construct()
    {
        $this->wareModel = new Warehouse;
        $this->routeModel = new RouteList;
        $this->countryModel = new Country;
    }

    public function index()
    {
        if(!env('WARE_LIST')){
            if(Auth::user()->username!="dev8th"){
                return view('pages.maintenance');
            }
        }
        $this->roleAccess();
        $data['warehouse'] = $this->wareModel::all();
        $data['country'] = $this->countryModel::all();
        $data['route'] = $this->routeModel::all();
        return view('pages.warehouse',$data);
    }

    public function inputChecking(Request $request)
    {
        $id = $request->input('warehouseID');
        $idOld = $request->input('warehouseIDOld');
        $check = Warehouse::where('id', $id)->first();
        $status = $check == null ? true : ($id == $idOld ? true : false);

        return json_encode($status);
    }

    public function table(Request $request)
    {
        $this->roleAccess();
        $data = [];
        $no = $request->input('start');
        $search = $request->input('search')['value'];
        $filterTanggal = $request->input("filterTanggal")!="[object Object]"?$this->dateFilterFormat($request->input("filterTanggal")):"";
        $filterCountry = $request->input("filterCountry");
        $filterRoute = $request->input("filterRoute");
        $filterWarehouse = $request->input("filterWarehouse");
        $filterCustType = $request->input("filterCustType");
        $filter = [
            $filterTanggal,
            $filterWarehouse,
            $filterCountry,
            $filterRoute,
            $filterCustType,
        ];
        $wareModel = new Warehouse();
        $lists = $wareModel->getDT($request, $search, $filter);

        $totalRowM = 0 ;
        foreach ($lists as $list) {

            $getRank = DB::table('users')->where("username",$list->updated_by)->value("rank");
            $jabatan = $getRank != null ? "<div class='bg-mismass' style='padding:1px 5px'>".$getRank."</div>" : "";
            $editBtn = Auth::user()->warelist_edit?"<a class='dropdown-item pointlink' id='editBtn' data-id='" . $list->id . "' data-name='" . $list->name . "' data-location='".$list->location."' data-country='".$list->country_id."' data-route='".$list->route_id."'>Edit Data</a>":"";
            $hapusBtn = Auth::user()->warelist_hapus?"<a class='dropdown-item pointlink' id='hapusBtn' onclick=\"konfirm_hapus('" . $list->id . "','" . $list->name . "','Warehouse','" . url('/warehouse/hapus/') . "','warehouse')\"><div style='color:red'>Hapus Data</div></a>":"";
            $wholeBtn = "<div class='btn-group dropleft'><button type='button' class='btn btn-secondary nobtn' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'><i class='fas fa-ellipsis-v'></i></button><div class='dropdown-menu' x-placement='right-start' style='position: absolute; transform: translate3d(111px, 0px, 0px); top: 0px; left: 0px; will-change: transform;'>".$editBtn.$hapusBtn."</div></div>";
            $routeLabel = $list->route_id=="IMP" ? "<div class='alert alert-primary d-inline-block p-1 mb-1'>IMPORT</div>" : "<div class='alert alert-danger d-inline-block p-1 mb-1'>EXPORT</div>";
            $custTypeLabel = $filterCustType=="" ? "<div class='alert alert-secondary d-inline-block p-1'>ALL</div>" : ($filterCustType=="IND" ? "<div class='alert alert-success d-inline-block p-1'>INDIVIDUAL</div>" : "<div class='alert alert-warning d-inline-block p-1'>CORPORATE</div>");

            $no++;
            $row = [];
            $row[] = $no;
            $row[] = "<div style='font-weight:700'>" . $list->updated_by . "</div>".$jabatan."<div>Edited At : </div><div>" . $this->dateFormatIndo($list->updated_at,2) . "</div>";
            $row[] = "<div style='font-weight:700'>" . $list->id . "</div><div>" . $list->name . "</div><div>Created At : </div><div>".$this->dateFormatIndo($list->created_at,0)."</div>";
            $row[] = $list->location."<div>".$routeLabel."</div><div>".$custTypeLabel."</div>";
            $row[] = "<div>".$list->custByWarehouse." Customer</div><div>".$list->serviceByWarehouse." Service</div><div>".$list->invoiceByWarehouse." Invoice</div>";
            $row[] = "<div>".round($list->weightByWarehouse,2)." Kg</div><div>".$list->itemByWarehouse." Item</div><div>".round($list->cbmByWarehouse,2)." CBM</div>";
            if(Auth::user()->warelist_nom){
                $getPendapatan = "<div style='font-weight:700'>Pendapatan : ".$this->rupiah($list->incomeRpByWarehouse)."</div>";
                $getPaid = "<div>Paid : ".$this->rupiah($list->incomePaidRpByWarehouse)."</div>";
                $getUnpaid = "<div>Unpaid : ".$this->rupiah($list->incomeUnpaidRpByWarehouse)."</div>";
                $getDiskon = "<div>Diskon : ".$this->rupiah($list->discountRpByWarehouse)."</div>";

                $row[] = "<div>".$getPendapatan.$getDiskon.$getUnpaid.$getPaid."</div>";

                $getPendapatanForeign = "<div style='font-weight:700'>Pendapatan : ".$this->dollarSG($list->incomeSGDByWarehouse)."</div>";
                $getPaidForeign = "<div>Paid : ".$this->dollarSG($list->incomePaidSGDByWarehouse)."</div>";
                $getUnpaidForeign = "<div>Unpaid : ".$this->dollarSG($list->incomeUnpaidSGDByWarehouse)."</div>";
                $getDiskonForeign = "<div>Diskon : ".$this->dollarSG($list->discountSGDByWarehouse)."</div>";
                
                $row[] = "<div>".$getPendapatanForeign.$getDiskonForeign.$getUnpaidForeign.$getPaidForeign."</div>";
            }
            if(Auth::user()->warelist_edit||Auth::user()->warelist_hapus){
                $row[] = $wholeBtn;
            }

            $data[] = $row;

        }

        $recordsFiltered = $wareModel->countFiltered($request, $search, $filter);
        $output = [
            'draw' => $request->input('draw'),
            'recordsTotal' => $wareModel->countAll(),
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ];

        return json_encode($output);
    }

    public function tambah(Request $request)
    {
        $username = Auth::user()->username;
        $id = $request->input('warehouseID');
        $name = $request->input('warehouseName') ?? "";
        $location = $request->input('warehouseLoc');
        $country = $request->input('country');
        $route = $request->input('route');

        $wareModel = new Warehouse();
        $wareModel->id = $id;
        $wareModel->status_id = 1;
        $wareModel->name = $name;
        $wareModel->location = $location;
        $wareModel->country_id = $country;
        $wareModel->route_id = $route;
        $wareModel->description = "";
        $wareModel->created_by = $username;
        $wareModel->updated_by = $username;

        $insert = $wareModel->save();

        if (!$insert) { 
            $encode = array("status" => "Gagal", "text" => "Gagal Buat Warehouse");
            return json_encode($encode);
        }

        $dataHistory = [
            "codename" => "BW",
            "created_at" => date("Y-m-d H:i:s"),
            "created_by" => $username,
            "description" => "<b>Buat Warehouse</b> dengan detail,<br><br>
            ID : <b>".$id."</b><br>
            Country : <b>".DB::table('country_list')->where("id",$country)->value("name")."</b><br>
            Route : <b>".DB::table('route_list')->where("id",$route)->value("name")."</b><br>
            Nama : <b>".$name."</b><br>
            Lokasi : <b>".$location."</b>",
        ];
        $insertHistory = DB::table("history_list")->insert($dataHistory);
        if(!$insertHistory){
            $encode = array("status" => "Gagal", "text" => "Gagal Buat History");
            return json_encode($encode);
        }

        $encode = array("status" => "Berhasil", "text" => "Berhasil Buat Warehouse");
        return json_encode($encode);
        
    }

    public function hapus(Request $request)
    {
        $id = $request->input('id');
        $subject = $request->input('subject');
        $username = Auth::user()->username;

        $encode = array("status" => 500, "title" => "Gagal", "text" => "Anda Tidak Bisa Menghapus Data Warehouse!");
        return json_encode($encode);

        // $wareModel = new Warehouse;
        // $lawas = $wareModel::where('id',$id)->first();
        // $dataHistory = [
        //     "codename" => "HW",
        //     "created_at" => date("Y-m-d H:i:s"),
        //     "created_by" => $username,
        //     "description" => "<b>Hapus Warehouse</b> dengan detail,<br><br>
        //     ID : <b>".$lawas->id."</b><br>
        //     Nama : <b>".$lawas->name."</b><br>
        //     Lokasi : <b>".$lawas->location."</b>",
        // ];
        // $insertHistory = DB::table("history_list")->insert($dataHistory);
        // if(!$insertHistory){
        //     $encode = array("status" => "Gagal", "text" => "Gagal Tambah History");
        //     return json_encode($encode);
        // }

        // $delete = $wareModel::where('id', $id)->delete();

        // if (!$delete) {
        //     $encode = array("status" => "Gagal", "text" => "Gagal Hapus " . $subject);
        //     return json_encode($encode);
        // }
        
        // $encode = array("status" => "Berhasil", "text" => "Berhasil Hapus " . $subject);
        // return json_encode($encode);
        

        
    }

    public function edit(Request $request)
    {
        $username = Auth::user()->username;
        $id = $request->input('warehouseID');
        $name = $request->input('warehouseName') ?? "";
        $location = $request->input('warehouseLoc');
        $country = $request->input('country');
        $route = $request->input('route');

        $wareModel = new Warehouse();
        $lawas = $wareModel::where("id",$id)->first();

        $dataHistory = [
            "codename" => "EW",
            "created_at" => date("Y-m-d H:i:s"),
            "created_by" => $username,
            "description" => "<b>Edit Warehouse</b> dengan detail,<br><br>
            ID : <b>".$id."</b><br>
            Country : <b>".DB::table('country_list')->where("id",$country)->value("name")."</b><br>
            Route : <b>".DB::table('route_list')->where("id",$route)->value("name")."</b><br>
            Nama : ".($lawas->name==$name?"<b>".$name."</b><br>":"<b style='color:red'>".$lawas->name."</b> <i class='fas fa-arrow-right'></i> <b style='color:red'>".$name."</b><br>")."
            Lokasi : ".($lawas->location==$location?"<b>".$location."</b>":"<b style='color:red'>".$lawas->location."</b> <i class='fas fa-arrow-right'></i> <b style='color:red'>".$location."</b>"),
        ];
        $insertHistory = DB::table("history_list")->insert($dataHistory);
        if(!$insertHistory){
            $encode = array("status" => "Gagal", "text" => "Gagal Tambah History");
            return json_encode($encode);
        }

        $data = [
            'updated_by' => $username,
            'id' => $id,
            'country_id' => $country,
            'route_id' => $route,
            'name' => $name,
            'location' => $location
        ];

        $wareModel = new Warehouse;
        $edit = $wareModel::where("id", $id)->update($data);

        if (!$edit) {
            $encode = array("status" => "Gagal", "text" => "Gagal Edit Warehouse");
            return json_encode($encode);
        }

        $encode = array("status" => "Berhasil", "text" => "Berhasil Edit Warehouse");
        return json_encode($encode);
    }

    public function export(Request $request)
    {
        $this->roleAccess();
        // $filterTanggal = $request->input("filterTanggal")!="[object Object]"?$this->dateFilterFormat($request->input("filterTanggal")):"";
        // $whereTanggal = $filterTanggal == "" ? "data_list.id!=''" : "data_list.created_at BETWEEN '$filterTanggal[0] 00:00:00' AND '$filterTanggal[1] 23:59:59'";
        // $data['titleTanggal'] = $this->dateFormatIndo($filterTanggal[0],1)." - ".$this->dateFormatIndo($filterTanggal[1],1);
        $wareModel = new Warehouse;
        $data['warehouse'] = $wareModel::selectRaw("warehouse_list.*")
                            // ->join('data_list','warehouse_list.id','=','data_list.warehouse_id')
                            // ->groupBy('warehouse_list.id')
                            ->whereRaw("warehouse_list.id!=''")
                            // ->whereRaw($whereTanggal)
                            ->orderBy("warehouse_list.updated_at","desc")
                            ->get();            
        $data['titleTanggal'] = $this->dateFormatIndo(date("Y-m-d"),1);
        return view('export.warehouse', $data);
    }

    public function getList(Request $request)
    {
        $countryId = $request->input("countryId");
        $routeId = $request->input("routeId");
        $mode = $request->input("mode");

        $getList = $this->wareModel
                    ->where("country_id", $countryId)
                    ->when(!empty($routeId), function ($q) use ($routeId) {
                        $q->where("route_id", $routeId);
                    })
                    ->orderBy("id", "ASC")
                    ->get();

        if($mode=="C"){
            $option = "<option value='' hidden>Pilih Warehouse</option>";
        }else{
            $option = "<option value=''>ALL WAREHOUSE</option>";
        }

        foreach($getList as $g){
            $option .= "<option value='".$g->id."'>".$g->id." - ".$g->name." - ".$g->location."</option>";
        }

        return $option;
    }
}
