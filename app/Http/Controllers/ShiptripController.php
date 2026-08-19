<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Warehouse;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Endpoint;
use App\Models\ShipTrip;
use App\Models\TrackSystem;
use App\Models\Whatsapp;
use Spatie\Image\Image;
use App\Mail\SendMail;
use App\Services\DeleteShipmentService;
use Illuminate\Support\Facades\Mail;

class ShiptripController extends Controller
{

    private $deleteShipmentService,$orderModel,$wareModel,$servModel,$shipTripModel,$trackSystemModel,$whatsappModel;

    public function __construct()
    {
        $this->orderModel = new Order;
        $this->wareModel = new Warehouse;
        $this->custModel = new Customer;
        $this->servModel = new Service;
        $this->shipTripModel = new Shiptrip;
        $this->trackSystemModel = new TrackSystem;
        $this->endpointModel = new Endpoint;
        $this->whatsappModel = new Whatsapp;
        $this->deleteShipmentService = new DeleteShipmentService;
    }

    public function newShipPage()
    {
        if(!env('NEW_SHIP')){
            if(Auth::user()->username!="dev8th"){
                return view('pages.maintenance');
            }
        }
        $this->roleAccess();
        $data['warehouse'] = $this->wareModel::all();
        $data['kn'] = DB::table("country_phone_codes")->orderBy("name","asc")->get();
        return view('pages.newship', $data);
    }

    public function shipTripPage()
    {
        if(!env('SHIP_TRIP')){
            if(Auth::user()->username!="dev8th"){
                return view('pages.maintenance');
            }
        }
        $this->roleAccess();
        $data['warehouse'] = $this->wareModel::all();
        $data['service'] = $this->servModel::all();
        $data['additional'] = DB::table("additional_list")->orderBy("order_byid", "asc")->get();
        $data['template'] = DB::table("template_list")->get();
        $data['listStatusManual'] = DB::table("shiptrip_track_status_manual")->where("id","!=","A")->get();
        $data['listStatusSkip'] = DB::table("shiptrip_track_status_skip")->get();
        return view("pages.shiptrip", $data);
    }

    public function createTrackId(Request $request)
    {
        
        //Initialize
        $switch = true;

        //Create And Check Track ID
        while($switch){
            $trackId = "";
            $id = $this->generateRandomInt();
            $wareId = $request->input('id');
            $trackId = "MMS".$id.$wareId;

            $check = DB::table('shiptrip_list')->whereRaw("ms_track_id='$trackId'")->first();

            if($check==null){
                $switch = false;
            }
        }

        //Return Fix Track ID
        $encode = array("status" => 200, "title" => "SUCCESS", "id" => $trackId);
        return json_encode($encode);
    }

    public function checkPrimaryTrack(Request $request)
    {
        $id = $request->input("id");
        $getData = DB::table("shiptrip_list")->where("shiptrip_list.ms_track_id",$id)->get();
        $check = DB::table("shiptrip_list")
                ->selectRaw("service_list.warehouse_id AS wareid,service_list.id AS servid,shiptrip_list.cust_id,shiptrip_list.track_type_id,shiptrip_list.track_group_type_id")
                ->join("service_list", "service_list.id", "=", "shiptrip_list.service_id")
                ->where("shiptrip_list.ms_track_id",$id)->first();

        if(count($getData)==0){
            $encode = array("status" => false, "secTrackId"=>"", "message" => "Track Id Tidak Ditemukan");
            return json_encode($encode);
        }

        if($check->track_type_id!="PRM"){
            $encode = array("status" => false, "secTrackId"=>"", "message" => "Bukan Track Id Primary");
            return json_encode($encode);
        }

        if($check->track_group_type_id!="DEF"){
            $encode = array("status" => false, "secTrackId"=>"", "message" => "Track Id Telah Tergabung");
            return json_encode($encode);
        }

        $length = count(DB::table("shiptrip_secondary_list")->where("ms_track_id",$id)->get());
        $idSec = $length+1;

        $getFirst = DB::table("cust_list")->where("id",$check->cust_id)->first();
        $getSecond = DB::table("shiptrip_cust_list")->where("id",$id)->first();

        $encode = array(
            "status" => true, 
            "secTrackId"=>$id.$idSec, 
            "wareId"=> $check->wareid, 
            "servId"=> $check->servid, 
            "cust"=> array(
                "id" => $check->cust_id,
                "typeId" => $getFirst->cust_type_id,
                "firstName" => $getFirst->first_name,
                "middleName" => $getFirst->middle_name,
                "lastName" => $getFirst->last_name,
                "kodeNegara" => $getFirst->country_id,
                "phone" => $getFirst->phone,
                "email" => $getFirst->email,
                "address" => $getFirst->address,
                "subDistrict" => $getFirst->sub_district,
                "district" => $getFirst->district,
                "city" => $getFirst->city,
                "prov" => $getFirst->prov,
                "postalCode" => $getFirst->postal_code
            ), 
            "secondName" => $getSecond->first_name,
            "secondKodeNegara" => $getSecond->country_id,
            "secondPhone" => $getSecond->phone,
            "message" => "");

        return json_encode($encode);

    }

    public function checkResiLn(Request $request)
    {
        $id = $request->input("id");
        $edit = $request->input("edit");
        $check = DB::table("shiptrip_foreign_track_list")->where("id",$id)->first();
        if($check!=null){
            if($edit==""){
                $encode = array("status" => false, "message" => "Resi LN Telah Diinput");
                return json_encode($encode);
            }else{
                $check2 = DB::table("shiptrip_foreign_track_list")->where("id",$id)->where("ms_track_id",$edit)->first();
                if($check2==null){
                    $encode = array("status" => false, "message" => "Resi LN Telah Diinput");
                    return json_encode($encode);
                }
            }
        }

        $encode = array("status" => true, "message" => "");
        return json_encode($encode);
    }
    
    // public function checkForeignTrack(Request $request)
    // {
    //     $id   = $request->id;
    //     $edit = $request->edit;
    
    //     $query = DB::table('shiptrip_foreign_track_list')
    //         ->where('id', $id);
    
    //     // Jika mode edit → abaikan data milik dirinya sendiri
    //     if (!empty($edit)) {
    //         $query->where('ms_track_id', '!=', $edit);
    //     }
    
    //     // exists() jauh lebih ringan daripada first()
    //     $exists = $query->exists();
    
    //     // remote validator:
    //     // true  = valid
    //     // false = tidak valid
    //     return response()->json(!$exists);
    // }


    public function checkForeignTrack(Request $request)
    {
        $id = $request->input("id");
        $edit = $request->input("edit");
        $check = DB::table('shiptrip_foreign_track_list')->where("id",$id)->first();

        if($check!=null){
            if($edit==""){
                $encode = array("status" => false, "message" => "Resi LN Duplikat");
                return json_encode($encode);
            }else{
                $check2 = DB::table("shiptrip_foreign_track_list")->where("id",$id)->where("ms_track_id",$edit)->first();
                if($check2==null){
                    $encode = array("status" => false, "message" => "Resi LN Duplikat");
                    return json_encode($encode);
                }
            }
        }

        $encode = array("status" => true, "message" => "Resi LN Duplikat");
        return json_encode($encode);
    }

