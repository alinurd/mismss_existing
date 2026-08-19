<?php

namespace App\Http\Controllers;

use illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Models\Edit;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\Tracking;
use App\Models\Warehouse;
use App\Models\Service;
use App\Models\Customer;
use App\Models\Whatsapp;
use App\Models\DokuSystem;
use App\Http\Controllers\Controller;
use App\Http\Controllers\DokuController;
use DOKU\Common\Config;
use DOKU\Common\Utils;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Mail;
use App\Services\ResiService;
use App\Services\DeleteOrderService;
use App\Services\DeleteShipmentService;
use App\Services\ChangeToCorService;
use App\Services\CreateInvoiceAdditionalService;

class ShipmentController extends Controller
{
    private $createInvoiceAddService,$deleteOrderService,$deleteShipmentService,
    $changeService,$resiService,$controller,$dokuController,$dokuModel,$invoiceModel,
    $wareModel,$custModel,$orderModel,$servModel,$trackingModel,$whatsappModel,$editModel;

    public function __construct()
    {
        $this->controller = new Controller;
        $this->dokuController = new DokuController;
        $this->dokuModel = new DokuSystem;
        $this->invoiceModel = new Invoice;
        $this->wareModel = new Warehouse;
        $this->custModel = new Customer;
        $this->orderModel = new Order;
        $this->servModel = new Service;
        $this->trackingModel = new Tracking;
        $this->whatsappModel = new Whatsapp;
        $this->editModel = new Edit;
        $this->resiService = new ResiService;
        $this->changeService = new changeToCorService;
        $this->deleteOrderService = new DeleteOrderService;
        $this->deleteShipmentService = new DeleteShipmentService;
        $this->createInvoiceAddService = new CreateInvoiceAdditionalService;
    }

    public function index()
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
    
    public function import(Request $request){
        $array = Excel::toArray(0, $request->file('excel'));
        return json_encode($array);
    }

    public function lastShippingId()
    {
        $check = DB::table("pool_shipping_id")->count();
        if ($check < 1) {
            $encode = array("shippingIdAv" => "TR/ALY/".date("y")."0001");
            return json_encode($encode);
        }
        $check2 = DB::table("pool_shipping_id")->orderBy("id", "desc")->first();
        $lastId = $check2->id + 1;

        $rolling = true;
        while($rolling==true){
            $check3 = DB::table("data_list")->where("shipping_number","TR/ALY/".date("y")."000" . $lastId)->count();
            $rolling = false;
            if($check3 > 0){
                $rolling = true;
                $lastId ++;            
            }
        }

        $encode = array("shippingIdAv" => "TR/ALY/".date("y")."000" . $lastId);
        return json_encode($encode);
    }

    public function foreignRate(){
        $foreignCurrency = "SGD";
        $table = DB::table("foreign_rate");
        $get = $table->where("id",$foreignCurrency)->first();
        $finalRate = $get->value;
        $dateDBOld = strtotime(date("Y-m-d",strtotime($get->updated_at)));
        $dateNow = strtotime(date("Y-m-d"));
        if($dateNow-$dateDBOld>0||$finalRate==0){
            $finalRate = $this->getForeignRate($foreignCurrency);
            $updateData = [
                "updated_at" => date("Y-m-d H:i:s"),
                "value" => $finalRate
            ];
            $table->where("id",$foreignCurrency)->update($updateData);
        }
        return json_encode($finalRate);
    }

    // public function buatOrder(Request $request)
    // {
    //     $username = Auth::user()->username;
    //     $fullAddress = $request->input('address').", ".$request->input('subDistrict').", ".$request->input('district').", ".$request->input('city').", ".$request->input('prov').", ".$request->input('postalCode');
    //     $custId = $request->input("regCust");
    //     $custTypeId = $request->input('custTypeId');
    //     $textFail = "Gagal Buat Order";
    //     $textSuccess = "Shipment Order Berhasil Dibuat. Silahkan Cek Pada Halaman Shipment List.";
        
    //     $firstName = $request->input("firstName");
    //     $middleName = $request->input('middleName');
    //     $lastName = $request->input("lastName");
    //     $fullName = $middleName=="" ? ($lastName==""?$firstName:$firstName." ".$lastName) : ($lastName==""?$firstName." ".$middleName:$firstName." ".$middleName." ".$lastName);

    //     if ($request->input('statusCust') != "REG") {

    //         $this->custModel->first_name = $request->input('firstName');
    //         $this->custModel->middle_name = $request->input('middleName') ?? "";
    //         $this->custModel->last_name = $request->input('lastName') ?? "";
    //         $this->custModel->country_id = $request->input('kodeNegara');
    //         $this->custModel->phone = $request->input('phone');
    //         $this->custModel->email = $request->input('email');
    //         $this->custModel->address = $request->input('address');
    //         $this->custModel->sub_district = $request->input('subDistrict') ?? "";
    //         $this->custModel->district = $request->input('district');
    //         $this->custModel->city = $request->input('city');
    //         $this->custModel->prov = $request->input('prov');
    //         $this->custModel->postal_code = $request->input('postalCode');
    //         $this->custModel->cust_type_id = $custTypeId;
    //         $this->custModel->created_by = $username;
    //         $this->custModel->updated_by = $username;
    //         $this->custModel->know_from_id = 1;

    //         $insert = $this->custModel->save();

    //         $custId = DB::table("cust_list")->where("phone", "=", $request->input("phone"))->value("id");

    //         $textFail = "Gagal Tambah Customer Dan Buat Order";
    //         $textSuccess = "Customer Dan Shipment Order Berhasil Dibuat. Silahkan Cek Pada Halaman Shipment List.";

    //         $dataHistory=[
    //             "codename" => "BC",
    //             "created_at" => date("Y-m-d H:i:s"),
    //             "created_by" => $username,
    //             "description" => "<b>Buat Customer/Client</b> dengan detail,<br><br>
    //             Tipe : <b>".DB::table("cust_type_list")->where("id", $custTypeId)->value("name")."</b><br>
    //             First Name : <b>".$request->input('firstName')."</b><br>
    //             Middle Name : <b>".$request->input('middleName')."</b><br>
    //             Last Name : <b>".$request->input('lastName')."</b><br>
    //             Telpon : <b>+".DB::table("country_phone_codes")->where("id",$request->input("kodeNegara"))->value("code").$request->input('phone')."</b><br>
    //             Email : <b>".$request->input('email')."</b><br>
    //             Alamat : <b>".$request->input('address').", ".$request->input('subDistrict').", ".$request->input('district').", ".$request->input('city').", ".$request->input('prov').", ".$request->input('postalCode')."</b>",
    //         ];
    
    //         $insertHistory = DB::table('history_list')->insert($dataHistory);
            
    //         if(!$insertHistory){
    //             $encode = array("status" => "Gagal", "text" => "Gagal Buat History");
    //             return json_encode($encode);
    //         }
    //     }
        
    //     $phoneFix = "+".DB::table("country_phone_codes")->where("id",$request->input("kodeNegara"))->value("code").$request->input('phone');

    //     $this->orderModel->cust_id = $custId;
    //     $this->orderModel->cust_type_id = $custTypeId;
    //     $this->orderModel->order_status_id = "READY";
    //     $this->orderModel->invoice_id = "";
    //     $this->orderModel->created_by = Auth::user()->username;
    //     $this->orderModel->updated_by = Auth::user()->username;
    //     $this->orderModel->first_name = $this->noSingleQuo($request->input('firstName'));
    //     $this->orderModel->middle_name = $this->noSingleQuo($request->input('middleName')) ?? "";
    //     $this->orderModel->last_name = $this->noSingleQuo($request->input('lastName')) ?? "";
    //     $this->orderModel->phone = $phoneFix;
    //     $this->orderModel->email = $request->input('email');
    //     $this->orderModel->address = $this->noSingleQuo($request->input('address'));
    //     $this->orderModel->sub_district = $this->noSingleQuo($request->input('subDistrict')) ?? "";
    //     $this->orderModel->district = $this->noSingleQuo($request->input('district'));
    //     $this->orderModel->city = $this->noSingleQuo($request->input('city'));
    //     $this->orderModel->prov = $this->noSingleQuo($request->input('prov'));
    //     $this->orderModel->postal_code = $request->input('postalCode');
    //     $this->orderModel->second_name = $custTypeId=="IND" ? $this->noSingleQuo($fullName) : '';
    //     $this->orderModel->second_phone = $custTypeId=="IND" ? $request->input('phone') : '';

    //     $insert = $this->orderModel->save();

    //     if(!$insert){
    //         $encode = array("status" => "Gagal", "text" => $textFail);
    //         return json_encode($encode);
    //     }

    //     $dataHistory=[
    //         "codename" => "BO",
    //         "created_at" => date("Y-m-d H:i:s"),
    //         "created_by" => $username,
    //         "description" => "<b>Buat Order</b> dengan detail,<br><br>
    //         ID Sistem : <b>".DB::table("order_list")->where("phone",$request->input('phone'))->value("id")."</b><br>
    //         Tipe : <b>".DB::table("cust_type_list")->where("id", $custTypeId)->value("name")."</b><br>
    //         First Name : <b>".$request->input('firstName')."</b><br>
    //         Middle Name : <b>".$request->input('middleName')."</b><br>
    //         Last Name : <b>".$request->input('lastName')."</b><br>
    //         Telpon : <b>+".DB::table("country_phone_codes")->where("id",$request->input("kodeNegara"))->value("code").$request->input('phone')."</b><br>
    //         Email : <b>".$request->input('email')."</b><br>
    //         Alamat : <b>".$request->input('address').", ".$request->input('subDistrict').", ".$request->input('district').", ".$request->input('city').", ".$request->input('prov').", ".$request->input('postalCode')."</b>",
    //     ];
    //     $insertHistory = DB::table('history_list')->insert($dataHistory);
    //     if(!$insertHistory){
    //         $encode = array("status" => "Gagal", "text" => "Gagal Buat History");
    //         return json_encode($encode);
    //     }

    //     // $data = [
    //     //     "+".DB::table("country_phone_codes")->where("id",$request->input("kodeNegara"))->value("code").$request->input('phone'),
    //     //     $fullName,
    //     //     $request->input('email'),
    //     //     $fullAddress
    //     // ];
    //     // env('QONTAK_STATUS') ? $this->initializeCreateOrder($data) : '';
        
        
    //     // $getId = DB::table("order_list")->where("phone",$phoneFix)->value("id");
    //     // $this->createOrderSendWA($getId);

    //     $encode = array("status" => "Berhasil", "text" => $textSuccess);
    //     return json_encode($encode);
    // }

    public function tableOrder(string $custTypeId, Request $request)
    {
        $this->roleAccess();
        $data = [];
        $no = $request->input('start');
        $search = $request->input('search')['value'];
        $filterTanggal = $this->dateFilterFormat($request->input('filterTanggal'));
        $filterWarehouse = $request->input('filterWarehouse');
        $filterService = $request->input('filterService');
        $filterCustomer = $request->input('filterCustomer');
        $filter = [
            "tanggal" => $filterTanggal,
            "warehouse" => $filterWarehouse,
            "service" => $filterService,
            "customer" => $filterCustomer,
            "custTypeId" => $custTypeId
        ];
        $lists = $this->orderModel->getDT($request, $search, $filter);

        foreach ($lists as $list) {
            $getRank = DB::table('users')->where("username",$list->updated_by)->value("rank");            
            $jabatan = $getRank != null ? "<div class='bg-mismass' style='padding:1px 5px'>".$getRank."</div>" : "";
            $fullName = "<div class='fw-bold'>".$list->first_name." ".$list->middle_name." ".$list->last_name."</div>";
            $detailAddr = "<div>".$list->phone."</div><div>".$list->city.", ".$list->prov.", ".$list->postal_code."</div>";
            $note = $list->ms_track_id!="" ? DB::table("shiptrip_list")->where("ms_track_id",$list->ms_track_id)->value("note") : "" ;
            $noteEl = $note!="" ? "<div class='bg-catatan'>Catatan</div>" : "" ;

            $getImg = DB::table("shiptrip_image_list")
                        ->selectRaw("GROUP_CONCAT(CONCAT_WS('.',shiptrip_image_list.id,shiptrip_image_list.ext) SEPARATOR ', ') as images")
                        ->where("ms_track_id",$list->ms_track_id)
                        ->groupBy("ms_track_id")
                        ->get();
            if(count($getImg)==0){
                $images = "";
            }else{
                $images = $getImg[0]->images;
            }
            $totalForeign = $list->total_foreign=="0 Resi LN" ? "-" : $list->total_foreign;
            $totalForeignEl = $list->total_foreign=="0 Resi LN" ? "-" : "<div style='display:flex'><div style='margin-right:5px'>".$list->total_foreign."</div><a class='pointlink lookresiln' data-id='".$list->ms_track_id."' data-resi-ln='".$list->foreign_tracks."' data-catatan='".$note."' data-images='".$images."' data-baseurl='".url('/')."'><i class='fas fa-eye'></i></a></div>".$noteEl;
            
            // $hapusBtn = Auth::user()->shiplist_hapus_order?"<a class='dropdown-item pointlink' id='hapusBtn' onclick=\"konfirm_hapus('" . $list->id . "','" . $list->first_name . "','Order','" . url('/shiplist/hapus/order') . "','shiplist')\"><div style='color:red'>Hapus Data</div></a>":"";
            $hapusBtn = Auth::user()->shiplist_hapus_order?"<a class='dropdown-item pointlink' id='hapusOrderBtn' data-id='".$list->id."' data-name='".$list->first_name."' data-mstrackid='".$list->ms_track_id."'><div style='color:red'>Hapus Data</div></a>":"";

            $arrayChangeToCor = [
                "id" => $list->id,
                "custTypeId" => $list->cust_type_id,
                "msTrackId" => $list->ms_track_id,
                "firstName" => $list->first_name
            ];
            $changeBtn = $this->changeService->changeToCorButton($arrayChangeToCor);
            $buatBtn = Auth::user()->shiplist_buat_invoice?"<a class='dropdown-item pointlink' id='buatBtn' data-warehouse='".$list->warehouse_id."'".
                    "data-indtocor='".$list->ind_to_cor."' data-service='".$list->service_id."' data-ms-track='".$list->ms_track_id."'".
                    "data-total-foreign='".$totalForeign."' data-foreign-tracks='".$list->foreign_tracks."' data-note='".$note."'".
                    "data-ship-created-by='".$list->to_sg_created_by."' data-ship-created-at='".$this->dateFormatIndo($list->to_sg_man_created_at,1)."'".
                    "data-drop-created-by='".$list->drop_created_by."' data-drop-created-at='".$this->dateFormatIndo($list->drop_man_created_at,1)."'".
                    "data-custId='".$list->cust_id."' data-custTypeId='".$list->cust_type_id."' data-id='" . $list->id . "'".
                    "data-firstName='" . $list->first_name . "' data-middleName='" . $list->middle_name . "' data-lastName='" . $list->last_name . "'".
                    "data-phone='".$list->phone."' data-email='".$list->email."' data-address='".$list->address."'".
                    "data-subDistrict='".$list->sub_district."' data-district='".$list->district."' data-city='".$list->city."'".
                    "data-prov='".$list->prov."' data-postalCode='".$list->postal_code."' data-secondName='".$list->second_name."'".
                    "data-secondPhone='".$list->second_phone."' data-tanggal='" . $this->dateFormatIndo($list->created_at,1) . "' data-images='".$images."' data-baseurl='".url('/')."'>Buat Invoice</a>":"";
            $wholeBtn = "<div class='btn-group dropleft'><button type='button' class='btn btn-secondary nobtn' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'><i class='fas fa-ellipsis-v'></i></button><div class='dropdown-menu' x-placement='right-start' style='position: absolute; transform: translate3d(111px, 0px, 0px); top: 0px; left: 0px; will-change: transform;'>".$buatBtn.$changeBtn.$hapusBtn."</div></div>";
            $detailControl = "<input type='checkbox' style='margin-left:4px' name='checkShipment' data-warehouse='".$list->warehouse_id."'".
                    "data-indtocor='".$list->ind_to_cor."' data-service='".$list->service_id."' data-ms-track='".$list->ms_track_id."'".
                    "data-total-foreign='".$totalForeign."' data-foreign-tracks='".$list->foreign_tracks."' data-note='".$note."'".
                    "data-ship-created-by='".$list->to_sg_created_by."' data-ship-created-at='".$this->dateFormatIndo($list->to_sg_man_created_at,1)."'".
                    "data-drop-created-by='".$list->drop_created_by."' data-drop-created-at='".$this->dateFormatIndo($list->drop_man_created_at,1)."'".
                    "data-custId='".$list->cust_id."' data-custTypeId='".$list->cust_type_id."' data-id='" . $list->id . "'".
                    "data-firstName='" . $list->first_name . "' data-middleName='" . $list->middle_name . "' data-lastName='" . $list->last_name . "'".
                    "data-phone='".$list->phone."' data-email='".$list->email."' data-address='".$list->address."'".
                    "data-subDistrict='".$list->sub_district."' data-district='".$list->district."' data-city='".$list->city."'".
                    "data-prov='".$list->prov."' data-postalCode='".$list->postal_code."' data-secondName='".$list->second_name."'".
                    "data-secondPhone='".$list->second_phone."' data-tanggal='" . $this->dateFormatIndo($list->created_at,1) . "' data-images='".$images."' data-baseurl='".url('/')."'>";

            $no++;
            $row = [];
            $row[] = "<div class='orderNum'>".$no."</div>".$detailControl;
            $row[] = $this->orderModel->checkMsTrackId($list->ms_track_id);
            $row[] = $this->orderModel->checkWareServ($list->id);
            $row[] = "<div class='fw-bold'>" . $list->updated_by . "</div>".$jabatan."<div>" . $this->dateFormatIndo($list->updated_at,2) . "</div>";
            $row[] = $fullName.$detailAddr;
            $row[] = $totalForeignEl;
            $row[] = $list->order_status_id == "READY" ? "<div id='btnStatusId' data-orderid='" . $list->id . "' class='btnStatus btnStatusReady'>" . $list->order_status_id . "</div>" : "<div id='btnStatusId' data-orderid='" . $list->id . "' class='btnStatus btnStatusHold'>" . $list->order_status_id . "</div>";
            if(Auth::user()->shiplist_buat_invoice||Auth::user()->shiplist_hapus_order){
                $row[] = $wholeBtn;
            }
            $data[] = $row;
        }

        $output = [
            'draw' => $request->input('draw'),
            'recordsTotal' => $this->orderModel->countAll(),
            'recordsFiltered' => $this->orderModel->countFiltered($request, $search, $filter),
            'data' => $data
        ];

        return json_encode($output);
    }

    public function hapusOrder(Request $request)
    {
        $id = $request->input('id');
        $note = $request->input('note');
        $now = date("Y-m-d H:i:s");
        $msTrackId = DB::table("order_list")->where("id",$id)->value("ms_track_id");
        $username = Auth::user()->username;

        //Delete Order
        $arrayDeleteOrder = [
            "id" => $id,
            "now" => $now,
            "note" => $note
        ];
        $deleteOrder = $this->deleteOrderService->execute($arrayDeleteOrder);
        if($deleteOrder['status']!==200){
            $encode = array("status" => $deleteOrder['status'], "title" => $deleteOrder['title'], "text" => $deleteOrder['text']);
            return json_encode($encode);
        }

        $text = "Berhasil Hapus Order";

        //Delete Shipment
        if($msTrackId!==""){
            $text = "Berhasil Hapus Order & Shipment";

            $arrayDeleteShipment = [
                "id" => $msTrackId,
                "now" => $now,
                "note" => $note
            ];
            $deleteShipment = $this->deleteShipmentService->execute($arrayDeleteShipment);
            if($deleteShipment['status']!==200){
                $encode = array("status" => $deleteShipment['status'], "title" => $deleteShipment['title'], "text" => $deleteShipment['text']);
                return json_encode($encode);
            }
        }

        $encode = array("status" => 200, "title" => "Berhasil", "text" => $text);
        return json_encode($encode);
    }

    public function changeToCor(Request $request)
    {
        $id = $request->input("id");
        $result = $this->changeService->execute($id);
        return json_encode($result);
    }

    public function editOrderStatus(Request $request)
    {
        $this->roleAccess();
        $status = $request->input("status");
        $orderId = $request->input("orderId");
        $statusNow = $status == "READY" ? "HOLD" : "READY";
        $username = Auth::user()->username;

        if(!Auth::user()->shiplist_ganti_status){
            $encode = array("status" => "Gagal","text" => "Anda Tidak Berwenang Mengganti Status Order");
            return json_encode($encode);
        }

        $update = DB::table("order_list")->where("id", "=", $orderId)->update(["order_status_id" => $statusNow]);
        $encode = array("status" => "Gagal");
        if ($update) {
            $encode = array("status" => "Berhasil");
        }

        return json_encode($encode);
    }

    public function shiplist()
    {
        if(!env('SHIP_LIST')){
            if(Auth::user()->username!="dev8th"){
                return view('pages.maintenance');
            }
        }
        $this->roleAccess();
        $data['warehouse'] = $this->wareModel::all();
        $data['service'] = $this->servModel::all();
        $data['additional'] = DB::table("additional_list")->orderBy("order_byid", "asc")->get();
        $data['template'] = DB::table("template_list")->get();
        $data['customer'] = $this->custModel::all();
        $view = Auth::user()->role_id==755387?"pages.shiplistpacker":"pages.shiplist";
        return view($view, $data);
    }

    // public function buatInvoiceQueue(Request $request)
    // {
    //     $other = $request->input("other");
    //     $invoiceId = $request->input("invoiceId");
    //     $uniqId = $request->input("uniqId");

    //     if($other=="false"){
            
    //         //Check WA Blast
    //         $templateId = $request->input("templateId");
    //         if($invoiceId==""){
    //             $dataCheckWaBlast = [
    //                 $request->input("mismassOrderId"),
    //                 Auth::user()->username,
    //                 "BI",
    //             ];
    //             $checkWaBlast = $this->whatsappModel->checkTimeBlast($dataCheckWaBlast);
    //             if(!$checkWaBlast['status']){
    //                 $encode = array("status" => 505, "title" => "Gagal", "message" => "Menunggu Waktu Whatsapp Blast", "queuetime" => $checkWaBlast['queueTime'], "createdtime" => $checkWaBlast['createdTime'], "now" => $checkWaBlast['now']);
    //                 return json_encode($encode);
    //             }
    //             $templateId = $checkWaBlast['templateId'];
    //         }

    //         if($invoiceId==""){
    //             $uniqId = str::random(30);
    //             DB::table('pool_invoice_id')->insert(
    //                 ['uniq_id' => $uniqId]
    //             );
    //             $mismass_invoice_id = DB::table('pool_invoice_id')->where("uniq_id", $uniqId)->value("id");
    //             $prefix_mismass_invoice_id = "INV/AJV/" . date("y");
    //             $invoiceId = $prefix_mismass_invoice_id . $mismass_invoice_id;
    //         }

    //         $serviceName =  DB::table('service_list')->where("id", $request->input("serviceval"))->value("name");

