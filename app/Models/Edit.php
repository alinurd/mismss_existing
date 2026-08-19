<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class Edit extends Model
{
    use HasFactory;
    
    private $controller;
    
    public function __construct()
    {
        $this->controller = new Controller;
    }

    public function revisiButton($array){
        if(!Auth::user()->revisi_btn){
            return "";
        }

        if($array['filterPayStatus']!="PENDING"){
            return "";
        }

        $revisiBtn = "<a class='dropdown-item pointlink' id='editInvoiceBtn' data-create-inv-add='false' data-tglInv='".date("d-m-Y",strtotime($array['invoiceDate']))."' data-id='" . $array['orderId'] . "' data-mstracks='".$array['msTrackId']."'>Revisi Invoice</a>";
        if($array['invAdd']){
            $revisiBtn = "<a class='dropdown-item pointlink' id='editInvoiceAddBtn' data-create-inv-add='true' data-tglInv='".date("d-m-Y",strtotime($array['invoiceDate']))."' data-id='" . $array['orderId'] . "' data-mstracks='".$array['msTrackId']."'>Revisi Invoice</a>";
        }

        return $revisiBtn;
    }

    public function checkData($request){
        $totalBiaya = 0;
        $diff = 0;
        $blastStatus = false;
        $skip = false;

        $getOld = DB::table('data_list')
                    ->where('mismass_invoice_id',$request->input('mismassInvoiceId'))
                    ->get();

        if(count($request->input("warehouseval"))!=count($getOld)){
            [$diff++,$blastStatus=true];
            $skip = true;
        }

        $i = 0;
        if(!$skip){
            foreach($getOld as $go){

                $doku_invoice_id = $request->input("invoiceDoku") ?? "";
                
                $cons_first_name = $request->input("consFirstName")[$i] ?? "";
                $cons_middle_name = $request->input("consMiddleName")[$i] ?? "";
                $cons_last_name = $request->input("consLastName")[$i] ?? "";
                $cons_email = $request->input("consEmail")[$i] ?? "";
                $cons_phone = $request->input("consPhone")[$i] ?? "";
                $cons_address = $request->input("consAddress")[$i] ?? "";
                $cons_sub_district = $request->input("consSubDistrict")[$i] ?? "";
                $cons_district = $request->input("consDistrict")[$i] ?? "";
                $cons_city = $request->input("consCity")[$i] ?? "";
                $cons_prov = $request->input("consProv")[$i] ?? "";
                $cons_postal_code = $request->input("consPostalCode")[$i] ?? "";
                
                $revisionNote = $request->input("revisionNote") ?? "";
                $doku_link = $request->input("linkDoku") ?? "";
                $bank_name = $request->input("namaBank") ?? "";
                $bank_account_name = $request->input("namaRekening") ?? "";
                $bank_account_id = $request->input("noRekening") ?? "";
                $template_id = $request->input("templateId");
                $length = isset($request->input("panjang")[$i]) ? $this->controller->normalizeInput($request->input("panjang")[$i]) : 0;
                $width = isset($request->input("lebar")[$i]) ? $this->controller->normalizeInput($request->input("lebar")[$i]) : 0;
                $height = isset($request->input("tinggi")[$i]) ? $this->controller->normalizeInput($request->input("tinggi")[$i]) : 0;
                $weight = isset($request->input("kg")[$i]) ? $this->controller->normalizeInput($request->input("kg")[$i]) : 0;
                $cbm = isset($request->input("cbm")[$i]) ? $this->controller->normalizeInput($request->input("cbm")[$i]) : 0;
                $actualWeight = isset($request->input("actualKg")[$i]) ? $this->controller->normalizeInput($request->input("actualKg")[$i]) : 0;
                $item = isset($request->input("item")[$i]) ? $this->controller->normalizeInput($request->input("item")[$i]) : 0;
                $discount = $this->controller->normalizeInput($request->input("discount" . $i));
                $additional_desc = $request->input("additionalDesc" . $i) ?? "";
                $additional_nom = $this->controller->normalizeInput($request->input("additionalNominal" . $i));
                $packing = $this->controller->normalizeInput($request->input("packing" . $i));
                $packing_per = $this->controller->normalizeInput($request->input("packingPer" . $i));
                $packing_total = $this->controller->normalizeInput($request->input("packingTotal" . $i));
                $packing_desc = $request->input("packingDesc" . $i) ?? "";
                $import_permit = $this->controller->normalizeInput($request->input("import" . $i));
                $import_permit_per = $this->controller->normalizeInput($request->input("importPer" . $i));
                $import_permit_total = $this->controller->normalizeInput($request->input("importTotal" . $i));
                $import_permit_desc = $request->input("importDesc" . $i) ?? "";
                $document = $this->controller->normalizeInput($request->input("document" . $i));
                $document_per = $this->controller->normalizeInput($request->input("documentPer" . $i));
                $document_total = $this->controller->normalizeInput($request->input("documentTotal" . $i));
                $document_desc = $request->input("documentDesc" . $i) ?? "";
                $dr_medicine = $this->controller->normalizeInput($request->input("medicine" . $i));
                $dr_medicine_per = $this->controller->normalizeInput($request->input("medicinePer" . $i));
                $dr_medicine_total = $this->controller->normalizeInput($request->input("medicineTotal" . $i));
                $dr_medicine_desc = $request->input("medicineDesc" . $i) ?? "";
                $insurance_item_price = $this->controller->normalizeInput($request->input("insurancePriceItem" . $i));
                $insurance_percent = $this->controller->normalizeInput($request->input("insurancePercent" . $i));
                $insurance_total = round($this->controller->normalizeInput($request->input("insuranceTotal" . $i)));
                $fee_item_price = $this->controller->normalizeInput($request->input("feePriceItem" . $i));
                $fee_percent = $this->controller->normalizeInput($request->input("feePercent" . $i));
                $fee_total = round($this->controller->normalizeInput($request->input("feeTotal" . $i)));
                $tax_item_price = $this->controller->normalizeInput($request->input("taxPriceItem" . $i));
                $tax_percent = $this->controller->normalizeInput($request->input("taxPercent" . $i));
                $tax_total = round($this->controller->normalizeInput($request->input("taxTotal" . $i)));
                $extra_cost_price = $this->controller->normalizeInput($request->input("extraCostPrice" . $i));
                $extra_cost_dest = $request->input("extraCostDest" . $i) ?? "";
                $extra_cost_vendor_name = $request->input("extraCostVendorName" . $i) ?? "";
                $extra_cost_shipping_number = $request->input("extraCostShippingNum" . $i) ?? "";
                $pickup_weight = $this->controller->normalizeInput($request->input("pickUpWeight" . $i));
                $pickup_charge = $this->controller->normalizeInput($request->input("pickUpCharge" . $i));
                $sub_total = isset($request->input("subTotal")[$i]) ? $this->controller->normalizeInput($request->input("subTotal")[$i]) : 0;
                $fc_symbol = $request->input("foreignSymbol") ?? "";
                $fc_value = $this->controller->normalizeInput($request->input("foreignRateValue")) ?? 0;

                $go->warehouse_id != $request->input("warehouseval")[$i] ? [$diff++,$blastStatus=true] : '';
                $go->service_id != $request->input("serviceval")[$i] ? [$diff++,$blastStatus=true] : '';
                $go->mismass_invoice_date != date("Y-m-d H:i:s", strtotime($request->input("tanggalInvoice"))) ? [$diff++,$blastStatus=true] : '';
                
                $go->cons_first_name != $cons_first_name ? $diff++ : '';
                $go->cons_middle_name != $cons_middle_name ? $diff++ : '';
                $go->cons_last_name != $cons_last_name ? $diff++ : '';
                $go->cons_email != $cons_email ? $diff++ : '';
                $go->cons_phone != $cons_phone ? $diff++ : '';
                $go->cons_address != $cons_address ? $diff++ : '';
                $go->cons_sub_district != $cons_sub_district ? $diff++ : '';
                $go->cons_district != $cons_district ? $diff++ : '';
                $go->cons_city != $cons_city ? $diff++ : '';
                $go->cons_prov != $cons_prov ? $diff++ : '';
                $go->cons_postal_code != $cons_postal_code ? $diff++ : '';

                $go->revision_note != $revisionNote ? $diff++ : '';
                $go->doku_invoice_id != $doku_invoice_id ? [$diff++,$blastStatus=true] : '';
                $go->doku_link != $doku_link ? [$diff++,$blastStatus=true] : '';
                $go->bank_name != $bank_name ? [$diff++,$blastStatus=true] : '';
                $go->bank_account_name != $bank_account_name ? [$diff++,$blastStatus=true] : '';
                $go->bank_account_id != $bank_account_id ? [$diff++,$blastStatus=true] : '';
                $go->template_id != $template_id ? [$diff++,$blastStatus=true] : '' ;
                $go->length != $length ? [$diff++,$blastStatus=true] : '';
                $go->width != $width ? [$diff++,$blastStatus=true] : '';
                $go->height != $height ? [$diff++,$blastStatus=true] : '';
                $go->weight != $weight ? [$diff++,$blastStatus=true] : '';
                $go->cbm != $cbm ? [$diff++,$blastStatus=true] : '';
                $go->actual_weight != $actualWeight ? [$diff++,$blastStatus=true] : '';
                $go->item != $item ? [$diff++,$blastStatus=true] : '';
                $go->discount != $discount ? [$diff++,$blastStatus=true] : '';
                $go->additional_desc != $additional_desc ? [$diff++,$blastStatus=true] : '';
                $go->additional_nom != $additional_nom ? [$diff++,$blastStatus=true] : '';
                $go->packing != $packing ? [$diff++,$blastStatus=true] : '';
                $go->packing_per != $packing_per ? [$diff++,$blastStatus=true] : '';
                $go->packing_total != $packing_total ? [$diff++,$blastStatus=true] : '';
                $go->packing_desc != $packing_desc ? [$diff++,$blastStatus=true] : '';
                $go->import_permit != $import_permit ? [$diff++,$blastStatus=true] : '';
                $go->import_permit_per != $import_permit_per ? [$diff++,$blastStatus=true] : '';
                $go->import_permit_total != $import_permit_total ? [$diff++,$blastStatus=true] : '';
                $go->import_permit_desc != $import_permit_desc ? [$diff++,$blastStatus=true] : '';
                $go->document != $document ? [$diff++,$blastStatus=true] : '';
                $go->document_per != $document_per ? [$diff++,$blastStatus=true] : '';
                $go->document_total != $document_total ? [$diff++,$blastStatus=true] : '';
                $go->document_desc != $document_desc ? [$diff++,$blastStatus=true] : '';
                $go->dr_medicine != $dr_medicine ? [$diff++,$blastStatus=true] : '';
                $go->dr_medicine_per != $dr_medicine_per ? [$diff++,$blastStatus=true] : '';
                $go->dr_medicine_total != $dr_medicine_total ? [$diff++,$blastStatus=true] : '';
                $go->dr_medicine_desc != $dr_medicine_desc ? [$diff++,$blastStatus=true] : '';
                $go->insurance_item_price != $insurance_item_price ? [$diff++,$blastStatus=true] : '';
                $go->insurance_percent != $insurance_percent ? [$diff++,$blastStatus=true] : '';
                $go->insurance_total != $insurance_total ? [$diff++,$blastStatus=true] : '';
                $go->tax_item_price != $tax_item_price ? [$diff++,$blastStatus=true] : '';
                $go->tax_percent != $tax_percent ? [$diff++,$blastStatus=true] : '';
                $go->tax_total != $tax_total ? [$diff++,$blastStatus=true] : '';
                $go->fee_item_price != $fee_item_price ? [$diff++,$blastStatus=true] : '';
                $go->fee_percent != $fee_percent ? [$diff++,$blastStatus=true] : '';
                $go->fee_total != $fee_total ? [$diff++,$blastStatus=true] : '';
                $go->extra_cost_price != $extra_cost_price ? [$diff++,$blastStatus=true] : '';
                $go->extra_cost_dest != $extra_cost_dest ? [$diff++,$blastStatus=true] : '';
                $go->extra_cost_vendor_name != $extra_cost_vendor_name ? [$diff++,$blastStatus=true] : '';
                $go->extra_cost_shipping_number != $extra_cost_shipping_number ? [$diff++,$blastStatus=true] : '';
                $go->pickup_weight != $pickup_weight ? [$diff++,$blastStatus=true] : '';
                $go->pickup_charge != $pickup_charge ? [$diff++,$blastStatus=true] : '';
                $go->sub_total != $sub_total ? [$diff++,$blastStatus=true] : '';
                $go->fc_symbol != $fc_symbol ? [$diff++,$blastStatus=true] : '';
                $fcValue = $go->fc_symbol == "" ? "" : $go->fc_value;
                $fcValue != $fc_value ? [$diff++,$blastStatus=true] : '';

                $i++;
            }
        }

        for ($i = 0; $i <= count($request->input("warehouseval")) - 1; $i++) {
            $sub_total = isset($request->input("subTotal")[$i]) ? $this->controller->normalizeInput($request->input("subTotal")[$i]) : 0;
            $adjust_fee = $i==0 ? $this->controller->normalizeInput($request->input("adjustFee")) : 0;
            $totalBiaya += $sub_total+$adjust_fee;
        }

        $data = [
            "blastStatus" => $blastStatus,
            "diff" => $diff,
            "totalBiaya" =>$totalBiaya
        ];

        return $data;
    }

    // public function createNewInvoice($invoiceId){
    //     $i = explode("-", $invoiceId);
    
    //     // Jika belum pernah direvisi -> tambah -1
    //     if (!isset($i[1])) {
    //         return $invoiceId . "-1";
    //     }
    
    //     // Jika sudah ada revisi -> tambah 1 pada angkanya
    //     $newNumber = intval($i[1]) + 1;
    //     return $i[0] . "-" . $newNumber;
    // }
    public function createNewInvoice($invoiceId)
    {
        // Jika sudah ada revisi dalam format (...), contoh: INV/AJV/123(1)
        if (preg_match('/\((\d+)\)$/', $invoiceId, $match)) {
            $newNumber = intval($match[1]) + 1;
            // Ganti angka lama dengan angka baru
            return preg_replace('/\(\d+\)$/', '(' . $newNumber . ')', $invoiceId);
        }
    
        // Jika masih menggunakan format lama INV/AJV/123-1
        if (strpos($invoiceId, '-') !== false) {
            $i = explode('-', $invoiceId);
            return $i[0] . "(" . intval($i[1])+1 . ")"; 
        }
    
        // Jika belum pernah direvisi → tambahkan (1)
        return $invoiceId . "(1)";
    }
}