    public function checkShipmentDate(Request $request)
    {
        $id = $request->input("id");
        $date = date("Y-m-d H:i:s",strtotime($this->dateFilterFormat($request->input("date"))[0]));

        //check If id = invoice id
        $getDataList = DB::table("data_list")->where("mismass_invoice_id",$id)->first();
        if($getDataList!=null){
            $getDataOrder = DB::table("order_list")->select("ms_track_id")->where("invoice_id",$id)->first();
            $id = $getDataOrder->ms_track_id;
        }

        if($id!=""){
            $get = DB::table("shiptrip_track_list")->selectRaw("created_at")->where("ms_track_id",$id)->orderBy("created_at","DESC")->first();

            if($get==null){
                $encode = array("status" => false, "message" => "Track ID Tidak Ditemukan");
                return json_encode($encode);
            }

            if(strtotime($date)-strtotime($get->created_at)<=0){
                $message = "Tanggal Shipment Tidak Sinkron. Harus Melebihi ".$this->dateFormatIndo($get->created_at,4);
                $encode = array("status" => false, "message" => $message);
                return json_encode($encode);
            }
        }

        $encode = array("status" => true, "message" => "");
        return json_encode($encode);
    }

    public function checkShipmentDateNow(Request $request)
    {
        $date = date("Y-m-d",strtotime($this->dateFilterFormat($request->input("date"))[0]));
        $dateNow = date("Y-m-d");

        if(strtotime($dateNow)-strtotime($date)<0){
            $encode = array("status" => false, "message" => "Melebihi Tanggal Saat Ini");
            return json_encode($encode);
        }

        $encode = array("status" => true, "message" => "");
        return json_encode($encode);
    }

    public function checkShipmentDateHourNow(Request $request)
    {
        $date = date("Y-m-d H:i:s",strtotime($this->dateFilterFormat($request->input("date"))[0]));
        $dateNow = date("Y-m-d H:i:s");

        if(strtotime($dateNow)-strtotime($date)<0){
            $encode = array("status" => false, "message" => "Melebihi Tanggal & Waktu Saat Ini");
            return json_encode($encode);
        }

        $encode = array("status" => true, "message" => "");
        return json_encode($encode);
    }