    //         $data = [
    //             "cust_id" => $request->input("dbCustId"),
    //             "cust_type_id" => $request->input("dbCustTypeId"),
    //             "mismass_order_id" => $request->input("mismassOrderId"),
    //             "mismass_invoice_id" => $invoiceId,
    //             "warehouse_id" => $request->input("warehouseval"),
    //             "service_id" => $request->input("serviceval"),
    //             "sender_first_name" => $this->noSingleQuo($request->input("senderFirstName")) ?? "",
    //             "sender_middle_name" => $this->noSingleQuo($request->input("senderMiddleName")) ?? "",
    //             "sender_last_name" => $this->noSingleQuo($request->input("senderLastName")) ?? "",
    //             "sender_email" => $request->input("senderEmail") ?? "",
    //             "sender_phone" => $request->input("senderPhone") ?? "",
    //             "sender_address" => $this->noSingleQuo($request->input("senderAddress")) ?? "",
    //             "sender_sub_district" => $this->noSingleQuo($request->input("senderSubDistrict")) ?? "",
    //             "sender_district" => $this->noSingleQuo($request->input("senderDistrict")) ?? "",
    //             "sender_city" => $this->noSingleQuo($request->input("senderCity")) ?? "",
    //             "sender_prov" => $this->noSingleQuo($request->input("senderProv")) ?? "",
    //             "sender_postal_code" => $request->input("senderPostalCode") ?? "",
    
    //             "cons_first_name" => $this->noSingleQuo($request->input("consFirstName")) ?? "",
    //             "cons_middle_name" => $this->noSingleQuo($request->input("consMiddleName")) ?? "",
    //             "cons_last_name" => $this->noSingleQuo($request->input("consLastName")) ?? "",
    //             "cons_email" => $request->input("consEmail") ?? "",
    //             "cons_phone" => $request->input("consPhone") ?? "",
    //             "cons_address" => $this->noSingleQuo($request->input("consAddress")),
    //             "cons_sub_district" => $this->noSingleQuo($request->input("consSubDistrict")) ?? "",
    //             "cons_district" => $this->noSingleQuo($request->input("consDistrict")) ?? "",
    //             "cons_city" => $this->noSingleQuo($request->input("consCity")) ?? "",
    //             "cons_prov" => $this->noSingleQuo($request->input("consProv")) ?? "",
    //             "cons_postal_code" => $request->input("consPostalCode") ?? "",
                
    //             "length" => $request->input("panjang")!="" ? $this->normalizeInput($request->input("panjang")) : 0,
    //             "width" => $request->input("lebar")!="" ? $this->normalizeInput($request->input("lebar")) : 0,
    //             "height" => $request->input("tinggi")!="" ? $this->normalizeInput($request->input("tinggi")) : 0,
    //             "weight" => $request->input("kg")!="" ? $this->normalizeInput($request->input("kg")) : 0,
    //             "cbm" => $request->input("cbm")!="" ? $this->normalizeInput($request->input("cbm")) : 0,
    //             "actual_weight" => $request->input("actualKg")!="" ? $this->normalizeInput($request->input("actualKg")) : 0,
    //             "item" => $request->input("item")!="" ? $this->normalizeInput($request->input("item")) : 0,
    //             "service_name" => $serviceName,
    //             "service_price_per" => $request->input("pricePer")!="" ? $this->normalizeInput($request->input("pricePer")) : 0,
    
    //             "discount" => $this->normalizeInput($request->input("discount")),
    //             "additional_desc" => $request->input("additionalDesc") ?? "",
    //             "additional_nom" => $this->normalizeInput($request->input("additionalNominal")),
    //             "packing" => $this->normalizeInput($request->input("packing")),
    //             "packing_per" => $this->normalizeInput($request->input("packingPer")),
    //             "packing_total" => $this->normalizeInput($request->input("packingTotal")),
    //             "packing_desc" => $request->input("packingDesc") ?? "",
    //             "import_permit" => $this->normalizeInput($request->input("import")),
    //             "import_permit_per" => $this->normalizeInput($request->input("importPer")),
    //             "import_permit_total" => $this->normalizeInput($request->input("importTotal")),
    //             "import_permit_desc" => $request->input("importDesc") ?? "",
    //             "document" => $this->normalizeInput($request->input("document")),
    //             "document_per" => $this->normalizeInput($request->input("documentPer")),
    //             "document_total" => $this->normalizeInput($request->input("documentTotal")),
    //             "document_desc" => $request->input("documentDesc") ?? "",
    //             "dr_medicine" => $this->normalizeInput($request->input("medicine")),
    //             "dr_medicine_per" => $this->normalizeInput($request->input("medicinePer")),
    //             "dr_medicine_total" => $this->normalizeInput($request->input("medicineTotal")),
    //             "dr_medicine_desc" => $request->input("medicineDesc") ?? "",
    //             "insurance_item_price" => $this->normalizeInput($request->input("insurancePriceItem")),
    //             "insurance_percent" => $this->normalizeInput($request->input("insurancePercent")),
    //             "insurance_total" => round($this->normalizeInput($request->input("insuranceTotal"))),
    //             "fee_item_price" => $this->normalizeInput($request->input("feePriceItem")),
    //             "fee_percent" => $this->normalizeInput($request->input("feePercent")),
    //             "fee_total" => round($this->normalizeInput($request->input("feeTotal"))),
    //             "tax_item_price" => $this->normalizeInput($request->input("taxPriceItem")),
    //             "tax_percent" => $this->normalizeInput($request->input("taxPercent")),
    //             "tax_total" => round($this->normalizeInput($request->input("taxTotal"))),
    //             "extra_cost_price" => $this->normalizeInput($request->input("extraCostPrice")),
    //             "extra_cost_dest" => $request->input("extraCostDest") ?? "",
    //             "extra_cost_vendor_name" => $request->input("extraCostVendorName") ?? "",
    //             "extra_cost_shipping_number" => $request->input("extraCostShippingNum") ?? "",
    //             "pickup_weight" => $this->normalizeInput($request->input("pickUpWeight")),
    //             "pickup_charge" => $this->normalizeInput($request->input("pickUpCharge")),
    //             "sub_total" => $request->input("subTotal")!="" ? $this->normalizeInput($request->input("subTotal")) : 0
    //             ];
    
    //             $insert = $this->invoiceModel::create($data);
    
    //             if ($insert) {
    //                 $encode = array("status" => 200, "invoiceId" => $invoiceId, "uniqId" => $uniqId, "templateId" => $templateId);
    //                 return json_encode($encode);
    //             }

    //     }

    //     // dd($request->input());

    //     //Payment
    //     $result = "-";
    //     $paymentLink = "-";
    //     $randomLink = str::random(20);

    //     if($request->input("pembayaran")=="DOKU"){

    //         $params = array();
    //         $params['order']['price'] = $request->input("totalBiaya")!="" ? $this->normalizeInput($request->input("totalBiaya")) : 0;
    //         $params['order']['invoice_number'] = $invoiceId;
    //         $params['payment']['payment_due_date'] = 7*1440;
    //         $params['customer']['id'] = $request->input("dbCustId");
    //         $params['customer']['name'] = $request->input("dbCustTypeId")=="IND" ? $request->input("consFirstName")." ".$request->input("consMiddleName")." ".$request->input("consLastName") : $request->input("senderFirstName")." ".$request->input("senderMiddleName")." ".$request->input("senderLastName");
    //         $params['customer']['phone'] = $request->input("dbCustTypeId")=="IND" ? $request->input("consPhone") : $request->input("senderPhone");
    //         $params['customer']['email'] = $request->input("dbCustTypeId")=="IND" ? $request->input("consEmail") : $request->input("senderEmail");
    //         $params['customer']['address'] = $request->input("dbCustTypeId")=="IND" ? $request->input("consAddress") : $request->input("senderAddress");
    //         $result = $this->dokuModel->generate($params);

    //         if($result==null){
    //             $encode = array("status" => 500, "title" => "Gagal", "text" => "Doku Tidak Memberikan Respon.");
    //             return json_encode($encode);
    //         }

    //         if($result['status']==503){
    //             $encode = array("status" => 500, "title" => "Gagal",  "text" => $result['message']);
    //             return json_encode($encode);
    //         }

    //         $paymentLink = url("/payment"."/".$request->input("uniqId"));

    //         $updateData = [
    //             "doku_token_id" => $result['token_id'],
    //             "doku_expired_date" => date("Y-m-d H:i:s", strtotime($result['expired_date'])),
    //             "payment_status" => "PENDING",
    //             "doku_link" => $request->input("uniqId"),
    //             "doku_invoice_id" => $request->input("invoiceDoku") ?? "",
    //             "created_by" => Auth::user()->username,
    //             "updated_by" => Auth::user()->username,
    //             // "mismass_invoice_date" => date("Y-m-d"),
    //             "mismass_invoice_date" => date("Y-m-d", strtotime($this->dateFilterFormat($request->input("tanggalInvoice"))[0])),
    //             "mismass_invoice_link" => $randomLink,
    //             "invoice_status" => "UNPAID",
    //             "template_id" => $request->input("templateId"),
    //             "fc_symbol" => $this->normalizeInput($request->input("foreignRateValue"))>0?$request->input("foreignSymbol"):"", 
    //             "fc_value" => $this->normalizeInput($request->input("foreignRateValue"))>0?$this->normalizeInput($request->input("foreignRateValue")):0
    //         ];

    //     }elseif($request->input("pembayaran")=="BANK"){

    //         $updateData = [
    //             "bank_name" => $request->input("namaBank") ?? "",
    //             "bank_account_name" => $request->input("namaRekening") ?? "",
    //             "bank_account_id" => $request->input("noRekening") ?? "",
    //             "payment_status" => "PENDING",
    //             "created_by" => Auth::user()->username,
    //             "updated_by" => Auth::user()->username,
    //             // "mismass_invoice_date" => date("Y-m-d"),
    //             "mismass_invoice_date" => date("Y-m-d", strtotime($this->dateFilterFormat($request->input("tanggalInvoice"))[0])),
    //             "mismass_invoice_link" => $randomLink,
    //             "invoice_status" => "UNPAID",
    //             "template_id" => $request->input("templateId"),
    //             "fc_symbol" => $this->normalizeInput($request->input("foreignRateValue"))>0?$request->input("foreignSymbol"):"", 
    //             "fc_value" => $this->normalizeInput($request->input("foreignRateValue"))>0?$this->normalizeInput($request->input("foreignRateValue")):0
    //         ];

    //     }

    //     $updatingData = $this->invoiceModel->where("mismass_invoice_id",$invoiceId)->update($updateData);

    //     if($request->input("trackId")!=null){

    //         for($i = 0; $i <= count($request->input("trackId")) - 1; $i++){
    //             //Update Order List
    //             $dataOrder = [
    //                 "invoice_id" => $invoiceId
    //             ];
    //             $UpdateDataOrder = DB::table("order_list")->where("ms_track_id",$request->input("trackId")[$i])->update($dataOrder);

    //             $updateShipTripData = [
    //                 "cr_inv_created_at" => date("Y-m-d H:i:s"),
    //                 // "cr_inv_man_created_at" => date("Y-m-d H:i:s"),
    //                 "cr_inv_man_created_at" => date("Y-m-d", strtotime($this->dateFilterFormat($request->input("tanggalInvoice"))[0])),
    //                 "cr_inv_created_by" => Auth::user()->username,
    //                 "cr_inv_updated_at" => date("Y-m-d H:i:s"),
    //                 "cr_inv_updated_by" => Auth::user()->username
    //             ];
    //             $updateShipTrip = DB::table("shiptrip_list")->where("ms_track_id",$request->input('trackId')[$i])->update($updateShipTripData);

    //             //Create Tracking
    //             $dataTracking = [
    //                 "created_at" => date("Y-m-d H:i:s"),
    //                 "created_by" => Auth::user()->username,
    //                 "ms_track_id" => $request->input('trackId')[$i],
    //                 "track_status_id" => 10,
    //                 "track_status_manual_id" => "A",
    //                 "text" => DB::table("shiptrip_track_status")->where("id","10")->value("value")
    //             ];
    //             $updateTracking = DB::table("shiptrip_track_list")->insert($dataTracking);
    //         }

    //     }else{
    //         //Update Order List
    //         $dataOrder = [
    //             "invoice_id" => $invoiceId
    //         ];
    //         $UpdateDataOrder = DB::table("order_list")->where("id",$request->input("mismassOrderId"))->update($dataOrder);
    //     }

    //     //Send WA
    //     $data = array(
    //         "phone" => $request->input("dbCustTypeId")=="IND" ? $request->input("consPhone") : $request->input("senderPhone"),
    //         "fullName" => $request->input("dbCustTypeId")=="IND" ? $request->input("consFirstName")." ".$request->input("consMiddleName")." ".$request->input("consLastName") : $request->input("senderFirstName")." ".$request->input("senderMiddleName")." ".$request->input("senderLastName"),
    //         "invoiceLink" => "https://print.app-mismass.com/p/".$randomLink,
    //         "invoiceDate" => $this->dateFormatIndo(date("Y-m-d H:i:s"),1),
    //         "invoice" => $invoiceId,
    //         "paymentLink" => $paymentLink,
    //         "statusPay" => "UNPAID",
    //         "custType" => DB::table("cust_type_list")->where("id",$request->input("dbCustTypeId"))->value("name"),
    //         "reference" => DB::table("cust_list")->where("id",$request->input("dbCustId"))->value("reference"),
    //         "templateId" => $request->input("templateId")
    //     );

    //     $descSendWa = "-";
    //     if(env('WA_GATEWAY')){
    //         $resultSendWa = $this->whatsappModel->createInvoiceSendWA($data);
    //         $statusSendWA = $resultSendWa->success ? "Terkirim" : "Gagal Kirim";
    //         $refSendWA = $resultSendWa->ref;
    //         $msgSendWA = $resultSendWa->message;
    //         $phoneSendWa = "(".$resultSendWa->phone.")";
    //         $descSendWa = "<b>".$statusSendWA." (".$refSendWA.")</b> - ".$msgSendWA." ".$phoneSendWa."<br>";
    //     }

    //     //Send Email
    //     if(env('SEND_EMAIL')){
    //         $ccEmail = explode(",",env('MAIL_CC'));
    //         $bccEmail = env('MAIL_BCC');
    //         $email = $request->input("dbCustTypeId")=="IND" ? $request->input("consEmail") : $request->input("senderEmail");
    //         $data['subject'] = "Create Invoice | ".$invoiceId;
    //         $data['modes'] = "CINV"; //Webform = "WEB", create invoice = "CINV", create shipment = "CSHI"
    //         $sendingMail = Mail::to($email)
    //                         ->cc($ccEmail)
    //                         ->bcc($bccEmail)
    //                         ->send(new SendMail($data));
    //     }

    //     //Create History
    //     $dataHistory = [
    //         "codename" => "BI",
    //         "created_at" => date("Y-m-d H:i:s"),
    //         "created_by" => Auth::user()->username,
    //         "whatsapp_desc" => "Send Whatsapp Status : ".$descSendWa,
    //         "description" => $this->createDescForInvoice($invoiceId,$descSendWa,"BI"),
    //     ];
    //     $insertHistory = DB::table("history_list")->insert($dataHistory);
    //     if(!$insertHistory){
    //         $encode = array("status" => 500, "title" => "Gagal", "text" => "Gagal Buat History");
    //         return json_encode($encode);
    //     }

    //     $encode = array("status" => 200, "title" => "Berhasil", "text" => "Data Invoice Berhasil dibuat dan telah dikirim ke Whatsapp Customer. Silahkan Cek Pada Tabel Tracking.", "url" => url('/printout/invoice/' . $this->invOnlyId($invoiceId)));
    //     return json_encode($encode);
    // }
    
