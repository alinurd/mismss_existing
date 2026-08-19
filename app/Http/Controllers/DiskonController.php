<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Diskon;

class DiskonController extends Controller
{
    public function index()
    {
        $this->roleAccess();
        $data['cust'] = DB::table("cust_list")
                        ->selectRaw("cust_list.*,(SELECT sum(data_list.discount) FROM data_list WHERE data_list.cust_id=cust_list.id) as totalDiskon")
                        ->where("cust_list.cust_type_id","IND")
                        ->orderBy("cust_list.first_name","asc")
                        ->get();
        $data['totaldiskon'] = DB::table("data_list")->selectRaw("sum(discount) as totaldiskon")->where("cust_type_id","IND")->value("totaldiskon");
        return view('pages.diskon',$data);
    }

    public function getTotalDisc(Request $request){
        $custTypeId = $request->input("custTypeId");
        $customerId = $request->input("customerId");
        $filterTanggal = $this->dateFilterFormat($request->input("filterTanggal"));
        
        if($customerId==null||$customerId==""){
            $filterCustomer="";
        }else{
            $filterCustomer = " AND cust_id='$customerId'";
        }

        if($filterTanggal[0]==""||$filterTanggal[0]==null){
            $filterTanggal="";
        }else{
            $filterTanggal = " AND mismass_invoice_date BETWEEN '" . date("Y-m-d", strtotime($filterTanggal[0])) . " 00:00:00' AND '" . date("Y-m-d", strtotime($filterTanggal[1])) . " 23:59:59'";

        }

        $where = "cust_type_id='$custTypeId' $filterCustomer $filterTanggal";

        $data = DB::table("data_list")->selectRaw("sum(discount) as totaldiskon")->whereRaw($where)->value("totaldiskon");
        $encode = array("data" => $data);
        return json_encode($encode);
    }