    public function createShipment(Request $request)
    {
        // ======================
        // INITIALIZE
        // ======================
        $username = Auth::user()->username;
        $custId = $request->input("regCust");
        $custTypeId = $request->input('custTypeId');        
        $autoCreatedAt = now();
        $wareId = $request->input('warehouse');
        $servId = $request->input('service');
        $trackTypeId = $request->input('formatResi');
        $anchorTrack = $request->input('anchorTrack');
        $trackGroupTypeId = "DEF";
        $trackStatusId = 1;
    
        $tanggalDrop = date("Y-m-d", strtotime(
            $this->dateFilterFormat($request->input("tanggalDrop"))[0]
        ));
    
        $trackId = $request->input('trackId');
        $note = $request->input('catatan') ?? "";
        $resiLn = "";
        $linkImg = "";
        $templateWaBlast = 0;
    
        try {
            $result = DB::transaction(function () use (
                $request,
                $trackId,
                $username,
                $autoCreatedAt,
                $custTypeId,
                $servId,
                $trackTypeId,
                $trackGroupTypeId,
                $anchorTrack,
                $tanggalDrop,
                $note,
                $wareId,
                $trackStatusId,
                &$templateWaBlast,
                &$custId,
                &$resiLn,
                &$linkImg
            ){
                // ======================
                // CHECK WA BLAST
                // ======================
                $checkWaBlast = $this->whatsappModel->checkTimeBlast([
                    $trackId,
                    $username,
                    "SH",
                ]);
    
                if (!$checkWaBlast['status']) {
                    throw new \Exception(json_encode([
                        "status" => 500,
                        "title" => "Gagal",
                        "message" => "Menunggu Waktu Whatsapp Blast",
                        "queuetime" => $checkWaBlast['queueTime'],
                        "createdtime" => $checkWaBlast['createdTime'],
                        "now" => $checkWaBlast['now']
                    ]));
                }
    
                $templateWaBlast = $checkWaBlast['waTemplateId'];
    
                // ======================
                // CREATE CUSTOMER (IF NEW)
                // ======================
                if ($request->input('statusCust') != "REG") {
    
                    $custId = DB::table("cust_list")->insertGetId([
                        "first_name" => $request->input('firstName'),
                        "middle_name" => $request->input('middleName') ?? "",
                        "last_name" => $request->input('lastName') ?? "",
                        "country_id" => $request->input('kodeNegara'),
                        "phone" => $request->input('phone'),
                        "email" => $request->input('email'),
                        "address" => $request->input('address'),
                        "sub_district" => $request->input('subDistrict') ?? "",
                        "district" => $request->input('district'),
                        "city" => $request->input('city'),
                        "prov" => $request->input('prov'),
                        "postal_code" => $request->input('postalCode'),
                        "cust_type_id" => $custTypeId,
                        "created_by" => $username,
                        "updated_by" => $username,
                        "know_from_id" => 1,
                        "created_at" => $autoCreatedAt,
                        "updated_at" => $autoCreatedAt,
                    ]);
    
                    DB::table('history_list')->insert([
                        "codename" => "BC",
                        "created_at" => $autoCreatedAt,
                        "created_by" => $username,
                        "description" => "<b>Buat Customer/Client</b> dengan detail,<br><br>
                                            Tipe : <b>".DB::table("cust_type_list")->where("id", $custTypeId)->value("name")."</b><br>
                                            First Name : <b>".$request->input('firstName')."</b><br>
                                            Middle Name : <b>".$request->input('middleName')."</b><br>
                                            Last Name : <b>".$request->input('lastName')."</b><br>
                                            Telpon : <b>+".DB::table("country_phone_codes")->where("id",$request->input("kodeNegara"))->value("code").$request->input('phone')."</b><br>
                                            Email : <b>".$request->input('email')."</b><br>
                                            Alamat : <b>".$request->input('address').", ".$request->input('subDistrict').", ".$request->input('district').", ".$request->input('city').", ".$request->input('prov').", ".$request->input('postalCode')."</b>",
                    ]);
                }
    
                // ======================
                // INSERT SHIPMENT
                // ======================
                DB::table('shiptrip_list')->insert([
                    'cust_id' => $custId,
                    'ms_track_id' => $trackId,
                    'track_type_id' => $trackTypeId,
                    'track_group_type_id' => $trackGroupTypeId,
                    'service_id' => $servId,
                    'track_status_id' => $trackStatusId,
                    "edited_at" => $autoCreatedAt,
                    "edited_by" => $username,
                    'drop_created_at' => $autoCreatedAt,
                    'drop_man_created_at' => $tanggalDrop." 00:00:01",
                    'drop_created_by' => $username,
                    'drop_updated_at' => $autoCreatedAt,
                    'drop_updated_by' => $username,
                    'note' => $note
                ]);
    
                // ======================
                // SECONDARY TRACK
                // ======================
                if ($trackTypeId == "SEC") {
                    DB::table('shiptrip_secondary_list')->insert([
                        'id' => $trackId,
                        'ms_track_id' => $anchorTrack,
                    ]);
                }
    
                // ======================
                // CONSIGNEE
                // ======================
                DB::table('shiptrip_cust_list')->insert([
                    'id' => $trackId,
                    'first_name' => $request->input('secondName') ?? "",
                    'middle_name' => "",
                    'last_name' => "",
                    'country_id' => $request->input('secondKodeNegara') ?? $request->input('kodeNegara'),
                    'phone' => $request->input('secondPhone') ?? "",
                    'email' => "",
                    'address' => "",
                    'sub_district' => "",
                    'district' => "",
                    'city' => "",
                    'prov' => "",
                    'postal_code' => ""
                ]);
    
                // ======================
                // IMAGE
                // ======================
                $linkImg = "";
                if ($request->hasFile('file')) {
                    foreach ($request->file('file') as $i => $file) {
                        $imgName = $this->generateRandomString(25);
                        $ext = $file->extension();
                        $fullImg = $imgName.".".$ext;
                        // $file->move(public_path('assets/photos'), $imgName.".".$ext);
                        Image::load($file->path())
                        ->optimize()
                        ->save(public_path('assets/photos/').$fullImg);
    
                        DB::table('shiptrip_image_list')->insert([
                            'id' => $imgName,
                            'ext' => $ext,
                            'ms_track_id' => $trackId,
                        ]);
    
                        $linkImg .= "<a target='_blank' href='".url('/assets/photos')."/".$fullImg."'>Foto".($i+1)."</a>, ";
                    }
                }
    
                // ======================
                // FOREIGN TRACKING
                // ======================
                $resiLn = "";
                foreach ($request->input('resiln', []) as $resi) {
                    DB::table('shiptrip_foreign_track_list')->insert([
                        'id' => $resi,
                        'ms_track_id' => $trackId,
                    ]);
                    $resiLn .= $resi.", ";
                }
    
                // ======================
                // TRACK STATUS
                // ======================
                DB::table('shiptrip_track_list')->insert([
                    'created_at' => $tanggalDrop." 00:00:00",
                    'created_by' => $username,
                    'ms_track_id' => $trackId,
                    'track_status_id' => 0,
                    'track_status_manual_id' => "A",
                    'text' => DB::table('shiptrip_track_status')->where('id',0)->value('value')
                ]);
    
                DB::table('shiptrip_track_list')->insert([
                    'created_at' => $tanggalDrop." 00:00:01",
                    'created_by' => $username,
                    'ms_track_id' => $trackId,
                    'track_status_id' => 1,
                    'track_status_manual_id' => "A",
                    'text' => DB::table('shiptrip_track_status')->where('id',1)->value('value')
                ]);
    
                return true;
            });
    
             //Send WA
            $getDataCust = DB::table("cust_list")->where("id",$custId)->first();
            $phoneFix = "+".DB::table("country_phone_codes")->where("id",$getDataCust->country_id)->value("code").$getDataCust->phone;
            $data = array(
                "phone" => $phoneFix,
                "fullName" => $getDataCust->first_name." ".$getDataCust->middle_name." ".$getDataCust->last_name,
                "shipmentNumber" => $trackId,
                "reference" => DB::table("cust_list")->where("id",$custId)->value("reference"),
                "templateId" => $templateWaBlast
            );
    
            $descSendWa = "-";
            if(env('WA_GATEWAY')){
                $resultSendWa = $this->whatsappModel->createShipmentSendWA($data);
                $statusSendWA = $resultSendWa->success ? "Terkirim" : "Gagal Kirim";
                $refSendWA = $resultSendWa->ref;
                $msgSendWA = $resultSendWa->message;
                $phoneSendWa = "(".$resultSendWa->phone.")";
                $descSendWa = "<b>".$statusSendWA." (".$refSendWA.")</b> - ".$msgSendWA." ".$phoneSendWa."<br>";
            }
    
            //Send Email
            if(env('SEND_EMAIL')){
                $ccEmail = explode(",",env('MAIL_CC'));
                $bccEmail = env('MAIL_BCC');
                $email = $getDataCust->email;
                $data['subject'] = "Create Shipment | ".$trackId;
                $data['modes'] = "CSHI"; //Webform = "WEB", create invoice = "CINV", create shipment = "CSHI"
                $sendingMail = Mail::to($email)
                                ->cc($ccEmail)
                                ->bcc($bccEmail)
                                ->send(new SendMail($data));
            }
    
            //history
            $wareData = DB::table('warehouse_list')->select('id','location')->where('id',$wareId)->first();
            $servName = DB::table('service_list')->where('id',$request->input('service'))->value('name');
            $dataHistory=[
                "codename" => "SH",
                "created_at" => $autoCreatedAt,
                "created_by" => $username,
                "whatsapp_desc" => "Send Whatsapp Status : ".$descSendWa,
                "description" => "<b>Create Shipment</b> dengan detail,<br><br>
                Tgl Drop : <b>".$this->dateFormatIndo($this->dateFilterFormat($request->input("tanggalDrop"))[0],2)."</b><br>
                Resi Tracking : <b>".$trackId."</b><br>
                Warehouse : <b>".$wareData->id." - ".$wareData->location."</b><br>
                Service : <b>".$servName."</b><br>
                Name : <b>".$custId."</b><br>
                Resi Luar Negeri : <b>".$resiLn."</b><br>
                Bukti Foto : <b>".$linkImg."</b><br>
                Send Whatsapp Status : ".$descSendWa
            ];
            $insertHistory = DB::table('history_list')->insert($dataHistory);
    
            return response()->json([
                "status" => 200,
                "title" => "Berhasil",
                "message" => "Pembuatan Shipment Baru Sukses!"
            ]);
    
        } catch (\Exception $e) {
    
            return response($e->getMessage(), 500)
            ->header('Content-Type', 'application/json');
        }
    }