    public function buatInvoiceQueue(Request $request)
    {
        $other = $request->input("other");
        $invoiceId = $request->input("invoiceId");
        $uniqId = $request->input("uniqId");

        if($other=="false"){
            
            //Check WA Blast
            $waTemplateId = $request->input("waTemplateId");
            if($invoiceId==""){
                $dataCheckWaBlast = [
                    $request->input("mismassOrderId"),
                    Auth::user()->username,
                    "BI",
                ];
                $checkWaBlast = $this->whatsappModel->checkTimeBlast($dataCheckWaBlast);
                if(!$checkWaBlast['status']){
                    $encode = array("status" => 505, "title" => "Gagal", "message" => "Menunggu Waktu Whatsapp Blast", "queuetime" => $checkWaBlast['queueTime'], "createdtime" => $checkWaBlast['createdTime'], "now" => $checkWaBlast['now']);
                    return json_encode($encode);
                }
                $waTemplateId = $checkWaBlast['waTemplateId'];
            }

            if($invoiceId==""){
                $uniqId = str::random(30);
                DB::table('pool_invoice_id')->insert(
                    ['uniq_id' => $uniqId]
                );
                $mismass_invoice_id = DB::table('pool_invoice_id')->where("uniq_id", $uniqId)->value("id");
                $prefix_mismass_invoice_id = "INV/AJV/" . date("y");
                $invoiceId = $prefix_mismass_invoice_id . $mismass_invoice_id;
            }

            $serviceName =  DB::table('service_list')->where("id", $request->input("serviceval"))->value("name");
            $indToCor = DB::table("order_list")->where("id",$request->input("mismassOrderId"))->value("ind_to_cor");

            $data = [
                "cust_id" => $request->input("dbCustId"),
                "cust_type_id" => $request->input("dbCustTypeId"),
                "ind_to_cor" => $indToCor,
                "mismass_order_id" => $request->input("mismassOrderId"),
                "mismass_invoice_id" => $invoiceId,
                "warehouse_id" => $request->input("warehouseval"),
                "service_id" => $request->input("serviceval"),
                "sender_first_name" => $this->noSingleQuo($request->input("senderFirstName")) ?? "",
                "sender_middle_name" => $this->noSingleQuo($request->input("senderMiddleName")) ?? "",
                "sender_last_name" => $this->noSingleQuo($request->input("senderLastName")) ?? "",
                "sender_email" => $request->input("senderEmail") ?? "",
                "sender_phone" => $request->input("senderPhone") ?? "",
                "sender_address" => $this->noSingleQuo($request->input("senderAddress")) ?? "",
                "sender_sub_district" => $this->noSingleQuo($request->input("senderSubDistrict")) ?? "",
                "sender_district" => $this->noSingleQuo($request->input("senderDistrict")) ?? "",
                "sender_city" => $this->noSingleQuo($request->input("senderCity")) ?? "",
                "sender_prov" => $this->noSingleQuo($request->input("senderProv")) ?? "",
                "sender_postal_code" => $request->input("senderPostalCode") ?? "",
    
                "cons_first_name" => $this->noSingleQuo($request->input("consFirstName")) ?? "",
                "cons_middle_name" => $this->noSingleQuo($request->input("consMiddleName")) ?? "",
                "cons_last_name" => $this->noSingleQuo($request->input("consLastName")) ?? "",
                "cons_email" => $request->input("consEmail") ?? "",
                "cons_phone" => $request->input("consPhone") ?? "",
                "cons_address" => $this->noSingleQuo($request->input("consAddress")),
                "cons_sub_district" => $this->noSingleQuo($request->input("consSubDistrict")) ?? "",
                "cons_district" => $this->noSingleQuo($request->input("consDistrict")) ?? "",
                "cons_city" => $this->noSingleQuo($request->input("consCity")) ?? "",
                "cons_prov" => $this->noSingleQuo($request->input("consProv")) ?? "",
                "cons_postal_code" => $request->input("consPostalCode") ?? "",
                
                "length" => $request->input("panjang")!="" ? $this->normalizeInput($request->input("panjang")) : 0,
                "width" => $request->input("lebar")!="" ? $this->normalizeInput($request->input("lebar")) : 0,
                "height" => $request->input("tinggi")!="" ? $this->normalizeInput($request->input("tinggi")) : 0,
                "weight" => $request->input("kg")!="" ? $this->normalizeInput($request->input("kg")) : 0,
                "cbm" => $request->input("cbm")!="" ? $this->normalizeInput($request->input("cbm")) : 0,
                "actual_weight" => $request->input("actualKg")!="" ? $this->normalizeInput($request->input("actualKg")) : 0,
                "item" => $request->input("item")!="" ? $this->normalizeInput($request->input("item")) : 0,
                "service_name" => $serviceName,
                "service_price_per" => $request->input("pricePer")!="" ? $this->normalizeInput($request->input("pricePer")) : 0,
    
                "discount" => $this->normalizeInput($request->input("discount")),
                "additional_desc" => $request->input("additionalDesc") ?? "",
                "additional_nom" => $this->normalizeInput($request->input("additionalNominal")),
                "packing" => $this->normalizeInput($request->input("packing")),
                "packing_per" => $this->normalizeInput($request->input("packingPer")),
                "packing_total" => $this->normalizeInput($request->input("packingTotal")),
                "packing_desc" => $request->input("packingDesc") ?? "",
                "import_permit" => $this->normalizeInput($request->input("import")),
                "import_permit_per" => $this->normalizeInput($request->input("importPer")),
                "import_permit_total" => $this->normalizeInput($request->input("importTotal")),
                "import_permit_desc" => $request->input("importDesc") ?? "",
                "export_permit" => $this->normalizeInput($request->input("export")),
                "export_permit_per" => $this->normalizeInput($request->input("exportPer")),
                "export_permit_total" => $this->normalizeInput($request->input("exportTotal")),
                "export_permit_desc" => $request->input("exportDesc") ?? "",
                "document" => $this->normalizeInput($request->input("document")),
                "document_per" => $this->normalizeInput($request->input("documentPer")),
                "document_total" => $this->normalizeInput($request->input("documentTotal")),
                "document_desc" => $request->input("documentDesc") ?? "",
                "dr_medicine" => $this->normalizeInput($request->input("medicine")),
                "dr_medicine_per" => $this->normalizeInput($request->input("medicinePer")),
                "dr_medicine_total" => $this->normalizeInput($request->input("medicineTotal")),
                "dr_medicine_desc" => $request->input("medicineDesc") ?? "",
                "insurance_item_price" => $this->normalizeInput($request->input("insurancePriceItem")),
                "insurance_percent" => $this->normalizeInput($request->input("insurancePercent")),
                "insurance_total" => round($request->input("insuranceTotal")),
                "fee_item_price" => $this->normalizeInput($request->input("feePriceItem")),
                "fee_percent" => $this->normalizeInput($request->input("feePercent")),
                "fee_total" => round($request->input("feeTotal")),
                "tax_item_price" => $this->normalizeInput($request->input("taxPriceItem")),
                "tax_percent" => $this->normalizeInput($request->input("taxPercent")),
                "tax_total" => round($request->input("taxTotal")),
                "extra_cost_price" => $this->normalizeInput($request->input("extraCostPrice")),
                "extra_cost_dest" => $request->input("extraCostDest") ?? "",
                "extra_cost_vendor_name" => $request->input("extraCostVendorName") ?? "",
                "extra_cost_shipping_number" => $request->input("extraCostShippingNum") ?? "",
                "pickup_weight" => $this->normalizeInput($request->input("pickUpWeight")),
                "pickup_charge" => $this->normalizeInput($request->input("pickUpCharge")),
                "other_pickup_fee" => $this->normalizeInput($request->input("pickUpFee")),
                "adjust_fee" => $this->normalizeInput($request->input("adjustFee")),
                "sub_total" => $request->input("subTotal")!="" ? $this->normalizeInput($request->input("subTotal")) : 0
                ];
    
                $insert = $this->invoiceModel::create($data);
    
                if ($insert) {
                    $encode = array("status" => 200, "invoiceId" => $invoiceId, "uniqId" => $uniqId, "waTemplateId" => $waTemplateId);
                    return json_encode($encode);
                }

        }

        //Payment
        $result = "-";
        $paymentLink = "-";
        $randomLink = str::random(20);

        if($request->input("pembayaran")=="DOKU"){

            $dokuName = $this->alphaNumSpace($request->input("senderFirstName")." ".$request->input("senderMiddleName")." ".$request->input("senderLastName"));
            $dokuPhone = $request->input("senderPhone");
            $dokuEmail = $request->input("senderEmail");
            $dokuAddress = $request->input("senderAddress");
            if($request->input("dbCustTypeId")=="IND"){
                $dokuName = $this->alphaNumSpace($request->input("consFirstName")." ".$request->input("consMiddleName")." ".$request->input("consLastName"));
                $dokuPhone = $request->input("consPhone");
                $dokuEmail = $request->input("consEmail");
                $dokuAddress = $request->input("consAddress");
            }

            $params = array();
            $params['order']['price'] = $request->input("totalBiaya")!="" ? $this->normalizeInput($request->input("totalBiaya")) : 0;
            $params['order']['invoice_number'] = $invoiceId;
            $params['payment']['payment_due_date'] = 7*1440;
            $params['customer']['id'] = $request->input("dbCustId");
            $params['customer']['name'] = $dokuName;
            $params['customer']['phone'] = $dokuPhone;
            $params['customer']['email'] = $dokuEmail;
            $params['customer']['address'] = $dokuAddress;
            $result = $this->dokuModel->generate($params);

            if($result==null){
                $encode = array("status" => 500, "title" => "Gagal", "text" => "Doku Tidak Memberikan Respon. Silahkan Create Invoice Kembali!");
                return json_encode($encode);
            }

            if($result['status']==503||$result['status']==28){
                $encode = array("status" => 500, "title" => "Gagal",  "text" => $result['message']);
                return json_encode($encode);
            }

            $paymentLink = url("/payment"."/".$request->input("uniqId"));

            $updateData = [
                "doku_token_id" => $result['token_id'],
                "doku_expired_date" => date("Y-m-d H:i:s", strtotime($result['expired_date'])),
                "payment_status" => "PENDING",
                "doku_link" => $request->input("uniqId"),
                "doku_invoice_id" => $request->input("invoiceDoku") ?? "",
                "created_by" => Auth::user()->username,
                "updated_by" => Auth::user()->username,
                // "mismass_invoice_date" => date("Y-m-d"),
                "mismass_invoice_date" => date("Y-m-d", strtotime($this->dateFilterFormat($request->input("tanggalInvoice"))[0])),
                "mismass_invoice_link" => $randomLink,
                "invoice_status" => "UNPAID",
                "template_id" => $request->input("templateId"),
                "fc_symbol" => $this->normalizeInput($request->input("foreignRateValue"))>0?$request->input("foreignSymbol"):"", 
                "fc_value" => $this->normalizeInput($request->input("foreignRateValue"))>0?$this->normalizeInput($request->input("foreignRateValue")):0
            ];

        }elseif($request->input("pembayaran")=="BANK"){

            $updateData = [
                "bank_name" => $request->input("namaBank") ?? "",
                "bank_account_name" => $request->input("namaRekening") ?? "",
                "bank_account_id" => $request->input("noRekening") ?? "",
                "payment_status" => "PENDING",
                "created_by" => Auth::user()->username,
                "updated_by" => Auth::user()->username,
                // "mismass_invoice_date" => date("Y-m-d"),
                "mismass_invoice_date" => date("Y-m-d", strtotime($this->dateFilterFormat($request->input("tanggalInvoice"))[0])),
                "mismass_invoice_link" => $randomLink,
                "invoice_status" => "UNPAID",
                "template_id" => $request->input("templateId"),
                "fc_symbol" => $this->normalizeInput($request->input("foreignRateValue"))>0?$request->input("foreignSymbol"):"", 
                "fc_value" => $this->normalizeInput($request->input("foreignRateValue"))>0?$this->normalizeInput($request->input("foreignRateValue")):0
            ];

        }

        $updatingData = $this->invoiceModel->where("mismass_invoice_id",$invoiceId)->update($updateData);

        if($request->input("trackId")!=null){

            for($i = 0; $i <= count($request->input("trackId")) - 1; $i++){
                //Update Order List
                $dataOrder = [
                    "invoice_id" => $invoiceId
                ];
                $UpdateDataOrder = DB::table("order_list")->where("ms_track_id",$request->input("trackId")[$i])->update($dataOrder);

                $updateShipTripData = [
                    "cr_inv_created_at" => date("Y-m-d H:i:s"),
                    // "cr_inv_man_created_at" => date("Y-m-d H:i:s"),
                    "cr_inv_man_created_at" => date("Y-m-d", strtotime($this->dateFilterFormat($request->input("tanggalInvoice"))[0])),
                    "cr_inv_created_by" => Auth::user()->username,
                    "cr_inv_updated_at" => date("Y-m-d H:i:s"),
                    "cr_inv_updated_by" => Auth::user()->username
                ];
                $updateShipTrip = DB::table("shiptrip_list")->where("ms_track_id",$request->input('trackId')[$i])->update($updateShipTripData);

                //Create Tracking
                $dataTracking = [
                    "created_at" => date("Y-m-d H:i:s"),
                    "created_by" => Auth::user()->username,
                    "ms_track_id" => $request->input('trackId')[$i],
                    "track_status_id" => 10,
                    "track_status_manual_id" => "A",
                    "text" => DB::table("shiptrip_track_status")->where("id","10")->value("value")
                ];
                $updateTracking = DB::table("shiptrip_track_list")->insert($dataTracking);
            }

        }else{
            //Update Order List
            $dataOrder = [
                "invoice_id" => $invoiceId
            ];
            $UpdateDataOrder = DB::table("order_list")->where("id",$request->input("mismassOrderId"))->update($dataOrder);
        }

        //Send WA
        $data = array(
            "phone" => $request->input("dbCustTypeId")=="IND" ? $request->input("consPhone") : $request->input("senderPhone"),
            "fullName" => $request->input("dbCustTypeId")=="IND" ? $request->input("consFirstName")." ".$request->input("consMiddleName")." ".$request->input("consLastName") : $request->input("senderFirstName")." ".$request->input("senderMiddleName")." ".$request->input("senderLastName"),
            "invoiceLink" => url('/p')."/".$randomLink,
            "invoiceDate" => $this->dateFormatIndo(date("Y-m-d H:i:s"),1),
            "invoice" => $invoiceId,
            "paymentLink" => $paymentLink,
            "statusPay" => "UNPAID",
            "custType" => DB::table("cust_type_list")->where("id",$request->input("dbCustTypeId"))->value("name"),
            "reference" => DB::table("cust_list")->where("id",$request->input("dbCustId"))->value("reference"),
            "templateId" => $request->input("waTemplateId")
        );

        $descSendWa = "-";
        if(env('WA_GATEWAY')){
            $resultSendWa = $this->whatsappModel->createInvoiceSendWA($data);
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
            $email = $request->input("dbCustTypeId")=="IND" ? $request->input("consEmail") : $request->input("senderEmail");
            $data['subject'] = "Create Invoice | ".$invoiceId;
            $data['modes'] = "CINV"; //Webform = "WEB", create invoice = "CINV", create shipment = "CSHI"
            $sendingMail = Mail::to($email)
                            ->cc($ccEmail)
                            ->bcc($bccEmail)
                            ->send(new SendMail($data));
        }

        //Create History
        $dataHistory = [
            "codename" => "BI",
            "created_at" => date("Y-m-d H:i:s"),
            "created_by" => Auth::user()->username,
            "whatsapp_desc" => "Send Whatsapp Status : ".$descSendWa,
            "description" => $this->createDescForInvoice($invoiceId,$descSendWa,"BI"),
        ];
        $insertHistory = DB::table("history_list")->insert($dataHistory);
        if(!$insertHistory){
            $encode = array("status" => 500, "title" => "Gagal", "text" => "Gagal Buat History");
            return json_encode($encode);
        }

        $encode = array("status" => 200, "title" => "Berhasil", "text" => "Data Invoice Berhasil dibuat dan telah dikirim ke Whatsapp Customer. Silahkan Cek Pada Tabel Tracking.", "url" => url('/printout/invoice/' . $this->invOnlyId($invoiceId)));
        return json_encode($encode);
    }

    public function tableInvoice(string $custTypeId, Request $request)
    {
        $this->roleAccess();
        $data = [];
        $filter = array();
        $no = $request->input('start');
        $search = $request->input('search')['value'];
        $filterTanggal = $this->dateFilterFormat($request->input('filterTanggal'));
        $filterWarehouse = $request->input('filterWarehouse');
        $filterService = $request->input('filterService');
        $filterPay = $request->input('filterPay');
        $filterPayStatus = $request->input('filterPayStatus');
        $filter = [
            "filterTanggal" => $filterTanggal,
            "filterWarehouse" => $filterWarehouse,
            "filterService" => $filterService,
            "filterPay" => $filterPay,
            "filterPayStatus" => $filterPayStatus,
            "custTypeId" => $custTypeId
        ];
        $lists = $this->invoiceModel->getDT($request, $search, $filter);

        foreach ($lists as $list) {
            $getData = DB::table('data_list')->select('id','inv_add','sub_total','weight','item','length','height','width','cons_first_name','cons_middle_name','cons_last_name','cons_phone','cons_address','cons_sub_district','cons_district','cons_city','cons_prov','cons_postal_code')->where('mismass_invoice_id',$list->mismass_invoice_id)->get();
            $countRow = count($getData);
            $dokuInvoiceId = $list->doku_invoice_id != "" ? ($list->updated_at!=$list->created_at?"<div style='color:red'>".$list->doku_invoice_id."</div>":$list->doku_invoice_id) : "-";

            $getRank = DB::table('users')->where("username",$list->updated_by)->value("rank");
            $jabatan = $getRank != null ? "<div class='bg-mismass' style='padding:1px 5px;'>".$getRank."</div>" : "";

            $fullName = $custTypeId=="IND" ? "<div class='fw-bold'>".$list->cons_first_name . " " . $list->cons_middle_name . " " . $list->cons_last_name."</div>" : "<div class='fw-bold'>".$list->sender_first_name . " " . $list->sender_middle_name . " " . $list->sender_last_name."</div>";
            $detailAddr = $custTypeId=="IND" ? "<div>".$list->cons_phone."</div><div>".$list->cons_city . ", " . $list->cons_prov . ", " . $list->cons_postal_code."</div>" : "<div>".$list->sender_phone."</div><div>".$list->sender_city . ", " . $list->sender_prov . ", " . $list->sender_postal_code."</div>";

            $checkmstrack = DB::table("order_list")->selectRaw("GROUP_CONCAT(ms_track_id SEPARATOR ', ') AS mstracks, ms_track_id")->where("invoice_id",$list->mismass_invoice_id)->first();
            $mstracks = "";
            $lastTrackingDate = "-";
            $dateNow = $this->dateFormatIndo(date("Y-m-d H:i:s"),1);
            if($checkmstrack->ms_track_id!=""){
                $mstracks = $checkmstrack->mstracks;
                $lastTrackingDate = DB::table("shiptrip_track_list")->where("ms_track_id",$checkmstrack->ms_track_id)->orderBy("created_at","desc")->value("created_at");
                $lastTrackingDate = $this->dateFormatIndo($lastTrackingDate,2);
            }
            
            $totalServiceOrCust = "<div>".$list->totalInvoice." ".($custTypeId=="IND"?"Service":"Customer")."</div>";

            $getPrimaryTrack = DB::table("order_list")
                                ->select("ms_track_id")
                                ->where("invoice_id",$list->mismass_invoice_id)
                                ->groupBy("ms_track_id")
                                ->orderBy("ms_track_id")
                                ->first();
                                
            $primaryTrack = "";
            if($getPrimaryTrack!=null){
                $primaryTrack = "<div class='fw-bold'>Bulky</div>";
                if($list->inv_add){
                    $primaryTrack = "<div class='fw-bold'>Additional</div>";
                }
                if($getPrimaryTrack->ms_track_id!=""){
                    $primaryTrack="<a class='fw-bold loadTracking' href='".url('/shiptrip/tracking')."?id=".$getPrimaryTrack->ms_track_id."' target='_blank'>".$getPrimaryTrack->ms_track_id."</a>";
                }
            }

            $arrayCreateInvoiceAdd = [
                "list" => $list,
                "filterPayStatus" => $filterPayStatus,
                "msTrackId" => $checkmstrack->ms_track_id
            ];
            $buatInvoiceAddBtn = $this->createInvoiceAddService->createInvoiceAddButton($arrayCreateInvoiceAdd);
            // $buatInvoiceAddBtn = "";

            $buatResiBtn = Auth::user()->shiplist_buat_resi?($filterPayStatus=="SUCCESS"?"<a class='dropdown-item pointlink' id='buatResiBtn' data-mstracks='".$mstracks."' data-getData='".$getData."' data-senderAddress='" . $list->sender_address . ", " . $list->sender_city . ", " . $list->sender_prov . ", " . $list->sender_postal_code . "' data-senderPhone='" . $list->sender_phone . "' data-senderName='" . $list->sender_first_name . " " . $list->sender_middle_name . " " . $list->sender_last_name . "' data-consAddress='" . $list->cons_address . ", " . $list->cons_city . ", " . $list->cons_prov . ", " . $list->cons_postal_code . "' data-consPhone='" . $list->cons_phone . "' data-consName='" . $list->cons_first_name . " " . $list->cons_middle_name . " " . $list->cons_last_name . "' data-custTypeId='".$list->cust_type_id."' data-countRow='".$countRow."' data-totalPrice='" . $this->rupiah($list->totalPrice) . "' data-totalItem='" . $list->totalItem . "' data-totalWeight='" . $list->totalWeight . "' data-custTypeName='" . $list->custTypeName . "' data-mismassInvoiceDate='" . $this->dateFormatIndo($list->created_at,1) . "' data-createdAt='" . $this->dateFormatIndo($list->created_at,1) . "' data-dokuInvoiceId='" . $list->doku_invoice_id . "' data-mismassInvoiceId='" . $list->mismass_invoice_id . "'>Buat Resi</a>":""):"";
            $resendBtn = $filterPayStatus=="FAILED"?"<a class='dropdown-item resendBtn pointlink' data-id='".$list->mismass_invoice_id."'>Resend Link</a>":"";
            $pindahBtn = Auth::user()->shiplist_pindah_status?($filterPayStatus=="PENDING"?"<a class='dropdown-item pointlink' id='pindahBtn' data-last-track-date='".$lastTrackingDate."' data-date-now='".$dateNow."' data-id='".$list->mismass_invoice_id."' data-trackid='".$list->ms_track_id."'>Update Ke Sukses</a>":""):"";
            $printoutInvoice = Auth::user()->shiplist_printout_invoice?"<a class='dropdown-item' href='" . url('/printout/invoice/' . $this->invOnlyId($list->mismass_invoice_id)) . "' target='_blank'>Print Invoice</a>":"";
            // $editInvoiceBtn = Auth::user()->shiplist_edit_invoice?($filterPay=="BANK"&&$filterPayStatus=="PENDING"?"<a class='dropdown-item pointlink' id='editInvoiceBtn' data-tglInv='".date("d-m-Y",strtotime($list->mismass_invoice_date))."' data-id='" . $list->mismass_order_id . "'>Edit Data</a>":""):"";
            $arrayRevisiBtn = [
                "filterPayStatus" => $filterPayStatus,
                "invAdd" => $list->inv_add,
                "invoiceDate" => $list->mismass_invoice_date,
                "orderId" => $list->mismass_order_id,
                "msTrackId" => $checkmstrack->ms_track_id
            ];
            $revisiBtn = $this->editModel->revisiButton($arrayRevisiBtn);
            // $revisiBtn = Auth::user()->revisi_btn?($filterPayStatus=="PENDING"?"<a class='dropdown-item pointlink' id='editInvoiceBtn' data-create-inv-add='false' data-tglInv='".date("d-m-Y",strtotime($list->mismass_invoice_date))."' data-id='" . $list->mismass_order_id . "' data-mstracks='".$checkmstrack->ms_track_id."'>Revisi Invoice</a>":""):"";
            $voidBtn = Auth::user()->void_btn?($filterPayStatus=="PENDING"?"<a class='dropdown-item pointlink' id='voidInvoiceBtn' data-id='" . $this->invOnlyId($list->mismass_invoice_id) . "'>Void Invoice</a>":""):"";
            $hapusBtn = "";
            // $hapusBtn = Auth::user()->shiplist_hapus_invoice?($filterPayStatus!="SUCCESS"?"<a class='dropdown-item pointlink' id='hapusBtn' onclick=\"konfirm_hapus('" . $list->mismass_invoice_id . "','" . $list->mismass_invoice_id . "','Invoice','" . url('/shiplist/hapus/invoice/') . "','shiplist')\"><div style='color:red'>Hapus Data</div></a>":""):"";
            $wholeBtn = "<div class='btn-group dropleft'><button type='button' class='btn btn-secondary nobtn' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'><i class='fas fa-ellipsis-v'></i></button><div class='dropdown-menu' x-placement='right-start' style='position: absolute; transform: translate3d(111px, 0px, 0px); top: 0px; left: 0px; will-change: transform;'>".$buatResiBtn.$pindahBtn.$resendBtn.$printoutInvoice.$revisiBtn.$voidBtn.$buatInvoiceAddBtn.$hapusBtn."</div></div>";
            
            $no++;
            $row = [];
            $row[] = $no;
            $row[] = $primaryTrack;
            $row[] = "<div class='fw-bold'>" . $list->mismass_invoice_id . "</div><div>" . $this->dateFormatIndo($list->mismass_invoice_date,1) . "</div><div class='text-primary'>Created : " . $this->dateFormatIndo($list->created_at,0) . "</div>";
            $row[] = "<div class='fw-bold'>" . $list->updated_by . "</div>".$jabatan."<div>" . $this->dateFormatIndo($list->updated_at,2) . "</div>";
            $row[] = $fullName.$detailAddr;
            $row[] = "<div>" . round($list->totalWeight,2) . " KG</div><div>" . $list->totalItem . " Item</div><div>".round($list->totalCbm,2)." CBM</div>".$totalServiceOrCust;
            
            if(Auth::user()->shiplist_nom){
                $detilDisc = "<div>Total : ".$this->rupiah($list->totalDisc+$list->totalPrice-$list->totalAdtFee)."</div><div>Diskon : ".$this->rupiah($list->totalDisc)."</div><div>OpA Fee : ".$this->rupiah($list->totalAdtFee)."</div>";
                $fcRateValue = $list->fc_symbol!=""?"<div>Nilai Tukar : ".$this->rupiah($list->fc_value)."</div>":"";
                $totalPriceForeign = $list->fc_symbol!=""?"<div style='color:red'>(".$this->dollarSG($list->totalPriceForeign).")</div>":"";
                $detilPrice = $list->doku_link!=""?"<div><a href='".url('/payment')."/".$list->doku_link."' target='_blank'>" . $list->doku_link . "</a></div>":"<div style='color:red'>".$list->bank_name." - ".$list->bank_account_name."</div><div style='color:blue'>".$list->bank_account_id."</div>";
                $detilExp = $filterPay=="DOKU"&&$filterPayStatus=="FAILED"?$this->checkResendTime($list->created_at,$list->doku_expired_date):"";
                $paymentSuccessAt = $list->invoice_status=="PAID"?$this->getSuccessTime($list->mismass_invoice_id):"";
                $row[] = $detilDisc."<div class='fw-bold'>Total Biaya : " . $this->rupiah($list->totalPrice) . " ".$totalPriceForeign."</div>".$fcRateValue.$detilPrice.$detilExp.$paymentSuccessAt;
            }

            if(Auth::user()->revisi_btn||Auth::user()->void_btn||Auth::user()->shiplist_edit_invoice||Auth::user()->shiplist_hapus_invoice||Auth::user()->shiplist_buat_resi||Auth::user()->shiplist_printout_invoice){
                $row[] = $wholeBtn;
            }

            $data[] = $row;
        }

        $output = [
            'draw' => $request->input('draw'),
            'recordsTotal' => $this->invoiceModel->countAll(),
            'recordsFiltered' => $this->invoiceModel->countFiltered($request, $search, $filter),
            'data' => $data
        ];

        return json_encode($output);
    }
    