    public function table(string $custTypeId, Request $request)
    {
        $this->roleAccess();
        $data = [];
        $no = $request->input('start');
        $search = $request->input('search')['value'];
        $filterTanggal = $this->dateFilterFormat($request->input('filterTanggal'));
        $filterCustomerId = $request->input('customerId');
        $filterOrderId = $request->input('mismassOrderId');
        $filter = [
            $filterTanggal,
            $filterCustomerId,
            $custTypeId,
            $filterOrderId
        ];
        $diskonModel = new Diskon();
        $lists = $diskonModel->getDT($request, $search, $filter);

        if ($filterOrderId=="") {

            foreach ($lists as $list) {

                $getData = DB::table('data_list')->select('id','sub_total','weight','item','length','height','width','cons_first_name','cons_middle_name','cons_last_name','cons_phone','cons_address','cons_sub_district','cons_district','cons_city','cons_prov','cons_postal_code','forwarder_id','forwarder_name','shipping_number','shipping_number_stats')->where('mismass_invoice_id',$list->mismass_invoice_id)->get();
                $countRow = count($getData);
                $detcountind="<input type='checkbox' style='margin-left:4px' name='checkResi' data-resi='".$this->resiOnlyId($list->shipping_number, $list->forwarder_id)."'>";
                $detailControl = $custTypeId=="COR" ? "<div class='detail-control hidden-child' id='" . $list->mismass_order_id . "' data-createdAt='".$this->dateFormatIndo($list->shipping_created_at,1)."'></div>":"";
                // $dokuInvoiceId = $list->doku_invoice_id!="" ? $list->doku_invoice_id : "-";
                $forwarder = $list->forwarder_id == "MISMASS" ? $list->forwarder_id." - ".$list->forwarder_name : ($list->forwarder_id=="PICK-UP" ? "<div style='color:red'>PICK-UP SENDIRI</div>" : $list->forwarder_name);
                $shippingNumber = $list->shipping_number_stats==1?"<div style='color:red'>".$list->shipping_number."</div>":$list->shipping_number;
                $fullname = $custTypeId=="COR" ? $list->sender_first_name . " " . $list->sender_middle_name . " " . $list->sender_last_name : $list->cons_first_name . " " . $list->cons_middle_name . " " . $list->cons_last_name;
                $phone = $custTypeId=="COR" ? $list->sender_phone : $list->cons_phone;
                $address = $custTypeId=="COR" ? $list->sender_address . ", " . $list->sender_sub_district . ", " . $list->sender_district . ", " . $list->sender_city . ", " . $list->sender_prov . ", " . $list->sender_postal_code : $list->cons_address . ", " . $list->cons_sub_district . ", " . $list->cons_district . ", " . $list->cons_city . ", " . $list->cons_prov . ", " . $list->cons_postal_code;
                $detilPrice = $list->doku_link!=""?"<div><a href='" . $list->doku_link . "' target='_blank'>" . $list->doku_link . "</a></div>":"<div style='color:red'>".$list->bank_name." - ".$list->bank_account_name."</div><div style='color:blue'>".$list->bank_account_id."</div>";
                $getRank = DB::table('users')->where("username",$list->shipping_updated_by)->value("rank");
                $jabatan = $getRank != null ? "<div class='bg-mismass' style='padding:1px 5px'>".$getRank."</div>" : "";

                $resiBtn = Auth::user()->shiplist_printout_resi?($custTypeId=="IND" ? "<a class='dropdown-item' href='" . url('/printout/resi/' . $this->resiNoGaring($this->resiOnlyId($list->shipping_number, $list->forwarder_id)).'=') . "' target='_blank'>Print Resi</a>" : ""):"";
                $resiBtnAll = Auth::user()->shiplist_printout_resi?"<a class='dropdown-item printResiAll' href='#' data-mismassInvoiceId='".$list->mismass_invoice_id."'>Print Resi All</a>":"";
                $invoiceBtn = Auth::user()->shiplist_printout_invoice?"<a class='dropdown-item' href='" . url('/printout/invoice/' . $this->invOnlyId($list->mismass_invoice_id)) . "' target='_blank'>Print Invoice</a>":"";
                $wholeBtn = "<div class='btn-group dropleft'><button type='button' class='btn btn-secondary nobtn' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'><i class='fas fa-ellipsis-v'></i></button><div class='dropdown-menu' x-placement='right-start' style='position: absolute; transform: translate3d(111px, 0px, 0px); top: 0px; left: 0px; will-change: transform;'>".$resiBtn.$invoiceBtn."</div></div>";

                $no++;
                $row = [];
                $row[] = "<div class='orderNum'>".$no."</div>".$detailControl;
                $row[] = "<div class='font-weight-bold'>" . $list->mismass_invoice_id . "</div><div>" . $this->dateFormatIndo($list->mismass_invoice_date,1) . "</div>";
                // $row[] = "<div class='font-weight-bold'>" . $dokuInvoiceId . "</div>";
                $row[] = "<div class='font-weight-bold'>" . $forwarder . "</div><div class='font-weight-bold'>" . $shippingNumber . "</div><div>" . $this->dateFormatIndo($list->shipping_updated_at,2) . "</div>";
                $row[] = "<div class='font-weight-bold'>" . $list->shipping_updated_by . "</div>".$jabatan."<div>" . $this->dateFormatIndo($list->shipping_updated_at,2) . "</div>";
                $row[] = $fullname;
                $row[] = "<div>" . $phone . "</div><div>" . $address . "</div>";
                $row[] = "<div>" . round($list->totalWeight,2) . " KG</div><div>" . $list->totalItem . " Item</div><div>".round($list->totalCbm,2)." CBM</div>";
                $detilDisc = "<div>Total : ".$this->rupiah($list->totalDisc+$list->totalPrice)."</div><div>Diskon : ".$this->rupiah($list->totalDisc)."</div>";
                $row[] = $detilDisc."<div class='font-weight-bold'>Total Biaya : " . $this->rupiah($list->totalPrice) . "</div>".$detilPrice;
                $row[] = $wholeBtn;
                $data[] = $row;

            }
        } else {

            foreach ($lists as $list) {

                // $invoiceStatus = $list->invoice_status=="PAID" ? "<div style='color:green'>PAID</div>" : "<div style='color:red'>UNPAID</div>";
                // $detailControl = $custTypeId=="COR" ? "<div class='detail-control hidden-child' id='" . $list->mismass_order_id . "' data-createdAt='".$this->dateFormatIndo($list->shipping_created_at,1)."'></div>":"<input type='checkbox' style='margin-left:4px' name='checkResi' data-resi='".$this->resiOnlyId($list->shipping_number, $list->forwarder_id)."'>";
                // $addressFull = $list->sender_address.", ".$list->sender_sub_district.", ".$list->sender_district.", ".$list->sender_city.", ".$list->sender_prov.", ".$list->sender_postal_code;
                // $getRank = DB::table('users')->where("username",$list->updated_by)->value("rank");
                // $jabatan = $getRank != null ? "<div class='bg-mismass' style='padding:1px 5px'>".$getRank."</div>" : "";
                // $resiBtnAll = Auth::user()->shiplist_printout_resi?($custTypeId=="COR"?($list->shipping_number!=""?"<a class='dropdown-item printResiAll' href='#' data-mismassInvoiceId='".$list->mismass_invoice_id."'>Print Resi All</a>":""):""):"";
                // $invoiceBtn = Auth::user()->shiplist_printout_invoice?"<a class='dropdown-item' href='" . url('/printout/invoice/' . $this->invOnlyId($list->mismass_invoice_id)) . "' target='_blank'>Print Invoice</a>":"";
                // $wholeBtn = "<div class='btn-group dropleft'><button type='button' class='btn btn-secondary nobtn' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'><i class='fas fa-ellipsis-v'></i></button><div class='dropdown-menu' x-placement='right-start' style='position: absolute; transform: translate3d(111px, 0px, 0px); top: 0px; left: 0px; will-change: transform;'>".$resiBtnAll.$invoiceBtn."</div></div>";
                
                // $no++;
                // $row = [];
                // $row[] = "<div class='orderNum'>".$no."</div>".$detailControl;
                // $row[] = "<div style='font-weight:bold'>".$list->mismass_invoice_id."</div><div>".$this->dateFormatIndo($list->mismass_invoice_date,1)."</div>";
                // $row[] = "<div style='font-weight:bold'>".$list->doku_invoice_id."</div>".$invoiceStatus;
                // $row[] = "<div>".$list->updated_by."</div>".$jabatan."<div>".$this->dateFormatIndo($list->updated_at,2)."</div>";
                // $row[] = "<div>".$list->sender_first_name." ".$list->sender_middle_name." ".$list->sender_last_name."</div>";
                // $row[] = "<div>".$list->sender_phone."</div><div>".$addressFull."</div>";
                // $row[] = "<div>".number_format((float)$list->totalWeight, 2, '.', '')." Kg</div><div>".$list->totalItem." Item</div><div>".$list->totalCbm." CBM</div>";
                // $row[] = "<div>Total : ".$this->rupiah($list->totalPrice+$list->totalDisc)."</div><div>Diskon : ".$this->rupiah($list->totalDisc)."</div><div style='font-weight:bold'>Total Biaya : ".$this->rupiah($list->totalPrice)."</div><div><a href='".$list->doku_link."' target='_blank'>".$list->doku_link."</a></div>";
                // $row[] = $wholeBtn;

                // $data[] = $row;

                $forwarder = $list->forwarder_id == "MISMASS" ? $list->forwarder_id." - ".$list->forwarder_name : ($list->forwarder_id=="PICK-UP" ? "<div style='color:red'>PICK-UP SENDIRI</div>" : $list->forwarder_name);

                $no++;
                $row = [];
                $row[] = $no;
                $row[] = "<div class='font-weight-bold'>" . $forwarder . "</div><div class='font-weight-bold'>" . $list->shipping_number . "</div>";
                $row[] = $list->cons_first_name." ".$list->cons_middle_name." ".$list->cons_last_name;
                $row[] = "<div>".$list->cons_phone."</div><div>".$list->cons_address.", ".$list->cons_sub_district.", ".$list->cons_district.", ".$list->cons_city.", ".$list->cons_prov.", ".$list->cons_postal_code."</div>";
                $row[] = "<div>" . number_format((float)$list->totalWeight, 2, '.', '') . " KG</div><div>" . $list->totalItem . " Item</div><div>".round($list->totalCbm,2)." CBM</div>";
                
                // if(Auth::user()->shiplist_printout_resi){
                $row[] = "<a class='btn btn-success' href='" . url('/printout/resi/' . $this->resiNoGaring($this->resiOnlyId($list->shipping_number, $list->forwarder_id))) . "=' target='_blank'><i class='fas fa-print'></i></a>";
                // }

                $data[] = $row;
            }
        }

        $output = [
            'draw' => $request->input('draw'),
            'recordsTotal' => $diskonModel->countAll($request, $search, $filter),
            'recordsFiltered' => $diskonModel->countFiltered($request, $search, $filter),
            'data' => $data
        ];

        return json_encode($output);
    }

}

?>