    public function createShipmentSecondary(Request $request)
    {
        $username = Auth::user()->username;
        $trackId = $request->input("trackId");
        $resiLn = $request->input("resiLn");
        $autoCreatedAt = date("Y-m-d H:i:s");

        //Check Resi LN
        $checkResiLN = DB::table("shiptrip_foreign_track_list")->where("id",$resiLn)->first();
        if($checkResiLN!=null){
            if($checkResiLN->ms_track_id!=$trackId){
                $encode =  array("status" => 500, "title" => "Gagal", "message" => "Resi LN Duplikat!");
                return json_encode($encode);
            }
            $delete = DB::table("shiptrip_foreign_track_list")->where("id",$resiLn)->delete();
        }

        //Check Track Id
        $getTrackData = DB::table("shiptrip_list")
                        ->select(
                            "track_type_id",
                            "service_id",
                            "cust_id",
                            "drop_man_created_at",
                        )
                        ->where("ms_track_id",$trackId)
                        ->first();

        if($getTrackData==null){
            $encode =  array("status" => 500, "title" => "Gagal", "message" => "Resi Primary Tidak Ada!");
            return json_encode($encode);
        }

        $trackTypeId = $getTrackData->track_type_id;
        $servId = $getTrackData->service_id;
        $custId = $getTrackData->cust_id;
        $dropManCreatedAt = $getTrackData->drop_man_created_at;
        $primaryTrack = $trackId;

        if($trackTypeId=="SEC"){
            $primaryTrack = DB::table("shiptrip_secondary_list")->where("id",$trackId)->value("ms_track_id");

            $getTrackData2 = DB::table("shiptrip_list")
                        ->select(
                            "service_id",
                            "cust_id",
                            "drop_man_created_at",
                        )
                        ->where("ms_track_id",$primaryTrack)
                        ->first();

            $servId = $getTrackData2->service_id;
            $custId = $getTrackData2->cust_id;
            $dropManCreatedAt = $getTrackData2->drop_man_created_at;
        }

        $length = count(DB::table("shiptrip_secondary_list")->where("ms_track_id",$primaryTrack)->get());
        $idSec = $length+1;
        $secondaryTrackId = $primaryTrack.$idSec;

        //Shiptrip List
        $insertShipTrip = DB::table('shiptrip_list')->insert([
            'cust_id' => $custId,
            'ms_track_id' => $secondaryTrackId,
            'track_type_id' => "SEC",
            'track_group_type_id' => "DEF",
            'service_id' => $servId,
            'track_status_id' => 2,
            'edited_at' => $autoCreatedAt,
            'edited_by' => $username,
            'drop_created_at' => $autoCreatedAt,
            'drop_man_created_at' => $dropManCreatedAt,
            'drop_created_by' => $username,
            'drop_updated_at' => $autoCreatedAt,
            'drop_updated_by' => $username,
        ]);
        if(!$insertShipTrip){
            $encode = array("status" => 500, "title" => "Gagal", "message" => "Input Data Shipment List Gagal!");
            return json_encode($encode);
        }

        //Resi LN
        $dataInsertResiLN = [
            "id" => $resiLn,
            "ms_track_id" => $secondaryTrackId
        ];
        $insertResiLN = DB::table("shiptrip_foreign_track_list")->insert($dataInsertResiLN);
        if(!$insertResiLN){
            $encode =  array("status" => 500, "title" => "Gagal", "message" => "Input Resi LN Gagal!");
            return json_encode($encode);
        }

        //Secondary List
        $dataInsertSecondaryList = [
            "id" => $secondaryTrackId,
            "ms_track_id" => $primaryTrack
        ];
        $insertSecondaryList = DB::table("shiptrip_secondary_list")->insert($dataInsertSecondaryList);
        if(!$insertSecondaryList){
            $encode =  array("status" => 500, "title" => "Gagal", "message" => "Input Secondary List Gagal!");
            return json_encode($encode);
        }

        //Tracking
        $wareId = DB::table('service_list')->where("id",$servId)->value('warehouse_id');
        $wareLoc = DB::table('warehouse_list')->where('id',$wareId)->value('location');
        $text = DB::table('shiptrip_track_status')->where('id',0)->value('value');
        $insertTracking = DB::table('shiptrip_track_list')->insert([
            'created_at' => date("Y-m-d",strtotime($dropManCreatedAt))." 00:00:00",
            'created_by' => $username,
            'ms_track_id' => $secondaryTrackId,
            'track_status_id' => 0,
            "track_status_manual_id" => "A", 
            'text' => $text,
        ]);
        $text2 = DB::table('shiptrip_track_status')->where('id',1)->value('value');
        $insertTracking2 = DB::table('shiptrip_track_list')->insert([
            'created_at' => date("Y-m-d",strtotime($dropManCreatedAt))." 00:00:01",
            'created_by' => $username,
            'ms_track_id' => $secondaryTrackId,
            'track_status_id' => 1,
            "track_status_manual_id" => "A", 
            'text' => $text2,
        ]);
        // if(!$insertTracking){
        //     $encode = array("status" => 500, "title" => "Gagal", "message" => "Input Data Shipment Tracking Gagal!");
        //     return json_encode($encode);
        // }

        //Cust
        $getDataCust = DB::table("shiptrip_cust_list")->where("id",$primaryTrack)->first();
        $insertCust = DB::table('shiptrip_cust_list')->insert([
            'id' => $secondaryTrackId,
            'country_id' => $getDataCust->country_id,
            'first_name' => $getDataCust->first_name,
            'middle_name' => $getDataCust->middle_name,
            'last_name' => $getDataCust->last_name,
            'phone' => $getDataCust->phone,
            'email' => $getDataCust->email,
            'address' => $getDataCust->address,
            'sub_district' => $getDataCust->sub_district,
            'district' => $getDataCust->district,
            'city' => $getDataCust->city,
            'prov' => $getDataCust->prov,
            'postal_code' => $getDataCust->postal_code,
        ]);
        if(!$insertCust){
            $encode = array("status" => 500, "title" => "Gagal", "message" => "Input Data Shipment Cust Gagal!");
            return json_encode($encode);
        }


        //History Create Shipment
        $servName = DB::table('service_list')->where('id',$servId)->value('name');
        $dataHistory=[
            "codename" => "SH",
            "created_at" => $autoCreatedAt,
            "created_by" => $username,
            "description" => "<b>Create Shipment Secondary</b> dengan detail,<br><br>
            Secondary Id : <b>".$secondaryTrackId."</b><br>
            Primary Id : <b>".$primaryTrack."</b><br>
            Tgl Drop : <b>".$this->dateFormatIndo($dropManCreatedAt)."</b><br>
            Warehouse : <b>".$wareId." - ".$wareLoc."</b><br>
            Service : <b>".$servName."</b><br>
            Resi Luar Negeri : <b>".$resiLn."</b><br>"
        ];
        $insertHistory = DB::table('history_list')->insert($dataHistory);
        if(!$insertHistory){
            $encode = array("status" => 500, "title" => "Gagal", "message" => "Gagal Buat History!");
            return json_encode($encode);
        }

        $encode = array("status" => 200, "title" => "Berhasil", "message" => "Resi Secondary Berhasil Dibuat!");
        return json_encode($encode);

    }

