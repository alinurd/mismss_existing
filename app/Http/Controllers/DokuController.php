<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Invoice;
use App\Models\DokuSystem;
use DOKU;
use DOKU\Common;
use DOKU\Common\Config;
use DOKU\Common\Utils;
use Carbon\Carbon;

class DokuController extends Controller
{
    private $dokuClient,$dokuModel,$invoiceModel,$poolInvoiceModel;

    public function __construct()
    {
        $this->dokuClient = new Doku\Client;
        $this->dokuModel = new DokuSystem;
        $this->invoiceModel = new Invoice;
        $this->poolInvoiceModel = DB::table('pool_invoice_id');
    }
    
    public function checkLink(Request $request)
    {
        $link = $request->input("link");
        $check = DB::table("data_list")->where("doku_link",$link)->first();
        if($check==null){
            $encode = array("status" => 404, "text" => "Link Tidak Aktif");
            return json_encode($encode);
        }

        $encode = array("status" => 200, "text" => "Link Masih Aktif");
        return json_encode($encode);
    }

    public function index(string $link)
    {
        $dataSelect = "mismass_invoice_id,payment_status,doku_expired_date,doku_token_id,SUM(sub_total+adjust_fee) AS total_biaya,sender_first_name,sender_middle_name,sender_last_name,sender_phone,sender_email,sender_address,cons_first_name,cons_middle_name,cons_last_name,cons_phone,cons_email,cons_address,cust_type_id";
        $getCustData = $this->invoiceModel::selectRaw($dataSelect)
                                            ->where("doku_link",$link)
                                            ->first();

        if($getCustData==null){
            abort(404);
        }
        
        $dateNow = strtotime(date("Y-m-d H:i:s"));
        $expireDate = strtotime($getCustData->doku_expired_date);
        
        if($expireDate-$dateNow<=0){
            if($getCustData->payment_status!="SUCCESS"){
                abort(419);
            }
        }

        // checkStatusDoku($mismass_invoice_id);
        
        $custInvoice = $getCustData->mismass_invoice_id;
        $custName = $getCustData->cust_type_id=="IND"?$getCustData->cons_first_name." ".$getCustData->cons_middle_name." ".$getCustData->cons_last_name:$getCustData->sender_first_name." ".$getCustData->sender_middle_name." ".$getCustData->sender_last_name;
        $custPhone = $getCustData->cust_type_id=="IND"?$getCustData->cons_phone:$getCustData->sender_phone;
        $custEmail = $getCustData->cust_type_id=="IND"?$getCustData->cons_email:$getCustData->sender_email;
        $custAddress = $getCustData->cust_type_id=="IND"?$getCustData->cons_address:$getCustData->sender_address;

        $getUrl = Config::getCheckoutUrl(env('DOKU_PRODUCTION'));
        $getUrl2 = "/checkout-link-v2";
        $tokenId = "/".$getCustData->doku_token_id;

        $data['custInvoice'] = $custInvoice;
        $data['custName'] = $custName;
        $data['custPhone'] = $custPhone;
        $data['custEmail'] = $custEmail;
        $data['custAddress'] = $custAddress;
        $data['url'] = $getUrl.$getUrl2.$tokenId;
        $data['totalCost'] = $this->rupiah($getCustData->total_biaya);
        $data['paymentStatus'] = $getCustData->payment_status;
        $data['link'] = $link;
        
        return view('dokupay',$data);
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

            //check[0] = invoice id
            //check[1] = payment date
            $sendTo = $this->dokuModel->sendToSuccessDoku([$check[0],$check[1]]);

            if(!$sendTo['status']){
                $totalWarning++;
                $warning .= "<br>".$g->mismass_invoice_id." => ".$sendTo['title'];
                continue;
            }

            if($statusAwal=="PENDING"){
                $pendingToSuccess .= $g->mismass_invoice_id.", ";
            }else if($statusAwal=="FAILED"){
                $failedToSuccess .= $g->mismass_invoice_id.", ";
            }
            $totalSuccess++;
        }

        //history
        $dataHistory=[
            "codename" => "PS",
            "created_at" => date("Y-m-d H:i:s"),
            "created_by" => "DOKU & MISMASS",
            "description" => "<b>Checking Invoice Doku</b> dengan detail,<br><br>
                            <label class='success'>Total Success</label> : <b>".$totalSuccess."</b><br>
                            <label class='primary'>Total Pending</label> : <b>".$totalPending."</b><br>
                            <label class='danger'>Total Failed</label> : <b>".$totalFailed."</b><br>
                            <label class='warning'>Total Warning</label> : <b>".$totalWarning."</b><br>
                            Total All : <b>".$totalAll."</b><br><br>
                            <label class='primary'>Pending</label> : <b>".$pending."</b><br>
                            <label class='danger'>Failed</label> : <b>".$failed."</b><br>
                            <label class='primary'>Pending</label> => <label class='success'>Success</label> : <b>".$pendingToSuccess."</b><br>
                            <label class='danger'>Failed</label> => <label class='success'>Success</label> : <b>".$failedToSuccess."</b><br>
                            <label class='warning'>Warning</label> : ".$warning,
        ];
            
