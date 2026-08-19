<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Warehouse;
use App\Models\RouteList;
use App\Models\Country;
use App\Models\Service;

class ServiceController extends Controller
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
        if(!env('SERV_LIST')){
            if(Auth::user()->username!="dev8th"){
                return view('pages.maintenance');
            }
        }
        $this->roleAccess();
        $data['warehouse'] = $this->wareModel::all();
        $data['route'] = $this->routeModel::all();
        $data['country'] = $this->countryModel::all();
        return view('pages.servlist', $data);
    }

    public function nameChecking(Request $request)
    {
        $name = $request->input('serviceName');
        $nameOld = $request->input('serviceNameOld');
        $warehouse = $request->input('warehouse');
        $check = Service::whereRaw("name='$name' AND warehouse_id='$warehouse'")->first();
        $status = $check == null ? true : ($name == $nameOld ? true : false);
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
        $filter = [
            $filterTanggal,
            $filterWarehouse,
            $filterCountry,
            $filterRoute,
        ];
        $servModel = new Service;
        $lists = $servModel->getDT($request, $search, $filter);

        foreach ($lists as $list) {
            $getRank = DB::table('users')->where("username",$list->updated_by)->value("rank");
            $jabatan = $getRank != null ? "<div class='bg-mismass' style='padding:1px 5px'>".$getRank."</div>" : "";
            $hapusBtn = Auth::user()->servlist_hapus?"<a class='dropdown-item pointlink' id='hapusBtn' onclick=\"konfirm_hapus('" . $list->id . "','" . $list->name . "','Service','" . url('/servlist/hapus/') . "','service')\"><div style='color:red'>Hapus Data</div></a>":"";
            $editBtn = Auth::user()->servlist_edit?"<a class='dropdown-item pointlink' id='editBtn' data-id='" . $list->id . "' data-countryid='" . $list->country_id . "' data-wareid='" . $list->warehouse_id . "' data-name='" . $list->name . "' data-pricekg='" . $list->pricekg . "' data-priceitem='" . $list->priceitem . "' data-pricevol='" . $list->pricevol . "' data-pricecbm='" . $list->pricecbm . "' data-description='" . $list->description . "'>Edit Data</a>":"";
            $wholeBtn = "<div class='btn-group dropleft'><button type='button' class='btn btn-secondary nobtn' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'><i class='fas fa-ellipsis-v'></i></button><div class='dropdown-menu' x-placement='right-start' style='position: absolute; transform: translate3d(111px, 0px, 0px); top: 0px; left: 0px; will-change: transform;'>".$editBtn.$hapusBtn."</div></div>";

            $no++;
            $row = [];
            $row[] = $no;
            $row[] = "<div style='font-weight:700'>" . $list->updated_by . "</div>".$jabatan."<div>Edited At : </div><div>" . $this->dateFormatIndo($list->updated_at,2) . "</div>";
            $row[] = "<div style='font-weight:700'>" . $list->name . "</div><div>Created At : </div><div>".$this->dateFormatIndo($list->created_at,0)."</div>";
            $row[] = "<div style='font-weight:700'>" . $list->warehouse_id . "</div><div>" . $list->warename . "</div>";
            $row[] = $list->location;
            if(Auth::user()->servlist_nom){
                $row[] = "<div style='font-weight:700'>" . $this->rupiah($list->pricekg) . "</div>";
                $row[] = "<div style='font-weight:700'>" . $this->rupiah($list->priceitem) . "</div>";
                $row[] = "<div style='font-weight:700'>" . $this->rupiah($list->pricevol) . "</div>";
                $row[] = "<div style='font-weight:700'>" . $this->rupiah($list->pricecbm) . "</div>";
            }
            // $row[] = $list->description;
            if(Auth::user()->servlist_edit||Auth::user()->servlist_hapus){
                $row[] = $wholeBtn;
            }

            $data[] = $row;
        }

        $output = [
            'draw' => $request->input('draw'),
            'recordsTotal' => $servModel->countAll(),
            'recordsFiltered' => $servModel->countFiltered($request, $search, $filter),
            'data' => $data
        ];

        return json_encode($output);
    }

    public function tambah(Request $request)
    {
        $username = Auth::user()->username;
        $name = $request->input('serviceName');
        $warehouse = $request->input('warehouse');
        $priceKg = str_replace(".", "", (string)$request->input('priceKg'));
        $priceVol = str_replace(".", "", (string)$request->input('priceVol'));
        $priceItem = str_replace(".", "", (string)$request->input('priceItem'));
        $priceCbm = str_replace(".", "", (string)$request->input('priceCbm'));
        $description = $request->input('description') ?? "";

        $servModel = new Service;
        $servModel->name = $name;
        $servModel->warehouse_id = $warehouse;
        $servModel->created_by = $username;
        $servModel->updated_by = $username;
        $servModel->pricekg = $priceKg;
        $servModel->pricevol = $priceVol;
        $servModel->priceitem = $priceItem;
        $servModel->pricecbm = $priceCbm;
        $servModel->description = $description;

        $insert = $servModel->save();

        if (!$insert) {
            $encode = array("status" => "Gagal", "text" => "Gagal Buat Service");
            return json_encode($encode);
        }

        $dataHistory=[
            "codename" => "BS",
            "created_at" => date("Y-m-d H:i:s"),
            "created_by" => $username,
            "description" => "<b>Buat Service</b> dengan detail,<br><br>
            ID Warehouse : <b>".$warehouse."</b><br>
            Nama Service : <b>".$name."</b><br>
            Harga/Kg : <b>".$this->rupiah($priceKg)."</b><br>
            Harga/Vol : <b>".$this->rupiah($priceVol)."</b><br>
            Harga/Item : <b>".$this->rupiah($priceItem)."</b><br>
            Harga/CBM : <b>".$this->rupiah($priceCbm)."</b><br>
            Deskripsi : <b>".$description."</b>",
        ];
        $insertHistory = DB::table('history_list')->insert($dataHistory);
        if(!$insertHistory){
            $encode = array("status" => "Gagal", "text" => "Gagal Buat History");
            return json_encode($encode);
        }

        $encode = array("status" => "Berhasil", "text" => "Berhasil Buat Service");
        return json_encode($encode);
    }

    public function hapus(Request $request)
    {
        $id = $request->input('id');
        $subject = $request->input('subject');
        $username = Auth::user()->username;

        // $encode = array("status" => "Gagal", "text" => "Anda Tidak Bisa Menghapus Data Service.");
        // return json_encode($encode);

        $servModel = new Service;
        $lawas = $servModel::where("id",$id)->first();
        $dataHistory=[
            "codename" => "HS",
            "created_at" => date("Y-m-d H:i:s"),
            "created_by" => $username,
            "description" => "<b>Hapus Service</b> dengan detail,<br><br>
            ID Warehouse : <b>".$lawas->warehouse."</b><br>
            Nama Service : <b>".$lawas->name."</b><br>
            Harga/Kg : <b>".$this->rupiah($lawas->pricekg)."</b><br>
            Harga/Vol : <b>".$this->rupiah($lawas->pricevol)."</b><br>
            Harga/Item : <b>".$this->rupiah($lawas->priceitem)."</b><br>
            Harga/CBM : <b>".$this->rupiah($lawas->pricecbm)."</b><br>
            Deskripsi : <b>".$lawas->description."</b>",
        ];
        $insertHistory = DB::table('history_list')->insert($dataHistory);
        if(!$insertHistory){
            $encode = array("status" => 500, "title" => "Gagal", "text" => "Gagal Buat History");
            return json_encode($encode);
        }

        $delete = $servModel::where('id', '=', $id)->delete();

        if (!$delete) {
            $encode = array("status" => 500, "title" => "Gagal", "text" => "Gagal Hapus ".$subject."!");
            return json_encode($encode);
        }

        //Notif Success
        $encode = array("status" => 200, "title" => "Berhasil", "text" => "Hapus ".$subject." Berhasil!");
        return json_encode($encode);
    }

    public function edit(Request $request)
    {
        $username = Auth::user()->username;
        $id = $request->input('serviceID');
        $wareId = $request->input('warehouse');
        $name = $request->input('serviceName');
        $priceKg = str_replace(".", "", (string)$request->input('priceKg'));
        $priceVol = str_replace(".", "", (string)$request->input('priceVol'));
        $priceItem = str_replace(".", "", (string)$request->input('priceItem'));
        $priceCbm = str_replace(".", "", (string)$request->input('priceCbm'));
        $description = $request->input("description");

        $servModel = new Service;
        $lawas = $servModel::where("id",$id)->first();

        $dataHistory=[
            "codename" => "ES",
            "created_at" => date("Y-m-d H:i:s"),
            "created_by" => $username,
            "description" => "<b>Edit Service</b> dengan detail,<br><br>
            ID Warehouse : ".($lawas->warehouse_id==$wareId?"<b>".$wareId."</b><br>":"<b style='color:red'>".$lawas->warehouse_id."</b> <i class='fas fa-arrow-right'></i> <b style='color:red'>".$wareId."</b><br>")."
        Nama Service : ".($lawas->name==$name?"<b>".$name."</b><br>":"<b style='color:red'>".$lawas->name."</b> <i class='fas fa-arrow-right'></i> <b style='color:red'>".$name."</b><br>")."
        Harga/Kg : ".($lawas->pricekg==$priceKg?"<b>".$this->rupiah($priceKg)."</b><br>":"<b style='color:red'>".$this->rupiah($lawas->pricekg)."</b> <i class='fas fa-arrow-right'></i> <b style='color:red'>".$this->rupiah($priceKg)."</b><br>")."
        Harga/Vol : ".($lawas->pricevol==$priceVol?"<b>".$this->rupiah($priceVol)."</b><br>":"<b style='color:red'>".$this->rupiah($lawas->pricevol)."</b> <i class='fas fa-arrow-right'></i> <b style='color:red'>".$this->rupiah($priceVol)."</b><br>")."
        Harga/Item : ".($lawas->priceitem==$priceItem?"<b>".$this->rupiah($priceItem)."</b><br>":"<b style='color:red'>".$this->rupiah($lawas->priceitem)."</b> <i class='fas fa-arrow-right'></i> <b style='color:red'>".$this->rupiah($priceItem)."</b><br>")."
        Harga/CBM : ".($lawas->pricecbm==$priceCbm?"<b>".$this->rupiah($priceCbm)."</b><br>":"<b style='color:red'>".$this->rupiah($lawas->pricecbm)."</b> <i class='fas fa-arrow-right'></i> <b style='color:red'>".$this->rupiah($priceCbm)."</b><br>")."
        Deskripsi : ".($lawas->description==$description?"<b>".$description."</b><br>":"<b style='color:red'>".$lawas->description."</b> <i class='fas fa-arrow-right'></i> <b style='color:red'>".$description."</b>"),
        ];
        $insertHistory = DB::table('history_list')->insert($dataHistory);
        if(!$insertHistory){
            $encode = array("status" => "Gagal", "text" => "Gagal Buat History");
            return json_encode($encode);
        }

        $data = [
            'updated_by' => $username,
            'id' => $id,
            'warehouse_id' => $wareId,
            'name' => $name,
            'pricekg' => $priceKg,
            'pricevol' => $priceVol,
            'priceitem' => $priceItem,
            'pricecbm' => $priceCbm,
            'description' => $description,
        ];

        $edit = $servModel::where('id', '=', $id)->update($data);

        if (!$edit) {
            $encode = array("status" => "Gagal", "text" => "Gagal Edit Service");
            return json_encode($encode);
        }

        $encode = array("status" => "Berhasil", "text" => "Berhasil Edit Service");
        return json_encode($encode);
    }

    public function export()
    {
        $this->roleAccess();
        $servModel = new Service;
        $data['service'] = $servModel::select(
            [
                "service_list.*",
                "warehouse_list.name as warename",
                "warehouse_list.location"
            ]
        )->join("warehouse_list", "warehouse_list.id", "=", "service_list.warehouse_id")
            ->orderBy("service_list.created_at", "desc")
            ->get();
        $data['tanggal'] = $this->dateFormatIndo(date("Y-m-d"),1);
        return view('export.service', $data);
    }
}
