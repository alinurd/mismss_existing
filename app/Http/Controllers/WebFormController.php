<?php

namespace App\Http\Controllers;

use illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\Tracking;
use App\Models\Warehouse;
use App\Models\Service;
use App\Models\Customer;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Mail;

class WebFormController extends Controller
{
    public function index()
    {
        $data['kn'] = DB::table("country_phone_codes")->orderBy("name","asc")->get();
        $data['bn'] = DB::table("knowfrom_list")->orderBy("id","asc")->get();
        return view("webform.index",$data);
    }

    public function input(Request $request)
    {

        $firstName = $request->input('firstName');
        $middleName = $request->input('middleName') ?? "";
        $lastName = $request->input('lastName') ?? "";
        $countryId = $request->input('kodeNegara');
        $phone = $request->input('phone');
        $email = $request->input('email');
        $address = $request->input('address');
        $subDistrict = $request->input('subDistrict') ?? "";
        $district = $request->input('district');
        $city = $request->input('city');
        $prov = $request->input('prov');
        $postalCode = $request->input('postalCode');
        $secondName = $request->input('secondName');
        $secondPhone = $request->input('secondPhone');
        $knowFrom = $request->input('knowFrom');

        $referral = $request->input('referral');
        $refId = 0;
        if($referral!=""){
            $refId = DB::table('users')->where('ref_code',$referral)->value('id');
            if($refId==""){
                $refId=0;
            }
        }
        
        $firstN = $firstName;
        $middleN = " ".$middleName ?? "";
        $lastN = " ".$lastName ?? "";
        $fullName = $firstN.$middleN.$lastN;
        
        $fullAddress = $address.", ".$subDistrict.", ".$district.", ".$city.", ".$prov.", ".$postalCode;
        $registerErrText = "Your data has been registered. Please Kindly message our admin for the further detail."; 

        $cek_phone = DB::table('cust_list')->where("phone","like","%".$phone."%")->first();

        if($cek_phone!=null){
            $encode = array("status" => "Something Wrong", "text" => $registerErrText, "noAdmin" => env('APP_ADMIN_NUMBER_1'));
            return json_encode($encode);
        }

        $cek_email = DB::table('cust_list')->where("email",$email)->first();

        if($cek_email!=null){
            $encode = array("status" => "Something Wrong", "text" => $registerErrText, "noAdmin" => env('APP_ADMIN_NUMBER_1'));
            return json_encode($encode);
        }

        $custModel = new Customer;
        $custModel->first_name = $firstName;
        $custModel->middle_name = $middleName;
        $custModel->last_name = $lastName;
        $custModel->country_id = $countryId;
        $custModel->phone = $phone;
        $custModel->email = $email;
        $custModel->address = $address;
        $custModel->sub_district = $subDistrict;
        $custModel->district = $district;
        $custModel->city = $city;
        $custModel->prov = $prov;
        $custModel->postal_code = $postalCode;
        $custModel->cust_type_id = "IND";
        $custModel->created_by = "WEBFORM";
        $custModel->updated_by = "WEBFORM";
        $custModel->know_from_id = $knowFrom;
        $custModel->reference = $refId;
        $custModel->link_ref = $refId!=0?1:0;

        $insert = $custModel->save();

        $custId = DB::table("cust_list")->where("phone", "=", $request->input("phone"))->value("id");

        if(!$insert){
            $encode = array("status" => "Something Wrong", "text" => "Fail to input Customer");
            return json_encode($encode);
        }
        
        $phoneFix = "+".DB::table("country_phone_codes")->where("id",$countryId)->value("code").$phone;

        // $orderModel = new Order;
        // $orderModel->cust_id = $custId;
        // $orderModel->cust_type_id = "IND";
        // $orderModel->order_status_id = "READY";
        // $orderModel->invoice_id = "";
        // $orderModel->created_by = "WEBFORM";
        // $orderModel->updated_by = "WEBFORM";
        // $orderModel->first_name = $firstName;
        // $orderModel->middle_name = $middleName;
        // $orderModel->last_name = $lastName;
        // $orderModel->phone = $phoneFix;
        // $orderModel->email = $email;
        // $orderModel->address = $address;
        // $orderModel->sub_district = $subDistrict;
        // $orderModel->district = $district;
        // $orderModel->city = $city;
        // $orderModel->prov = $prov;
        // $orderModel->postal_code = $postalCode;
        // $orderModel->second_name = $secondName;
        // $orderModel->second_phone = $secondPhone;

        // $insert2 = $orderModel->save();

        // if(!$insert2){
        //     $encode = array("status" => "Something Wrong", "text" => "Fail to Input Order");
        //     return json_encode($encode);
        // }

        if(env('SEND_EMAIL')){
            $ccEmail = explode(",",env('MAIL_CC'));
            $bccEmail = env('MAIL_BCC');

            $emailData = [
                "subject" => "New Form Order | ".$fullName." +".DB::table("country_phone_codes")->where("id",$countryId)->value("code").$phone,
                "name" => $fullName,
                "address" => $fullAddress,
                "email" => $email,
                "phone" => $phoneFix,
                "modes" => "WEB" //Webform = "WEB", create invoice = "CINV", create shipment = "CSHI"
            ];
            
            $sendingMail = Mail::to($email)
                            ->cc($ccEmail)
                            ->bcc($bccEmail)
                            ->send(new SendMail($emailData));
        }

//         $data = [
//             "phone" => $phone,
//             "message" => "Halo *".$fullName."* We have received your registration with below details : 

// Full Name : *".$fullName."*
// Whatsapp No : *".$phone."*
// Email : *".$email."*
// Full Address : *".$fullAddress."*
            
// Kindly wait for further confirmations. Thank You.

// Send from website https://www.mismasslogistic.com",
//         ];

        // if(!$sendingMail){
        //     $encode = array("status" => "Something Wrong", "text" => "Fail to send email");
        //     return json_encode($encode);
        // }

        // $sendWA = $this->sendWAForm($data);
        // $data = [
        //     "+".DB::table("country_phone_codes")->where("id",$countryId)->value("code").$phone,
        //     $fullName,
        //     $email,
        //     $fullAddress
        // ];
        // env('QONTAK_STATUS') ? $this->initializeCreateOrder($data) : '';

        // if(!$sendWA){
        //     $encode = array("status" => "Something Wrong", "text" => "Fail to send wa");
        //     return json_encode($encode);
        // }
        // $getId=DB::table("order_list")->where("phone",$phoneFix)->value("id");
        // $this->webFormSendWA($getId);

        $encode = array("status" => "Success", "text" => "Kindly wait for further confirmations. Thank You.");
        return json_encode($encode);

    }
}
