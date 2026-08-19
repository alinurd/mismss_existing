<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\PackerList;

class PackerListController extends Controller
{

    private $dokuController,$invoiceModel,$wareModel,$custModel,$orderModel,$servModel,$trackingModel;

    public function __construct()
    {
        $this->packerListModel = new PackerList;
    }

    public function inputPacker(Request $request)
    {
        $id = $request->input("id");
        $dateNow = date("Y-m-d H:i:s");
        $username = Auth::user()->username;

        $select = DB::table("order_list")->where("ms_track_id",$id)->first();
        
        if($select==null){
            $encode = array("status" => "405", "header" => "Gagal", "text" => "Resi Tracking Tidak Ditemukan");
            return json_encode($encode);
        }

        $update = DB::table("order_list")->where("ms_track_id",$id)->update(["packing_created_by"=>$username,"packing_created_at"=>$dateNow]);
    
        if(!$update){
            $encode = array("status" => "405", "header" => "Gagal", "text" => "Gagal Update Resi Tracking");
            return json_encode($encode);
        }

        $tipeInvoice = DB::table("order_list")->where("ms_track_id",$id)->value("cust_type_id");
        $dataHistory=[
            "codename" => "CP",
            "created_at" => date("Y-m-d H:i:s"),
            "created_by" => $username,
            "description" => "<b>Check Packing</b> dengan detail,<br><br>
            Resi Tracking : <b>".$id."</b><br>
            Tanggal Check : <b>".date("Y-m-d H:i:s")."</b><br>
            Tipe : <b>".($tipeInvoice=="IND"?"Individual":"Corporate")."</b>",
        ];
        $insertHistory = DB::table('history_list')->insert($dataHistory);
        if(!$insertHistory){
            $encode = array("status" => "405", "header" => "Gagal", "text" => "Gagal Buat History");
            return json_encode($encode);
        }

        $encode = array("status" => "200", "header" => "Berhasil", "text" => "Proses Check Berhasil");
        return json_encode($encode);
    }

    public function tablePackerList(string $custTypeId, Request $request)
    {
        $this->roleAccess();
        $data = [];
        $filter = [
            "filterTanggal" => $this->dateFilterFormat($request->input('filterTanggal')),
            "custTypeId" => $custTypeId,
            "mode" => "packlist"
        ];
        $no = $request->input('start');
        $search = $request->input('search')['value'];
        $lists = $this->packerListModel->getDT($request, $search, $filter);

        foreach ($lists as $list) {
            
            $getLatest = DB::table("shiptrip_track_list")->selectRaw("created_by,created_at,text")->where("ms_track_id",$list->ms_track_id)->orderBy("created_at","desc")->get();
            $shipmentDate = count($getLatest)>1?"<div>TRACK:".$this->dateFormatIndo($getLatest[0]->created_at,1)."<div>SHIP:".$this->dateFormatIndo($list->to_sg_man_created_at,1)."</div>":"";
            $invoiceId = $this->packerListModel->getInvoiceIdData($list->invoice_id);
            $getRank = DB::table('users')->where("username",$list->updated_by)->value("rank");
            $jabatan = $getRank != null ? "<div class='bg-mismass' style='padding:1px 5px;'>".$getRank."</div>" : "";
            $customer = "<div class='fw-bold'>".$list->full_name."</div><div>".$list->phone."</div><div>".$list->city.", ".$list->prov.", ".$list->postal_code."</div>";
            $getTotal = $this->packerListModel->getTotalByInvoice($list->invoice_id);
            $note = DB::table("shiptrip_list")->where("ms_track_id",$list->ms_track_id)->value("note");
            $noteEl = $note!=""?"<div class='bg-secondary p-1' style='display:inline-block;border:none;border-radius:5px'>Catatan</div>":"";

            $no++;
            $row = [];

            $row[] = $no;
            $row[] = "<a class='fw-bold loadTracking' href='".url('/shiptrip/tracking')."?id=".$list->ms_track_id."' target='_blank'>".$list->ms_track_id."</a>".$shipmentDate."<div>DROP:".$this->dateFormatIndo($list->drop_man_created_at,1)."</div>";
            $row[] = $invoiceId;
            $row[] = "<div class='fw-bold'>" . $list->updated_by . "</div>".$jabatan."<div>" . $this->dateFormatIndo($list->updated_at,2) . "</div>";
            $row[] = $customer;
            $row[] = "<div style='display:flex'><div style='margin-right:5px'>".$list->total_foreign."</div><a class='pointlink lookresiln' data-id='".$list->ms_track_id."' data-resi-ln='".$list->foreign_tracks."' data-catatan='".$note."'><i class='fas fa-eye'></i></a></div>".$noteEl;
            $row[] = $getTotal;
            $row[] = "<a class='aBtn' id='checkBtn' data-id='$list->ms_track_id'><i class='fas fa-check'></i></a>";

            $data[] = $row;
        }

        $output = [
            'draw' => $request->input('draw'),
            'recordsTotal' => $this->packerListModel->countAll(),
            'recordsFiltered' => $this->packerListModel->countFiltered($request, $search, $filter),
            'data' => $data
        ];

        return json_encode($output);
    }

