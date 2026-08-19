<?php

namespace App\Http\Controllers;

use App\Imports\UsersImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use DOKU;
use DOKU\Common;
use DOKU\Common\Config;
use DOKU\Common\Utils;
use App\Http\Controllers\DokuController;
use App\Http\Controllers\KomisiController;
use Spatie\Image\Image;
use App\Models\Diskon;
use App\Models\DokuSystem;
use App\Models\Invoice;
use App\Models\Whatsapp;
use App\Exports\DataListExport;
use Illuminate\Support\Number;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class TestController extends Controller
{

    private $dokuController,$diskonModel,$whatsappModel,$dokuModel;

    public function __construct()
    {
        $this->dokuController = new DokuController;
        $this->dokuModel = new DokuSystem;
        $this->diskonModel = new Diskon;
        $this->whatsappModel = new Whatsapp;
    }

    public function sandbox(){
        $check = DB::table("data_list")
                ->select("cust_type_id","ms_track_id")
                ->where("shipping_number","ABCDEFGH")
                ->get();

        dd(count($check));
    }

    public function revInv(){
        // $data = array(
        //     "phone" => 62081908156116,
        //     "fullName" => "Oktavianti - Rido",
        //     "invoiceLink" => "https://app-mismass.com/p/3XShatJoDY9gMv9IJ9ZH",
        //     "invoiceDate" => "12 Desember 2025",
        //     "invoice" => "INV/AJV/25022882(1)",
        //     "paymentLink" => "https://app-mismass.com/payment/MhqySUNVbwyGCAJ6RpEwcVRv8SEKx1",
        //     "statusPay" => "UNPAID",
        //     "custType" => "Individual",
        //     "reference" => 18,
        //     "templateId" => 1
        // );
                
        // $resultSendWa = $this->whatsappModel->revisiInvoiceSendWA($data);
        // dd($resultSendWa);
    }
    
    public function reqDoku(){
        // $params = array();
        // $params['order']['price'] = 800000;
        // $params['order']['invoice_number'] = "INV/AJV/25022882(1)";
        // $params['payment']['payment_due_date'] = 7*1440;
        // $params['customer']['id'] = "001051";
        // $params['customer']['name'] = "Oktavianti - Rido";
        // $params['customer']['phone'] = "62081908156116";
        // $params['customer']['email'] = "belum_ada_email@mismass.com";
        // $params['customer']['address'] = "Dehomes Residence 2 No.10 Jl. Kav IIP, Kalimulya";
        // $result = $this->dokuModel->generate($params);
        
        // dd($result);
    }
    
    public function sendWa(){
        $to = "+6283854778808";
        $message = "*Ini adalah test*\n".
                    "\n".
                    "ini test123\n".
                    "\n".
                    "*ini test dooooong*";

        $data = [
            "ref" => 0,
            "to" => $to,
            "message" => $message
        ];

        $result = $this->whatsappModel->sendWA($data);
        // $abc = json_decode($result);

        dd($result);
        
    }

    public function checkDoku()
    {
        //Initialize
        $totalSuccess = 0;
        $totalPending = 0;
        $totalFailed = 0;
        $totalWarning = 0;
        $pending = "";
        $failed = "";
        $pendingToSuccess = "";
        $failedToSuccess = "";
        $warning = "";

        //Ambil data failed & pending
        $get = DB::table('data_list')
                ->selectRaw("mismass_invoice_id,invoice_status,payment_status")
                ->whereRaw("payment_status='PENDING' AND doku_link!='' OR payment_status='FAILED' AND doku_link!=''")
                ->orderBy("payment_status","asc")
                ->groupBy("mismass_invoice_id")
                ->get();

        $totalAll = count($get);

        //cek status
        foreach($get as $g){
            $statusAwal = $g->payment_status;
            $check = $this->dokuModel->checkStatusInvoiceDoku($g->mismass_invoice_id);

            if($check==null){
                if($statusAwal=="PENDING"){
                    $totalPending++;
                    $pending .= $g->mismass_invoice_id.", ";
                }else if($statusAwal=="FAILED"){
                    $totalFailed++;
                    $failed .= $g->mismass_invoice_id.", ";
                }
                continue;
            }

            if($statusAwal=="PENDING"){
                $pendingToSuccess .= $g->mismass_invoice_id.", ";
            }else if($statusAwal=="FAILED"){
                $failedToSuccess .= $g->mismass_invoice_id.", ";
            }
            $totalSuccess++;
        }
        
        $object = "<label class='success'>Total Success</label> : <b>".$totalSuccess."</b><br>
                            <label class='primary'>Total Pending</label> : <b>".$totalPending."</b><br>
                            <label class='danger'>Total Failed</label> : <b>".$totalFailed."</b><br>
                            <label class='warning'>Total Warning</label> : <b>".$totalWarning."</b><br>
                            Total All : <b>".$totalAll."</b><br><br>
                            <label class='primary'>Pending</label> : <b>".$pending."</b><br>
                            <label class='danger'>Failed</label> : <b>".$failed."</b><br>
                            <label class='primary'>Pending</label> => <label class='success'>Success</label> : <b>".$pendingToSuccess."</b><br>
                            <label class='danger'>Failed</label> => <label class='success'>Success</label> : <b>".$failedToSuccess."</b><br>
                            <label class='warning'>Warning</label> : ".$warning;
        echo $object;

    }
    
    public function printOutInvoice(string $id)
    {
        // $this->roleAccess();
        // abort_if(!Auth::user()->shiplist_printout_invoice, 403);

        // $invoiceModel = new Invoice;
        // $data["full"] = $invoiceModel::select(
        //     "data_list.*",
        //     "service_list.name as servName",
        //     "cust_type_list.name as custTypeName",
        //     "warehouse_list.name as wareName",
        //     "warehouse_list.location as wareLoc",
        // )->join("warehouse_list", "warehouse_list.id", "=", "data_list.warehouse_id")
        // ->join("cust_type_list", "cust_type_list.id", "=", "data_list.cust_type_id")
        // ->join("service_list", "service_list.id", "=", "data_list.service_id")
        // ->where("mismass_invoice_id", "like", "%" . $id)
        // ->get();
        
        // if(count($data['full'])<1){
        //     abort(404);
        // }

        // $data["sum"] = $invoiceModel::selectRaw("
        // SUM(weight) as totalWeight,
        // SUM(item) as totalItem,
        // SUM(cbm) as totalCbm,
        // SUM(sub_total) as totalPrice")
        //     ->where("mismass_invoice_id", "like", "%" . $id)
        //     ->get();

        // $data["subTotalWeight"] = $invoiceModel::selectRaw("SUM(sub_total) as subTotalW")
        //     ->where("mismass_invoice_id", "like", "%" . $id)
        //     ->where("weight", ">", 0)
        //     ->get();

        // $data["subTotalItem"] = $invoiceModel::selectRaw("SUM(sub_total) as subTotalI")
        //     ->where("mismass_invoice_id", "like", "%" . $id)
        //     ->where("item", ">", 0)
        //     ->get();

        // $data["subTotalCbm"] = $invoiceModel::selectRaw("SUM(sub_total) as subTotalC")
        //     ->where("mismass_invoice_id", "like", "%" . $id)
        //     ->where("cbm", ">", 0)
        //     ->get();
        
        // $templateId = DB::table("data_list")
        //                 ->where("mismass_invoice_id", "like", "%" . $id)
        //                 ->value("template_id");

        // $data["template"] = DB::table("template_list")->where("id",$templateId)->get();

        // //Check MS Track
        // $checkmstrack = DB::table("order_list")->selectRaw("GROUP_CONCAT(ms_track_id SEPARATOR ', ') AS mstracks")->where("invoice_id","like", "%" . $id)->get();
        // $data["mstrack"] = "-";
        // if($checkmstrack[0]->mstracks!=""){
        //     $data["mstrack"] = $checkmstrack[0]->mstracks;
        // }

        // return view('printout.invoice-test', $data);
    }

    public function testTimeOut(Request $request)
    {   
        // $reqId = $request->input('id');
        // $dateTime = gmdate("Y-m-d H:i:s");
        // $dateTime = date(DATE_ISO8601, strtotime($dateTime));
        // $dateTimeFinal = substr($dateTime, 0, 19) . "Z";

        // $getUrl = Config::getBaseUrl(env('DOKU_PRODUCTION'));

        // $targetPath = '/orders/v1/status/'.$reqId;
        // $url = $getUrl.$targetPath;

        // $header['Client-Id'] = env('DOKU_CLIENT_ID');
        // $header['Request-Id'] = $reqId;
        // $header['Request-Timestamp'] = $dateTimeFinal;
        // $header['Request-Target'] = $targetPath;

        // $rawSignature = "Client-Id:" . $header['Client-Id'] . "\n"
        //     . "Request-Id:" . $header['Request-Id'] . "\n"
        //     . "Request-Timestamp:" . $header['Request-Timestamp'] . "\n"
        //     . "Request-Target:" . $header['Request-Target'];

        // $sig = base64_encode(hash_hmac('sha256', $rawSignature, env('DOKU_SECRET_KEY'), true));
        // $signature = 'HMACSHA256=' . $sig;

        // $ch = curl_init($url);  
        // curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        //     'Content-Type: application/json',
        //     'Signature:' . $signature,
        //     'Request-Id:' . $reqId,
        //     'Client-Id:' . env('DOKU_CLIENT_ID'),
        //     'Request-Timestamp:' . $dateTimeFinal,
        //     'Request-Target:' . $targetPath,

        // ));
        // $start = microtime(true);
        // $responseJson = curl_exec($ch);
        // $end = microtime(true);

        // $info = curl_getinfo($ch);
        // $error = curl_error($ch);

        // curl_close($ch);

        // $executionTime = $end - $start;

        // dd([
        //     'response' => json_decode($responseJson, true),
        //     'response_time_seconds' => $executionTime,
        //     'total_time' => $info['total_time'],
        //     'connect_time' => $info['connect_time'],
        //     'namelookup_time' => $info['namelookup_time'],
        //     'pretransfer_time' => $info['pretransfer_time'],
        //     'error' => $error,
        // ]);
    }

    public function test(){
        // $reqUrl = "https://api.wooblazz.com/o1/check_number";

        // $headersList = [
        //     "Accept: */*",
        //     "Content-Type: application/json",
        //     "Authorization: 2c07ca66112382c678fb7bf641d1c049"
        // ];

        // $data = [
        //     "number" => "+62081230888722"
        // ];

        // $ch = curl_init($reqUrl);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, $headersList);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        // $response = curl_exec($ch);

        // if ($response === false) {
        //     echo "Error: " . curl_error($ch);
        // } else {
        //     echo $response;
        // }

        // curl_close($ch);
        // dd($response);

    //     $reqUrl = "https://api.wooblazz.com/o1/send";

    // $headersList = [
    //     "Accept: */*",
    //     "Content-Type: application/json",
    //     "Authorization: 62561f0e1cea25805c459575e11a5b26"
    // ];

    // $data = [
    //     "to" => "6285232350505",
    //     "type" => "chat",
    //     "message" => "Sending a video #2"
    // ];

    // $ch = curl_init($reqUrl);
    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // curl_setopt($ch, CURLOPT_HTTPHEADER, $headersList);
    // curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    // $response = curl_exec($ch);

    // if ($response === false) {
    //     echo "Error: " . curl_error($ch);
    // } else {
    //     echo $response;
    // }

    // curl_close($ch);
    }
    
    public function updateTime(){
        $get = DB::table("data_list")
                ->where("shipping_updated_by","dev8th")
                ->where("updated_at","2025-09-19 08:56:28")
                ->where("invoice_status","PAID")
                ->where("shipping_number","")
                ->where("created_at","LIKE","2025-07")
                ->get();
                
        dd("Total : ".count($get));
    }

    public function testing(){

        $params = array();
        $params['order']['price'] = 10000;
        $params['order']['invoice_number'] = "INV/TEST0101010101";
        $params['payment']['payment_due_date'] = 7*1440;
        $params['customer']['id'] = "TEST01";
        $params['customer']['name'] = "Test";
        $params['customer']['phone'] = "099999";
        $params['customer']['email'] = "test0101@gmail.com";
        $params['customer']['address'] = "alamat test";
        $result = $this->dokuModel->generate($params);

        dd($result);

        // $id = "INV/AJV/25019593";
        // dd($this->dokuModel->checkStatusInvoiceDoku($id));
    }

    public function check(){
        $check = DB::table("shiptrip_foreign_track_list")
                ->selectRaw("GROUP_CONCAT(shiptrip_foreign_track_list.id SEPARATOR ', ') AS foreign_tracks")
                ->where("ms_track_id","MMS76319254AUID")
                ->get();

        dd($check[0]->foreign_tracks);
    }

    // public function testdoku(Request $request){
    //     $id = "INV/AJV/".$request->input("id");
    //     dd($this->dokuController->checkStatusDoku($id));
    // }
    public function insertTracking(){
        $data = [
            ["25019531","10/09/2025 11:41:58"],
            ["25019511","10/09/2025 11:12:31"],
            ["25019518","10/09/2025 11:34:18"],
            ["25019516","10/09/2025 13:05:43"],
            ["25019522","10/09/2025 12:41:28"],
            ["25019538","10/09/2025 12:07:59"],
            ["25019555","10/09/2025 12:38:06"],
            ["25019544","10/09/2025 11:38:48"],
            ["25019526","10/09/2025 14:10:17"],
            ["25019513","10/09/2025 12:44:49"]
        ];
        
        $num = 0;
        
        foreach($data as $d){
            $id = "INV/AJV/".$d[0];
            $preDate = str_replace("/","-",$d[1]);
            $date = date("Y-m-d H:i:s",strtotime($preDate));
            $getMSTrack = DB::table("order_list")->where("invoice_id",$id)->get();
            foreach($getMSTrack as $gm){
                $dataTracking = [
                    "created_at" => $date,
                    "created_by" => "DOKU",
                    "ms_track_id" => $gm->ms_track_id,
                    "track_status_id" => 11,
                    "track_status_manual_id" => "A",
                    "text" => DB::table("shiptrip_track_status")->where("id","11")->value("value")
                ];
                $updateTracking = DB::table("shiptrip_track_list")->insert($dataTracking);
            }
            $num++;
        }
        
        echo "Sukses : ".$num;
    }

    public function carbon(){
        $nowInGmtPlus7 = Carbon::now()->setTimezone('Asia/Jakarta'); // 'Asia/Jakarta' is a common timezone for GMT+7
        echo $nowInGmtPlus7;
    }

    public function nominal(){
        echo Number::currency(1000, 'SGD');
    }

    public function blastEmail(){
        $phone = "+6285232350505";
        $fullName = "DEVELOP";
        $invoiceLink = "app-mismass.com/p/testtest123";
        $invoiceDate = "28 Agustus 2025";
        $invoice = "INV12345678";
        $paymentLink = "app-mismass.com/payment/testtest123";
        $custType = "Individual";

        // $data = array(
        //     "phone" => $phone,
        //     "fullName" => $fullName,
        //     "invoiceLink" => $invoiceLink,
        //     "invoiceDate" => $invoiceDate,
        //     "invoice" => $invoice,
        //     "paymentLink" => $paymentLink,
        //     "statusPay" => "UNPAID",
        //     "custType" => $custType
        // );
        
        $data = array(
            "phone" => $phone,
            "fullName" => $fullName,
            "shipmentNumber" => "test1234",
            "reference" => "Test"
        );

        //Send Email
        $ccEmail = explode(",",env('MAIL_CC'));
        $bccEmail = env('MAIL_BCC');
        $email = "developer.8th@gmail.com";
        $data['subject'] = "Create Shipment | TEST | ".$invoice;
        $data['modes'] = "CSHI"; //Webform = "WEB", create invoice = "CINV", create shipment = "CSHI"
        $sendingMail = Mail::to($email)
                        ->cc($ccEmail)
                        ->bcc($bccEmail)
                        ->send(new SendMail($data));

        return $sendingMail;
    }
    
    public function orderqueue(){
        $id = [
            "002541",
            "002236",
            "002489",
            "002773",
            "002865",
            "003113",
            "000006",
            "002374",
            "002134",
            "001897",
            "002738",
            "001478",
            "001115",
            "000970",
            "002499",
            "001018",
            "001584",
            "000958",
            "000971",
            "000003",
            "001020",
            "002533",
            "003262",
            "000214",
            "001735",
            "000993",
            "000263",
            "002093",
            "000028",
            "001609"
        ];
        
        $num=1;
        $elm="";
        foreach($id as $i){
            
            $getDataCust = DB::table("cust_list")->where("id",$i)->first();
            $phoneFix = "+".DB::table("country_phone_codes")->where("id",$getDataCust->country_id)->value("code").$getDataCust->phone;
            
            $arrayOrderList = array(
                    "cust_id" => $i,
                    "cust_type_id" => $getDataCust->cust_type_id,
                    "order_status_id" => "READY",
                    "ms_track_id" => "",
                    "warehouse_id" => "",
                    "service_id" => 0,
                    "invoice_id" => "",
                    "created_at" => date("Y-m-d H:i:s"),
                    "created_by" => "DEVELOP",
                    "updated_at" => date("Y-m-d H:i:s"),
                    "updated_by" => "DEVELOP",
                    "first_name" => $getDataCust->first_name,
                    "middle_name" => $getDataCust->middle_name,
                    "last_name" => $getDataCust->last_name,
                    "phone" => $getDataCust->phone,
                    "email" => $getDataCust->email,
                    "address" => $getDataCust->address,
                    "sub_district" => $getDataCust->sub_district,
                    "district" => $getDataCust->district,
                    "city" => $getDataCust->city,
                    "prov" => $getDataCust->prov,
                    "postal_code" => $getDataCust->postal_code,
                    "second_name" => $getDataCust->first_name." ".$getDataCust->middle_name." ".$getDataCust->last_name,
                    "second_phone" => $getDataCust->phone,
                );
            $insert = DB::table("order_list")->insert($arrayOrderList);
            
            $status = "Gagal";
            if($insert){
                $status = "Berhasil";
            }
            
            $elm .= $num.".".$i." ".$status."<br>";
            $num++;
        }
        
        // dd($getDataCust);
        // dd($arrayOrderList);
        echo $elm;
    }

    // public function test(Request $request){
    //     // $filters = [
    //     //     "title" => "Ini Adalah Title",
    //     //     "custTypeId" => "IND",
    //     //     "warehouseId" => "SGIDO",
    //     //     "tanggalAwal" => "2023-12-01 00:00:00",
    //     //     "tanggalAkhir" => "2023-12-31 23:59:59",
    //     // ];
    //     // return Excel::download(new DataListExport($filters), 'testExport.xlsx');
    //     return view('test');
        
    // }

    // public function testing(Request $request){

    //     $custTypeId = "IND";
    //     $warehouseId = "SGIDO";
    //     $tanggalAwal = "2023-12-01 00:00:00";
    //     $tanggalAkhir = "2023-12-31 23:59:59";
    //     $orderBy = ["data_list.mismass_invoice_date","asc"];
    //     $groupBy = "data_list.mismass_invoice_id";
    //     $fltrs = [
    //         $custTypeId,
    //         $warehouseId,
    //         $tanggalAwal,
    //         $tanggalAkhir,
    //     ];
        
    //     $get = DB::table("data_list")->selectRaw(
    //         "data_list.*,
    //         warehouse_list.id as wareid,
    //         warehouse_list.name as warename,
    //         warehouse_list.location as wareloc,
    //         cust_list.reference"
    //     )
    //     ->join("cust_list","cust_list.id","=","data_list.cust_id")
    //     ->join("warehouse_list","warehouse_list.id","=","data_list.warehouse_id")
    //     ->join("order_list","order_list.id","=","data_list.mismass_order_id")
    //     ->where("data_list.cust_type_id",$custTypeId)
    //     ->where("data_list.warehouse_id",$warehouseId)
    //     ->whereRaw("mismass_invoice_date BETWEEN '$tanggalAwal' AND '$tanggalAkhir'")
    //     ->orderBy($orderBy[0],$orderBy[1])
    //     ->groupBy($groupBy)
    //     ->get();

    //     $title = "Ini Adalah Title";

    //     $encode = array("title" => $title, "data" => $get);
    //     return json_encode($get);
    // }

    // public function checkDoku(){
    //     $numSuccess = 0;
    //     $numPending = 0;
    //     $numFailed = 0;
    //     $arrSuccess = 0;
    //     $arrPending = 0;
    //     $arrFailed = 0;
    //     $data = array();
    //     $success = array();
    //     $pending = array();
    //     $failed = array();
    //     $get = DB::table('data_list')->selectRaw("mismass_invoice_id,invoice_status,payment_status")->whereRaw("payment_status='PENDING' OR payment_status='FAILED'")->orderBy("payment_status","asc")->groupBy("mismass_invoice_id")->get();
    //     $total = count($get);
    //     foreach($get as $g){
    //         $statusAwal = $g->payment_status;
    //         $check = $this->dokuController->checkStatusDoku($g->mismass_invoice_id);

    //         if($check!=null){
    //             $success[$arrSuccess] = [
    //                 "status" => $g->mismass_invoice_id." ".$statusAwal." => SUKSES",
    //                 "json" => $check[2]
    //             ];
    //             $sendTo = $this->dokuController->sendToSuccess([$check[0],$check[1]]);
    //             $arrSuccess++;
    //             $numSuccess++;
    //         }else{
    //             if($statusAwal=="PENDING"){
    //                 $pending[$arrPending] = $g->mismass_invoice_id." ".$statusAwal;
    //                 $arrPending++;
    //                 $numPending++;
    //             }else if($statusAwal=="FAILED"){
    //                 $failed[$arrFailed] = $g->mismass_invoice_id." ".$statusAwal;
    //                 $arrFailed++;
    //                 $numFailed++;
    //             }
    //         }

    //     }

    //     $data = [
    //         "totalAll" => $total,
    //         "Success" => ["total" => $numSuccess, "data" => $success], 
    //         "Pending" => ["total" => $numPending, "data" => $pending],
    //         "Failed" => ["total" => $numFailed, "data" => $failed],
    //     ];

    //     dd($data);
    // }

    // public function checkOutManual(){
    //     $params = array();
    //     $params['order']['price'] = 260000;
    //     $params['order']['invoice_number'] = "INV/AJV/25016638";
    //     $params['payment']['payment_due_date'] = 7*1440;
    //     $params['customer']['id'] = "003042";
    //     $params['customer']['name'] = "MICKEY SALIM";
    //     $params['customer']['phone'] = "+6585050548";
    //     $params['customer']['email'] = "mickeyjane28@gmail.com";
    //     $params['customer']['address'] = "JL. MAWAR 1 BLOK E NO. 23, CIPINANG INDAH 1";
    //     $result = $this->dokuController->generate($params);
    //     dd($result);
    // }

    // public function test(Request $request){
    //     $reqId = "INV/AJV/".$request->input("id");
    //     $dateTime = gmdate("Y-m-d H:i:s");
    //     $dateTime = date(DATE_ISO8601, strtotime($dateTime));
    //     $dateTimeFinal = substr($dateTime, 0, 19) . "Z";

    //     $getUrl = Config::getBaseUrl(env('DOKU_PRODUCTION'));

    //     $targetPath = '/orders/v1/status/'.$reqId;
    //     $url = $getUrl.$targetPath;

    //     $header['Client-Id'] = env('DOKU_CLIENT_ID');
    //     $header['Request-Id'] = $reqId;
    //     $header['Request-Timestamp'] = $dateTimeFinal;
    //     $header['Request-Target'] = $targetPath;

    //     $rawSignature = "Client-Id:" . $header['Client-Id'] . "\n"
    //         . "Request-Id:" . $header['Request-Id'] . "\n"
    //         . "Request-Timestamp:" . $header['Request-Timestamp'] . "\n"
    //         . "Request-Target:" . $header['Request-Target'];

    //     $sig = base64_encode(hash_hmac('sha256', $rawSignature, env('DOKU_SECRET_KEY'), true));
    //     $signature = 'HMACSHA256=' . $sig;

    //     $ch = curl_init($url);  
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    //         'Content-Type: application/json',
    //         'Signature:' . $signature,
    //         'Request-Id:' . $reqId,
    //         'Client-Id:' . env('DOKU_CLIENT_ID'),
    //         'Request-Timestamp:' . $dateTimeFinal,
    //         'Request-Target:' . $targetPath,

    //     ));
    //     $responseJson = curl_exec($ch);
    //     $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    //     curl_close($ch);
    //     if (is_string($responseJson) && $httpcode == 200) {
    //         $json=json_decode($responseJson, true);

    //         return $json;

    //         // if($json['transaction']['status']=="SUCCESS"){
    //         //     DokuController::sendToSuccess($reqId);
    //         //     return true;
    //         // }

    //         // return false;
    //     } else {
    //         echo $responseJson;
    //         return null;
    //     }
    // }

}