    public function editInvoice(Request $request){
        //Check WA Blast
        if($request->input('mismassInvoiceId')!=""){
            $dataCheckWaBlast = [
                $request->input("mismassOrderId"),
                Auth::user()->username,
                "EI",
            ];
            $checkWaBlast = $this->whatsappModel->checkTimeBlast($dataCheckWaBlast);
            if(!$checkWaBlast['status']){
                $encode = array("status" => 500, "title" => "Gagal", "message" => "Menunggu Waktu Whatsapp Blast", "queuetime" => $checkWaBlast['queueTime'], "createdtime" => $checkWaBlast['createdTime'], "now" => $checkWaBlast['now']);
                return json_encode($encode);
            }
            $waTemplateId = $checkWaBlast['waTemplateId'];
        }

        $checkData = $this->editModel->checkData($request);

        //jika tidak ada perbedaan maka tidak disimpan
        if($checkData['diff']==0){
            $encode = array("status" => 200, "title" => "Berhasil", "text" => "Tidak Ada Perubahan Data", "url" => url('/printout/invoice/' . $this->controller->invOnlyId($request->input('mismassInvoiceId'))));
            return json_encode($encode);
        }

        $now = date("Y-m-d H:i:s");
        $randomLink = $request->input('mismassInvoiceLink');
        $uniqId = $request->input('linkDoku');
        $newInvoice = $request->input('mismassInvoiceId');
        $indToCor = DB::table("data_list")->where("mismass_invoice_id",$request->input('mismassInvoiceId'))->value("ind_to_cor");

        //Ambil Data Created Agar Tidak Berubah
        $getCreated = DB::table("data_list")->select("doku_token_id","doku_expired_date","created_at","created_by")->where("mismass_invoice_id",$request->input("mismassInvoiceId"))->first();
        $dokuTokenId = $getCreated->doku_token_id;
        $dokuExpiredDate = $getCreated->doku_expired_date;
        $descSendWa = "-";
        if($checkData['blastStatus']){
            $randomLink = str::random(20);
            $uniqId = str::random(30);
            $newInvoice = $this->editModel->createNewInvoice($request->input('mismassInvoiceId'));

            //jika pembayaran doku
            // if(!env('SANDBOX')){

                $result = "-";
                $paymentLink = "-";
                if($request->input("pembayaran")=="DOKU"){

                    $dokuName = $this->alphaNumSpace($request->input("senderFirstName")[0]." ".$request->input("senderMiddleName")[0]." ".$request->input("senderLastName")[0]);
                    $dokuPhone = $request->input("senderPhone")[0];
                    $dokuEmail = $request->input("senderEmail")[0];
                    $dokuAddress = $request->input("senderAddress")[0];
                    if($request->input("custTypeId")=="IND"){
                        $dokuName = $this->alphaNumSpace($request->input("consFirstName")[0]." ".$request->input("consMiddleName")[0]." ".$request->input("consLastName")[0]);
                        $dokuPhone = $request->input("consPhone")[0];
                        $dokuEmail = $request->input("consEmail")[0];
                        $dokuAddress = $request->input("consAddress")[0];
                    }

                    $params = array();
                    $params['order']['price'] = $checkData['totalBiaya']!="" ? $checkData['totalBiaya'] : 0;
                    $params['order']['invoice_number'] = $newInvoice;
                    $params['payment']['payment_due_date'] = 7*1440;
                    $params['customer']['id'] = $request->input("custId");
                    $params['customer']['name'] = $dokuName;
                    $params['customer']['phone'] = $dokuPhone;
                    $params['customer']['email'] = $dokuEmail;
                    $params['customer']['address'] = $dokuAddress;
                    $result = $this->dokuModel->generate($params);

                    if($result==null){
                        $encode = array("status" => 500, "title" => "Gagal", "text" => "Doku Tidak Memberikan Respon.");
                        return json_encode($encode);
                    }

                    if($result['status']==503||$result['status']==28){
                        $encode = array("status" => 500, "title" => "Gagal",  "text" => $result['message']);
                        return json_encode($encode);
                    }

                    $paymentLink = url("/payment"."/".$uniqId);
                    $dokuTokenId = $result['token_id'];
                    $dokuExpiredDate = date("Y-m-d H:i:s", strtotime($result['expired_date']));
                }

                //Send WA
                $data = array(
                    "phone" => $request->input("custTypeId")=="IND" ? $request->input("consPhone")[0] : $request->input("senderPhone")[0],
                    "fullName" => $request->input("custTypeId")=="IND" ? $request->input("consFirstName")[0]." ".$request->input("consMiddleName")[0]." ".$request->input("consLastName")[0] : $request->input("senderFirstName")[0]." ".$request->input("senderMiddleName")[0]." ".$request->input("senderLastName")[0],
                    "invoiceLink" => url('/p')."/".$randomLink,
                    "invoiceDate" => $this->controller->dateFormatIndo($now,1),
                    "invoice" => $newInvoice,
                    "paymentLink" => $paymentLink,
                    "statusPay" => "UNPAID",
                    "custType" => DB::table("cust_type_list")->where("id",$request->input("custTypeId"))->value("name"),
                    "reference" => DB::table("cust_list")->where("id",$request->input("custId"))->value("reference"),
                    "templateId" => $waTemplateId
                );

                if(env('WA_GATEWAY')){
                    $resultSendWa = $this->whatsappModel->revisiInvoiceSendWA($data);
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
                    $email = $request->input("custTypeId")=="IND" ? $request->input("consEmail")[0] : $request->input("senderEmail")[0];
                    $data['subject'] = "Revisi Invoice | ".$newInvoice;
                    $data['modes'] = "EINV"; //Webform = "WEB", edit invoice = "EINV", create invoice = "CINV", create shipment = "CSHI"
                    $sendingMail = Mail::to($email)
                                    ->cc($ccEmail)
                                    ->bcc($bccEmail)
                                    ->send(new SendMail($data));
                }
            // }

        }
        
        $dataHistory = [
            "codename" => "EI",
            "created_at" => $now,
            "created_by" => Auth::user()->username,
            "whatsapp_desc" => "Send Whatsapp Status : ".$descSendWa,
            "description" => $this->controller->createDescForEditInvoice($request,$newInvoice,$dokuTokenId,$dokuExpiredDate,$uniqId),
        ];
        $insertHistory = DB::table("history_list")->insert($dataHistory);
        if(!$insertHistory){
            $encode = array("status" => 500, "title" => "Gagal", "text" => "Gagal Buat History");
            return json_encode($encode);
        }
        
        //Ganti Invoice di Order id
        DB::table('order_list')->where("invoice_id",$request->input("mismassInvoiceId"))->update(["invoice_id"=>$newInvoice]);

        //jika ada perbedaan maka yg lama dihapus dan yang baru diinsert
        DB::table('data_list')->where("mismass_invoice_id",$request->input("mismassInvoiceId"))->delete();

        $success = 0; 
        for ($i = 0; $i <= count($request->input("warehouseval")) - 1; $i++) {

            $values = array ("created_at" => $getCreated->created_at,
                            "created_by" => $getCreated->created_by,
                            "updated_at" => $now,
                            "updated_by" => Auth::user()->username,
                            "warehouse_id" => $request->input("warehouseval")[$i],
                            "cust_id" => $request->input("custId"),
                            "cust_type_id" => $request->input("custTypeId"),
                            "ind_to_cor" => $indToCor,
                            "inv_add" => $request->input("invAdd"),
                            "service_id" => $request->input("serviceval")[$i],
                            "mismass_order_id" => $request->input("mismassOrderId"),
                            "mismass_invoice_id" => $newInvoice,
                            "mismass_invoice_date" => date("Y-m-d",strtotime($request->input('tanggalInvoice'))),
                            "mismass_invoice_link" => $randomLink,
                            "invoice_status" => "UNPAID",
                            "payment_status" => "PENDING",
                            "revision_note" => $this->controller->noSingleQuo($request->input("revisionNote")),
                            "sender_first_name" => $this->controller->noSingleQuo($request->input("senderFirstName")[$i]) ?? "",
                            "sender_middle_name" => $this->controller->noSingleQuo($request->input("senderMiddleName"[$i])) ?? "",
                            "sender_last_name" => $this->controller->noSingleQuo($request->input("senderLastName")[$i]) ?? "",
                            "sender_email" => $request->input("senderEmail")[$i] ?? "",
                            "sender_phone" => $request->input("senderPhone")[$i] ?? "",
                            "sender_address" => $this->controller->noSingleQuo($request->input("senderAddress")[$i]) ?? "",
                            "sender_sub_district" => $this->controller->noSingleQuo($request->input("senderSubDistrict")[$i]) ?? "",
                            "sender_district" => $this->controller->noSingleQuo($request->input("senderDistrict")[$i]) ?? "",
                            "sender_city" => $this->controller->noSingleQuo($request->input("senderCity")[$i]) ?? "",
                            "sender_prov" => $this->controller->noSingleQuo($request->input("senderProv")[$i]) ?? "",
                            "sender_postal_code" => $request->input("senderPostalCode")[$i] ?? "",
                            "cons_first_name" => $this->controller->noSingleQuo($request->input("consFirstName")[$i]) ?? "",
                            "cons_middle_name" => $this->controller->noSingleQuo($request->input("consMiddleName")[$i]) ?? "",
                            "cons_last_name" => $this->controller->noSingleQuo($request->input("consLastName")[$i]) ?? "",
                            "cons_email" => $request->input("consEmail")[$i] ?? "",
                            "cons_phone" => $request->input("consPhone")[$i] ?? "",
                            "cons_address" => $this->controller->noSingleQuo($request->input("consAddress")[$i]) ?? "",
                            "cons_sub_district" => $this->controller->noSingleQuo($request->input("consSubDistrict")[$i]) ?? "",
                            "cons_district" => $this->controller->noSingleQuo($request->input("consDistrict")[$i]) ?? "",
                            "cons_city" => $this->controller->noSingleQuo($request->input("consCity")[$i]) ?? "",
                            "cons_prov" => $this->controller->noSingleQuo($request->input("consProv")[$i]) ?? "",
                            "cons_postal_code" => $request->input("consPostalCode")[$i] ?? "",
                            "doku_token_id"=> $request->input("pembayaran")=="DOKU" ? $dokuTokenId : "",
                            "doku_expired_date" => $request->input("pembayaran")=="DOKU" ? $dokuExpiredDate : "0000-00-00 00:00:00",
                            "doku_invoice_id" => $request->input("invoiceDoku") ?? "",
                            "doku_link" => $request->input("pembayaran")=="DOKU" ? $uniqId : "",
                            "bank_name" => $request->input("namaBank") ?? "",
                            "bank_account_name" => $request->input("namaRekening") ?? "",
                            "bank_account_id" => $request->input("noRekening") ?? "",
                            "template_id" => $request->input("templateId"),
                            "forwarder_id" => "",
                            "shipping_number" => "",
                            "shipping_created_at" => "",
                            "shipping_created_by" => "",
                            "shipping_updated_at" => "",
                            "shipping_updated_by" => "",
                            "length" => isset($request->input("panjang")[$i]) ? $this->controller->normalizeInput($request->input("panjang")[$i]) : 0,
                            "width" => isset($request->input("lebar")[$i]) ? $this->controller->normalizeInput($request->input("lebar")[$i]) : 0,
                            "height" => isset($request->input("tinggi")[$i]) ? $this->controller->normalizeInput($request->input("tinggi")[$i]) : 0,
                            "weight" => isset($request->input("kg")[$i]) ? $this->controller->normalizeInput($request->input("kg")[$i]) : 0,
                            "cbm" => isset($request->input("cbm")[$i]) ? $this->controller->normalizeInput($request->input("cbm")[$i]) : 0,
                            "actual_weight" => isset($request->input("actualKg")[$i]) ? $this->controller->normalizeInput($request->input("actualKg")[$i]) : 0,
                            "item" => isset($request->input("item")[$i]) ? $this->controller->normalizeInput($request->input("item")[$i]) : 0,
                            "service_name" => $request->input('serviceName'),
                            "service_price_per" => isset($request->input("pricePer")[$i]) ? $this->controller->normalizeInput($request->input("pricePer")[$i]) : 0,
                            "discount" => $this->controller->normalizeInput($request->input("discount" . $i)),
                            "additional_nom" => $this->controller->normalizeInput($request->input("additionalNominal" . $i)),
                            "additional_desc" => $request->input("additionalDesc" . $i) ?? "",
                            "packing" => $this->controller->normalizeInput($request->input("packing" . $i)),
                            "packing_per" => $this->controller->normalizeInput($request->input("packingPer" . $i)),
                            "packing_total" => $this->controller->normalizeInput($request->input("packingTotal" . $i)),
                            "packing_desc" => $request->input("packingDesc" . $i) ?? "",
                            "import_permit" => $this->controller->normalizeInput($request->input("import" . $i)),
                            "import_permit_per" => $this->controller->normalizeInput($request->input("importPer" . $i)),
                            "import_permit_total" => $this->controller->normalizeInput($request->input("importTotal" . $i)),
                            "import_permit_desc" => $request->input("importDesc" . $i) ?? "",
                            "export_permit" => $this->controller->normalizeInput($request->input("export" . $i)),
                            "export_permit_per" => $this->controller->normalizeInput($request->input("exportPer" . $i)),
                            "export_permit_total" => $this->controller->normalizeInput($request->input("exportTotal" . $i)),
                            "export_permit_desc" => $request->input("exportDesc" . $i) ?? "",
                            "document" => $this->controller->normalizeInput($request->input("document" . $i)),
                            "document_per" => $this->controller->normalizeInput($request->input("documentPer" . $i)),
                            "document_total" => $this->controller->normalizeInput($request->input("documentTotal" . $i)),
                            "document_desc" => $request->input("documentDesc" . $i) ?? "",
                            "dr_medicine" => $this->controller->normalizeInput($request->input("medicine" . $i)),
                            "dr_medicine_per" => $this->controller->normalizeInput($request->input("medicinePer" . $i)),
                            "dr_medicine_total" => $this->controller->normalizeInput($request->input("medicineTotal" . $i)),
                            "dr_medicine_desc" => $request->input("medicineDesc" . $i) ?? "",
                            "insurance_item_price" => $this->controller->normalizeInput($request->input("insurancePriceItem" . $i)),
                            "insurance_percent" => $this->controller->normalizeInput($request->input("insurancePercent" . $i)),
                            "insurance_total" => round($request->input("insuranceTotal" . $i)),
                            "fee_item_price" => $this->controller->normalizeInput($request->input("feePriceItem" . $i)),
                            "fee_percent" => $this->controller->normalizeInput($request->input("feePercent" . $i)),
                            "fee_total" => round($request->input("feeTotal" . $i)),
                            "tax_item_price" => $this->controller->normalizeInput($request->input("taxPriceItem" . $i)),
                            "tax_percent" => $this->controller->normalizeInput($request->input("taxPercent" . $i)),
                            "tax_total" => round($request->input("taxTotal" . $i)),
                            "extra_cost_price" => $this->controller->normalizeInput($request->input("extraCostPrice" . $i)),
                            "extra_cost_dest" => $request->input("extraCostDest" . $i) ?? "",
                            "extra_cost_vendor_name" => $request->input("extraCostVendorName" . $i) ?? "",
                            "extra_cost_shipping_number" => $request->input("extraCostShippingNum" . $i) ?? "",
                            "pickup_weight" => $this->controller->normalizeInput($request->input("pickUpWeight" . $i)),
                            "pickup_charge" => $this->controller->normalizeInput($request->input("pickUpCharge" . $i)),
                            "other_pickup_fee" => $this->controller->normalizeInput($request->input("pickUpFee" . $i)),
                            "sub_total" => isset($request->input("subTotal")[$i]) ? $this->controller->normalizeInput($request->input("subTotal")[$i]) : 0,
                            "adjust_fee" => $i==0?$this->controller->normalizeInput($request->input("adjustFee")):0,
                            "fc_symbol" => $this->controller->normalizeInput($request->input("foreignRateValue"))>0?$request->input("foreignSymbol"):"",
                            "fc_value" => $this->controller->normalizeInput($request->input("foreignRateValue"))>0?$this->controller->normalizeInput($request->input("foreignRateValue")):0);

            $insert = DB::table('data_list')->insert($values);

            if ($insert) {
                $success++;
            }
        }

        
        if ($success != count($request->input("warehouseval"))) {
            $encode = array("status" => 500, "title" => "Gagal", "text" => "Gagal Buat Invoice Err 003", "url" => "");
            return json_encode($encode);
        }

        $encode = array("status" => 200, "title" => "Berhasil", "text" => "Data Invoice Berhasil diedit dan telah dikirim ke Whatsapp Customer.", "url" => url('/printout/invoice/' . $this->controller->invOnlyId($newInvoice)));
        return json_encode($encode);

    }

    public function buatInvoiceAdditional(Request $request){

        //create Invoice Id Additional
        $mismassInvoiceId = $this->createInvoiceAddService->createInvoiceId($request->input('mismassInvoiceId'));
        $now = date("Y-m-d H:i:s");
        $randomLink = $request->input('mismassInvoiceLink');
        $uniqId = $request->input('linkDoku');
        $newInvoice = $mismassInvoiceId;
        $totalBiaya = 0;
        for ($i = 0; $i <= count($request->input("warehouseval")) - 1; $i++) {
            $sub_total = isset($request->input("subTotal")[$i]) ? $this->normalizeInput($request->input("subTotal")[$i]) : 0;
            $adjust_fee = $i==0 ? $this->controller->normalizeInput($request->input("adjustFee")) : 0;
            $totalBiaya += $sub_total+$adjust_fee;
        }
        $descSendWa = "-";
        $randomLink = str::random(20);
        $uniqId = str::random(30);

        //Check WA Blast
        $dataCheckWaBlast = [
            $mismassInvoiceId,
            Auth::user()->username,
            "BI",
        ];
        $checkWaBlast = $this->whatsappModel->checkTimeBlast($dataCheckWaBlast);

        if(!$checkWaBlast['status']){
            $encode = array("status" => 500, "title" => "Gagal", "message" => "Menunggu Waktu Whatsapp Blast", "queuetime" => $checkWaBlast['queueTime'], "createdtime" => $checkWaBlast['createdTime'], "now" => $checkWaBlast['now']);
            return json_encode($encode);
        }
        $waTemplateId = $checkWaBlast['waTemplateId'];

        //create Data Order
        $dataInsertOrder = [
            "now" => $now,
            "mismassInvoiceId" => $mismassInvoiceId,
            "mismassOrderId" => $request->input('mismassOrderId')
        ];
        $newOrderId = $this->createInvoiceAddService->insertOrderData($dataInsertOrder);

        //jika pembayaran doku
        // if(!env('SANDBOX')){

            $result = "-";
            $paymentLink = "-";
            if($request->input("pembayaran")=="DOKU"){

                $dokuName = $this->alphaNumSpace($request->input("senderFirstName")[0]." ".$request->input("senderMiddleName")[0]." ".$request->input("senderLastName")[0]);
                $dokuPhone = $request->input("senderPhone")[0];
                $dokuEmail = $request->input("senderEmail")[0];
                $dokuAddress = $request->input("senderAddress")[0];
                if($request->input("custTypeId")=="IND"){
                    $dokuName = $this->alphaNumSpace($request->input("consFirstName")[0]." ".$request->input("consMiddleName")[0]." ".$request->input("consLastName")[0]);
                    $dokuPhone = $request->input("consPhone")[0];
                    $dokuEmail = $request->input("consEmail")[0];
                    $dokuAddress = $request->input("consAddress")[0];
                }

                $params = array();
                $params['order']['price'] = $totalBiaya!="" ? $totalBiaya : 0;
                $params['order']['invoice_number'] = $newInvoice;
                $params['payment']['payment_due_date'] = 7*1440;
                $params['customer']['id'] = $request->input("custId");
                $params['customer']['name'] = $dokuName;
                $params['customer']['phone'] = $dokuPhone;
                $params['customer']['email'] = $dokuEmail;
                $params['customer']['address'] = $dokuAddress;
                $result = $this->dokuModel->generate($params);

                if($result==null){
                    $encode = array("status" => 500, "title" => "Gagal", "text" => "Doku Tidak Memberikan Respon.");
                    return json_encode($encode);
                }

                if($result['status']==503||$result['status']==28){
                    $encode = array("status" => 500, "title" => "Gagal",  "text" => $result['message']);
                    return json_encode($encode);
                }

                $paymentLink = url("/payment"."/".$uniqId);
                $dokuTokenId = $result['token_id'];
                $dokuExpiredDate = date("Y-m-d H:i:s", strtotime($result['expired_date']));
            }

            //Send WA
            $data = array(
                "phone" => $request->input("custTypeId")=="IND" ? $request->input("consPhone")[0] : $request->input("senderPhone")[0],
                "fullName" => $request->input("custTypeId")=="IND" ? $request->input("consFirstName")[0]." ".$request->input("consMiddleName")[0]." ".$request->input("consLastName")[0] : $request->input("senderFirstName")[0]." ".$request->input("senderMiddleName")[0]." ".$request->input("senderLastName")[0],
                "invoiceLink" => url('/p')."/".$randomLink,
                "invoiceDate" => $this->controller->dateFormatIndo($now,1),
                "invoice" => $newInvoice,
                "paymentLink" => $paymentLink,
                "statusPay" => "UNPAID",
                "custType" => DB::table("cust_type_list")->where("id",$request->input("custTypeId"))->value("name"),
                "reference" => DB::table("cust_list")->where("id",$request->input("custId"))->value("reference"),
                "templateId" => $waTemplateId
            );

            if(env('WA_GATEWAY')){
                $resultSendWa = $this->whatsappModel->createInvoiceSendWA($data);
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
                $email = $request->input("custTypeId")=="IND" ? $request->input("consEmail")[0] : $request->input("senderEmail")[0];
                $data['subject'] = "Buat Invoice Additional | ".$newInvoice;
                $data['modes'] = "CINV"; //Webform = "WEB", edit invoice = "EINV", create invoice = "CINV", create shipment = "CSHI"
                $sendingMail = Mail::to($email)
                                ->cc($ccEmail)
                                ->bcc($bccEmail)
                                ->send(new SendMail($data));
            }
        // }

        $success = 0; 
        for ($i = 0; $i <= count($request->input("warehouseval")) - 1; $i++) {

            $values = array ("created_at" => $now,
                            "created_by" => Auth::user()->username,
                            "updated_at" => $now,
                            "updated_by" => Auth::user()->username,
                            "warehouse_id" => $request->input("warehouseval")[$i],
                            "cust_id" => $request->input("custId"),
                            "cust_type_id" => $request->input("custTypeId"),
                            "service_id" => $request->input("serviceval")[$i],
                            "inv_add" => 1,
                            "mismass_order_id" => $newOrderId,
                            "mismass_invoice_id" => $newInvoice,
                            "mismass_invoice_date" => date("Y-m-d",strtotime($request->input('tanggalInvoice'))),
                            "mismass_invoice_link" => $randomLink,
                            "invoice_status" => "UNPAID",
                            "payment_status" => "PENDING",
                            "sender_first_name" => $this->controller->noSingleQuo($request->input("senderFirstName")[$i]) ?? "",
                            "sender_middle_name" => $this->controller->noSingleQuo($request->input("senderMiddleName"[$i])) ?? "",
                            "sender_last_name" => $this->controller->noSingleQuo($request->input("senderLastName")[$i]) ?? "",
                            "sender_email" => $request->input("senderEmail")[$i] ?? "",
                            "sender_phone" => $request->input("senderPhone")[$i] ?? "",
                            "sender_address" => $this->controller->noSingleQuo($request->input("senderAddress")[$i]) ?? "",
                            "sender_sub_district" => $this->controller->noSingleQuo($request->input("senderSubDistrict")[$i]) ?? "",
                            "sender_district" => $this->controller->noSingleQuo($request->input("senderDistrict")[$i]) ?? "",
                            "sender_city" => $this->controller->noSingleQuo($request->input("senderCity")[$i]) ?? "",
                            "sender_prov" => $this->controller->noSingleQuo($request->input("senderProv")[$i]) ?? "",
                            "sender_postal_code" => $request->input("senderPostalCode")[$i] ?? "",
                            "cons_first_name" => $this->controller->noSingleQuo($request->input("consFirstName")[$i]) ?? "",
                            "cons_middle_name" => $this->controller->noSingleQuo($request->input("consMiddleName")[$i]) ?? "",
                            "cons_last_name" => $this->controller->noSingleQuo($request->input("consLastName")[$i]) ?? "",
                            "cons_email" => $request->input("consEmail")[$i] ?? "",
                            "cons_phone" => $request->input("consPhone")[$i] ?? "",
                            "cons_address" => $this->controller->noSingleQuo($request->input("consAddress")[$i]) ?? "",
                            "cons_sub_district" => $this->controller->noSingleQuo($request->input("consSubDistrict")[$i]) ?? "",
                            "cons_district" => $this->controller->noSingleQuo($request->input("consDistrict")[$i]) ?? "",
                            "cons_city" => $this->controller->noSingleQuo($request->input("consCity")[$i]) ?? "",
                            "cons_prov" => $this->controller->noSingleQuo($request->input("consProv")[$i]) ?? "",
                            "cons_postal_code" => $request->input("consPostalCode")[$i] ?? "",
                            "doku_token_id"=> $request->input("pembayaran")=="DOKU" ? $dokuTokenId : "",
                            "doku_expired_date" => $request->input("pembayaran")=="DOKU" ? $dokuExpiredDate : "0000-00-00 00:00:00",
                            "doku_invoice_id" => $request->input("invoiceDoku") ?? "",
                            "doku_link" => $request->input("pembayaran")=="DOKU" ? $uniqId : "",
                            "bank_name" => $request->input("namaBank") ?? "",
                            "bank_account_name" => $request->input("namaRekening") ?? "",
                            "bank_account_id" => $request->input("noRekening") ?? "",
                            "template_id" => $request->input("templateId"),
                            "forwarder_id" => "",
                            "shipping_number" => "",
                            "shipping_created_at" => "",
                            "shipping_created_by" => "",
                            "shipping_updated_at" => "",
                            "shipping_updated_by" => "",
                            "length" => isset($request->input("panjang")[$i]) ? $this->controller->normalizeInput($request->input("panjang")[$i]) : 0,
                            "width" => isset($request->input("lebar")[$i]) ? $this->controller->normalizeInput($request->input("lebar")[$i]) : 0,
                            "height" => isset($request->input("tinggi")[$i]) ? $this->controller->normalizeInput($request->input("tinggi")[$i]) : 0,
                            "weight" => isset($request->input("kg")[$i]) ? $this->controller->normalizeInput($request->input("kg")[$i]) : 0,
                            "cbm" => isset($request->input("cbm")[$i]) ? $this->controller->normalizeInput($request->input("cbm")[$i]) : 0,
                            "actual_weight" => isset($request->input("actualKg")[$i]) ? $this->controller->normalizeInput($request->input("actualKg")[$i]) : 0,
                            "item" => isset($request->input("item")[$i]) ? $this->controller->normalizeInput($request->input("item")[$i]) : 0,
                            "service_name" => $request->input('serviceName'),
                            "service_price_per" => isset($request->input("pricePer")[$i]) ? $this->controller->normalizeInput($request->input("pricePer")[$i]) : 0,
                            "discount" => $this->controller->normalizeInput($request->input("discount" . $i)),
                            "additional_nom" => $this->controller->normalizeInput($request->input("additionalNominal" . $i)),
                            "additional_desc" => $request->input("additionalDesc" . $i) ?? "",
                            "packing" => $this->controller->normalizeInput($request->input("packing" . $i)),
                            "packing_per" => $this->controller->normalizeInput($request->input("packingPer" . $i)),
                            "packing_total" => $this->controller->normalizeInput($request->input("packingTotal" . $i)),
                            "packing_desc" => $request->input("packingDesc" . $i) ?? "",
                            "import_permit" => $this->controller->normalizeInput($request->input("import" . $i)),
                            "import_permit_per" => $this->controller->normalizeInput($request->input("importPer" . $i)),
                            "import_permit_total" => $this->controller->normalizeInput($request->input("importTotal" . $i)),
                            "import_permit_desc" => $request->input("importDesc" . $i) ?? "",
                            "export_permit" => $this->controller->normalizeInput($request->input("export" . $i)),
                            "export_permit_per" => $this->controller->normalizeInput($request->input("exportPer" . $i)),
                            "export_permit_total" => $this->controller->normalizeInput($request->input("exportTotal" . $i)),
                            "export_permit_desc" => $request->input("exportDesc" . $i) ?? "",
                            "document" => $this->controller->normalizeInput($request->input("document" . $i)),
                            "document_per" => $this->controller->normalizeInput($request->input("documentPer" . $i)),
                            "document_total" => $this->controller->normalizeInput($request->input("documentTotal" . $i)),
                            "document_desc" => $request->input("documentDesc" . $i) ?? "",
                            "dr_medicine" => $this->controller->normalizeInput($request->input("medicine" . $i)),
                            "dr_medicine_per" => $this->controller->normalizeInput($request->input("medicinePer" . $i)),
                            "dr_medicine_total" => $this->controller->normalizeInput($request->input("medicineTotal" . $i)),
                            "dr_medicine_desc" => $request->input("medicineDesc" . $i) ?? "",
                            "insurance_item_price" => $this->controller->normalizeInput($request->input("insurancePriceItem" . $i)),
                            "insurance_percent" => $this->controller->normalizeInput($request->input("insurancePercent" . $i)),
                            "insurance_total" => round($request->input("insuranceTotal" . $i)),
                            "fee_item_price" => $this->controller->normalizeInput($request->input("feePriceItem" . $i)),
                            "fee_percent" => $this->controller->normalizeInput($request->input("feePercent" . $i)),
                            "fee_total" => round($request->input("feeTotal" . $i)),
                            "tax_item_price" => $this->controller->normalizeInput($request->input("taxPriceItem" . $i)),
                            "tax_percent" => $this->controller->normalizeInput($request->input("taxPercent" . $i)),
                            "tax_total" => round($request->input("taxTotal" . $i)),
                            "extra_cost_price" => $this->controller->normalizeInput($request->input("extraCostPrice" . $i)),
                            "extra_cost_dest" => $request->input("extraCostDest" . $i) ?? "",
                            "extra_cost_vendor_name" => $request->input("extraCostVendorName" . $i) ?? "",
                            "extra_cost_shipping_number" => $request->input("extraCostShippingNum" . $i) ?? "",
                            "pickup_weight" => $this->controller->normalizeInput($request->input("pickUpWeight" . $i)),
                            "pickup_charge" => $this->controller->normalizeInput($request->input("pickUpCharge" . $i)),
                            "other_pickup_fee" => $this->controller->normalizeInput($request->input("pickUpFee" . $i)),
                            "sub_total" => isset($request->input("subTotal")[$i]) ? $this->controller->normalizeInput($request->input("subTotal")[$i]) : 0,
                            "adjust_fee" => $i==0?$this->controller->normalizeInput($request->input("adjustFee")):0,
                            "fc_symbol" => $this->controller->normalizeInput($request->input("foreignRateValue"))>0?$request->input("foreignSymbol"):"",
                            "fc_value" => $this->controller->normalizeInput($request->input("foreignRateValue"))>0?$this->controller->normalizeInput($request->input("foreignRateValue")):0);

            $insert = DB::table('data_list')->insert($values);

            if ($insert) {
                $success++;
            }
        }
        
        if ($success != count($request->input("warehouseval"))) {
            $encode = array("status" => 500, "title" => "Gagal", "text" => "Gagal Buat Invoice Err 003", "url" => "");
            return json_encode($encode);
        }

        //Create History
        $dataHistory = [
            "codename" => "BI",
            "created_at" => $now,
            "created_by" => Auth::user()->username,
            "whatsapp_desc" => "Send Whatsapp Status : ".$descSendWa,
            "description" => $this->createDescForInvoice($newInvoice,$descSendWa,"BI"),
        ];
        $insertHistory = DB::table("history_list")->insert($dataHistory);
        if(!$insertHistory){
            $encode = array("status" => 500, "title" => "Gagal", "text" => "Gagal Buat History");
            return json_encode($encode);
        }

        $encode = array("status" => 200, "title" => "Berhasil", "text" => "Data Invoice Additional Berhasil dibuat dan telah dikirim ke Whatsapp Customer. Silahkan Cek Pada Tabel Tracking.", "url" => url('/printout/invoice/' . $this->invOnlyId($newInvoice)));
        return json_encode($encode);

    }