        $insertHistory = DB::table('history_list')->insert($dataHistory);
        
        if(!$insertHistory){
            $encode = array("status" => "Gagal", "text" => "Gagal Buat History");
            return json_encode($encode);
        }
    }

    public function notification()
    {
        $dataDokuModel = DB::table('data_doku');
        $notificationHeader = getallheaders();
        $notificationBody = file_get_contents('php://input');
        $notificationPath = '/dokunotif'; // Adjust according to your notification path
        $secretKey = env('DOKU_SECRET_KEY'); // Adjust according to your secret key

        $digest = base64_encode(hash('sha256', $notificationBody, true));
        $rawSignature = "Client-Id:" . $notificationHeader['Client-Id'] . "\n"
            . "Request-Id:" . $notificationHeader['Request-Id'] . "\n"
            . "Request-Timestamp:" . $notificationHeader['Request-Timestamp'] . "\n"
            . "Request-Target:" . $notificationPath . "\n"
            . "Digest:" . $digest;

        $signature = base64_encode(hash_hmac('sha256', $rawSignature, $secretKey, true));
        $finalSignature = 'HMACSHA256=' . $signature;

        if ($finalSignature == $notificationHeader['Signature']) {
            $value = json_decode($notificationBody,true);
            $invoiceNumber = $value['order']['invoice_number'];
            $dateNow = Carbon::now()->setTimezone('Asia/Jakarta');

            $checkInvoice = $this->invoiceModel->select("invoice_status")->where('mismass_invoice_id',$invoiceNumber)->first();
            if($checkInvoice->invoice_status=="PAID"){
                return response('OK', 200)->header('Content-Type', 'text/plain');
            }

            $this->invoiceModel->where('mismass_invoice_id',$invoiceNumber)->update(['payment_status'=>'SUCCESS','invoice_status'=>'PAID','payment_success_auto_at'=>$dateNow]);
            
            //HISTORY
            $dataHistory = [
                "codename" => "PS",
                "created_at" => $dateNow,
                "created_by" => "DOKU",
                "description" => $this->createDescForPaymentSuccess($invoiceNumber),
            ];
            $insertHistory = DB::table("history_list")->insert($dataHistory);

            //Create Tracking
            $getMsTrack = DB::table("order_list")
                        ->select("ms_track_id")
                        ->where("invoice_id",$invoiceNumber)
                        ->get();
            foreach($getMsTrack as $gm){
                if($gm->ms_track_id!=""){
                    $dataTracking = [
                        "created_at" => $dateNow,
                        "created_by" => "DOKU",
                        "ms_track_id" => $gm->ms_track_id,
                        "track_status_id" => 11,
                        "track_status_manual_id" => "A",
                        "text" => DB::table("shiptrip_track_status")->where("id","11")->value("value")
                    ];
                    $updateTracking = DB::table("shiptrip_track_list")->insert($dataTracking);
                }
            }
            
            //SEND WA PEMBAYARAN BERHASIL
            // $get = DB::table("data_list")->where("mismass_invoice_id",$invoiceNumber)->first();
            // $data = array(
            //     "phone" => $get->cust_type_id=="IND" ? $get->cons_phone : $get->sender_phone,
            //     "fullName" => $get->cust_type_id=="IND" ? $get->cons_first_name." ".$get->cons_middle_name." ".$get->cons_last_name : $get->sender_first_name." ".$get->sender_middle_name." ".$get->sender_last_name,
            //     "invoiceDate" => $this->dateFormatIndo($get->mismass_invoice_date,1),
            //     "invoice" => $get->mismass_invoice_id,
            //     "paymentLink" => url('/payment')."/".$get->doku_link, //Link Payment Untuk Doku
            //     "statusPay" => "PAID",
            //     "custType" => DB::table("cust_type_list")->where("id",$get->cust_type_id)->value("name")
            // );
            // $this->invoicePaidSendWA($data);

            return response('OK', 200)->header('Content-Type', 'text/plain');
        } else {
            return response('Invalid Signature', 300)->header('Content-Type', 'text/plain');
        }
    }

}
