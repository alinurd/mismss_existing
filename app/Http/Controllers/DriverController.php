<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Image\Image;

class DriverController extends Controller
{
    public function loginDriver(){
        return view('driver.login');
    }

    public function authDriver(Request $request)
    {
        $data = [
            'username' => $request->input('username'),
            'password' => $this->saltThis($request->input('password')),
        ];

        $remember = $request->input('remember') == "on" ? true : false;

        if (!Auth::attempt($data, $remember)) {
            return redirect('/d/l')->with('status', 'errors');
        }

        if(Auth::user()->role_id!="876384"){
            return redirect('/d/l')->with('status', 'errors');
        }

        return redirect('/d/h');
    }

    public function index()
    {
        if(!Auth::check()){
            return redirect('/d/l');
        }

        if(Auth::user()->role_id!="876384"){
            return redirect('/d/l');
        }

        $data['user'] = Auth::user()->username;

        return view('driver.index',$data);
    }

    public function list(){

        if(!Auth::check()){
            return redirect('/d/l')->with('status', 'errors');
        }

        if(Auth::user()->role_id!="876384"){
            return redirect('/d/l');
        }

        return view('driver.list');
    }

    public function updateStatus(Request $request){
        $id = $request->input("endId");
        // $resi = $request->input("resi");
        $status = $request->input("endStatus");
        $username = Auth::user()->username;
        $date = date("Y-m-d H:i:s");
        $trackStatusId = $request->input("trackStatusId");
        $receiver = $request->input("receiver");
        $reason = $request->input("reason");
        $linkImg = "-";
        $getDataList = DB::table("data_list")->select("ms_track_id","shipping_number","cust_type_id")->where("id",$id)->first();
        // dd($getDataList);
        $resi = $getDataList->cust_type_id=="IND" ? ($getDataList->ms_track_id!="" ? $getDataList->ms_track_id : $getDataList->shipping_number ) : $getDataList->shipping_number;
        $whereDataList = $getDataList->cust_type_id=="IND"?($getDataList->ms_track_id!=""?"ms_track_id='$getDataList->ms_track_id'":"shipping_number='$getDataList->shipping_number'"):"shipping_number='$getDataList->shipping_number'";
        
        if($status == "ongoing"){
            if($trackStatusId==22||$trackStatusId==13){
                $detail = "[".$receiver."]";
                $fullImg = "";
                $linkImg = "-";
                if($request->file('buktiFoto')!=null){
                    $linkImg = "";
                    // for($i=0;$i<count($request->file('buktiFoto'));$i++){
                    $imgName = $this->generateRandomString(25);
                    $ext = $request->file('buktiFoto')[0]->extension();
                    $fullImg = $imgName.".".$ext;
                    $def = Image::load($request->file('buktiFoto')[0]->path())
                            ->optimize()
                            ->save(public_path('assets/pod/').$fullImg);
                    DB::table('shiptrip_pod_image_list')->insert([
                        'id' => $imgName,
                        'ext' => $ext,
                        'shipping_number' => $resi,
                    ]);
                    $linkImg = "<a target='_blank' href='".url('/assets/pod')."/".$fullImg."'>Bukti Foto</a>, ";
                    $detail .= "<br><a target='_blank' href='".url('/assets/pod')."/".$fullImg."'>Lihat bukti penerimaan</a>";
                }
    
                $text = DB::table('shiptrip_track_status')->where('id',$trackStatusId)->value('value');
                $data = [
                    "track_man_created_at" => $date,
                    "track_status_id" => $trackStatusId,
                    "shipping_updated_at" => $date,
                    "shipping_updated_by" => $username,
                    "shipping_success_by" => $username,
                    "shipping_success_at" => $date,
                    "shipping_success_receiver" => $receiver,
                    "shipping_success_pod" => $fullImg,
                    "shipping_status" => $text." ".$detail
                ];
    
                $codename = "DS";
                $title = "Driver Sukses Kirim";
            }else{
                $detail = "[".$reason."]";
                $fullImg = "";
                $linkImg = "-";    
                $text = DB::table('shiptrip_track_status')->where('id',$trackStatusId)->value('value');
                $data = [
                    "track_man_created_at" => $date,
                    "track_status_id" => $trackStatusId,
                    "shipping_updated_at" => $date,
                    "shipping_updated_by" => $username,
                    "shipping_status" => $text." ".$detail
                ];
    
                $codename = "DO";
                $title = "Driver Update Status";
            }
        }else{
            $trackStatusId = 15;
            $detail = "";
            $text = DB::table('shiptrip_track_status')->where('id',$trackStatusId)->value('value');
            $data = [
                "shipping_status" => $text." ".$detail,
                "shipping_updated_at" => $date,
                "shipping_updated_by" => $username,
                "track_status_id" => $trackStatusId,
                "track_man_created_at" => $date,
                "forwarder_name" => $username
            ];

            $codename = "DO";
            $title = "Driver Proses Kirim";
        }

        $update = DB::table("data_list")->whereRaw($whereDataList)->update($data);

        if(!$update){
            $encode = array("status" => 503, "text" => "Gagal Update Resi");
            return json_encode($encode);
        }

        if($getDataList->ms_track_id!=""){
            $insertTrack = DB::table('shiptrip_track_list')->insert([
                'created_at' => $date,
                'created_by' => $username,
                'ms_track_id' => $resi,
                'track_status_id' => $trackStatusId,
                "track_status_manual_id" => "A", 
                'text' => $text." ".$detail,
            ]);
        }

        $getDataForHistory = DB::table('data_list')
                            ->selectRaw(
                                "to_sg_man_created_at,
                                CONCAT_WS(' - ',forwarder_id,forwarder_name) AS full_forwarder,
                                ms_track_id,
                                shipping_number,
                                cons_phone,
                                CONCAT_WS(' ',cons_first_name,cons_middle_name,cons_last_name) AS full_name,
                                CONCAT_WS(', ',cons_address,cons_sub_district,cons_district,cons_city,cons_prov,cons_postal_code) AS full_address"
                            )
                            ->whereRaw($whereDataList)
                            ->first();
        $dataHistory=[
            "codename" => $codename,
            "created_at" => date("Y-m-d H:i:s"),
            "created_by" => $username,
            "description" => "<b>".$title."</b> dengan detail,<br><br>
            Tgl Shipment : <b>".$this->dateFormatIndo($getDataForHistory->to_sg_man_created_at,1)."</b><br>
            Pengiriman : <b>".$getDataForHistory->full_forwarder."</b><br>
            Resi Utama : <b>".($getDataForHistory->ms_track_id ?? "-")."</b><br>
            Resi Lokal : <b>".$getDataForHistory->shipping_number."</b><br>
            Nama : <b>".$getDataForHistory->full_name."</b><br>
            Telpon :<b>".$getDataForHistory->cons_phone."</b><br>
            Alamat : <b>".$getDataForHistory->full_address."</b><br>
            Status Update : <b>".$text." ".$detail."</b><br>
            Bukti Foto : <b>".$linkImg."</b><br>",
        ];
        $insertHistory = DB::table('history_list')->insert($dataHistory);
        if(!$insertHistory){
            $encode = array("status" => "405", "header" => "Gagal", "text" => "Gagal Buat History");
            return json_encode($encode);
        }

        $encode = array("status" => 200, "text" => "Berhasil Update Resi");
        return json_encode($encode);
    }
    