    // public function editInvoice(Request $request){
    //     $n = 0;
    //     $i = 0;    
    //     $success = 0; 
    //     $getOld = DB::table('data_list')
    //                 ->where('mismass_invoice_id',$request->input('mismassInvoiceId'))
    //                 ->get();
        
    //     // dd($request->input("warehouseval"));

    //     if(count($request->input("warehouseval"))!=count($getOld)){
    //         $n++;
    //     }

    //     foreach($getOld as $go){

    //             $doku_invoice_id = $request->input("invoiceDoku") ?? "";
                
    //             $sender_first_name = $request->input("senderFirstName")[$i] ?? "";
    //             $sender_middle_name = $request->input("senderMiddleName")[$i] ?? "";
    //             $sender_last_name = $request->input("senderLastName")[$i] ?? "";
    //             $sender_email = $request->input("senderEmail")[$i] ?? "";
    //             $sender_phone = $request->input("senderPhone")[$i] ?? "";
    //             $sender_address = $request->input("senderAddress")[$i] ?? "";
    //             $sender_sub_district = $request->input("senderSubDistrict")[$i] ?? "";
    //             $sender_district = $request->input("senderDistrict")[$i] ?? "";
    //             $sender_city = $request->input("senderCity")[$i] ?? "";
    //             $sender_prov = $request->input("senderProv")[$i] ?? "";
    //             $sender_postal_code = $request->input("senderPostalCode")[$i] ?? "";
                
    //             $cons_first_name = $request->input("consFirstName")[$i] ?? "";
    //             $cons_middle_name = $request->input("consMiddleName")[$i] ?? "";
    //             $cons_last_name = $request->input("consLastName")[$i] ?? "";
    //             $cons_email = $request->input("consEmail")[$i] ?? "";
    //             $cons_phone = $request->input("consPhone")[$i] ?? "";
    //             $cons_address = $request->input("consAddress")[$i] ?? "";
    //             $cons_sub_district = $request->input("consSubDistrict")[$i] ?? "";
    //             $cons_district = $request->input("consDistrict")[$i] ?? "";
    //             $cons_city = $request->input("consCity")[$i] ?? "";
    //             $cons_prov = $request->input("consProv")[$i] ?? "";
    //             $cons_postal_code = $request->input("consPostalCode")[$i] ?? "";
                
    //             $doku_link = $request->input("linkDoku") ?? "";
    //             $bank_name = $request->input("namaBank") ?? "";
    //             $bank_account_name = $request->input("namaRekening") ?? "";
    //             $bank_account_id = $request->input("noRekening") ?? "";
    //             $template_id = $request->input("templateId");
    //             $length = isset($request->input("panjang")[$i]) ? $this->normalizeInput($request->input("panjang")[$i]) : 0;
    //             $width = isset($request->input("lebar")[$i]) ? $this->normalizeInput($request->input("lebar")[$i]) : 0;
    //             $height = isset($request->input("tinggi")[$i]) ? $this->normalizeInput($request->input("tinggi")[$i]) : 0;
    //             $weight = isset($request->input("kg")[$i]) ? $this->normalizeInput($request->input("kg")[$i]) : 0;
    //             $cbm = isset($request->input("cbm")[$i]) ? $this->normalizeInput($request->input("cbm")[$i]) : 0;
    //             $actualWeight = isset($request->input("actualKg")[$i]) ? $this->normalizeInput($request->input("actualKg")[$i]) : 0;
    //             $item = isset($request->input("item")[$i]) ? $this->normalizeInput($request->input("item")[$i]) : 0;
    //             $discount = $this->normalizeInput($request->input("discount" . $i));
    //             $additional_desc = $request->input("additionalDesc" . $i) ?? "";
    //             $additional_nom = $this->normalizeInput($request->input("additionalNominal" . $i));
    //             $packing = $this->normalizeInput($request->input("packing" . $i));
    //             $packing_per = $this->normalizeInput($request->input("packingPer" . $i));
    //             $packing_total = $this->normalizeInput($request->input("packingTotal" . $i));
    //             $packing_desc = $request->input("packingDesc" . $i) ?? "";
    //             $import_permit = $this->normalizeInput($request->input("import" . $i));
    //             $import_permit_per = $this->normalizeInput($request->input("importPer" . $i));
    //             $import_permit_total = $this->normalizeInput($request->input("importTotal" . $i));
    //             $import_permit_desc = $request->input("importDesc" . $i) ?? "";
    //             $document = $this->normalizeInput($request->input("document" . $i));
    //             $document_per = $this->normalizeInput($request->input("documentPer" . $i));
    //             $document_total = $this->normalizeInput($request->input("documentTotal" . $i));
    //             $document_desc = $request->input("documentDesc" . $i) ?? "";
    //             $dr_medicine = $this->normalizeInput($request->input("medicine" . $i));
    //             $dr_medicine_per = $this->normalizeInput($request->input("medicinePer" . $i));
    //             $dr_medicine_total = $this->normalizeInput($request->input("medicineTotal" . $i));
    //             $dr_medicine_desc = $request->input("medicineDesc" . $i) ?? "";
    //             $insurance_item_price = $this->normalizeInput($request->input("insurancePriceItem" . $i));
    //             $insurance_percent = $this->normalizeInput($request->input("insurancePercent" . $i));
    //             $insurance_total = round($this->normalizeInput($request->input("insuranceTotal" . $i)));
    //             $fee_item_price = $this->normalizeInput($request->input("feePriceItem" . $i));
    //             $fee_percent = $this->normalizeInput($request->input("feePercent" . $i));
    //             $fee_total = round($this->normalizeInput($request->input("feeTotal" . $i)));
    //             $tax_item_price = $this->normalizeInput($request->input("taxPriceItem" . $i));
    //             $tax_percent = $this->normalizeInput($request->input("taxPercent" . $i));
    //             $tax_total = round($this->normalizeInput($request->input("taxTotal" . $i)));
    //             $extra_cost_price = $this->normalizeInput($request->input("extraCostPrice" . $i));
    //             $extra_cost_dest = $request->input("extraCostDest" . $i) ?? "";
    //             $extra_cost_vendor_name = $request->input("extraCostVendorName" . $i) ?? "";
    //             $extra_cost_shipping_number = $request->input("extraCostShippingNum" . $i) ?? "";
    //             $pickup_weight = $this->normalizeInput($request->input("pickUpWeight" . $i));
    //             $pickup_charge = $this->normalizeInput($request->input("pickUpCharge" . $i));
    //             $sub_total = isset($request->input("subTotal")[$i]) ? $this->normalizeInput($request->input("subTotal")[$i]) : 0;
    //             $fc_symbol = $request->input("foreignSymbol") ?? "";
    //             $fc_value = $this->normalizeInput($request->input("foreignRateValue")) ?? 0;

    //             $go->warehouse_id != $request->input("warehouseval")[$i] ? $n++ : '';
    //             $go->service_id != $request->input("serviceval")[$i] ? $n++ : '';
    //             $go->mismass_order_id != $request->input("mismassOrderId") ? $n++ : '';
    //             $go->mismass_invoice_date != date("Y-m-d H:i:s", strtotime($this->dateFilterFormat($request->input("tanggalInvoice"))[0])) ? $n++ : '';
    //             $go->sender_first_name != $sender_first_name ? $n++ : '';
    //             $go->sender_middle_name != $sender_middle_name ? $n++ : '';
    //             $go->sender_last_name != $sender_last_name ? $n++ : '';
    //             $go->sender_email != $sender_email ? $n++ : '';
    //             $go->sender_phone != $sender_phone ? $n++ : '';
    //             $go->sender_address != $sender_address ? $n++ : '';
    //             $go->sender_sub_district != $sender_sub_district ? $n++ : '';
    //             $go->sender_district != $sender_district ? $n++ : '';
    //             $go->sender_city != $sender_city ? $n++ : '';
    //             $go->sender_prov != $sender_prov ? $n++ : '';
    //             $go->sender_postal_code != $sender_postal_code ? $n++ : '';
                
    //             $go->cons_first_name != $cons_first_name ? $n++ : '';
    //             $go->cons_middle_name != $cons_middle_name ? $n++ : '';
    //             $go->cons_last_name != $cons_last_name ? $n++ : '';
    //             $go->cons_email != $cons_email ? $n++ : '';
    //             $go->cons_phone != $cons_phone ? $n++ : '';
    //             $go->cons_address != $cons_address ? $n++ : '';
    //             $go->cons_sub_district != $cons_sub_district ? $n++ : '';
    //             $go->cons_district != $cons_district ? $n++ : '';
    //             $go->cons_city != $cons_city ? $n++ : '';
    //             $go->cons_prov != $cons_prov ? $n++ : '';
    //             $go->cons_postal_code != $cons_postal_code ? $n++ : '';

    //             $go->doku_invoice_id != $doku_invoice_id ? $n++ : '';
    //             $go->doku_link != $doku_link ? $n++ : '';
    //             $go->bank_name != $bank_name ? $n++ : '';
    //             $go->bank_account_name != $bank_account_name ? $n++ : '';
    //             $go->bank_account_id != $bank_account_id ? $n++ : '';
    //             $go->template_id != $template_id ? $n++ : '' ;
    //             $go->length != $length ? $n++ : '';
    //             $go->width != $width ? $n++ : '';
    //             $go->height != $height ? $n++ : '';
    //             $go->weight != $weight ? $n++ : '';
    //             $go->cbm != $cbm ? $n++ : '';
    //             $go->actual_weight != $actualWeight ? $n++ : '';
    //             $go->item != $item ? $n++ : '';
    //             $go->discount != $discount ? $n++ : '';
    //             $go->additional_desc != $additional_desc ? $n++ : '';
    //             $go->additional_nom != $additional_nom ? $n++ : '';
    //             $go->packing != $packing ? $n++ : '';
    //             $go->packing_per != $packing_per ? $n++ : '';
    //             $go->packing_total != $packing_total ? $n++ : '';
    //             $go->packing_desc != $packing_desc ? $n++ : '';
    //             $go->import_permit != $import_permit ? $n++ : '';
    //             $go->import_permit_per != $import_permit_per ? $n++ : '';
    //             $go->import_permit_total != $import_permit_total ? $n++ : '';
    //             $go->import_permit_desc != $import_permit_desc ? $n++ : '';
    //             $go->document != $document ? $n++ : '';
    //             $go->document_per != $document_per ? $n++ : '';
    //             $go->document_total != $document_total ? $n++ : '';
    //             $go->document_desc != $document_desc ? $n++ : '';
    //             $go->dr_medicine != $dr_medicine ? $n++ : '';
    //             $go->dr_medicine_per != $dr_medicine_per ? $n++ : '';
    //             $go->dr_medicine_total != $dr_medicine_total ? $n++ : '';
    //             $go->dr_medicine_desc != $dr_medicine_desc ? $n++ : '';
    //             $go->insurance_item_price != $insurance_item_price ? $n++ : '';
    //             $go->insurance_percent != $insurance_percent ? $n++ : '';
    //             $go->insurance_total != $insurance_total ? $n++ : '';
    //             $go->tax_item_price != $tax_item_price ? $n++ : '';
    //             $go->tax_percent != $tax_percent ? $n++ : '';
    //             $go->tax_total != $tax_total ? $n++ : '';
    //             $go->fee_item_price != $fee_item_price ? $n++ : '';
    //             $go->fee_percent != $fee_percent ? $n++ : '';
    //             $go->fee_total != $fee_total ? $n++ : '';
    //             $go->extra_cost_price != $extra_cost_price ? $n++ : '';
    //             $go->extra_cost_dest != $extra_cost_dest ? $n++ : '';
    //             $go->extra_cost_vendor_name != $extra_cost_vendor_name ? $n++ : '';
    //             $go->extra_cost_shipping_number != $extra_cost_shipping_number ? $n++ : '';
    //             $go->pickup_weight != $pickup_weight ? $n++ : '';
    //             $go->pickup_charge != $pickup_charge ? $n++ : '';
    //             $go->sub_total != $sub_total ? $n++ : '';
    //             $go->fc_symbol != $fc_symbol ? $n++ : '';
    //             $go->fc_value != $fc_value ? $n++ : '';

    //             $i++;
    //     }

    //     //jika tidak ada perbedaan maka tidak disimpan
    //     if($n==0){
    //         $encode = array("status" => "Berhasil", "text" => "Tidak Ada Perubahan Data", "url" => url('/printout/invoice/' . $this->invOnlyId($request->input('mismassInvoiceId'))));
    //         return json_encode($encode);
    //     }

    //     $dataHistory = [
    //         "codename" => "EI",
    //         "created_at" => date("Y-m-d H:i:s"),
    //         "created_by" => Auth::user()->username,
    //         "description" => $this->createDescForEditInvoice($request),
    //     ];
    //     $insertHistory = DB::table("history_list")->insert($dataHistory);
    //     if(!$insertHistory){
    //         $encode = array("status" => "Gagal", "text" => "Gagal Buat History");
    //         return json_encode($encode);
    //     }

    //     //Ambil Data Created Agar Tidak Berubah
    //     $getCreated = DB::table("data_list")->select("created_at","created_by")->where("mismass_invoice_id",$request->input("mismassInvoiceId"))->first();

    //     //jika ada perbedaan maka yg lama dihapus dan yang baru diinsert
    //     DB::table('data_list')->where("mismass_invoice_id",$request->input("mismassInvoiceId"))->delete();

    //     for ($i = 0; $i <= count($request->input("warehouseval")) - 1; $i++) {