    public function editNote(Request $request){
        $username = Auth::user()->username;
        $trackId = $request->input('msTrackId');
        $note = $request->input("note");

        //Edit Service
        $dataUpdate = [
            "edited_at" => date("Y-m-d H:i:s"),
            "edited_by" => $username,
            'note' => $note
        ];
        $update = DB::table("shiptrip_list")->where("ms_track_id",$trackId)->update($dataUpdate);

        $dataHistory=[
            "codename" => "EH",
            "created_at" => date("Y-m-d H:i:s"),
            "created_by" => $username,
            "description" => "<b>Edit Shipment Note</b> dengan detail,<br><br>
            Resi : <b>".$trackId."</b><br>
            Catatan : <b>".$note."</b></br>"
        ];
        $insertHistory = DB::table('history_list')->insert($dataHistory);
        if(!$insertHistory){
            $encode = array("status" => 500, "title" => "Gagal", "msg" => "Gagal Buat History!");
            return json_encode($encode);
        }

        $encode = array("status" => 200, "title" => "Berhasil", "msg" => "Edit Catatan Sukses!");
        return json_encode($encode);
    }

    public function editShipment(Request $request)
    {
        $username = Auth::user()->username;
        $wareId = $request->input('warehouse');
        $servId = $request->input('service');
        $trackId = $request->input('msTrackId');
        $note = $request->input("catatan");
        // dd($note);

        //Check Warehouse
        // if($wareId!=null){
        //     $servIdDb = DB::table("shiptrip_list")->where("ms_track_id",$trackId)->value("service_id");
        //     $wareIdDb = DB::table("service_list")->where("id",$servIdDb)->value("warehouse_id");
        //     if($wareIdDb!=$wareId){
        //         $text = DB::table('shiptrip_track_status')->where('id',1)->value('value');
        //         $wareId = DB::table('service_list')->where("id",$servId)->value('warehouse_id');
        //         $wareLoc = DB::table('warehouse_list')->where('id',$wareId)->value('location');
        //         $updateTracking = DB::table('shiptrip_track_list')
        //                             ->where("ms_track_id",$trackId)
        //                             ->where("track_status_id",1)
        //                             ->update([
        //                                 'text' => $text." [".$wareId." - ".$wareLoc."]",
        //                             ]);
        //         if(!$updateTracking){
        //             $encode = array("status" => 500, "title" => "Gagal", "message" => "Edit Data Shipment Tracking Gagal!");
        //             return json_encode($encode);
        //         }
        //     }
        // }

        //Edit Service
        $dataUpdate = [
            "service_id" => $servId,
            "edited_at" => date("Y-m-d H:i:s"),
            "edited_by" => $username,
            'note' => $note
        ];
        // dd($dataUpdate);
        $update = DB::table("shiptrip_list")->where("ms_track_id",$trackId)->update($dataUpdate);


        //Insert Foreign Tracking ID
        $resiLn = "";
        $deleteResiLN = DB::table("shiptrip_foreign_track_list")->where("ms_track_id",$trackId)->delete();
        for($a=0;$a<count($request->input('resiln'));$a++){
            if($request->input('resiln')[$a]!=""){
                DB::table('shiptrip_foreign_track_list')->insert([
                    'id' => $request->input('resiln')[$a],
                    'ms_track_id' => $trackId,
                ]);
                $resiLn .= $request->input('resiln')[$a].", ";
            }
        }

        //Check Image Old
        $linkImg = "-";
        $imgNumber = 1;
        if($request->input('imageOld')!=null){
            $arrayImgOld = count($request->input('imageOld'));
            $arrayImgDb = count(DB::table("shiptrip_image_list")->where("ms_track_id",$trackId)->get());
            $getImgDb = DB::table("shiptrip_image_list")->select("id","ext")->where("ms_track_id",$trackId)->get();
            if($arrayImgDb!=$arrayImgOld){
                foreach($getImgDb as $g){
                    $same=0;
                    $fullImg = $g->id.".".$g->ext;
                    for($i=0;$i<$arrayImgOld;$i++){
                        if($g->id==$request->input('imageOld')[$i]){
                            $same++;
                        }
                    }                   
                    if($same>0){
                        $linkImg .= "<a target='_blank' href='".url('/assets/photos')."/".$fullImg."'>Foto ".$imgNumber."</a>, ";
                        $imgNumber++;
                    }else{
                        $deleteImg = DB::table("shiptrip_image_list")->where("id",$g->id)->delete();
                        $imgFile = "/assets/photo/".$fullImg;
                        if(file_exists($imgFile)){
                            unlink($imgFile);
                        }
                    }
                }
            }else{
                foreach($getImgDb as $g){
                    $fullImg = $g->id.".".$g->ext;
                    $linkImg .= "<a target='_blank' href='".url('/assets/photos')."/".$fullImg."'>Foto ".$imgNumber."</a>, ";
                    $imgNumber++;
                }
            }
        }else{
            $deleteImg = DB::table("shiptrip_image_list")->where("ms_track_id",$trackId)->delete();
        }

        //Insert Image
        if($request->file('file')!=null){
            $linkImg = "";
            for($i=0;$i<count($request->file('file'));$i++){
                $imgName = $this->generateRandomString(25);
                $ext = $request->file('file')[$i]->extension();
                $fullImg = $imgName.".".$ext;
                $def = Image::load($request->file('file')[$i]->path())
                        ->optimize()
                        ->save(public_path('assets/photos/').$fullImg);
                DB::table('shiptrip_image_list')->insert([
                    'id' => $imgName,
                    'ext' => $ext,
                    'ms_track_id' => $trackId,
                ]);
                $linkImg .= "<a target='_blank' href='".url('/assets/photos')."/".$fullImg."'>Foto ".$imgNumber."</a>, ";
            }
        }

        //History Create Shipment
        $servData = DB::table('service_list')->select("name","warehouse_id")->where('id',$servId)->first();
        $wareData = DB::table('warehouse_list')->select("location")->where("id",$servData->warehouse_id)->first();
        $dataHistory=[
            "codename" => "EH",
            "created_at" => date("Y-m-d H:i:s"),
            "created_by" => $username,
            "description" => "<b>Edit Shipment</b> dengan detail,<br><br>
            Resi : <b>".$trackId."</b><br>
            Warehouse : <b>".$servData->warehouse_id." - ".$wareData->location."</b><br>
            Service : <b>".$servData->name."</b><br>
            Resi Luar Negeri : <b>".$resiLn."</b><br>
            Bukti Foto : <b>".$linkImg."</b><br>
            Catatan : <b>".$note."</b></br>"
        ];
        $insertHistory = DB::table('history_list')->insert($dataHistory);
        if(!$insertHistory){
            $encode = array("status" => 500, "title" => "Gagal", "message" => "Gagal Buat History!");
            return json_encode($encode);
        }

        $encode = array("status" => 200, "title" => "Berhasil", "message" => "Edit Shipment Sukses!");
        return json_encode($encode);

    }