    public function tablePackerChecked(string $custTypeId, Request $request)
    {
        $this->roleAccess();
        $data = [];
        $filter = [
            "filterTanggal" => $this->dateFilterFormat($request->input('filterTanggal')),
            "custTypeId" => $custTypeId,
            "mode" => "packchecked"
        ];
        $no = $request->input('start');
        $search = $request->input('search')['value'];
        $lists = $this->packerListModel->getDT($request, $search, $filter);

        foreach ($lists as $list) {
            $getLatest = DB::table("shiptrip_track_list")->selectRaw("created_by,created_at,text")->where("ms_track_id",$list->ms_track_id)->orderBy("created_at","desc")->get();
            $shipmentDate = count($getLatest)>1?"<div>TRACK:".$this->dateFormatIndo($getLatest[0]->created_at,1)."<div>SHIP:".$this->dateFormatIndo($list->to_sg_man_created_at,1)."</div>":"";
            $invoiceId = $this->packerListModel->getInvoiceIdData($list->invoice_id);
            $getRank = DB::table('users')->where("username",$list->updated_by)->value("rank");
            $jabatan = $getRank != null ? "<div class='bg-mismass' style='padding:1px 5px;'>".$getRank."</div>" : "";
            $customer = "<div class='fw-bold'>".$list->full_name."</div><div>".$list->phone."</div><div>".$list->city.", ".$list->prov.", ".$list->postal_code."</div>";
            $getTotal = $this->packerListModel->getTotalByInvoice($list->invoice_id);
            $note = DB::table("shiptrip_list")->where("ms_track_id",$list->ms_track_id)->value("note");
            $noteEl = $note!=""?"<div class='bg-secondary p-1' style='display:inline-block;border:none;border-radius:5px'>Catatan</div>":"";

            $no++;
            $row = [];

            $row[] = $no;
            $row[] = "<a class='fw-bold loadTracking' href='".url('/shiptrip/tracking')."?id=".$list->ms_track_id."' target='_blank'>".$list->ms_track_id."</a>".$shipmentDate."<div>DROP:".$this->dateFormatIndo($list->drop_man_created_at,1)."</div>";
            $row[] = $invoiceId;
            $row[] = "<div class='fw-bold'>" . $list->updated_by . "</div>".$jabatan."<div>" . $this->dateFormatIndo($list->updated_at,2) . "</div>";
            $row[] = "<div class='fw-bold'>" . $list->packing_created_by . "</div><div class='bg-mismass' style='padding:1px 5px;'>Packer</div><div>" . $this->dateFormatIndo($list->packing_created_at,2) . "</div>";
            $row[] = $customer;
            $row[] = "<div style='display:flex'><div style='margin-right:5px'>".$list->total_foreign."</div><a class='pointlink lookresiln' data-id='".$list->ms_track_id."' data-resi-ln='".$list->foreign_tracks."' data-catatan='".$note."'><i class='fas fa-eye'></i></a></div>".$noteEl;
            $row[] = $getTotal;

            $data[] = $row;
        }

        $output = [
            'draw' => $request->input('draw'),
            'recordsTotal' => $this->packerListModel->countAll(),
            'recordsFiltered' => $this->packerListModel->countFiltered($request, $search, $filter),
            'data' => $data
        ];

        return json_encode($output);
    }
}
