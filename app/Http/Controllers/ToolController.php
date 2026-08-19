<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ToolController extends Controller
{
    public function indexBulky(){
        if(!env('BULKY_PAGE')){
            if(Auth::user()->username!="dev8th"){
                return view('pages.maintenance');
            }
        }
        $data['customer'] = DB::table("cust_list")
                            ->select(
                                "id",
                                "cust_type_id",
                                "first_name",
                                "middle_name",
                                "last_name",
                            )
                            ->get();
        return view("pages.000bulky",$data);
    }

    public function inputBulky(Request $request){
        $id = $request->input("custId");
        $total = count($id);
        $totalSukses = 0;
        $totalGagal = 0;

        foreach($id as $i){

            $getDataCust = DB::table("cust_list")->where("id",$i)->first();
            $phoneFix = "+".DB::table("country_phone_codes")->where("id",$getDataCust->country_id)->value("code").$getDataCust->phone;
            $username = Auth::user()->username=="dev8th" ? "DEVELOP" : Auth::user()->username ;
            
            $arrayOrderList = array(
                    "cust_id" => $i,
                    "cust_type_id" => $getDataCust->cust_type_id,
                    "order_status_id" => "READY",
                    "ms_track_id" => "",
                    "warehouse_id" => "",
                    "service_id" => 0,
                    "invoice_id" => "",
                    "created_at" => date("Y-m-d H:i:s"),
                    "created_by" => $username,
                    "updated_at" => date("Y-m-d H:i:s"),
                    "updated_by" => $username,
                    "first_name" => $getDataCust->first_name,
                    "middle_name" => $getDataCust->middle_name,
                    "last_name" => $getDataCust->last_name,
                    "phone" => $phoneFix,
                    "email" => $getDataCust->email,
                    "address" => $getDataCust->address,
                    "sub_district" => $getDataCust->sub_district,
                    "district" => $getDataCust->district,
                    "city" => $getDataCust->city,
                    "prov" => $getDataCust->prov,
                    "postal_code" => $getDataCust->postal_code,
                    "second_name" => $getDataCust->first_name." ".$getDataCust->middle_name." ".$getDataCust->last_name,
                    "second_phone" => $phoneFix,
                );
            $insert = DB::table("order_list")->insert($arrayOrderList);

            //History
            $dataHistory=[
                        "codename" => "BU",
                        "created_at" => date("Y-m-d H:i:s"),
                        "created_by" => $username,
                        "description" => "<b>Bulky Data</b> dengan detail,<br><br>
                        Tipe : <b>".DB::table("cust_type_list")->where("id", $getDataCust->cust_type_id)->value("name")."</b><br>
                        First Name : <b>".$getDataCust->first_name."</b><br>
                        Middle Name : <b>".$getDataCust->middle_name."</b><br>
                        Last Name : <b>".$getDataCust->last_name."</b><br>
                        Telpon : <b>".$getDataCust->phone."</b><br>
                        Email : <b>".$getDataCust->email."</b><br>
                        Alamat : <b>".$getDataCust->address.", ".$getDataCust->sub_district.", ".$getDataCust->district.", ".$getDataCust->city.", ".$getDataCust->prov.", ".$getDataCust->postal_code."</b>",
                    ];
            $insertHistory = DB::table('history_list')->insert($dataHistory);
            
            if($insert){
                $totalSukses++;
                continue;
            }

            $totalGagal++;
            
        }

        $encode = array("status" => 200, "title" => "Sukses", "text" => "Berhasil : ".$totalSukses." Gagal : ".$totalGagal." Total : ".$total);
        return json_encode($encode);

    }
}