    //         $values = array ("created_at" => $getCreated->created_at,
    //                         "created_by" => $getCreated->created_by,
    //                         "updated_at" => date("Y-m-d H:i:s"),
    //                         "updated_by" => Auth::user()->username,
    //                         "warehouse_id" => $request->input("warehouseval")[$i],
    //                         "cust_id" => $request->input("custId"),
    //                         "cust_type_id" => $request->input("custTypeId"),
    //                         "service_id" => $request->input("serviceval")[$i],
    //                         "mismass_order_id" => $request->input("mismassOrderId"),
    //                         "mismass_invoice_id" => $request->input('mismassInvoiceId'),
    //                         "mismass_invoice_date" => date("Y-m-d",strtotime($request->input('tanggalInvoice'))),
    //                         "mismass_invoice_link" => $request->input('mismassInvoiceLink'),
    //                         "invoice_status" => "UNPAID",
    //                         "payment_status" => "PENDING",
    //                         "sender_first_name" => $this->noSingleQuo($request->input("senderFirstName")[$i]) ?? "",
    //                         "sender_middle_name" => $this->noSingleQuo($request->input("senderMiddleName")[$i]) ?? "",
    //                         "sender_last_name" => $this->noSingleQuo($request->input("senderLastName")[$i]) ?? "",
    //                         "sender_email" => $request->input("senderEmail")[$i] ?? "",
    //                         "sender_phone" => $request->input("senderPhone")[$i] ?? "",
    //                         "sender_address" => $this->noSingleQuo($request->input("senderAddress")[$i]) ?? "",
    //                         "sender_sub_district" => $this->noSingleQuo($request->input("senderSubDistrict")[$i]) ?? "",
    //                         "sender_district" => $this->noSingleQuo($request->input("senderDistrict")[$i]) ?? "",
    //                         "sender_city" => $this->noSingleQuo($request->input("senderCity")[$i]) ?? "",
    //                         "sender_prov" => $this->noSingleQuo($request->input("senderProv")[$i]) ?? "",
    //                         "sender_postal_code" => $request->input("senderPostalCode")[$i] ?? "",
    //                         "cons_first_name" => $this->noSingleQuo($request->input("consFirstName")[$i]) ?? "",
    //                         "cons_middle_name" => $this->noSingleQuo($request->input("consMiddleName")[$i]) ?? "",
    //                         "cons_last_name" => $this->noSingleQuo($request->input("consLastName")[$i]) ?? "",
    //                         "cons_email" => $request->input("consEmail")[$i] ?? "",
    //                         "cons_phone" => $request->input("consPhone")[$i] ?? "",
    //                         "cons_address" => $this->noSingleQuo($request->input("consAddress")[$i]) ?? "",
    //                         "cons_sub_district" => $this->noSingleQuo($request->input("consSubDistrict")[$i]) ?? "",
    //                         "cons_district" => $this->noSingleQuo($request->input("consDistrict")[$i]) ?? "",
    //                         "cons_city" => $this->noSingleQuo($request->input("consCity")[$i]) ?? "",
    //                         "cons_prov" => $this->noSingleQuo($request->input("consProv")[$i]) ?? "",
    //                         "cons_postal_code" => $request->input("consPostalCode")[$i] ?? "",
    //                         "doku_invoice_id" => $request->input("invoiceDoku") ?? "",
    //                         "doku_link" => $request->input("linkDoku") ?? "",
    //                         "bank_name" => $request->input("namaBank") ?? "",
    //                         "bank_account_name" => $request->input("namaRekening") ?? "",
    //                         "bank_account_id" => $request->input("noRekening") ?? "",
    //                         "template_id" => $request->input("templateId"),
    //                         "forwarder_id" => "",
    //                         "shipping_number" => "",
    //                         "shipping_created_at" => "",
    //                         "shipping_created_by" => "",
    //                         "shipping_updated_at" => "",
    //                         "shipping_updated_by" => "",
    //                         "length" => isset($request->input("panjang")[$i]) ? $this->normalizeInput($request->input("panjang")[$i]) : 0,
    //                         "width" => isset($request->input("lebar")[$i]) ? $this->normalizeInput($request->input("lebar")[$i]) : 0,
    //                         "height" => isset($request->input("tinggi")[$i]) ? $this->normalizeInput($request->input("tinggi")[$i]) : 0,
    //                         "weight" => isset($request->input("kg")[$i]) ? $this->normalizeInput($request->input("kg")[$i]) : 0,
    //                         "cbm" => isset($request->input("cbm")[$i]) ? $this->normalizeInput($request->input("cbm")[$i]) : 0,
    //                         "actual_weight" => isset($request->input("actualKg")[$i]) ? $this->normalizeInput($request->input("actualKg")[$i]) : 0,
    //                         "item" => isset($request->input("item")[$i]) ? $this->normalizeInput($request->input("item")[$i]) : 0,
    //                         "service_name" => $request->input('serviceName'),
    //                         "service_price_per" => isset($request->input("pricePer")[$i]) ? $this->normalizeInput($request->input("pricePer")[$i]) : 0,
    //                         "discount" => $this->normalizeInput($request->input("discount" . $i)),
    //                         "additional_nom" => $this->normalizeInput($request->input("additionalNominal" . $i)),
    //                         "additional_desc" => $request->input("additionalDesc" . $i) ?? "",
    //                         "packing" => $this->normalizeInput($request->input("packing" . $i)),
    //                         "packing_per" => $this->normalizeInput($request->input("packingPer" . $i)),
    //                         "packing_total" => $this->normalizeInput($request->input("packingTotal" . $i)),
    //                         "packing_desc" => $request->input("packingDesc" . $i) ?? "",
    //                         "import_permit" => $this->normalizeInput($request->input("import" . $i)),
    //                         "import_permit_per" => $this->normalizeInput($request->input("importPer" . $i)),
    //                         "import_permit_total" => $this->normalizeInput($request->input("importTotal" . $i)),
    //                         "import_permit_desc" => $request->input("importDesc" . $i) ?? "",
    //                         "document" => $this->normalizeInput($request->input("document" . $i)),
    //                         "document_per" => $this->normalizeInput($request->input("documentPer" . $i)),
    //                         "document_total" => $this->normalizeInput($request->input("documentTotal" . $i)),
    //                         "document_desc" => $request->input("documentDesc" . $i) ?? "",
    //                         "dr_medicine" => $this->normalizeInput($request->input("medicine" . $i)),
    //                         "dr_medicine_per" => $this->normalizeInput($request->input("medicinePer" . $i)),
    //                         "dr_medicine_total" => $this->normalizeInput($request->input("medicineTotal" . $i)),
    //                         "dr_medicine_desc" => $request->input("medicineDesc" . $i) ?? "",
    //                         "insurance_item_price" => $this->normalizeInput($request->input("insurancePriceItem" . $i)),
    //                         "insurance_percent" => $this->normalizeInput($request->input("insurancePercent" . $i)),
    //                         "insurance_total" => round($this->normalizeInput($request->input("insuranceTotal" . $i))),
    //                         "fee_item_price" => $this->normalizeInput($request->input("feePriceItem" . $i)),
    //                         "fee_percent" => $this->normalizeInput($request->input("feePercent" . $i)),
    //                         "fee_total" => round($this->normalizeInput($request->input("feeTotal" . $i))),
    //                         "tax_item_price" => $this->normalizeInput($request->input("taxPriceItem" . $i)),
    //                         "tax_percent" => $this->normalizeInput($request->input("taxPercent" . $i)),
    //                         "tax_total" => round($this->normalizeInput($request->input("taxTotal" . $i))),
    //                         "extra_cost_price" => $this->normalizeInput($request->input("extraCostPrice" . $i)),
    //                         "extra_cost_dest" => $request->input("extraCostDest" . $i) ?? "",
    //                         "extra_cost_vendor_name" => $request->input("extraCostVendorName" . $i) ?? "",
    //                         "extra_cost_shipping_number" => $request->input("extraCostShippingNum" . $i) ?? "",
    //                         "pickup_weight" => $this->normalizeInput($request->input("pickUpWeight" . $i)),
    //                         "pickup_charge" => $this->normalizeInput($request->input("pickUpCharge" . $i)),
    //                         "sub_total" => isset($request->input("subTotal")[$i]) ? $this->normalizeInput($request->input("subTotal")[$i]) : 0,
    //                         "fc_symbol" => $this->normalizeInput($request->input("foreignRateValue"))>0?$request->input("foreignSymbol"):"",
    //                         "fc_value" => $this->normalizeInput($request->input("foreignRateValue"))>0?$this->normalizeInput($request->input("foreignRateValue")):0);

    //         $insert = DB::table('data_list')->insert($values);

    //         if ($insert) {
    //             $success++;
    //         }
    //     }

    //     // $data = [
    //     //     $request->input("custTypeId")=="IND" ? $request->input("consPhone")[0] : $request->input("senderPhone")[0],
    //     //     $request->input("custTypeId")=="IND" ? $request->input("consFirstName")[0]." ".$request->input("consMiddleName")[0]." ".$request->input("consLastName")[0] : $request->input("senderFirstName")[0]." ".$request->input("senderMiddleName")[0]." ".$request->input("senderLastName")[0],
    //     //     $this->dateFormatIndo(date("Y-m-d H:i:s"),1),
    //     //     $request->input("linkDoku")!="" ? $request->input("linkDoku") : "-",
    //     //     $this->dateFormatIndo($this->dateFilterFormat($request->input("tanggalInvoice"))[0],1),
    //     //     $request->input('mismassInvoiceId'),
    //     //     $request->input("invoiceDoku")!="" ? $request->input("invoiceDoku") : "-",
    //     //     "UNPAID",
    //     //     DB::table("cust_type_list")->where("id",$request->input("custTypeId"))->value("name")
    //     // ];
    //     // env('QONTAK_STATUS') ?  $this->initializeEditInvoice($data) : '';

    //     $encode = array("status" => "Gagal", "text" => "Gagal Buat Invoice", "url" => "");
    //     if ($success == count($request->input("warehouseval"))) {
    //         $encode = array("status" => "Berhasil", "text" => "Data Invoice Berhasil diedit dan telah dikirim ke Whatsapp Customer.", "url" => url('/printout/invoice/' . $this->invOnlyId($request->input('mismassInvoiceId'))));
    //     }

    //     return json_encode($encode);

    // }

    // public function updateInvoice(Request $request){
    //     $invoiceNumber = $request->input('invoiceNumber');
    //     $dateNow = date("Y-m-d H:i:s");
    //     $updateInvoice = $this->invoiceModel->where('mismass_invoice_id',$invoiceNumber)->update(['payment_status'=>'SUCCESS','invoice_status'=>'PAID','payment_success_at'=>$dateNow]);
        
    //     if(!$updateInvoice){
    //         $encode = array("status" => "Gagal", "text" => "Gagal Update Invoice");
    //         return json_encode($encode);
    //     }   

    //     //INSERT DATA HISTORY
    //     $dataHistory = [
    //         "codename" => "PS",
    //         "created_at" => date("Y-m-d H:i:s"),
    //         "created_by" => Auth::user()->username,
    //         "description" => $this->createDescForPaymentSuccess($invoiceNumber),
    //     ];
    //     $insertHistory = DB::table("history_list")->insert($dataHistory);
    //     if(!$insertHistory){
    //         $encode = array("status" => "Gagal", "text" => "Gagal Buat History");
    //         return json_encode($encode);
    //     }

    //     $encode = array("status" => "Berhasil", "text" => "Data Berhasil Diupdate.");
    //     return json_encode($encode);
    // }

    public function editInvoiceGetData(Request $request){
        $mismassOrderId = $request->input("mismassOrderId");

        $data = DB::table("data_list")
                ->select("data_list.*","order_list.created_at as orderCreatedAt")
                ->join("order_list","order_list.id","=","data_list.mismass_order_id")
                ->where("data_list.mismass_order_id","=",$mismassOrderId)
                ->get();

        $output = [
            'data' => $data
        ];

        return json_encode($output);
    }

    public function hapusInvoice(Request $request){
        $mismassInvoiceId = $request->input("id");

        // $this->sendWa($mismassInvoiceId,"HI");

        $dataHistory = [
            "codename" => "HI",
            "created_at" => date("Y-m-d H:i:s"),
            "created_by" => Auth::user()->username,
            "description" => $this->createDescForInvoice($mismassInvoiceId,"HI"),
        ];
        $insertHistory = DB::table("history_list")->insert($dataHistory);
        if(!$insertHistory){
            $encode = array("status" => 500, "title" => "Gagal", "text" => "Gagal Buat History");
            return json_encode($encode);
        }

        $delete = DB::table("data_list")->where("mismass_invoice_id",$mismassInvoiceId)->delete();
        if(!$delete){
            $encode = array("status" => 500, "title" => "Gagal", "text" => "Gagal Hapus Invoice 001");
            return json_encode($encode);
        }

        // $orderModel = new Order;
        // $update = $orderModel->where("id",$get->mismass_order_id)->update(["invoice_id" => ""]);

        // if(!$update){
        //     $encode = array("status" => "Gagal", "text" => "Gagal Hapus Invoice 002");
        //     return json_encode($encode);
        // }

        $lawas = DB::table("order_list")->where("invoice_id",$mismassInvoiceId)->first();
        $dataHistory=[
            "codename" => "HO",
            "created_at" => date("Y-m-d H:i:s"),
            "created_by" => Auth::user()->username,
            "description" => "<b>Hapus Order</b> dengan detail,<br><br>
            ID Sistem : <b>".$lawas->id."</b><br>
            Tipe : <b>".DB::table("cust_type_list")->where("id", $lawas->cust_type_id)->value("name")."</b><br>
            First Name : <b>".$lawas->first_name."</b><br>
            Middle Name : <b>".$lawas->middle_name."</b><br>
            Last Name : <b>".$lawas->last_name."</b><br>
            Telpon : <b>".$lawas->phone."</b><br>
            Email : <b>".$lawas->email."</b><br>
            Alamat : <b>".$lawas->address.", ".$lawas->sub_district.", ".$lawas->district.", ".$lawas->city.", ".$lawas->prov.", ".$lawas->postal_code."</b>",
        ];
        $insertHistory = DB::table('history_list')->insert($dataHistory);
        if(!$insertHistory){
            $encode = array("status" => 500, "title" => "Gagal", "text" => "Gagal Buat History");
            return json_encode($encode);
        }

        $deleteOrder = DB::table("order_list")->where("invoice_id",$mismassInvoiceId)->delete();
        if(!$deleteOrder){
            $encode = array("status" => 500, "title" => "Gagal", "text" => "Gagal Hapus Invoice 002");
            return json_encode($encode);
        }

        $encode = array("status" => 200, "title" => "Berhasil", "text" => "Berhasil Hapus Invoice");
        return json_encode($encode);
    }
    
    public function updateInvoiceStatus(Request $request){

        //Initialize
        $id = $request->input("id");
        $tanggalJam = date("Y-m-d H:i:s",strtotime($this->dateFilterFormat($request->input("tanggalJamInvoice"))[0]));
        $get = DB::table("data_list")->where("mismass_invoice_id",$id)->first();
        
        //If Invoice Is Nowhere
        if($get==null){
            $encode = array("status" => 405, "header" => "Gagal", "text" => "Invoice Tidak Ada");
            return json_encode($encode);
        }
        
        //If Invoice Is Already Success
        if($get->payment_status=="SUCCESS"){
            $encode = array("status" => 405, "header" => "Gagal", "text" => "Invoice Sudah Terbayar");
            return json_encode($encode);
        }
        
        //Update To Success
        $pindahStatus = $this->invoiceModel->where("mismass_invoice_id",$id)->update(['payment_status'=>'SUCCESS','invoice_status'=>'PAID','payment_success_at'=>$tanggalJam]);
        if(!$pindahStatus){
            $encode = array("status" => 405, "header" => "Gagal","text" => "Gagal Update Invoice Doku");
            return json_encode($encode);
        }

        //Create History
        $dataHistory = [
            "codename" => "PS",
            "created_at" => date("Y-m-d H:i:s"),
            "created_by" => Auth::user()->username,
            "description" => $this->createDescForPaymentSuccess($id),
        ];
        $insertHistory = DB::table("history_list")->insert($dataHistory);
        if(!$insertHistory){
            $encode = array("status" => "Gagal", "text" => "Gagal Buat History");
            return json_encode($encode);
        }

        //Create Tracking
        $getMsTrack = DB::table("order_list")
                    ->select("ms_track_id")
                    ->where("invoice_id",$id)
                    ->get();
        foreach($getMsTrack as $gm){
            if($gm->ms_track_id!=""){
                $dataTracking = [
                    "created_at" => $tanggalJam,
                    "created_by" => Auth::user()->username,
                    "ms_track_id" => $gm->ms_track_id,
                    "track_status_id" => 11,
                    "track_status_manual_id" => "A",
                    "text" => DB::table("shiptrip_track_status")->where("id","11")->value("value")
                ];
                $updateTracking = DB::table("shiptrip_track_list")->insert($dataTracking);
            }
        }
        
        //Send Details To Customer/Client
        // if(env("WA_GATEWAY")){
        //     $data = array(
        //         "phone" => $get->cust_type_id=="IND" ? $get->cons_phone : $get->sender_phone,
        //         "fullName" => $get->cust_type_id=="IND" ? $get->cons_first_name." ".$get->cons_middle_name." ".$get->cons_last_name : $get->sender_first_name." ".$get->sender_middle_name." ".$get->sender_last_name,
        //         "invoiceDate" => $this->dateFormatIndo($get->mismass_invoice_date,1),
        //         "invoiceLink" => url('/p')."/".$get->mismass_invoice_link,
        //         "invoice" => $get->mismass_invoice_id,
        //         "paymentLink" => $get->doku_link!=""?url('/payment')."/".$get->doku_link:"", //Link Payment Untuk Doku
        //         "statusPay" => "PAID",
        //         "custType" => DB::table("cust_type_list")->where("id",$get->cust_type_id)->value("name")
        //     );
        //     $this->invoicePaidSendWA($data);
        // }
        
        //Return If All Was Success
        $encode = array("status" => 200, "header" => "Berhasil", "text" => "Invoice Berhasil Diupdate");
        return json_encode($encode);
    }

    public function buatResi(Request $request)
    {
        $cust_type_id = $request->input("custTypeId");

        if($cust_type_id=="IND"){
            
            if($request->input("noResi")[0]==""){
                $encode = array("status" => "Gagal", "text" => "Shipping Number Tidak Ada");
                return json_encode($encode);
            }

            $namaForwarder = $request->input("namaForwarder")[0] ?? "";
            $shipping_number = $request->input("noResi")[0];
            $shipping_stats = ($request->input("shippingNumberStatsValue")[0] ?? "") == "checked" ? 1 : 0;
            if ($request->input("tipeForwarder")[0]  != "VENDOR") {
                $uniqId = str::random(30);
                DB::table('pool_shipping_id')->insert(
                    ['uniq_id' => $uniqId]
                );
                $shipping_id = DB::table('pool_shipping_id')->where("uniq_id", $uniqId)->value("id");
                $prefix_shipping_id = "TR/ALY/" . date("y");
                $shipping_number = $prefix_shipping_id . $shipping_id;
            }

            $toSgMan = "0000-00-00 00:00:00";
            $dropMan = "0000-00-00 00:00:00";
            if($request->input("anchorMsTrack")[0]!=null){
                $getShipTrip = DB::table("shiptrip_list")
                                ->select("to_sg_man_created_at","drop_man_created_at")
                                ->where("ms_track_id",$request->input("anchorMsTrack")[0])
                                ->first();
                $toSgMan = $getShipTrip->to_sg_man_created_at;
                $dropMan = $getShipTrip->drop_man_created_at;
            }

            $trackStatusId = $this->trackingModel->getStatusIdTracking($request->input("tipeForwarder")[0]);

            $update = DB::table("data_list")->where("mismass_invoice_id", "=", $request->input("mismassInvoiceId"))
            ->update([
                "to_sg_man_created_at" => $toSgMan,
                "drop_man_created_at" => $dropMan,
                "track_man_created_at" => date("Y-m-d H:i:s"),
                "track_status_id" => $trackStatusId,
                "ms_track_id" => $request->input("anchorMsTrack")[0] ?? "",
                "forwarder_id" => $request->input("tipeForwarder")[0],
                "forwarder_name" => $namaForwarder,
                "shipping_number_stats" => $shipping_stats, 
                "shipping_number" => $shipping_number,
                "shipping_created_at" => date("Y-m-d H:i:s"),
                "shipping_created_by" => Auth::user()->username,
                "shipping_updated_at" => date("Y-m-d H:i:s"),
                "shipping_updated_by" => Auth::user()->username,
                "shipping_status" => $this->trackingModel->getTextTracking($request->input("tipeForwarder")[0],$namaForwarder),
                "invoice_status" => "PAID"
            ]);

            $encode = array("status" => "Gagal", "text" => "Gagal Buat Resi");
            if ($update) {
                if(!$shipping_stats){
                    if(env('WA_GATEWAY')){
                        // $this->createResiConsSendWA($request->input("mismassInvoiceId"));
                    }
                }

                //Tracking
                if($request->input("anchorMsTrack")[0]!=null){
                    $getMsTrack = DB::table("order_list")
                        ->select("ms_track_id")
                        ->where("invoice_id",$request->input("mismassInvoiceId"))
                        ->get();
                    foreach($getMsTrack as $gm){
                        if($gm->ms_track_id!=""){
                            $dataTracking = [
                                "created_at" => date("Y-m-d H:i:s"),
                                "created_by" => Auth::user()->username,
                                "ms_track_id" => $gm->ms_track_id,
                                "track_status_id" => $trackStatusId,
                                "track_status_manual_id" => "A", 
                                "text" => $this->trackingModel->getTextTracking($request->input("tipeForwarder")[0],$namaForwarder)
                            ];
                            $updateTracking = DB::table("shiptrip_track_list")->insert($dataTracking);
    
                            DB::table("shiptrip_list")
                            ->where("ms_track_id", "=", $gm->ms_track_id)
                            ->update([
                                "track_status_id" => $trackStatusId,
                            ]);
                        }
                    }
                }

                // $encode = array("status" => "Berhasil", "text" => "Berhasil Buat Resi Dan Telah Dikirim Ke Whatsapp Customer. Silahkan Cek Pada Tabel Status.", "url" => url('/printout/resi/' . $this->resiNoGaring($this->resiOnlyId($shipping_number, $request->input("tipeForwarder")[0]).'=')));
                $encode = array("status" => "Berhasil", "text" => "Berhasil Buat Resi Dan Telah Dikirim Ke Whatsapp Customer. Silahkan Cek Pada Tabel Status.", "url" => url('/printout/resi/' . $this->encodeURLCustom($shipping_number).'='));
            }

        }else{
            
            for($i=0;$i<=count($request->input('id'))-1;$i++){
                if($request->input("noResi")[$i]==""){
                    $encode = array("status" => "Gagal", "text" => "Shipping Number Tidak Ada");
                    return json_encode($encode);
                }
            }

            $saklar = true;
            $urlResi = "/printout/resi/";
            for($i=0;$i<=count($request->input('id'))-1;$i++){
                $shipping_number = $request->input("noResi")[$i];
                // $shipping_stats = 0;
                // $namaForwarder = "";
                $shipping_stats = ($request->input("shippingNumberStatsValue")[$i] ?? "") == "checked" ? 1 : 0;
                $namaForwarder = $request->input("namaForwarder")[$i] ?? "";
                if ($request->input("tipeForwarder")[$i]  != "VENDOR") {
                    $uniqId = str::random(30);
                    DB::table('pool_shipping_id')->insert(
                        ['uniq_id' => $uniqId]
                    );
                    $shipping_id = DB::table('pool_shipping_id')->where("uniq_id", $uniqId)->value("id");
                    $prefix_shipping_id = "TR/ALY/" . date("y");
                    $shipping_number = $prefix_shipping_id . $shipping_id;
                
                }
                // else{
                //     $shipping_stats = ($request->input("shippingNumberStatsValue")[$i] ?? "") == "on" ? 1 : 0;
                //     $namaForwarder = $request->input("namaForwarder")[$i] ?? "";
                // }

                $toSgMan = "0000-00-00 00:00:00";
                $dropMan = "0000-00-00 00:00:00";
                if($request->input("anchorMsTrack")[$i]!=null){
                    $getShipTrip = DB::table("shiptrip_list")
                                    ->select("to_sg_man_created_at","drop_man_created_at")
                                    ->where("ms_track_id",$request->input("anchorMsTrack")[$i])
                                    ->first();
                    $toSgMan = $getShipTrip->to_sg_man_created_at;
                    $dropMan = $getShipTrip->drop_man_created_at;
                }

                $trackStatusId = $this->trackingModel->getStatusIdTracking($request->input("tipeForwarder")[$i]);
                
                // $urlResi .= $this->resiNoGaring($this->resiOnlyId($shipping_number, $request->input("tipeForwarder")[$i]))."=";
                $urlResi .= $this->encodeURLCustom($shipping_number).'=';

                $update = DB::table("data_list")->where("id", "=", $request->input("id")[$i])
                        ->update([
                            "to_sg_man_created_at" => $toSgMan,
                            "drop_man_created_at" => $dropMan,
                            "track_man_created_at" => date("Y-m-d H:i:s"),
                            "track_status_id" => $trackStatusId,
                            "ms_track_id" => $request->input("anchorMsTrack")[$i] ?? "",
                            "forwarder_id" => $request->input("tipeForwarder")[$i],
                            "forwarder_name" => $namaForwarder,
                            "shipping_number_stats" => $shipping_stats, 
                            "shipping_number" => $shipping_number,
                            "shipping_created_at" => date("Y-m-d H:i:s"),
                            "shipping_created_by" => Auth::user()->username,
                            "shipping_updated_at" => date("Y-m-d H:i:s"),
                            "shipping_updated_by" => Auth::user()->username,
                            "shipping_status" => $this->trackingModel->getTextTracking($request->input("tipeForwarder")[$i],$namaForwarder),
                            "invoice_status" => "PAID"
                        ]);

                //Tracking
                if($request->input("anchorMsTrack")[$i]!=null){
                    $dataTracking = [
                        "created_at" => date("Y-m-d H:i:s"),
                        "created_by" => Auth::user()->username,
                        "ms_track_id" => $shipping_number,
                        "track_status_id" => $this->trackingModel->getStatusIdTracking($request->input("tipeForwarder")[$i]),
                        "track_status_manual_id" => "A", 
                        "text" => $this->trackingModel->getTextTracking($request->input("tipeForwarder")[$i],$namaForwarder)
                    ];
                    $updateTracking = DB::table("shiptrip_track_list")->insert($dataTracking);                        
                }
                
                // if($saklar){
                    // if($shipping_stats==0){
                        // $get = DB::table("data_list")->where("mismass_invoice_id",$request->input("mismassInvoiceId"))->first();
                        // if($get->bank_name!=""){
                            // $data = array(
                            //     "phone" => $get->cust_type_id=="IND" ? $get->cons_phone : $get->sender_phone,
                            //     "fullName" => $get->cust_type_id=="IND" ? $get->cons_first_name." ".$get->cons_middle_name." ".$get->cons_last_name : $get->sender_first_name." ".$get->sender_middle_name." ".$get->sender_last_name,
                            //     "invoiceDate" => $this->dateFormatIndo($get->mismass_invoice_date,1),
                            //     "invoiceLink" => url('/p')."/".$get->mismass_invoice_link,
                            //     "invoice" => $get->mismass_invoice_id,
                            //     "paymentLink" => url('/payment')."/".$get->doku_link, //Link Payment Untuk Doku
                            //     "statusPay" => "PAID",
                            //     "custType" => DB::table("cust_type_list")->where("id",$get->cust_type_id)->value("name")
                            // );
                            // $this->invoicePaidSendWA($data);
                        // }
                        // env('QONTAK_STATUS') ? $this->initializeCreateResiCOR($request->input('id')[$i],0) : '';
                    // }
                // }
                // if($shipping_stats==0){
                    // $this->createResiConsSendWA($request->input("mismassInvoiceId"));
                    // env('QONTAK_STATUS') ? $this->initializeCreateResiCOR($request->input('id')[$i],1) : '';
                // }
                // $saklar = false;
            }

            if($request->input("anchorMsTrack")[0]!=null){
                //Update Tracking Untuk Corporate
                $arrayCorOnly = [
                    $request->input("mismassInvoiceId"),
                    Auth::user()->username,
                    $trackStatusId
                ];
                $updateCorOnly = $this->trackingModel->updateTrackingForCorOnly($arrayCorOnly);
                // $getMsTrack = DB::table("order_list")
                //             ->select("ms_track_id")
                //             ->where("invoice_id",$request->input("mismassInvoiceId"))
                //             ->get();

                // foreach($getMsTrack as $gm){
                //     DB::table("shiptrip_list")
                //     ->where("ms_track_id", "=", $gm->ms_track_id)
                //     ->update([
                //         "track_status_id" => $trackStatusId,
                //     ]);
                // }
            }
            $encode = array("status" => "Berhasil", "text" => "Berhasil Buat Resi Dan Telah Dikirim Ke Whatsapp Customer. Silahkan Cek Pada Tabel Status.", "url" => url($urlResi));
        }

        $dataHistory = [
            "codename" => "BR",
            "created_at" => date("Y-m-d H:i:s"),
            "created_by" => Auth::user()->username,
            "description" => $this->createDescForResi($request->input("mismassInvoiceId")),
        ];
        $insertHistory = DB::table("history_list")->insert($dataHistory);
        if(!$insertHistory){
            $encode = array("status" => "Gagal", "text" => "Gagal Buat History");
            return json_encode($encode);
        }

        return json_encode($encode);
    }

