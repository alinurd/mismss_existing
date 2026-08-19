<?php

namespace App\Http\Controllers;

use App\Models\Voids;
use App\Models\DokuSystem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class VoidController extends Controller
{
    private $voidModel,$dokuModel;

    public function __construct()
    {
        $this->voidModel = new Voids;
        $this->dokuModel = new DokuSystem;
    }

    public function index(){
        if(!env('VOID_LIST')){
            if(Auth::user()->username!="dev8th"){
                return view('pages.maintenance');
            }
        }
        return view("pages.void");
    }

    public function history(string $id){
        $data['servData'] = DB::table("void_list")->where("mismass_invoice_id","INV/AJV/".$id)->get();
        if(count($data['servData'])<1){
            abort(404);
        }
        $data['coreData'] = DB::table("void_list")->where("mismass_invoice_id","INV/AJV/".$id)->first();

        return view("printout.voidhistory", $data);
    }

    public function create(Request $request){
        $invoiceId = $request->input('invoiceId');
        $note = $request->input('note');
        $mode = $request->input('mode');

        //cek invoice ada apa tidak
        $cek = DB::table("data_list")->select("invoice_status","doku_link")->where("mismass_invoice_id",$invoiceId)->first();
        if($cek==null){
            $encode = array("status" => 500, "title" => "Gagal", "text" => "Invoice Sudah Tidak Ada");
            return json_encode($encode);
        }

        //cek invoice status pending apa tidak
        if($cek->invoice_status=="PAID"){
            $encode = array("status" => 500, "title" => "Gagal", "text" => "Invoice Sudah Lunas");
            return json_encode($encode);
        }

        //cek status doku langsung jika pembayaran dengan doku
        if(!env('SANDBOX')){
            if($cek->doku_link!=""){
                $cekDoku = $this->dokuModel->checkStatusInvoiceDoku($invoiceId);
                if($cekDoku!=null){
                    $sendToSuccess = $this->dokuModel->sendToSuccessDoku([$cekDoku[0],$cekDoku[1]]);
                    $encode = array("status" => 500, "title" => "Gagal", "text" => "Invoice Sudah Lunas Cek Doku");
                    return json_encode($encode);
                }
            }
        }

        //ambil data_list pindah ke void_list
        $insertArray = [
            "invoiceId" => $invoiceId,
            "note" => $note,
            "mode" => $mode
        ];
        $insert = $this->voidModel->insertDataVoid($insertArray);

        //hapus data invoice di data_list
        DB::table('data_list')->where("mismass_invoice_id",$invoiceId)->delete();

        //cek order_list apakah ada resi tracking
        $trackingArray = [
            "invoiceId" => $invoiceId,
            "mode" => $mode
        ];
        $this->voidModel->voidTracking($trackingArray);

        $textSuccess = "Invoice ".$invoiceId." berhasil di VOID, ";
        $textSuccess .= $mode=="INR"? "Invoice dan Tracking telah dihapus permanen." : "Tracking dikembalikan ke Create Invoice.";
        $encode = array("status" => 200, "title" => "Berhasil", "text" => $textSuccess);
        return json_encode($encode);

    }

    public function total(string $custTypeId, Request $request){
        $filterTanggal = $this->dateFilterFormat($request->input('filterTanggal'));
        $filterPayment = $request->input('filterPayment');
        $filter = [
            "filterTanggal" => $filterTanggal,
            "filterPayment" => $filterPayment,
            "custTypeId" => $custTypeId
        ];

        $total = $this->voidModel->getAllTotal($filter);

        $output = [
            'total' => $total
        ];

        return json_encode($output);
    }

    public function table(string $custTypeId, Request $request){
        $this->roleAccess();
        $data = [];
        $no = $request->input('start');
        $search = $request->input('search')['value'];
        $filterTanggal = $this->dateFilterFormat($request->input('filterTanggal'));
        $filterPayment = $request->input('filterPayment');
        $filter = [
            "filterTanggal" => $filterTanggal,
            "filterPayment" => $filterPayment,
            "custTypeId" => $custTypeId
        ];
        $lists = $this->voidModel->getDT($request, $search, $filter);

        $totalAll = 0;
        foreach ($lists as $list) {
            $msTrackId = $this->voidModel->formatLinkTrackingMsTrack($list->ms_track_id,$list->mode,$list->inv_add);
            $rankCreated = DB::table('users')->where("username",$list->created_by)->value("rank");
            $rankVoid = DB::table('users')->where("username",$list->void_by)->value("rank");
            $rankCreatedLabel = $rankCreated != null ? "<div class='bg-mismass' style='padding:1px 5px;'>".$rankCreated."</div>" : "";
            $rankVoidLabel = $rankVoid != null ? "<div class='bg-mismass' style='padding:1px 5px;'>".$rankVoid."</div>" : "";
            $fullName = $custTypeId=="IND" ? "<div class='fw-bold'>".$list->cons_first_name . " " . $list->cons_middle_name . " " . $list->cons_last_name."</div>" : "<div class='fw-bold'>".$list->sender_first_name . " " . $list->sender_middle_name . " " . $list->sender_last_name."</div>";
            $detailAddr = $custTypeId=="IND" ? "<div>".$list->cons_phone."</div><div>".$list->cons_city . ", " . $list->cons_prov . ", " . $list->cons_postal_code."</div>" : "<div>".$list->sender_phone."</div><div>".$list->sender_city . ", " . $list->sender_prov . ", " . $list->sender_postal_code."</div>";

            $no++;
            $row = [];
            $row[] = $no;
            $row[] = $msTrackId;
            $row[] = "<div class='fw-bold'>" . $list->mismass_invoice_id . "</div><div>" . $this->dateFormatIndo($list->mismass_invoice_date,1) . "</div>";
            $row[] = "<div class='fw-bold'>" . $list->created_by . "</div>".$rankCreatedLabel."<div>" . $this->dateFormatIndo($list->created_at,2) . "</div>";
            $row[] = "<div class='fw-bold'>" . $list->void_by . "</div>".$rankVoidLabel."<div>" . $this->dateFormatIndo($list->void_at,2) . "</div>";
            $row[] = $fullName.$detailAddr;
            $row[] = "<div class='fw-bold'>".$list->payment."</div><div>".$this->rupiah($list->totalSubTotal)."</div>";
            $row[] = $list->note;
            $row[] = "<a target='_blank' href='".url('/void/history')."/".$this->invOnlyId($list->mismass_invoice_id)."'><i class='fas fa-eye'></i></a>";
            $data[] = $row;

            $totalAll += $list->totalSubTotal;
        }

        $output = [
            'draw' => $request->input('draw'),
            'recordsTotal' => $this->voidModel->countAll(),
            'recordsFiltered' => $this->voidModel->countFiltered($request, $search, $filter),
            'totalAll' => $this->voidModel->getAllTotal($request, $search, $filter),
            'data' => $data
        ];

        return json_encode($output);
    }
}