    public function qrCodeChecking(Request $request){
        $resi = $request->input("resi");
        $username = Auth::user()->username;
        $cek = DB::table("data_list")->where("shipping_number","LIKE","%".$resi."%")->first();
        
        if($cek==null){
            $encode = array("status" => 404, "text" => "Data Tidak Ditemukan");
            return json_encode($encode);
        }
        
        if($cek->forwarder_id!="MISMASS"){
            $encode = array("status" => 400, "text" => "Resi Dikirim oleh Vendor");
            return json_encode($encode);
        }
        
        if($cek->forwarder_id=="MISMASS"&&$cek->shipping_status=="SUKSES"){
            $encode = array("status" => 400, "text" => "Status Resi Sudah Sukses");
            return json_encode($encode);
        }
        
        if($cek->forwarder_id=="MISMASS"&&$cek->forwarder_name!=""){
            if($cek->forwarder_name!=$username){
                $encode = array("status" => 400, "text" => "Kurir Tidak Sama");
                return json_encode($encode);
            }
            $encode = array("status" => 200, "id" => $cek->id, "tab" => "ongoing", "text" => "Apakah Anda Yakin Akan Menyelesaikan Resi ".$cek->shipping_number." ?");
            return json_encode($encode);
        }
        
        $encode = array("status" => 200, "id" => $cek->id, "tab" => "shipment", "text" => "Apakah Anda Yakin Akan Memproses Resi ".$cek->shipping_number." ?");
        return json_encode($encode);
        
    }