    public function editResi(Request $request){
        //Initialize
        $username = Auth::user()->username;
        $now = date("Y-m-d H:i:s");
        $urlResi = "/printout/resi/";

        //Checking Data
        $check = $this->resiService->checkDataEditResi($request);

        //Jika Data Tidak Ada Perbedaan
        if($check["diff"]==0){
            $encode = array("status" => "Berhasil", "text" => "Tidak Ada Perubahan Data", "url" => "");
            return json_encode($encode);
        }

        //Create History
        $dataHistory = [
            "codename" => "ER",
            "created_at" => $now,
            "created_by" => $username,
            "description" => $this->createDescForEditResi($request),
        ];
        $insertHistory = DB::table("history_list")->insert($dataHistory);
        if(!$insertHistory){
            $encode = array("status" => "Gagal", "text" => "Gagal Buat History");
            return json_encode($encode);
        }

        //Update Data
        $arrayUpdateDataEditResi = [
            "now" => $now,
            "request" => $request,
            "urlResi" => $urlResi
        ];
        $update = $this->resiService->updateDataEditResi($arrayUpdateDataEditResi);

        //Return Jika Sukses
        if(!$update['status']){
            $encode = array("status" => $update['name'], "text" => $update['msg']);
            return json_encode($encode);
        }

        $urlResi = $update['urlResi'];
        $encode = array("status" => $update['name'], "text" => $update['msg'], "url" => url($urlResi));
        return json_encode($encode);
    }

    // public function editResi(Request $request)
    // {
    //         $saklar = true;
    //         $urlResi = "/printout/resi/";
    //         $noSame = 0;
    //         $now = date("Y-m-d H:i:s");
    //         $arrayOldData["forwarder_id"]=[];
    //         $arrayOldData["forwarder_name"]=[];            

    //         for($i=0;$i<=count($request->input('id'))-1;$i++){
    //             $oldData = DB::table("data_list")->where("id",$request->input("id")[$i])->first();

    //             $shippingNumberStatsNew = ($request->input("shippingNumberStatsValue")[$i] ?? "") == "checked" ? 1 : 0;

    //             $request->input("consFirstName")[$i]!=$oldData->cons_first_name?$noSame++:'';
    //             $request->input("consMiddleName")[$i]!=$oldData->cons_middle_name?$noSame++:'';
    //             $request->input("consLastName")[$i]!=$oldData->cons_last_name?$noSame++:'';
    //             $request->input("consPhone")[$i]!=$oldData->cons_phone?$noSame++:'';
    //             $request->input("consAddress")[$i]!=$oldData->cons_address?$noSame++:'';
    //             $request->input("consSubDistrict")[$i]!=$oldData->cons_sub_district?$noSame++:'';
    //             $request->input("consDistrict")[$i]!=$oldData->cons_district?$noSame++:'';
    //             $request->input("consCity")[$i]!=$oldData->cons_city?$noSame++:'';
    //             $request->input("consProv")[$i]!=$oldData->cons_prov?$noSame++:'';
    //             $request->input("consPostalCode")[$i]!=$oldData->cons_postal_code?$noSame++:'';
    //             $request->input("tipeForwarder")[$i]!=$oldData->forwarder_id?$noSame++:'';
    //             $arrayOldData["forwarder_id"][$i]=$oldData->forwarder_id;
    //             ($request->input("namaForwarder")[$i]??"")!=$oldData->forwarder_name?$noSame++:'';
    //             $arrayOldData["forwarder_name"][$i]=$oldData->forwarder_name;
    //             $shippingNumberStatsNew!=$oldData->shipping_number_stats?$noSame++:'';


    //             if($request->input("noResi")[$i]!=$oldData->shipping_number){
    //                 $noSame++;

    //                 //Change tracking number
    //                 if($request->input("custTypeId")!="IND"){
    //                     $checkmstrack = DB::table("order_list")->selectRaw("GROUP_CONCAT(ms_track_id SEPARATOR ', ') AS mstracks")->where("invoice_id",$request->input("mismassInvoiceId"))->get();
    //                     if($checkmstrack[0]->mstracks!=""){
    //                         $data = [
    //                             "ms_track_id" => $request->input("noResi")[$i]
    //                         ];
    //                         $update = DB::table("shiptrip_track_list")
    //                                 ->where("ms_track_id","=",$oldData->shipping_number)
    //                                 ->update($data);
    //                     }
    //                 }
    //             };

    //         }

    //         if($noSame==0){
    //             $encode = array("status" => "Berhasil", "text" => "Tidak Ada Perubahan Data", "url" => "");
    //             return json_encode($encode);
    //         }

    //         $dataHistory = [
    //             "codename" => "ER",
    //             "created_at" => $now,
    //             "created_by" => Auth::user()->username,
    //             "description" => $this->createDescForEditResi($request),
    //         ];
    //         $insertHistory = DB::table("history_list")->insert($dataHistory);
    //         if(!$insertHistory){
    //             $encode = array("status" => "Gagal", "text" => "Gagal Buat History");
    //             return json_encode($encode);
    //         }

    //         for($i=0;$i<=count($request->input('id'))-1;$i++){

    //                 $shipping_number = $request->input("noResi")[$i];
    //                 $shippingNumberStatsNew = ($request->input("shippingNumberStatsValue")[$i] ?? "") == "checked" ? 1 : 0;
    //                 if ($request->input("tipeForwarder")[$i] != "VENDOR") {
    //                     if($request->input("forwarderIdOld")[$i] == "VENDOR"){
    //                         $uniqId = str::random(30);
    //                         DB::table('pool_shipping_id')->insert(
    //                             ['uniq_id' => $uniqId]
    //                         );
    //                         $shipping_id = DB::table('pool_shipping_id')->where("uniq_id", $uniqId)->value("id");
    //                         $prefix_shipping_id = "TR/ALY/" . date("y");
    //                         $shipping_number = $prefix_shipping_id . $shipping_id;
    //                     }
    //                 }
                    
    //                 // $urlResi .= $this->resiNoGaring($this->resiOnlyId($shipping_number, $request->input("tipeForwarder")[$i]))."=";
    //                 $urlResi .= $this->encodeURLCustom($shipping_number).'=';

    //                 $anchor = "id";
    //                 $anchorValue = $request->input("id")[$i];
    //                 if($request->input("custTypeId")=="IND"){
    //                     $anchor = "shipping_number";
    //                     $anchorValue = $request->input("shippingNumberOld") ?: "-";
    //                 }

    //                 $arrayTextTrackingEdit = [];
    //                 $arrayTextTrackingEdit = [
    //                     "forwarder_id" => $request->input("tipeForwarder")[$i],
    //                     "forwarder_id_old" => $arrayOldData["forwarder_id"][$i],
    //                     "forwarder_name" => $request->input("namaForwarder")[$i],
    //                     "forwarder_name_old" => $arrayOldData["forwarder_name"][$i]
    //                 ];

    //                 DB::table("data_list")->where($anchor, "=", $anchorValue)
    //                         ->update([
    //                             "cons_first_name" => $this->noSingleQuo($request->input("consFirstName")[$i]),
    //                             "cons_middle_name" => $this->noSingleQuo($request->input("consMiddleName")[$i]) ?? "",
    //                             "cons_last_name" => $this->noSingleQuo($request->input("consLastName")[$i]) ?? "",
    //                             "cons_phone" => $request->input("consPhone")[$i],
    //                             "cons_address" => $this->noSingleQuo($request->input("consAddress")[$i]),
    //                             "cons_sub_district" => $this->noSingleQuo($request->input("consSubDistrict")[$i]) ?? "",
    //                             "cons_district" => $this->noSingleQuo($request->input("consDistrict")[$i]),
    //                             "cons_city" => $this->noSingleQuo($request->input("consCity")[$i]),
    //                             "cons_prov" => $this->noSingleQuo($request->input("consProv")[$i]),
    //                             "cons_postal_code" => $request->input("consPostalCode")[$i],
    //                             "forwarder_id" => $request->input("tipeForwarder")[$i],
    //                             "forwarder_name" => $request->input("namaForwarder")[$i] ?? "",
    //                             "shipping_number_stats" => $shippingNumberStatsNew,
    //                             "shipping_number" => $shipping_number,
    //                             "shipping_updated_at" => $now,
    //                             "shipping_updated_by" => Auth::user()->username,
    //                             "shipping_status" => $this->trackingModel->getTextTrackingEdit($arrayTextTrackingEdit),
    //                         ]);

    //                 //Buat Track Baru Jika Ada Perbedaan Forwarder ID / Forwarder Name
    //                 if($request->input("tipeForwarder")[$i]!=$arrayOldData["forwarder_id"][$i] && 
    //                 ($request->input("namaForwarder")[$i]??"")!=$arrayOldData["forwarder_name"][$i]){

    //                     if($request->input("namaForwarder")[$i]=="JNE"||$request->input("namaForwarder")[$i]=="SENTRAL CARGO"&&
    //                         $arrayOldData["forwarder_name"][$i]=="JNE"||$arrayOldData["forwarder_name"][$i]=="SENTRAL CARGO"){

    //                     }else{
                            
    //                         $msTrackId = $shipping_number;
    //                         if($request->input("custTypeId")=="IND"){
    //                             if($request->input("anchorMsTrack")[0]!=null){
    //                                 $getMsTrack = DB::table("order_list")
    //                                     ->select("ms_track_id")
    //                                     ->where("invoice_id",$request->input("mismassInvoiceId"))
    //                                     ->get();
    //                                 foreach($getMsTrack as $gm){
    //                                     if($gm->ms_track_id!=""){
    //                                         $dataTracking = [
    //                                             "created_at" => date("Y-m-d H:i:s"),
    //                                             "created_by" => Auth::user()->username,
    //                                             "ms_track_id" => $gm->ms_track_id,
    //                                             "track_status_id" => $trackStatusId,
    //                                             "track_status_manual_id" => "A", 
    //                                             "text" => $this->trackingModel->getTextTracking($request->input("tipeForwarder")[0],$namaForwarder)
    //                                         ];
    //                                         $updateTracking = DB::table("shiptrip_track_list")->insert($dataTracking);
                    
    //                                         DB::table("shiptrip_list")
    //                                         ->where("ms_track_id", "=", $gm->ms_track_id)
    //                                         ->update([
    //                                             "track_status_id" => $trackStatusId,
    //                                         ]);
    //                                     }
    //                                 }
    //                             }
    //                         }
    //                             $trackStatusId = $this->trackingModel->getStatusIdTracking($request->input("tipeForwarder")[$i]);
    //                             $dataTracking = [
    //                                 "created_at" => $now,
    //                                 "created_by" => Auth::user()->username,
    //                                 "ms_track_id" => $shipping_number,
    //                                 "track_status_id" => $trackStatusId,
    //                                 "track_status_manual_id" => "A",
    //                                 "text" => $this->trackingModel->getTextTrackingEdit($arrayTextTrackingEdit)
    //                             ];
    //                             $updateTracking = DB::table("shiptrip_track_list")->insert($dataTracking);
    //                     }

    //                 }
    //                 ////////////////////////////////////////////////////////////////////

    //                 if($saklar){
    //                     if($shippingNumberStatsNew==0){
    //                         // env('QONTAK_STATUS') ? $this->initializeCreateResiCOR($request->input('id')[$i],0) : '';
    //                     }
    //                 }

    //                 if($shippingNumberStatsNew==0){
    //                     // env('QONTAK_STATUS') ? $this->initializeCreateResiCOR($request->input('id')[$i],1) : '';
    //                 }
                    
    //                 $saklar = false;

    //         }

    //         $encode = array("status" => "Berhasil", "text" => "Berhasil Edit Resi. Notif Perubahan Telah Dikirim Ke Customer", "url" => url($urlResi));

    //     return json_encode($encode);
    // }

    public function tableTracking(string $custTypeId, Request $request)
    {
        $this->roleAccess();
        $data = [];
        $no = $request->input('start');
        $search = $request->input('search')['value'];
        $filterTanggal = $this->dateFilterFormat($request->input('filterTanggal'));
        $filterWarehouse = $request->input('filterWarehouse');
        $filterService = $request->input('filterService');
        $filterOrderId = $request->input('mismassOrderId');
        $filterPay = $request->input('filterPay');
        $filterPayStatus = $request->input('filterPayStatus');
        $filter = [
            "filterTanggal" => $filterTanggal,
            "filterWarehouse" => $filterWarehouse,
            "filterService" => $filterService,
            "filterOrderId" => $filterOrderId,
            "filterPay" => $filterPay,
            "filterPayStatus" => $filterPayStatus,
            "custTypeId" => $custTypeId
        ];
        $lists = $this->trackingModel->getDT($request, $search, $filter);
        
        if($filterOrderId==""){

                foreach ($lists as $list) {
                    $getData = DB::table('data_list')->select('id','inv_add','sub_total','weight','item','length','height','width','cons_first_name','cons_middle_name','cons_last_name','cons_phone','cons_address','cons_sub_district','cons_district','cons_city','cons_prov','cons_postal_code','forwarder_id','forwarder_name','shipping_number','shipping_number_stats')->where('mismass_invoice_id',$list->mismass_invoice_id)->get();
                    $countRow = count($getData);

                    $checkmstrack = DB::table("order_list")->selectRaw("GROUP_CONCAT(ms_track_id SEPARATOR ', ') AS mstracks, ms_track_id")->where("invoice_id",$list->mismass_invoice_id)->orderBy("ms_track_id","ASC")->get();
                    $mstracks = "";
                    if($checkmstrack[0]->ms_track_id!=""){
                        $mstracks = $checkmstrack[0]->mstracks;
                    }

                    $detailControl = $custTypeId=="COR" ? "<div class='detail-control hidden-child' id='" . $list->mismass_order_id . "' data-createdAt='".$this->dateFormatIndo($list->shipping_created_at,1)."'></div>":"<input type='checkbox' style='margin-left:4px' name='checkResi' data-resi='".$this->resiOnlyId($list->shipping_number, $list->forwarder_id)."'>";
                    $dokuInvoiceId = $list->doku_invoice_id!="" ? $list->doku_invoice_id : "-";
                    $forwarder = $list->forwarder_id == "MISMASS" ? "KURIR MISMASS" : ($list->forwarder_id=="PICK-UP" ? "<div style='color:red'>PICK-UP SENDIRI</div>" : ($list->forwarder_id=="BULKY" ? "-" : $list->forwarder_name));
                                        
                    $msTrackId = "<div class='fw-bold'>Bulky</div>";
                    if($list->inv_add){
                        $msTrackId = "<div class='fw-bold'>Additional</div>";
                    }
                    if($list->ms_track_id!=""){
                        $msTrackId="<a class='fw-bold loadTracking' href='".url('/shiptrip/tracking')."?id=".$list->ms_track_id."' target='_blank'>".$list->ms_track_id."</a>";
                    }

                    $shippingNumber = $list->shipping_number_stats==1?"<div style='color:red'>".$list->shipping_number."</div>":$list->shipping_number;
                    $shippingNumberElm = $list->forwarder_id == "VENDOR" ? "<div class='fw-bold'>" . $shippingNumber . "</div>" : "";
                    $fullname = $custTypeId=="COR" ? $list->sender_first_name . " " . $list->sender_middle_name . " " . $list->sender_last_name : $list->cons_first_name . " " . $list->cons_middle_name . " " . $list->cons_last_name;
                    $phone = $custTypeId=="COR" ? $list->sender_phone : $list->cons_phone;
                    $address = $custTypeId=="COR" ? $list->sender_city . ", " . $list->sender_prov . ", " . $list->sender_postal_code : $list->cons_city . ", " . $list->cons_prov . ", " . $list->cons_postal_code;
                    $detilPrice = $list->doku_link!=""?"<div><a href='".url('/payment')."/".$list->doku_link."' target='_blank'>" . $list->doku_link . "</a></div>":"<div style='color:red'>".$list->bank_name." - ".$list->bank_account_name."</div><div style='color:blue'>".$list->bank_account_id."</div>";
                    $paymentSuccessAt = $this->getSuccessTime($list->mismass_invoice_id);
                    $getRank = DB::table('users')->where("username",$list->shipping_updated_by)->value("rank");
                    $jabatan = $getRank != null ? "<div class='bg-mismass' style='padding:1px 5px'>".$getRank."</div>" : "";
                    
                    $totalServiceOrCust = "<div>".$list->totalInvoice." ".($custTypeId=="IND"?"Service":"Customer")."</div>";

                    $resiBtn = Auth::user()->shiplist_printout_resi?($custTypeId=="IND" ? "<a class='dropdown-item' href='" . url('/printout/resi/' . $this->encodeURLCustom($list->shipping_number).'=') . "' target='_blank'>Print Resi</a>" : ""):"";
                    $resiBtnAll = Auth::user()->shiplist_printout_resi?"<a class='dropdown-item printResiAll pointlink' data-mismassInvoiceId='".$list->mismass_invoice_id."'>Print Resi All</a>":"";
                    $invoiceBtn = Auth::user()->shiplist_printout_invoice?"<a class='dropdown-item' href='" . url('/printout/invoice/' . $this->invOnlyId($list->mismass_invoice_id)) . "' target='_blank'>Print Invoice</a>":"";
                    $editResiArray = [
                        $mstracks,
                        $getData,
                        $list,
                        $countRow,
                        $list->cust_type_id,
                        "INV"
                    ];
                    $resiEditBtn = $this->resiService->editResiButton($editResiArray);
                    $arrayCreateInvoiceAdd = [
                        "list" => $list,
                        "filterPayStatus" => "SUCCESS",
                        "msTrackId" => $checkmstrack[0]->ms_track_id
                    ];
                    $buatInvoiceAddBtn = $this->createInvoiceAddService->createInvoiceAddButton($arrayCreateInvoiceAdd);
                    $wholeBtn = "<div class='btn-group dropleft'><button type='button' class='btn btn-secondary nobtn' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'><i class='fas fa-ellipsis-v'></i></button><div class='dropdown-menu' x-placement='right-start' style='position: absolute; transform: translate3d(111px, 0px, 0px); top: 0px; left: 0px; will-change: transform;'>".$resiBtn.$resiBtnAll.$invoiceBtn.$buatInvoiceAddBtn.$resiEditBtn."</div></div>";

                    $no++;
                    $row = [];
                    $row[] = "<div class='orderNum'>".$no."</div>".$detailControl;
                    $row[] = "<div class='fw-bold'>" . $list->mismass_invoice_id . "</div><div>" . $this->dateFormatIndo($list->mismass_invoice_date,1) . "</div><div class='text-primary'>Created : " . $this->dateFormatIndo($list->created_at,0) . "</div>";
                    $row[] = $msTrackId;
                    $row[] = "<div class='fw-bold'>" . $forwarder . "</div>".$shippingNumberElm."<div>" . $this->dateFormatIndo($list->shipping_updated_at,2) . "</div>";
                    $row[] = "<div class='fw-bold'>" . $list->shipping_updated_by . "</div>".$jabatan."<div>" . $this->dateFormatIndo($list->shipping_updated_at,2) . "</div>";
                    $row[] = "<div class='fw-bold'>" . $fullname . "</div><div>" . $phone . "</div><div>" . $address . "</div>";
                    $row[] = "<div>" . round($list->totalWeight,2) . " KG</div><div>" . $list->totalItem . " Item</div><div>".round($list->totalCbm,2)." CBM</div>".$totalServiceOrCust;

                    if(Auth::user()->shiplist_nom){
                        $detilDisc = "<div>Total : ".$this->rupiah($list->totalDisc+$list->totalPrice-$list->totalAdtFee)."</div><div>Diskon : ".$this->rupiah($list->totalDisc)."</div><div>OpA Fee : ".$this->rupiah($list->totalAdtFee)."</div>";
                        $fcRateValue = $list->fc_symbol!=""?"<div>Nilai Tukar : ".$this->rupiah($list->fc_value)."</div>":"";
                        $totalPriceForeign = $list->fc_symbol!=""?"<div style='color:red'>(".$this->dollarSG($list->totalPriceForeign).")</div>":"";
                        $row[] = $detilDisc."<div class='fw-bold'>Total Biaya : " . $this->rupiah($list->totalPrice) . " ".$totalPriceForeign."</div>".$fcRateValue.$detilPrice.$paymentSuccessAt;
                    }
                    
                    if(Auth::user()->shiplist_printout_invoice||Auth::user()->shiplist_printout_resi||Auth::user()->shiplist_edit_resi){
                        $row[] = $wholeBtn;
                    }
    
                    // <a class='dropdown-item' id='editResiBtn' data-shippingnumber='" . $list->shipping_number . "' href='#'>Edit Data</a>
                    // <a class='dropdown-item' id='hapusBtn' onclick=\"konfirm_hapus('" . $list->shipping_number . "','" . $list->shipping_number . "','Resi','" . url('/shiplist/hapus/resi/') . "','" . url('/shiplist') . "','" . csrf_token() . "')\" href='#'><div style='color:red'>Hapus Data</div></a>
    
                    $data[] = $row;
                }

        }else{

            foreach ($lists as $list) {
                $getData = DB::table('data_list')->select('id','sub_total','weight','item','length','height','width','cons_first_name','cons_middle_name','cons_last_name','cons_phone','cons_address','cons_sub_district','cons_district','cons_city','cons_prov','cons_postal_code','forwarder_id','forwarder_name','shipping_number','shipping_number_stats')->where('shipping_number',$list->shipping_number)->get();
                $countRow = count($getData);
                $shippingNumber = $list->shipping_number_stats==1?"<div style='color:red'>".$list->shipping_number."</div>":$list->shipping_number;
                $forwarder = $list->forwarder_id == "MISMASS" ? "KURIR MISMASS" : ($list->forwarder_id=="PICK-UP" ? "<div style='color:red'>PICK-UP SENDIRI</div>" : $list->forwarder_name);
                
                $checkmstrack = DB::table("order_list")->selectRaw("GROUP_CONCAT(ms_track_id SEPARATOR ', ') AS mstracks, ms_track_id")->where("invoice_id",$list->mismass_invoice_id)->orderBy("ms_track_id","ASC")->get();
                $mstracks = "";
                if($checkmstrack[0]->ms_track_id!=""){
                    $mstracks = $checkmstrack[0]->mstracks;
                }

                $no++;
                $row = [];
                $row[] = $no;
                $row[] = "<div class='fw-bold'>" . $forwarder . "</div><div class='fw-bold'>" . $shippingNumber . "</div>";
                $row[] = $list->cons_first_name." ".$list->cons_middle_name." ".$list->cons_last_name;
                $row[] = "<div>".$list->cons_phone."</div><div>".$list->cons_address.", ".$list->cons_sub_district.", ".$list->cons_district.", ".$list->cons_city.", ".$list->cons_prov.", ".$list->cons_postal_code."</div>";
                $row[] = "<div>" . number_format((float)$list->totalWeight, 2, '.', '') . " KG</div><div>" . $list->totalItem . " Item</div><div>".round($list->totalCbm,2)." CBM</div>";
                
                if(Auth::user()->shiplist_printout_resi){
                    // $row[] = "<a class='btn btn-success' href='" . url('/printout/resi/' . $this->resiNoGaring($this->resiOnlyId($list->shipping_number, $list->forwarder_id))) . "=' target='_blank'><i class='fas fa-print'></i></a>";
                    // $row[] = "<a class='btn btn-success' href='" . url('/printout/resi/' . $this->encodeURLCustom($list->shipping_number)) . "=' target='_blank'><i class='fas fa-print'></i></a>";
                    $resiBtn = "<a class='dropdown-item' href='" . url('/printout/resi/' . $this->encodeURLCustom($list->shipping_number).'=') . "' target='_blank'>Print Resi</a>";
                }

                $editResiArray = [
                    $list->shipping_number,
                    $getData,
                    $list,
                    $countRow,
                    $list->cust_type_id,
                    "TRACK"
                ];
                $resiEditBtn = $this->resiService->editResiButton($editResiArray);
                $wholeBtn = "<div class='btn-group dropleft'><button type='button' class='btn btn-secondary nobtn' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'><i class='fas fa-ellipsis-v'></i></button><div class='dropdown-menu' x-placement='right-start' style='position: absolute; transform: translate3d(111px, 0px, 0px); top: 0px; left: 0px; will-change: transform;'>".$resiBtn.$resiEditBtn."</div></div>";
                $row[] = $wholeBtn;

                $data[] = $row;
            }

        }

        $output = [
            'draw' => $request->input('draw'),
            'recordsTotal' => $this->trackingModel->countAll($request, $search, $filter),
            'recordsFiltered' => $this->trackingModel->countFiltered($request, $search, $filter),
            'data' => $data
        ];

        return json_encode($output);
    }