    public function updateShipment(Request $request)
    {
        //Initialize
        $msTrackId = "";
        $foreignTrackId = "";
        $modeStatus = $request->input("modeStatus");
        $trackStatusManual = $request->input("trackStatusManual");
        $trackStatusSkip = $request->input("trackStatusSkip");
        $tanggalShipment = date("Y-m-d H:i:s",strtotime($this->dateFilterFormat($request->input("tanggalShipment"))[0]));
        $tanggalShipmentAuto = date("Y-m-d H:i:s");
        $username = Auth::user()->username;

        //All Shipment
        for($i=0;$i<count($request->input('trackId'));$i++){
            //Update Shipment

            $nowStepId = DB::table("shiptrip_list")->where("ms_track_id",$request->input('trackId')[$i])->value("track_status_id");
            if($modeStatus=="AUTO"){
                $nextStepId = DB::table("shiptrip_track_status")->where("id",$nowStepId)->value("next_step");
                $nextStep = DB::table("shiptrip_track_status")->select("id","value")->where("id",$nextStepId)->first();
                $statusPengiriman = $nextStep->value;
                $nextStepId = $nextStep->id;
                $trackStatusManualId = "A";
                $codename = $this->shipTripModel->getCodename($nextStepId);
                $dataShipment = [
                    "tanggalShipment" => $tanggalShipment,
                    "tanggalShipmentAuto" => $tanggalShipmentAuto,
                    "username" => $username,
                    "nextStep" => $nextStepId
                ];
                $dataShipTrip = $this->shipTripModel->getDataShipTrip($dataShipment);
            }elseif($modeStatus=="SKIP"){
                $nextStepId = $trackStatusSkip;
                $nextStep = DB::table("shiptrip_track_status")->select("id","value")->where("id",$nextStepId)->first();
                $statusPengiriman = $nextStep->value;
                $nextStepId = $nextStep->id;
                $trackStatusManualId = "A";
                $codename = $this->shipTripModel->getCodename($nextStepId);
                $dataShipment = [
                    "tanggalShipment" => $tanggalShipment,
                    "tanggalShipmentAuto" => $tanggalShipmentAuto,
                    "username" => $username,
                    "nextStep" => $nextStepId
                ];
                $dataShipTrip = $this->shipTripModel->getDataShipTrip($dataShipment);
            }else{
                $statusPengiriman = DB::table("shiptrip_track_status_manual")->where("id",$trackStatusManual)->value("value");
                $nextStepId = $nowStepId;
                $trackStatusManualId = $trackStatusManual;
                $codename = "UM";
                $dataShipment = [
                    "tanggalShipment" => $tanggalShipment,
                    "tanggalShipmentAuto" => $tanggalShipmentAuto,
                    "username" => $username,
                    "nextStep" => $nextStepId
                ];
                $dataShipTrip = $this->shipTripModel->getDataShipTripManual($dataShipment);
            }
            $update = DB::table("shiptrip_list")->where("ms_track_id",$request->input('trackId')[$i])->update($dataShipTrip);


            //Create Order
            if($nextStepId>8){
                $getShipTripData = DB::table("shiptrip_list")->select("*")->where("ms_track_id", $request->input('trackId')[$i])->first();
                $custId = $getShipTripData->cust_id;
                $serviceId = $getShipTripData->service_id;
                $warehouseId = DB::table("service_list")->where("id",$serviceId)->value("warehouse_id");
                $getCustData = DB::table("cust_list")->select("*")->where("id",$custId)->first();
                $getCustSecondData = DB::table("shiptrip_cust_list")->where("id", $request->input('trackId')[$i])->first();
                $phoneFix = "+".DB::table("country_phone_codes")->where("id",$getCustData->country_id)->value("code").$getCustData->phone;

                $values = array(
                    "cust_id" => $custId,
                    "cust_type_id" => $getCustData->cust_type_id,
                    "order_status_id" => "READY",
                    "ms_track_id" => $request->input('trackId')[$i],
                    "warehouse_id" => $warehouseId,
                    "service_id" => $serviceId,
                    "invoice_id" => "",
                    "created_at" => $tanggalShipmentAuto,
                    "created_by" => $username,
                    "updated_at" => $tanggalShipmentAuto,
                    "updated_by" => $username,
                    "drop_created_at" => $getShipTripData->drop_created_at,
                    "drop_man_created_at" => $getShipTripData->drop_man_created_at,
                    "drop_created_by" => $getShipTripData->drop_created_by,
                    "drop_updated_at" => $getShipTripData->drop_updated_at,
                    "drop_updated_by" => $getShipTripData->drop_updated_by,
                    "to_sg_created_at" => $getShipTripData->to_sg_created_at,
                    "to_sg_man_created_at" => $getShipTripData->to_sg_man_created_at,
                    "to_sg_created_by" => $getShipTripData->to_sg_created_by,
                    "to_sg_updated_at" => $getShipTripData->to_sg_updated_at,
                    "to_sg_updated_by" => $getShipTripData->to_sg_updated_by,
                    "first_name" => $getCustData->first_name,
                    "middle_name" => $getCustData->middle_name,
                    "last_name" => $getCustData->last_name,
                    "phone" => $phoneFix,
                    "email" => $getCustData->email,
                    "address" => $getCustData->address,
                    "sub_district" => $getCustData->sub_district,
                    "district" => $getCustData->district,
                    "city" => $getCustData->city,
                    "prov" => $getCustData->prov,
                    "postal_code" => $getCustData->postal_code,
                    "second_name" => $getCustData->cust_type_id == "IND" ? $getCustSecondData->first_name : '',
                    "second_phone" => $getCustData->cust_type_id == "IND" ? $getCustSecondData->phone : '',
                );
                $insert = DB::table("order_list")->insert($values);

                $updateShipTripData = [
                    "cr_order_created_at" => $tanggalShipmentAuto,
                    "cr_order_man_created_at" =>$tanggalShipment,
                    "cr_order_created_by" => $username,
                    "cr_order_updated_at" => $tanggalShipmentAuto,
                    "cr_order_updated_by" => $username
                ];
                $updateShipTrip = DB::table("shiptrip_list")->where("ms_track_id",$request->input('trackId')[$i])->update($updateShipTripData);

                // $dataHistory=[
                //     "codename" => "BO",
                //     "created_at" => date("Y-m-d H:i:s"),
                //     "created_by" => $username,
                //     "description" => "<b>Buat Order</b> dengan detail,<br><br>
                //     ID Sistem : <b>".DB::table("order_list")->where("phone",$phoneFix)->value("id")."</b><br>
                //     Tipe : <b>".DB::table("cust_type_list")->where("id", $getCustData->cust_type_id)->value("name")."</b><br>
                //     Resi Utama : <b>".$request->input('trackId')[$i]."</b><br>
                //     First Name : <b>".$getCustData->first_name."</b><br>
                //     Middle Name : <b>".$getCustData->middle_name."</b><br>
                //     Last Name : <b>".$getCustData->last_name."</b><br>
                //     Telpon : <b>".$phoneFix."</b><br>
                //     Email : <b>".$getCustData->email."</b><br>
                //     Alamat : <b>".$getCustData->address.", ".$getCustData->sub_district.", ".$getCustData->district.", ".$getCustData->city.", ".$getCustData->prov.", ".$getCustData->postal_code."</b>",
                // ];
                // $insertHistory = DB::table('history_list')->insert($dataHistory);
            }

            //Update Track List
            $dataTracking = [
                "created_at" => $tanggalShipment,
                "created_by" => $username,
                "ms_track_id" => $request->input('trackId')[$i],
                "track_status_id" => $nextStepId,
                "track_status_manual_id" => $trackStatusManualId,
                "text" => $statusPengiriman
            ];
            $update2 = DB::table("shiptrip_track_list")->insert($dataTracking);

            //Get Data Foreign Track List
            $get = DB::table("shiptrip_foreign_track_list")->selectRaw("GROUP_CONCAT(id SEPARATOR ', ') AS foreignId")->where("ms_track_id",$request->input('trackId')[$i])->first();

            $msTrackId .= "(".$request->input('trackId')[$i]."=>".$get->foreignId."), ";
        }

        //History
        $dataHistory=[
            "codename" => $codename,
            "created_at" => $tanggalShipmentAuto,
            "created_by" => $username,
            "description" => "<b>Update Shipment</b> dengan detail,<br><br>
            Tgl Shipment : <b>".$this->dateFormatIndo($this->dateFilterFormat($request->input("tanggalShipment"))[0],2)."</b><br>
            Status Pengiriman : <b>".$statusPengiriman."</b><br>
            Shipment ID : <b>".$msTrackId."</b><br>"
        ];
        $insertHistory = DB::table('history_list')->insert($dataHistory);
        if(!$insertHistory){
            $encode = array("status" => 500, "title" => "Gagal", "text" => "Gagal Buat History!");
            return json_encode($encode);
        }

        $encode = array("status" => 200, "title" => "Berhasil", "text" => "Update Shipment Berhasil!");
        return json_encode($encode);
    }