    public function table(Request $request){

        if(!Auth::check()){
            return redirect('/d/l')->with('status', 'errors');
        }

        if(Auth::user()->role_id!="876384"){
            return redirect('/d/l');
        }

        $status = $request->input("status");
        $offset = $request->input("offset");
        $perload = $request->input("perload");
        $search = $request->input("searchText");
        $driver = Auth::user()->username;

        $arraySearch = [
            "shipping_number",
            "cust_type_id",
            "cons_first_name",
            "cons_middle_name",
            "cons_last_name",
            "cons_address",
            "cons_sub_district",
            "cons_district",
            "cons_city",
            "cons_prov",
            "cons_postal_code",
            "cons_phone",
        ];

        $where = "";
        $groupBy = "shipping_number";
        if($status=="shipment"){
            $query = "ms_track_id!='' AND forwarder_id='MISMASS' AND forwarder_name='' AND shipping_success_by='' AND updated_at>'2025-02-07 00:00:00'";
            $groupBy = "mismass_invoice_id";
        }elseif($status=="ongoing"){
            $query = "ms_track_id!='' AND forwarder_id='MISMASS' AND forwarder_name='$driver' AND shipping_success_by='' AND updated_at>'2025-02-07 00:00:00'";
        }else{
            $query = "ms_track_id!='' AND forwarder_id='MISMASS' AND forwarder_name='$driver' AND shipping_success_by!='' AND updated_at>'2025-02-07 00:00:00'";
        }

        if (!empty($search)) {
            for ($i = 0; $i <= count($arraySearch) - 1; $i++) {
                if ($i < count($arraySearch) - 1) {
                    $where .= $query . "AND $arraySearch[$i] LIKE '%$search%' OR ";
                } else {
                    $where .= $query . "AND $arraySearch[$i] LIKE '%$search%'";
                }
            }
        }else{
            $where = $query;
        }

        $data = DB::table("data_list")
                ->whereRaw($where)
                ->skip($offset)
                ->take($perload)
                ->orderBy("shipping_updated_at","DESC")
                ->groupBy($groupBy)
                ->get();

        $output = [
            'totalData' => count($data),
            'data' => $data
        ];

        return json_encode($output);
    }

    public function export(){

        if(!Auth::check()){
            return redirect('/d/l')->with('status', 'errors');
        }

        if(Auth::user()->role_id!="876384"){
            return redirect('/d/l');
        }

        return view('driver.export');
    }

    public function qrcode(){

        if(!Auth::check()){
            return redirect('/d/l')->with('status', 'errors');
        }

        if(Auth::user()->role_id!="876384"){
            return redirect('/d/l');
        }

        return view('driver.qrcode');
    }
}