    public function printOutInvoice(string $id)
    {
        $this->roleAccess();
        abort_if(!Auth::user()->shiplist_printout_invoice, 403);

        $invoiceModel = new Invoice;
        $data["full"] = $invoiceModel::select(
            "data_list.*",
            "service_list.name as servName",
            "cust_type_list.name as custTypeName",
            "warehouse_list.name as wareName",
            "warehouse_list.location as wareLoc",
        )->join("warehouse_list", "warehouse_list.id", "=", "data_list.warehouse_id")
        ->join("cust_type_list", "cust_type_list.id", "=", "data_list.cust_type_id")
        ->join("service_list", "service_list.id", "=", "data_list.service_id")
        ->where("mismass_invoice_id", "like", "%" . $id)
        ->get();
        
        if(count($data['full'])<1){
            abort(404);
        }

        $data["sum"] = $invoiceModel::selectRaw("
        SUM(weight) as totalWeight,
        SUM(item) as totalItem,
        SUM(cbm) as totalCbm,
        SUM(adjust_fee) as adjustFee,
        SUM(sub_total) as totalPrice")
            ->where("mismass_invoice_id", "like", "%" . $id)
            ->get();

        $data["subTotalWeight"] = $invoiceModel::selectRaw("SUM(sub_total) as subTotalW")
            ->where("mismass_invoice_id", "like", "%" . $id)
            ->where("weight", ">", 0)
            ->get();

        $data["subTotalItem"] = $invoiceModel::selectRaw("SUM(sub_total) as subTotalI")
            ->where("mismass_invoice_id", "like", "%" . $id)
            ->where("item", ">", 0)
            ->get();

        $data["subTotalCbm"] = $invoiceModel::selectRaw("SUM(sub_total) as subTotalC")
            ->where("mismass_invoice_id", "like", "%" . $id)
            ->where("cbm", ">", 0)
            ->get();
        
        $templateId = DB::table("data_list")
                        ->where("mismass_invoice_id", "like", "%" . $id)
                        ->value("template_id");
 
        $data["template"] = DB::table("template_list")->where("id",$templateId)->get();

        //Check MS Track
        $checkmstrack = DB::table("order_list")->selectRaw("GROUP_CONCAT(ms_track_id SEPARATOR ', ') AS mstracks")->where("invoice_id","like", "%" . $id)->get();
        $data["mstrack"] = "-";
        if($checkmstrack[0]->mstracks!=""){
            $data["mstrack"] = $checkmstrack[0]->mstracks;
        }

        return view('printout.invoice', $data);
    }

    public function printOutResi(string $id)
    {   
        $this->roleAccess();
        abort_if(!Auth::user()->shiplist_printout_resi, 403);
        $id = $this->decodeURLCustom($id);
        $array = explode("=",$id);

        $trackingModel = new Tracking;
        for($i=0;$i<=count($array)-2;$i++){
            // $array[$i] = str_replace("$","/",$array[$i]);
            $data["get"][$i] = $trackingModel::select(
                "data_list.*",
                "cust_type_list.name as custTypeName",
            )
                ->join("cust_type_list", "cust_type_list.id", "=", "data_list.cust_type_id")
                ->where("shipping_number", "like", "%" . $array[$i])
                ->get();
        }
            
        if(count($data['get'][0])<1){
            abort(404);
        }

        return view('printout.resi', $data);
    }

    public function printOutInvoiceForCustomer(string $id)
    {
        $invoiceModel = new Invoice;
        $data["full"] = $invoiceModel::select(
            "data_list.*",
            "service_list.name as servName",
            "cust_type_list.name as custTypeName",
            "warehouse_list.name as wareName",
            "warehouse_list.location as wareLoc",

        )
            ->join("warehouse_list", "warehouse_list.id", "=", "data_list.warehouse_id")
            ->join("cust_type_list", "cust_type_list.id", "=", "data_list.cust_type_id")
            ->join("service_list", "service_list.id", "=", "data_list.service_id")
            ->where("mismass_invoice_link", "=", $id)
            ->get();
            
        if(count($data['full'])<1){
            abort(404);
        }
        
        $invoiceId = DB::table("data_list")->where("mismass_invoice_link",$id)->value("mismass_invoice_id");

        $data["sum"] = $invoiceModel::selectRaw("
        SUM(weight) as totalWeight,
        SUM(item) as totalItem,
        SUM(cbm) as totalCbm,
        SUM(adjust_fee) as adjustFee,
        SUM(sub_total) as totalPrice")
            ->where("mismass_invoice_id", "like", "%" . $invoiceId)
            ->get();

        $data["subTotalWeight"] = $invoiceModel::selectRaw("SUM(sub_total) as subTotalW")
            ->where("mismass_invoice_id", "like", "%" . $invoiceId)
            ->where("weight", ">", 0)
            ->get();

        $data["subTotalItem"] = $invoiceModel::selectRaw("SUM(sub_total) as subTotalI")
            ->where("mismass_invoice_id", "like", "%" . $invoiceId)
            ->where("item", ">", 0)
            ->get();

        $data["subTotalCbm"] = $invoiceModel::selectRaw("SUM(sub_total) as subTotalC")
            ->where("mismass_invoice_id", "like", "%" . $invoiceId)
            ->where("cbm", ">", 0)
            ->get();
        
        $templateId = DB::table("data_list")
                        ->where("mismass_invoice_id", "like", "%" . $invoiceId)
                        ->value("template_id");
                        
        $data["template"] = DB::table("template_list")->where("id",$templateId)->get();

        //Check MS Track
        $checkmstrack = DB::table("order_list")->selectRaw("GROUP_CONCAT(ms_track_id SEPARATOR ', ') AS mstracks")->where("invoice_id","like", "%" . $invoiceId)->get();
        $data["mstrack"] = "-";
        if($checkmstrack[0]->mstracks!=""){
            $data["mstrack"] = $checkmstrack[0]->mstracks;
        }

        return view('printout.invoice', $data);
    }

    public function resend(Request $request)
    {
        $invoiceId = $request->input("id");
        $randomLink = str::random(20);
        $uniqId = str::random(30);

        $get = $this->invoiceModel::selectRaw("created_at,payment_status,invoice_status,mismass_invoice_date,SUM(adjust_fee) as adjustFee,SUM(sub_total) as totalBiaya,cust_type_id,cons_first_name,cons_middle_name,cons_last_name,cons_phone,cons_email,cons_address,sender_first_name,sender_middle_name,sender_last_name,sender_phone,sender_email,sender_address")
                                ->where('mismass_invoice_id',$invoiceId)
                                ->first();

        if($get->payment_status!="FAILED"){
            $encode = array("status" => 500,"title" => "Gagal", "text" => "Status Pembayaran Sudah Berubah");
            return json_encode($encode);
        }

        if($get->invoice_status=="PAID"){
            $encode = array("status" => 500,"title" => "Gagal", "text" => "Status Pembayaran Sudah Terbayar");
            return json_encode($encode);
        }

        $dokuName = $this->alphaNumSpace($get->sender_first_name." ".$get->sender_middle_name." ".$get->sender_last_name);
        $dokuPhone = $get->sender_phone;
        $dokuEmail = $get->sender_email;
        $dokuAddress = $get->sender_address;
        if($get->cust_type_id=="IND"){
            $dokuName = $this->alphaNumSpace($get->cons_first_name." ".$get->cons_middle_name." ".$get->cons_last_name);
            $dokuPhone = $get->cons_phone;
            $dokuEmail = $get->cons_email;
            $dokuAddress = $get->cons_address;
        }

        $params['order']['price'] = $get->totalBiaya+$get->adjustFee;
        $params['order']['invoice_number'] = $invoiceId;
        $params['payment']['payment_due_date'] = 7*1440;
        $params['customer']['id'] = $get->cust_type_id;
        $params['customer']['name'] = $dokuName;
        $params['customer']['phone'] = $dokuPhone;
        $params['customer']['email'] = $dokuEmail;
        $params['customer']['address'] = $dokuAddress;
        $result = $this->dokuModel->generate($params);

        if($result==null){
            $encode = array("status" => 500, "title" => "Gagal", "text" => "Doku Tidak Memberikan Respon. Silahkan Create Invoice Kembali!");
            return json_encode($encode);
        }

        if($result['status']==503||$result['status']==28){
            $encode = array("status" => 500, "title" => "Gagal",  "text" => $result['message']);
            return json_encode($encode);
        }

        //HISTORY
        $dataHistory = [
            "codename" => "RC",
            "created_at" => date("Y-m-d H:i:s"),
            "created_by" => Auth::user()->username,
            "description" => $this->createDescForResendLink($invoiceId),
        ];
        $insertHistory = DB::table("history_list")->insert($dataHistory);
        if(!$insertHistory){
            $encode = array("status" => 500,"title" => "Gagal", "text" => "Gagal Buat History");
            return json_encode($encode);
        }

        $dataDokuAll = [
            "doku_token_id" => $result['token_id'],
            "doku_expired_date" => date("Y-m-d H:i:s", strtotime($result['expired_date'])),
            "payment_status" => "PENDING",
            "doku_link" => $uniqId,
            "mismass_invoice_link" => $randomLink
        ];
        $insertDataDoku = $this->invoiceModel->where("mismass_invoice_id",$invoiceId)->update($dataDokuAll);

        $paymentLink = url("/payment"."/".$uniqId);

        // $data = array(
        //     "phone" => $get->cust_type_id=="IND" ? $get->cons_phone : $get->sender_phone,
        //     "fullName" => $get->cust_type_id=="IND" ? $get->cons_first_name." ".$get->cons_middle_name." ".$get->cons_last_name : $get->sender_first_name." ".$get->sender_middle_name." ".$get->sender_last_name,
        //     "invoiceLink" => url('/p')."/".$randomLink,
        //     "invoiceDate" => $this->dateFormatIndo($get->mismass_invoice_date,1),
        //     "invoice" => $invoiceId,
        //     "paymentLink" => $paymentLink, //Link Payment Untuk Doku
        //     "statusPay" => "UNPAID",
        //     "custType" => DB::table("cust_type_list")->where("id",$get->cust_type_id)->value("name")
        // );
        // $this->createInvoiceSendWA($data);
        // env('QONTAK_STATUS') ? $this->initializeCreateInvoice($data) : '';

        $encode = array("status" => 200,"title" => "Berhasil", "text" => "Resend Link Berhasil!!");

        return json_encode($encode);
    }

    // public function editDetilCons(Request $request){
    //     $n=0;
    //     $orderId = $request->input("orderId");
    //     $firstName = $request->input("firstName");
    //     $middleName = $request->input("middleName");
    //     $lastName = $request->input("lastName");
    //     $phone = $request->input("phone");
    //     $email = $request->input("email");
    //     $address = $request->input("address");
    //     $subDistrict = $request->input("subDistrict");
    //     $district = $request->input("district");
    //     $city = $request->input("city");
    //     $prov = $request->input("prov");
    //     $postalCode = $request->input("postalCode");

    //     $getOrderList = DB::table("order_list")->where("id",$orderId)->get();
        
    //     foreach($getOrderList as $gol){
    //         $oldCreatedAt = $gol->created_at;
    //         $oldInvoiceId = $gol->invoice_id;
    //         $oldFirstName = $gol->first_name;
    //         $oldMiddleName = $gol->middle_name;
    //         $oldLastName = $gol->last_name;
    //         $oldPhone = $gol->phone;
    //         $oldEmail = $gol->email;
    //         $oldAddress = $gol->address;
    //         $oldSubDistrict = $gol->sub_district;
    //         $oldDistrict = $gol->district;
    //         $oldCity = $gol->city;
    //         $oldProv = $gol->prov;
    //         $oldPostalCode = $gol->postal_code;
            
    //         $oldFirstName != $firstName ? $n++ : '';
    //         $oldMiddleName != $middleName ? $n++ : '';
    //         $oldLastName != $lastName ? $n++ : '';
    //         $oldPhone != $phone ? $n++ : '';
    //         $oldEmail != $email ? $n++ : '';
    //         $oldAddress != $address ? $n++ : '';
    //         $oldSubDistrict != $subDistrict ? $n++ : '';
    //         $oldDistrict != $district ? $n++ : '';
    //         $oldCity != $city ? $n++ : '';
    //         $oldProv != $prov ? $n++ : '';
    //         $oldPostalCode != $postalCode ? $n++ : '';
    //     }
        
    //     if($n==0){
    //         $encode = array("status" => "Berhasil", "text" => "Tidak Ada Perubahan Data", "data" => "");
    //         return json_encode($encode);
    //     }

    //     $updateDataList = DB::table("data_list")->where("mismass_order_id",$orderId)->update([
    //         "updated_at" => date("Y-m-d H:i:s"),
    //         "updated_by" => Auth::user()->username,
    //         "cons_first_name" => $firstName,
    //         "cons_middle_name" => $middleName,
    //         "cons_last_name" => $lastName,
    //         "cons_phone" => $phone,
    //         "cons_email" => $email,
    //         "cons_address" => $address,
    //         "cons_sub_district" => $subDistrict,
    //         "cons_district" => $district,
    //         "cons_city" => $city,
    //         "cons_prov" => $prov,
    //         "cons_postal_code" => $postalCode,
    //     ]);

    //     if(!$updateDataList){
    //         $encode = array("status" => "Gagal", "text" => "Gagal Input Data List");
    //         return json_encode($encode);
    //     }

    //     $updateOrderList = DB::table("order_list")->where("id",$orderId)->update([
    //         "updated_at" => date("Y-m-d H:i:s"),
    //         "updated_by" => Auth::user()->username,
    //         "first_name" => $firstName,
    //         "middle_name" => $middleName,
    //         "last_name" => $lastName,
    //         "phone" => $phone,
    //         "email" => $email,
    //         "address" => $address,
    //         "sub_district" => $subDistrict,
    //         "district" => $district,
    //         "city" => $city,
    //         "prov" => $prov,
    //         "postal_code" => $postalCode,
    //     ]);

    //     if(!$updateOrderList){
    //         $encode = array("status" => "Gagal", "text" => "Gagal Input Order List");
    //         return json_encode($encode);
    //     }

    //     $description = "<b>Edit Data Penerima</b> dengan detail,<br><br>
    //     <div style='display:flex'>
    //     <div style='width:50%;padding-right:10px;'>
    //     <b style='font-size:20px'>DATA LAMA</b><br>
    //     Tanggal order : <b>".$this->dateFormatIndo($oldCreatedAt,2)."</b><br>
    //     ID Sistem : <b>".$orderId."</b><br>
    //     No. invoice Mismass: <b>".$oldInvoiceId."</b><br>
    //     <br>
    //     Nama Depan : <b>".$oldFirstName."</b><br>
    //     Nama Tengah : <b>".$oldMiddleName."</b><br>
    //     Nama Terakhir : <b>".$oldLastName."</b><br>
    //     Email : <b>".$oldEmail."</b><br>
    //     Telpon : <b>".$oldPhone."</b><br>
    //     Alamat : <b>".$oldAddress."</b><br>
    //     Kelurahan : <b>".$oldSubDistrict."</b><br>
    //     Kecamatan : <b>".$oldDistrict."</b><br>
    //     Kab/Kota : <b>".$oldCity."</b><br>
    //     Provinsi : <b>".$oldProv."</b><br>
    //     Kode Pos : <b>".$oldPostalCode."</b><br>
    //     <br>        
    //     </div>
    //     <div style='width:50%;padding-right:10px;'>
    //     <b style='font-size:20px'>DATA BARU</b><br>
    //     Tanggal order : <b>".$this->dateFormatIndo($oldCreatedAt,2)."</b><br>
    //     ID Sistem : <b>".$orderId."</b><br>
    //     No. invoice Mismass: <b>".$oldInvoiceId."</b><br>
    //     <br>
    //     Nama Depan : <b>".$firstName."</b><br>
    //     Nama Tengah : <b>".$middleName."</b><br>
    //     Nama Terakhir : <b>".$lastName."</b><br>
    //     Email : <b>".$email."</b><br>
    //     Telpon : <b>".$phone."</b><br>
    //     Alamat : <b>".$address."</b><br>
    //     Kelurahan : <b>".$subDistrict."</b><br>
    //     Kecamatan : <b>".$district."</b><br>
    //     Kab/Kota : <b>".$city."</b><br>
    //     Provinsi : <b>".$prov."</b><br>
    //     Kode Pos : <b>".$postalCode."</b><br>
    //     <br>
    //     </div>
    //     </div>";

    //     $dataHistory = [
    //         "codename" => "EDP",
    //         "created_at" => date("Y-m-d H:i:s"),
    //         "created_by" => Auth::user()->username,
    //         "description" => $description,
    //     ];
    //     $insertHistory = DB::table("history_list")->insert($dataHistory);
    //     if(!$insertHistory){
    //         $encode = array("status" => "Gagal", "text" => "Gagal Buat History");
    //         return json_encode($encode);
    //     }

    //     $data["firstName"] = $firstName;
    //     $data["middleName"] = $middleName;
    //     $data["lastName"] = $lastName;
    //     $data["phone"] = $phone;
    //     $data["email"] = $email;
    //     $data["address"] = $address;
    //     $data["subDistrict"] = $subDistrict;
    //     $data["district"] = $district;
    //     $data["city"] = $city;
    //     $data["prov"] = $prov;
    //     $data["postalCode"] = $postalCode;

    //     $encode = array("status" => "Berhasil", "text" => "Berhasil Edit Data Penerima", "data"=>$data);
    //     return json_encode($encode);
    // }
    
    public function warehouseList(Request $request){
        $navCust = $request->input("navCust");
        $navTab = $request->input("navTab");


        if($navTab!="nav-ci-tab"){
            $get = DB::table("warehouse_list")
                    ->select("*")
                    ->get();

            $option = "<option value=''>ALL WAREHOUSE</option>";
            foreach($get as $g){
                $option .= "<option data-color='blue' value='".$g->id."'>".$g->id." - ".$g->name." - ".$g->location."</option>";
            }

            $encode = array("data" => $option);
            return json_encode($encode);

        }

        $get = DB::table("order_list")
                ->selectRaw("
                    COALESCE(NULLIF(warehouse_id, ''), 'BULKY') AS warehouse_group,
                    COUNT(*) AS total
                ")
                ->where("invoice_id","=","")
                ->where("cust_type_id","=",$navCust)
                ->groupBy("warehouse_group")
                ->orderBy("total","DESC")
                ->get();

        // dd($get);
    
        $option = "<option value=''>ALL WAREHOUSE</option>";
        foreach($get as $g){
            if($g->warehouse_group=="BULKY"){
                $option .= "<option data-color='blue' value='".$g->warehouse_group."'>".$g->warehouse_group." - (".$g->total.")</option>";
            }else{
                $get2 = DB::table("warehouse_list")
                        ->select("*")
                        ->where("id",$g->warehouse_group)
                        ->first();
                $option .= "<option data-color='blue' value='".$g->warehouse_group."'>".$g->warehouse_group." - ".$get2->name." - ".$get2->location." - (".$g->total.")</option>";
            }
        }

        $encode = array("data" => $option);
        return json_encode($encode);
    }
}