    public function shipTripTable(string $navShip, string $navType, string $custTypeId, Request $request)
    {

        $this->roleAccess();
        $data = [];
        
        $no = $request->input('start');
        $search = $request->input('search')['value'];
        
        $filterTanggal = $request->input("filterTanggal")!="[object Object]"?$this->dateFilterFormat($request->input("filterTanggal")):"";
        $filterWarehouse = $request->input("filterWarehouse");
        
        $filter = [
            'tanggal' => $filterTanggal,
            'warehouse' => $filterWarehouse,
            'navShip' => $navShip,
            'navType' => $navType,
            'custTypeId' => $custTypeId
        ];

        if($navShip=="end"){

            $data = $this->endpointModel->callTableEndPoint($request, $filter);
            $output = [
                'draw' => $request->input('draw'),
                'recordsTotal' => $this->endpointModel->countAll(),
                'recordsFiltered' => $this->endpointModel->countFiltered($request, $search, $filter),
                'data' => $data
            ];

        }else{

            $lists = $this->shipTripModel->getDT($request, $search, $filter);
            $data = $this->shipTripModel->callTable($request, $lists, $filter);
            $output = [
                'draw' => $request->input('draw'),
                'recordsTotal' => $this->shipTripModel->countAll(),
                'recordsFiltered' => $this->shipTripModel->countFiltered($request, $search, $filter),
                'data' => $data
            ];

        }

        return json_encode($output);
    }

    public function hapusShipment(Request $request)
    {
        //Initialize
        $id = $request->input('trackId');
        $note = $request->input('note');
        $now = $now = date("Y-m-d H:i:s");

        //Delete Shipment
        $arrayDeleteShipment = [
            "id" => $id,
            "now" => $now,
            "note" => $note
        ];
        $deleteShipment = $this->deleteShipmentService->execute($arrayDeleteShipment);
        $encode = array("status" => $deleteShipment['status'], "title" => $deleteShipment['title'], "text" => $deleteShipment['text']);
        return json_encode($encode);
    }

    public function updateEndpoint(Request $request)
    {
        //Initialize
        $username = Auth::user()->username;
        $trackStatusId = $request->input("trackStatusId");
        $location = $request->input("location");
        $reason = $request->input("reason");
        $receiver = $request->input("receiver");
        $custTypeId = $request->input("custTypeId");
        $msTrackId = $request->input("msTrackId");
        $shippingNumber = $request->input("shippingNumber");
        $date = date("Y-m-d H:i:s");
        $endPointSuccessLabel = "Update Endpoint Sukses!";
        // dd($msTrackId);
        
        if($shippingNumber==""){
            $encode = array("status" => 500, "title" => "Gagal", "message" => "shipping number tidak ada!");
            return json_encode($encode);
        }

        //Insert POD list
        $detail = $trackStatusId==13||$trackStatusId==22 ? "[".$receiver."]" : ( $trackStatusId==20||$trackStatusId==21 ? "[".$reason."]" : "[".$location."]" );
        $linkImg = "-";
        $fullImg = "";
        $resi = $custTypeId=="IND"? ( $msTrackId!="" ? $msTrackId : $shippingNumber ) : $shippingNumber;
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
            // }
        }

        $text = DB::table('shiptrip_track_status')->where('id',$trackStatusId)->value('value');
        
        //Update data_list
        $whereDataList = $custTypeId=="IND"?($msTrackId!=""?"ms_track_id='$msTrackId'":"shipping_number='$shippingNumber'"):"shipping_number='$shippingNumber'";
        if($trackStatusId==13||$trackStatusId==22){
            $endPointSuccessLabel = "Resi ".($msTrackId!=""?$msTrackId:$shippingNumber)." Telah Diterima";
            $dataListArray = [
                "shipping_status" => $text." ".$detail,
                "shipping_success_at" => $date,
                "shipping_success_by" => $username,
                "shipping_success_receiver" => $receiver,
                "shipping_success_pod" => $fullImg,
                "shipping_updated_at" => $date,
                "shipping_updated_by" => $username,
                "track_status_id" => $trackStatusId,
                "track_man_created_at" => $date
            ];
        }else{
            $dataListArray = [
                "shipping_status" => $text." ".$detail,
                "shipping_reason" => $reason,
                "shipping_location" => $location,
                "shipping_updated_at" => $date,
                "shipping_updated_by" => $username,
                "track_status_id" => $trackStatusId,
                "track_man_created_at" => $date
            ];
        }
        $updateDataList = $this->endpointModel->whereRaw($whereDataList)->update($dataListArray);
        if(!$updateDataList){
            $encode = array("status" => 500, "title" => "Gagal", "message" => "Gagal Update Data!");
            return json_encode($encode);
        }

        //track status list
        if($msTrackId!=""){            
            if($custTypeId=="IND"){
                $invoiceId = $this->endpointModel->where("ms_track_id",$msTrackId)->value("mismass_invoice_id");
                $getMSTrack = DB::table("order_list")->select("ms_track_id")->where("invoice_id",$invoiceId)->get();
                // dd($getMSTrack);
                foreach($getMSTrack as $g){
                    $insertTrack = DB::table('shiptrip_track_list')->insert([
                        'created_at' => $date,
                        'created_by' => $username,
                        'ms_track_id' => $g->ms_track_id,
                        'track_status_id' => $trackStatusId,
                        "track_status_manual_id" => "A", 
                        'text' => $text." ".$detail,
                    ]);

                    // dd($insertTrack);
                }
            }else{
                DB::table('shiptrip_track_list')->insert([
                    'created_at' => $date,
                    'created_by' => $username,
                    'ms_track_id' => $shippingNumber,
                    'track_status_id' => $trackStatusId,
                    "track_status_manual_id" => "A", 
                    'text' => $text." ".$detail,
                ]);
            }
        }

        //History
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
            "codename" => "UE",
            "created_at" => $date,
            "created_by" => $username,
            "description" => "<b>Update Endpoint</b> dengan detail,<br><br>
            Tgl Shipment : <b>".$this->dateFormatIndo($getDataForHistory->to_sg_man_created_at,1)."</b><br>
            Pengiriman : <b>".$getDataForHistory->full_forwarder."</b><br>
            Resi Utama : <b>".($getDataForHistory->ms_track_id ?? "-")."</b><br>
            Resi Lokal : <b>".$getDataForHistory->shipping_number."</b><br>
            Nama : <b>".$getDataForHistory->full_name."</b><br>
            Telpon :<b>".$getDataForHistory->cons_phone."</b><br>
            Alamat : <b>".$getDataForHistory->full_address."</b><br>
            Status Update : <b>".$text." ".$detail."</b><br>
            Bukti Foto : <b>".$linkImg."</b><br>"
        ];
        $insertHistory = DB::table('history_list')->insert($dataHistory);
        if(!$insertHistory){
            $encode = array("status" => 500, "title" => "Gagal", "message" => "Gagal Buat History!");
            return json_encode($encode);
        }

        $encode = array("status" => 200, "title" => "Berhasil", "message" => $endPointSuccessLabel);
        return json_encode($encode);
    }

    public function trackShipment(Request $request){
        if(!env('TRACK_RESI')){
            if(Auth::user()->username!="dev8th"){
                return view('pages.maintenance');
            }
        }
        $id = $request->input("id");
        $backpage = $request->input("backpage");
        $data["backpage"] = url("/")."/".$backpage;
        $data["dataid"] = $backpage;
        $data["id"] = $id;
        return view("pages.tracking",$data);
    }

    public function trackShipmentSystem(Request $request){
        //Initialize
        $id = $request->input("id");
        $data = $this->trackSystemModel->getWayBills($id);
        if($data['wayBill']==""){
            $encode = array("status" => 500, "title" => "Gagal", "text" => "No Resi Tidak Ditemukan!", "id"=>$id);
            return json_encode($encode);
        }
        $encode = $this->trackSystemModel->loadTracking($data,true);
        return json_encode($encode);
    }

    public function trackShipmentSystemChange(Request $request){
        //Initialize
        $id = $request->input("id");

        //check MSTrackID
        $checkOne = DB::table("shiptrip_track_list")->where("ms_track_id",$id)->first();
        if($checkOne==null){
            $encode = array("status" => 500, "title" => "Gagal", "text" => "No Resi Tidak Ditemukan!", "id"=>$id);
            return json_encode($encode);
        }
        $waybill[] = ["id"=>$id,"txt"=>$id];

        //check customer type id
        $custTypeId = DB::table("shiptrip_list")
                        ->select("cust_list.cust_type_id")
                        ->join("cust_list", "cust_list.id", "=", "shiptrip_list.cust_id")
                        ->where("ms_track_id",$id)->value("cust_list.cust_type_id");
        $data['custTypeId'] = $custTypeId;

        //Last Return
        $data['wayBill'] = $waybill;
        $encode = $this->trackSystemModel->loadTracking($data,false);
        return json_encode($encode);

    }

    public function trackShipmentCustomer(Request $request){
        return view("trackingcust.tracking");
    }

    public function trackShipmentCustomerSelect(Request $request){
        $id = $request->input("id");

        $getWaybills = $this->trackSystemModel->getWayBills($id);
        $data['wayBills'] = $getWaybills['wayBill'];
        return view("trackingcust.tracking-select",$data);
    }

    public function trackShipmentDirect(Request $request){
        return view("trackingcust.tracking-direct");
    }

    public function trackShipmentAdmin(Request $request){
        return view("trackingcust.tracking-admin");
    }
    
    // public function warehouseList(Request $request){
    //     $navCust = $request->input("navCust");
    //     $navTab = $request->input("navTab");
    //     $navType = $request->input("navType");
    //     $filterTrackStatus = $this->shipTripModel->checkFilterTrackStatus($navTab,$navType);
    
    //     $get = DB::table("warehouse_list")
    //         ->selectRaw("
    //             warehouse_list.id,
    //             warehouse_list.name,
    //             warehouse_list.location,
    //             COUNT(DISTINCT shiptrip_list.ms_track_id) AS total
    //         ")
    //         ->leftJoin("service_list", "service_list.warehouse_id", "=", "warehouse_list.id")
    
    //         ->leftJoin("shiptrip_list", function($join) use ($filterTrackStatus) {
    //             $join->on("shiptrip_list.service_id", "=", "service_list.id")
    //                  ->where("shiptrip_list.track_status_id", $filterTrackStatus);
    //         })
    
    //         ->leftJoin("cust_list", function($join) use ($navCust) {
    //             $join->on("cust_list.id", "=", "shiptrip_list.cust_id")
    //                  ->where("cust_list.cust_type_id", $navCust);
    //         })
    
    //         ->groupBy("warehouse_list.id")
    //         ->orderBy("total","DESC")
    //         ->get();
    

    //     $option = "<option value=''>ALL WAREHOUSE</option>";
    //     foreach($get as $g){
    //         $option .= "<option data-color='blue' value='".$g->id."'>".$g->id." - ".$g->name." - ".$g->location." - (".$g->total.")</option>";
    //     }

    //     $encode = array("data" => $option);
    //     return json_encode($encode);
    // }
    public function warehouseList(Request $request){
        $navCust = $request->input("navCust");
        $navTab = $request->input("navTab");
        $navType = $request->input("navType");
        $filterTrackStatus = $this->shipTripModel->checkFilterTrackStatus($navTab,$navType);
    
        $get = DB::table("warehouse_list")
            ->selectRaw("
                warehouse_list.id,
                warehouse_list.name,
                warehouse_list.location,
                COUNT(DISTINCT shiptrip_list.ms_track_id) AS total
            ")
            ->leftJoin("service_list", "service_list.warehouse_id", "=", "warehouse_list.id")
            ->leftJoin("shiptrip_list", "shiptrip_list.service_id", "=", "service_list.id")
            ->leftJoin("cust_list", "cust_list.id", "=", "shiptrip_list.cust_id")
            ->where("cust_list.cust_type_id", $navCust)
            ->where("shiptrip_list.track_status_id", $filterTrackStatus)
            ->groupBy("warehouse_list.id")
            ->orderBy("total","DESC")
            ->get();
    

        $option = "<option value=''>ALL WAREHOUSE</option>";
        foreach($get as $g){
            $option .= "<option data-color='blue' value='".$g->id."'>".$g->id." - ".$g->name." - ".$g->location." - (".$g->total.")</option>";
        }

        $encode = array("data" => $option);
        return json_encode($encode);
    }
}
