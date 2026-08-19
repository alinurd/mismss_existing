<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class Dashboard extends Model
{
    use HasFactory;

    private $controller;

    public function __construct()
    {
        $this->controller = new Controller;
    }

    public function loadFinance($request)
{
    $country = $request->input("filterCountry");
    $warehouse = $request->input("filterWarehouse");
    $tanggalAwal = $request->input("filterTanggalAwal");
    $tanggalAkhir = $request->input("filterTanggalAkhir");
    $mode = $request->input("mode");

    $start = date("Y-m-d 00:00:00", strtotime($tanggalAwal));
    $end   = date("Y-m-d 23:59:59", strtotime($tanggalAkhir));

    // ===============================
    // 🔹 BASE QUERY (dipakai ulang)
    // ===============================
    $baseQuery = DB::table("data_list")
        ->join("warehouse_list", "warehouse_list.id", "=", "data_list.warehouse_id")
        ->whereBetween("data_list.created_at", [$start, $end])
        ->where(function ($q) {
            $q->where("data_list.doku_link", "!=", "")
              ->orWhere("data_list.bank_name", "!=", "");
        })
        ->when($warehouse, fn($q) => $q->where("data_list.warehouse_id", $warehouse))
        ->when($country, fn($q) => $q->where("warehouse_list.country_id", $country));

    // ===============================
    // 🔹 SUMMARY
    // ===============================
    $get = (clone $baseQuery)->selectRaw("
    SUM(CASE WHEN warehouse_list.route_id='EXP' THEN (sub_total+adjust_fee) ELSE 0 END) as totalExport,
    SUM(CASE WHEN warehouse_list.route_id='EXP' THEN weight ELSE 0 END) as totalBeratExport,
    SUM(CASE WHEN (warehouse_list.route_id='EXP' AND cust_type_id='IND') THEN weight ELSE 0 END) as totalBeratIndExport,
    SUM(CASE WHEN (warehouse_list.route_id='EXP' AND cust_type_id='COR') THEN weight ELSE 0 END) as totalBeratCorExport,
    SUM(CASE WHEN warehouse_list.route_id='EXP' THEN cbm ELSE 0 END) as totalCbmExport,
    SUM(CASE WHEN (warehouse_list.route_id='EXP' AND cust_type_id='IND') THEN cbm ELSE 0 END) as totalCbmIndExport,
    SUM(CASE WHEN (warehouse_list.route_id='EXP' AND cust_type_id='COR') THEN cbm ELSE 0 END) as totalCbmCorExport,
    COUNT(DISTINCT CASE WHEN warehouse_list.route_id='EXP' THEN data_list.cust_id END) as totalCustExport,
    COUNT(DISTINCT CASE WHEN (warehouse_list.route_id='EXP' AND cust_type_id='IND') THEN data_list.cust_id END) as totalCustIndExport,
    COUNT(DISTINCT CASE WHEN (warehouse_list.route_id='EXP' AND cust_type_id='COR') THEN data_list.cust_id END) as totalCustCorExport,
    SUM(CASE WHEN (warehouse_list.route_id='EXP' AND fc_symbol='') THEN sub_total+adjust_fee ELSE 0 END) as totalPendapatanExport,
    SUM(CASE WHEN (warehouse_list.route_id='EXP' AND fc_symbol!='') THEN sub_total+adjust_fee ELSE 0 END) as totalPendapatanForeignRpExport,
    SUM(CASE WHEN (warehouse_list.route_id='EXP' AND fc_symbol!='') THEN (sub_total+adjust_fee)/NULLIF(fc_value,0) ELSE 0 END) as totalPendapatanForeignExport,
    SUM(CASE WHEN warehouse_list.route_id='EXP' THEN discount ELSE 0 END) as totalDiskonExport,
    SUM(CASE WHEN (warehouse_list.route_id='EXP' AND cust_type_id='IND' AND fc_symbol='') THEN sub_total+adjust_fee ELSE 0 END) as totalPendapatanIndExport,
    SUM(CASE WHEN (warehouse_list.route_id='EXP' AND cust_type_id='COR' AND fc_symbol='') THEN sub_total+adjust_fee ELSE 0 END) as totalPendapatanCorExport,
    SUM(CASE WHEN (warehouse_list.route_id='EXP' AND cust_type_id='IND' AND fc_symbol!='') THEN (sub_total+adjust_fee)/NULLIF(fc_value,0) ELSE 0 END) as totalPendapatanForeignIndExport,
    SUM(CASE WHEN (warehouse_list.route_id='EXP' AND cust_type_id='COR' AND fc_symbol!='') THEN (sub_total+adjust_fee)/NULLIF(fc_value,0) ELSE 0 END) as totalPendapatanForeignCorExport,
    SUM(CASE WHEN (warehouse_list.route_id='EXP' AND cust_type_id='IND') THEN discount ELSE 0 END) as totalDiskonIndExport,
    SUM(CASE WHEN (warehouse_list.route_id='EXP' AND fc_symbol='') THEN discount ELSE 0 END) as totalDiskonRpExport,
    SUM(CASE WHEN (warehouse_list.route_id='EXP' AND cust_type_id='IND' AND fc_symbol='') THEN discount ELSE 0 END) as totalDiskonIndRpExport,
    SUM(CASE WHEN (warehouse_list.route_id='EXP' AND cust_type_id='COR' AND fc_symbol='') THEN discount ELSE 0 END) as totalDiskonCorRpExport,
    SUM(CASE WHEN (warehouse_list.route_id='EXP' AND cust_type_id='COR') THEN discount ELSE 0 END) as totalDiskonCorExport,
    SUM(CASE WHEN (warehouse_list.route_id='EXP' AND fc_symbol!='') THEN discount/NULLIF(fc_value,0) ELSE 0 END) as totalDiskonSGDExport,    
    SUM(CASE WHEN (warehouse_list.route_id='EXP' AND cust_type_id='IND' AND fc_symbol!='') THEN discount/NULLIF(fc_value,0) ELSE 0 END) as totalDiskonIndSGDExport,
    SUM(CASE WHEN (warehouse_list.route_id='EXP' AND cust_type_id='COR' AND fc_symbol!='') THEN discount/NULLIF(fc_value,0) ELSE 0 END) as totalDiskonCorSGDExport,
    SUM(CASE WHEN (warehouse_list.route_id='EXP' AND invoice_status='PAID' AND fc_symbol='') THEN sub_total+adjust_fee ELSE 0 END) as totalPaidExport,
    SUM(CASE WHEN (warehouse_list.route_id='EXP' AND invoice_status='UNPAID' AND fc_symbol='') THEN sub_total+adjust_fee ELSE 0 END) as totalUnpaidExport,
    SUM(CASE WHEN (warehouse_list.route_id='EXP' AND invoice_status='PAID' AND fc_symbol!='') THEN (sub_total+adjust_fee)/NULLIF(fc_value,0) ELSE 0 END) as totalPaidForeignExport,
    SUM(CASE WHEN (warehouse_list.route_id='EXP' AND invoice_status='UNPAID' AND fc_symbol!='') THEN (sub_total+adjust_fee)/NULLIF(fc_value,0) ELSE 0 END) as totalUnpaidForeignExport,
    SUM(CASE WHEN (warehouse_list.route_id='EXP') THEN (sub_total+adjust_fee) ELSE 0 END) as totalPendapatanAllExport,
    SUM(CASE WHEN (warehouse_list.route_id='EXP' AND cust_type_id='IND') THEN (sub_total+adjust_fee) ELSE 0 END) as totalPendapatanAllIndExport,
    SUM(CASE WHEN (warehouse_list.route_id='EXP' AND cust_type_id='COR') THEN (sub_total+adjust_fee) ELSE 0 END) as totalPendapatanAllCorExport,
    SUM(CASE WHEN (warehouse_list.route_id='EXP' AND invoice_status='PAID') THEN (sub_total+adjust_fee) ELSE 0 END) as totalPaidAllExport,
    SUM(CASE WHEN (warehouse_list.route_id='EXP' AND invoice_status='UNPAID') THEN (sub_total+adjust_fee) ELSE 0 END) as totalUnpaidAllExport,

    SUM(CASE WHEN warehouse_list.route_id='IMP' THEN (sub_total+adjust_fee) ELSE 0 END) as totalImport,
    SUM(CASE WHEN warehouse_list.route_id='IMP' THEN weight ELSE 0 END) as totalBeratImport,
    SUM(CASE WHEN (warehouse_list.route_id='IMP' AND cust_type_id='IND') THEN weight ELSE 0 END) as totalBeratIndImport,
    SUM(CASE WHEN (warehouse_list.route_id='IMP' AND cust_type_id='COR') THEN weight ELSE 0 END) as totalBeratCorImport,
    SUM(CASE WHEN warehouse_list.route_id='IMP' THEN cbm ELSE 0 END) as totalCbmImport,
    SUM(CASE WHEN (warehouse_list.route_id='IMP' AND cust_type_id='IND') THEN cbm ELSE 0 END) as totalCbmIndImport,
    SUM(CASE WHEN (warehouse_list.route_id='IMP' AND cust_type_id='COR') THEN cbm ELSE 0 END) as totalCbmCorImport,
    COUNT(DISTINCT CASE WHEN warehouse_list.route_id='IMP' THEN data_list.cust_id END) as totalCustImport,
    COUNT(DISTINCT CASE WHEN (warehouse_list.route_id='IMP' AND cust_type_id='IND') THEN data_list.cust_id END) as totalCustIndImport,
    COUNT(DISTINCT CASE WHEN (warehouse_list.route_id='IMP' AND cust_type_id='COR') THEN data_list.cust_id END) as totalCustCorImport,
    SUM(CASE WHEN (warehouse_list.route_id='IMP' AND fc_symbol='') THEN sub_total+adjust_fee ELSE 0 END) as totalPendapatanImport,
    SUM(CASE WHEN (warehouse_list.route_id='IMP' AND fc_symbol!='') THEN sub_total+adjust_fee ELSE 0 END) as totalPendapatanForeignRpImport,
    SUM(CASE WHEN (warehouse_list.route_id='IMP' AND fc_symbol!='') THEN (sub_total+adjust_fee)/NULLIF(fc_value,0) ELSE 0 END) as totalPendapatanForeignImport,
    SUM(CASE WHEN warehouse_list.route_id='IMP' THEN discount ELSE 0 END) as totalDiskonImport,
    SUM(CASE WHEN (warehouse_list.route_id='IMP' AND cust_type_id='IND' AND fc_symbol='') THEN sub_total+adjust_fee ELSE 0 END) as totalPendapatanIndImport,
    SUM(CASE WHEN (warehouse_list.route_id='IMP' AND cust_type_id='COR' AND fc_symbol='') THEN sub_total+adjust_fee ELSE 0 END) as totalPendapatanCorImport,
    SUM(CASE WHEN (warehouse_list.route_id='IMP' AND cust_type_id='IND' AND fc_symbol!='') THEN (sub_total+adjust_fee)/NULLIF(fc_value,0) ELSE 0 END) as totalPendapatanForeignIndImport,
    SUM(CASE WHEN (warehouse_list.route_id='IMP' AND cust_type_id='COR' AND fc_symbol!='') THEN (sub_total+adjust_fee)/NULLIF(fc_value,0) ELSE 0 END) as totalPendapatanForeignCorImport,
    SUM(CASE WHEN (warehouse_list.route_id='IMP' AND cust_type_id='IND') THEN discount ELSE 0 END) as totalDiskonIndImport,
    SUM(CASE WHEN (warehouse_list.route_id='IMP' AND fc_symbol='') THEN discount ELSE 0 END) as totalDiskonRpImport,
    SUM(CASE WHEN (warehouse_list.route_id='IMP' AND cust_type_id='IND' AND fc_symbol='') THEN discount ELSE 0 END) as totalDiskonIndRpImport,
    SUM(CASE WHEN (warehouse_list.route_id='IMP' AND cust_type_id='COR' AND fc_symbol='') THEN discount ELSE 0 END) as totalDiskonCorRpImport,
    SUM(CASE WHEN (warehouse_list.route_id='IMP' AND cust_type_id='COR') THEN discount ELSE 0 END) as totalDiskonCorImport,
    SUM(CASE WHEN (warehouse_list.route_id='IMP' AND fc_symbol!='') THEN discount/NULLIF(fc_value,0) ELSE 0 END) as totalDiskonSGDImport,    
    SUM(CASE WHEN (warehouse_list.route_id='IMP' AND cust_type_id='IND' AND fc_symbol!='') THEN discount/NULLIF(fc_value,0) ELSE 0 END) as totalDiskonIndSGDImport,
    SUM(CASE WHEN (warehouse_list.route_id='IMP' AND cust_type_id='COR' AND fc_symbol!='') THEN discount/NULLIF(fc_value,0) ELSE 0 END) as totalDiskonCorSGDImport,
    SUM(CASE WHEN (warehouse_list.route_id='IMP' AND invoice_status='PAID' AND fc_symbol='') THEN sub_total+adjust_fee ELSE 0 END) as totalPaidImport,
    SUM(CASE WHEN (warehouse_list.route_id='IMP' AND invoice_status='UNPAID' AND fc_symbol='') THEN sub_total+adjust_fee ELSE 0 END) as totalUnpaidImport,
    SUM(CASE WHEN (warehouse_list.route_id='IMP' AND invoice_status='PAID' AND fc_symbol!='') THEN (sub_total+adjust_fee)/NULLIF(fc_value,0) ELSE 0 END) as totalPaidForeignImport,
    SUM(CASE WHEN (warehouse_list.route_id='IMP' AND invoice_status='UNPAID' AND fc_symbol!='') THEN (sub_total+adjust_fee)/NULLIF(fc_value,0) ELSE 0 END) as totalUnpaidForeignImport,
    SUM(CASE WHEN (warehouse_list.route_id='IMP') THEN (sub_total+adjust_fee) ELSE 0 END) as totalPendapatanAllImport,
    SUM(CASE WHEN (warehouse_list.route_id='IMP' AND cust_type_id='IND') THEN (sub_total+adjust_fee) ELSE 0 END) as totalPendapatanAllIndImport,
    SUM(CASE WHEN (warehouse_list.route_id='IMP' AND cust_type_id='COR') THEN (sub_total+adjust_fee) ELSE 0 END) as totalPendapatanAllCorImport,
    SUM(CASE WHEN (warehouse_list.route_id='IMP' AND invoice_status='PAID') THEN (sub_total+adjust_fee) ELSE 0 END) as totalPaidAllImport,
    SUM(CASE WHEN (warehouse_list.route_id='IMP' AND invoice_status='UNPAID') THEN (sub_total+adjust_fee) ELSE 0 END) as totalUnpaidAllImport,

    SUM(weight) as totalBerat,
    SUM(CASE WHEN cust_type_id='IND' THEN weight ELSE 0 END) as totalBeratInd,
    SUM(CASE WHEN cust_type_id='COR' THEN weight ELSE 0 END) as totalBeratCor,
    
    SUM(cbm) as totalCbm,
    SUM(CASE WHEN cust_type_id='IND' THEN cbm ELSE 0 END) as totalCbmInd,
    SUM(CASE WHEN cust_type_id='COR' THEN cbm ELSE 0 END) as totalCbmCor,
    
    COUNT(DISTINCT(data_list.cust_id)) as totalCust,
    COUNT(DISTINCT CASE WHEN cust_type_id='IND' THEN cust_id END) as totalCustInd,
    COUNT(DISTINCT CASE WHEN cust_type_id='COR' THEN cust_id END) as totalCustCor,
    
    SUM(CASE WHEN fc_symbol='' THEN sub_total+adjust_fee ELSE 0 END) as totalPendapatan,
    SUM(CASE WHEN fc_symbol!='' THEN (sub_total+adjust_fee)/NULLIF(fc_value,0) ELSE 0 END) as totalPendapatanForeign,
    
    SUM(discount) as totalDiskon,
    
    SUM(CASE WHEN cust_type_id='IND' AND fc_symbol='' THEN sub_total+adjust_fee ELSE 0 END) as totalPendapatanInd,
    SUM(CASE WHEN cust_type_id='COR' AND fc_symbol='' THEN sub_total+adjust_fee ELSE 0 END) as totalPendapatanCor,
    
    SUM(CASE WHEN cust_type_id='IND' AND fc_symbol!='' THEN (sub_total+adjust_fee)/NULLIF(fc_value,0) ELSE 0 END) as totalPendapatanForeignInd,
    SUM(CASE WHEN cust_type_id='COR' AND fc_symbol!='' THEN (sub_total+adjust_fee)/NULLIF(fc_value,0) ELSE 0 END) as totalPendapatanForeignCor,

    SUM(CASE WHEN cust_type_id='IND' THEN discount ELSE 0 END) as totalDiskonInd,
    SUM(CASE WHEN fc_symbol='' THEN discount ELSE 0 END) as totalDiskonRp,
    SUM(CASE WHEN cust_type_id='IND' AND fc_symbol='' THEN discount ELSE 0 END) as totalDiskonIndRp,
    SUM(CASE WHEN cust_type_id='COR' AND fc_symbol='' THEN discount ELSE 0 END) as totalDiskonCorRp,
    SUM(CASE WHEN cust_type_id='COR' THEN discount ELSE 0 END) as totalDiskonCor,
    SUM(CASE WHEN fc_symbol!='' THEN discount/NULLIF(fc_value,0) ELSE 0 END) as totalDiskonSGD,    
    SUM(CASE WHEN cust_type_id='IND' AND fc_symbol!='' THEN discount/NULLIF(fc_value,0) ELSE 0 END) as totalDiskonIndSGD,
    SUM(CASE WHEN cust_type_id='COR' AND fc_symbol!='' THEN discount/NULLIF(fc_value,0) ELSE 0 END) as totalDiskonCorSGD,
    
    SUM(CASE WHEN invoice_status='PAID' AND fc_symbol='' THEN sub_total+adjust_fee ELSE 0 END) as totalPaid,
    SUM(CASE WHEN invoice_status='UNPAID' AND fc_symbol='' THEN sub_total+adjust_fee ELSE 0 END) as totalUnpaid,
    
    SUM(CASE WHEN invoice_status='PAID' AND fc_symbol!='' THEN (sub_total+adjust_fee)/NULLIF(fc_value,0) ELSE 0 END) as totalPaidForeign,
    SUM(CASE WHEN invoice_status='UNPAID' AND fc_symbol!='' THEN (sub_total+adjust_fee)/NULLIF(fc_value,0) ELSE 0 END) as totalUnpaidForeign,

    SUM(sub_total + adjust_fee) as totalPendapatanAll,
    SUM(CASE WHEN cust_type_id='IND' THEN sub_total+adjust_fee ELSE 0 END) as totalPendapatanAllInd,
    SUM(CASE WHEN cust_type_id='COR' THEN sub_total+adjust_fee ELSE 0 END) as totalPendapatanAllCor,

    SUM(CASE WHEN invoice_status='PAID' THEN sub_total+adjust_fee ELSE 0 END) as totalPaidAll,
    SUM(CASE WHEN invoice_status='UNPAID' THEN sub_total+adjust_fee ELSE 0 END) as totalUnpaidAll
    ")->first();
    // ===============================
    // 🔹 DATA CHART
    // ===============================
    $dataLabel = [];
    $dataTotalBerat = [];
    $dataTotalCust = [];
    $dataTotalIncomeInd = [];
    $dataTotalIncomeCor = [];

    // ===============================
    // 🔹 MODE HARIAN
    // ===============================
    if ($mode == "Harian") {

        $getData = DB::select("
            WITH RECURSIVE tanggal_range AS (
                SELECT DATE(?) AS tgl
                UNION ALL
                SELECT DATE_ADD(tgl, INTERVAL 1 DAY)
                FROM tanggal_range
                WHERE tgl < DATE(?)
            )
            SELECT 
                t.tgl,
                COALESCE(SUM(d.weight),0) as totalBerat,
                COALESCE(COUNT(DISTINCT d.cust_id),0) as totalCust,
                COALESCE(SUM(CASE WHEN d.cust_type_id='IND' THEN d.sub_total+d.adjust_fee ELSE 0 END),0) as totalIncomeInd,
                COALESCE(SUM(CASE WHEN d.cust_type_id='COR' THEN d.sub_total+d.adjust_fee ELSE 0 END),0) as totalIncomeCor
            FROM tanggal_range t
            LEFT JOIN data_list d ON DATE(d.created_at) = t.tgl
            LEFT JOIN warehouse_list w ON w.id = d.warehouse_id
            WHERE (d.doku_link != '' OR d.bank_name != '')
            " . ($warehouse ? " AND d.warehouse_id = '$warehouse'" : "") . "
            " . ($country ? " AND w.country_id = '$country'" : "") . "
            GROUP BY t.tgl
            ORDER BY t.tgl
        ", [$tanggalAwal, $tanggalAkhir]);

        foreach ($getData as $g) {
            $dataLabel[] = date("d-m-Y", strtotime($g->tgl));
            $dataTotalBerat[] = $g->totalBerat;
            $dataTotalCust[] = $g->totalCust;
            $dataTotalIncomeInd[] = $g->totalIncomeInd;
            $dataTotalIncomeCor[] = $g->totalIncomeCor;
        }

    // ===============================
    // 🔹 MODE BULANAN
    // ===============================
    } elseif ($mode == "Bulanan") {

        $year = date("Y", strtotime($tanggalAwal));

        for ($i = 1; $i <= 12; $i++) {

            $query = (clone $baseQuery)
                ->whereYear("data_list.created_at", $year)
                ->whereMonth("data_list.created_at", $i);

            $g = $query->selectRaw("
                COALESCE(SUM(weight),0) as totalBerat,
                COALESCE(COUNT(DISTINCT data_list.cust_id),0) as totalCust,
                COALESCE(SUM(CASE WHEN cust_type_id='IND' THEN sub_total+adjust_fee ELSE 0 END),0) as totalIncomeInd,
                COALESCE(SUM(CASE WHEN cust_type_id='COR' THEN sub_total+adjust_fee ELSE 0 END),0) as totalIncomeCor
            ")->first();

            $dataLabel[] = date("M", mktime(0, 0, 0, $i, 1));
            $dataTotalBerat[] = $g->totalBerat;
            $dataTotalCust[] = $g->totalCust;
            $dataTotalIncomeInd[] = $g->totalIncomeInd;
            $dataTotalIncomeCor[] = $g->totalIncomeCor;
        }

    // ===============================
    // 🔹 MODE TAHUNAN
    // ===============================
    } else {

        $year = date("Y");
        $minYear = max(2023, $year - 10);

        for ($y = $minYear; $y <= $year; $y++) {

            $query = (clone $baseQuery)
                ->whereYear("data_list.created_at", $y);

            $g = $query->selectRaw("
                COALESCE(SUM(weight),0) as totalBerat,
                COALESCE(COUNT(DISTINCT data_list.cust_id),0) as totalCust,
                COALESCE(SUM(CASE WHEN cust_type_id='IND' THEN sub_total+adjust_fee ELSE 0 END),0) as totalIncomeInd,
                COALESCE(SUM(CASE WHEN cust_type_id='COR' THEN sub_total+adjust_fee ELSE 0 END),0) as totalIncomeCor
            ")->first();

            $dataLabel[] = $y;
            $dataTotalBerat[] = $g->totalBerat;
            $dataTotalCust[] = $g->totalCust;
            $dataTotalIncomeInd[] = $g->totalIncomeInd;
            $dataTotalIncomeCor[] = $g->totalIncomeCor;
        }
    }

    return response()->json([
        "totalBeratExport" => $get->totalBeratExport ?? 0,
        "totalBeratIndExport" => $get->totalBeratIndExport ?? 0,
        "totalBeratCorExport" => $get->totalBeratCorExport ?? 0,
        "totalCbmExport" => $get->totalCbmExport ?? 0,
        "totalCbmIndExport" => $get->totalCbmIndExport ?? 0,
        "totalCbmCorExport" => $get->totalCbmCorExport ?? 0,
        "totalCustomerExport" => $get->totalCustExport ?? 0,
        "totalCustomerIndExport" => $get->totalCustIndExport ?? 0,
        "totalCustomerCorExport" => $get->totalCustCorExport ?? 0,
        "totalPendapatanExport" => $get->totalPendapatanExport ?? 0,
        "totalPendapatanForeignExport" => $this->controller->dollarSG($get->totalPendapatanForeignExport ?? 0),
        "totalDiskonExport" => $get->totalDiskonExport ?? 0,
        "totalPendapatanIndExport" => $get->totalPendapatanIndExport ?? 0,
        "totalPendapatanCorExport" => $get->totalPendapatanCorExport ?? 0,
        "totalPendapatanForeignIndExport" => $this->controller->dollarSG($get->totalPendapatanForeignIndExport ?? 0),
        "totalPendapatanForeignCorExport" => $this->controller->dollarSG($get->totalPendapatanForeignCorExport ?? 0),
        "totalDiskonIndExport" => $get->totalDiskonIndExport ?? 0,
        "totalDiskonCorExport" => $get->totalDiskonCorExport ?? 0,
        "totalDiskonRpExport"=>$get->totalDiskonRpExport,
        "totalDiskonIndRpExport"=>$get->totalDiskonIndRpExport,
        "totalDiskonIndSGDExport"=>$this->controller->dollarSG($get->totalDiskonIndSGDExport ?? 0),
        "totalDiskonSGDExport"=>$this->controller->dollarSG($get->totalDiskonSGDExport ?? 0),
        "totalDiskonCorRpExport"=>$get->totalDiskonCorRpExport,
        "totalDiskonCorSGDExport"=>$this->controller->dollarSG($get->totalDiskonCorSGDExport ?? 0),
        "totalPaidExport" => $get->totalPaidExport ?? 0,
        "totalUnpaidExport" => $get->totalUnpaidExport ?? 0,
        "totalPaidForeignExport" => $this->controller->dollarSG($get->totalPaidForeignExport ?? 0),
        "totalUnpaidForeignExport" => $this->controller->dollarSG($get->totalUnpaidForeignExport ?? 0),

        "totalBeratImport" => $get->totalBeratImport ?? 0,
        "totalBeratIndImport" => $get->totalBeratIndImport ?? 0,
        "totalBeratCorImport" => $get->totalBeratCorImport ?? 0,
        "totalCbmImport" => $get->totalCbmImport ?? 0,
        "totalCbmIndImport" => $get->totalCbmIndImport ?? 0,
        "totalCbmCorImport" => $get->totalCbmCorImport ?? 0,
        "totalCustomerImport" => $get->totalCustImport ?? 0,
        "totalCustomerIndImport" => $get->totalCustIndImport ?? 0,
        "totalCustomerCorImport" => $get->totalCustCorImport ?? 0,
        "totalPendapatanImport" => $get->totalPendapatanImport ?? 0,
        "totalPendapatanForeignImport" => $this->controller->dollarSG($get->totalPendapatanForeignImport ?? 0),
        "totalDiskonImport" => $get->totalDiskonImport ?? 0,
        "totalPendapatanIndImport" => $get->totalPendapatanIndImport ?? 0,
        "totalPendapatanCorImport" => $get->totalPendapatanCorImport ?? 0,
        "totalPendapatanForeignIndImport" => $this->controller->dollarSG($get->totalPendapatanForeignIndImport ?? 0),
        "totalPendapatanForeignCorImport" => $this->controller->dollarSG($get->totalPendapatanForeignCorImport ?? 0),
        "totalDiskonIndImport" => $get->totalDiskonIndImport ?? 0,
        "totalDiskonCorImport" => $get->totalDiskonCorImport ?? 0,
        "totalDiskonRpImport"=>$get->totalDiskonRpImport,
        "totalDiskonIndRpImport"=>$get->totalDiskonIndRpImport,
        "totalDiskonIndSGDImport"=>$this->controller->dollarSG($get->totalDiskonIndSGDImport ?? 0),
        "totalDiskonSGDImport"=>$this->controller->dollarSG($get->totalDiskonSGDImport ?? 0),
        "totalDiskonCorRpImport"=>$get->totalDiskonCorRpImport,
        "totalDiskonCorSGDImport"=>$this->controller->dollarSG($get->totalDiskonCorSGDImport ?? 0),
        "totalPaidImport" => $get->totalPaidImport ?? 0,
        "totalUnpaidImport" => $get->totalUnpaidImport ?? 0,
        "totalPaidForeignImport" => $this->controller->dollarSG($get->totalPaidForeignImport ?? 0),
        "totalUnpaidForeignImport" => $this->controller->dollarSG($get->totalUnpaidForeignImport ?? 0),

        "totalBerat" => $get->totalBerat ?? 0,
        "totalBeratInd" => $get->totalBeratInd ?? 0,
        "totalBeratCor" => $get->totalBeratCor ?? 0,
    
        "totalCbm" => $get->totalCbm ?? 0,
        "totalCbmInd" => $get->totalCbmInd ?? 0,
        "totalCbmCor" => $get->totalCbmCor ?? 0,
    
        "totalCustomer" => ($get->totalCustExport ?? 0) + ($get->totalCustImport ?? 0),
        "totalCustomerInd" => ($get->totalCustIndExport ?? 0) + ($get->totalCustIndImport ?? 0),
        "totalCustomerCor" => ($get->totalCustCorExport ?? 0) + ($get->totalCustCorImport ?? 0),
    
        "totalPendapatan" => $get->totalPendapatan ?? 0,
        "totalPendapatanForeign" => $this->controller->dollarSG($get->totalPendapatanForeign ?? 0),
        "totalDiskon" => $get->totalDiskon ?? 0,
    
        "totalPendapatanInd" => $get->totalPendapatanInd ?? 0,
        "totalPendapatanCor" => $get->totalPendapatanCor ?? 0,
    
        "totalPendapatanForeignInd" => $this->controller->dollarSG($get->totalPendapatanForeignInd ?? 0),
        "totalPendapatanForeignCor" => $this->controller->dollarSG($get->totalPendapatanForeignCor ?? 0),
    
        "totalDiskonInd" => $get->totalDiskonInd ?? 0,
        "totalDiskonCor" => $get->totalDiskonCor ?? 0,
        "totalDiskonRp"=>$get->totalDiskonRp,
        "totalDiskonIndRp"=>$get->totalDiskonIndRp,
        "totalDiskonIndSGD"=>$this->controller->dollarSG($get->totalDiskonIndSGD ?? 0),
        "totalDiskonSGD"=>$this->controller->dollarSG($get->totalDiskonSGD ?? 0),
        "totalDiskonCorRp"=>$get->totalDiskonCorRp,
        "totalDiskonCorSGD"=>$this->controller->dollarSG($get->totalDiskonCorSGD ?? 0),
    
        "totalPaid" => $get->totalPaid ?? 0,
        "totalUnpaid" => $get->totalUnpaid ?? 0,
        "totalPaidForeign" => $this->controller->dollarSG($get->totalPaidForeign ?? 0),
        "totalUnpaidForeign" => $this->controller->dollarSG($get->totalUnpaidForeign ?? 0),
    
        "dateRangeTitle" => $mode,
        "dateRange" => date("d M Y", strtotime($tanggalAwal)) . " - " . date("d M Y", strtotime($tanggalAkhir)),
    
        "dataLabel" => $dataLabel,
        "dataTotalBerat" => $dataTotalBerat,
        "dataTotalCust" => $dataTotalCust,
        "dataTotalIncomeInd" => $dataTotalIncomeInd,
        "dataTotalIncomeCor" => $dataTotalIncomeCor
    ]);
}

public function loadTracking($request)
{
    $country  = $request->input("filterCountry");
    $warehouse = $request->input("filterWarehouse");
    $tanggalAwal = $request->input("filterTanggalAwal");
    $tanggalAkhir = $request->input("filterTanggalAkhir");
    $mode = $request->input("mode");
    $kind = $request->input("kind");

    $start = date("Y-m-d 00:00:00", strtotime($tanggalAwal));
    $end   = date("Y-m-d 23:59:59", strtotime($tanggalAkhir));

    if ($mode == 'Harian') {
        $selectDate = "DATE(s.to_sg_man_created_at)";
    } elseif ($mode == 'Bulanan') {
        $selectDate = "DATE_FORMAT(s.to_sg_man_created_at, '%Y-%m')";
    } else {
        $selectDate = "DATE_FORMAT(s.to_sg_man_created_at, '%Y')";
    }

    // =========================================
    // 🔹 BASE AR (TANPA JOIN BERAT)
    // =========================================
    $baseAR = DB::table("shiptrip_list as s")
        ->join("service_list as sv", "sv.id", "=", "s.service_id")
        ->join("warehouse_list as w", "w.id", "=", "sv.warehouse_id")
        ->join("cust_list as c", "c.id", "=", "s.cust_id")
        ->whereBetween("s.to_sg_man_created_at", [$start, $end])
        ->when($warehouse, fn($q) => $q->where("sv.warehouse_id", $warehouse))
        ->when($country, fn($q) => $q->where("w.country_id", $country));

    // =========================================
    // 🔹 KIND AR (SHIPMENT & REDLINE)
    // =========================================
    if ($kind == "AR") {

        $summary = (clone $baseAR)->selectRaw("
            COUNT(DISTINCT s.ms_track_id) as totalResi,

            COUNT(DISTINCT CASE WHEN c.cust_type_id='IND' THEN s.ms_track_id END) as totalResiInd,
            COUNT(DISTINCT CASE WHEN c.cust_type_id='COR' THEN s.ms_track_id END) as totalResiCor,

            COUNT(DISTINCT CASE 
                WHEN s.redline_created_at!='0000-00-00 00:00:00' 
                THEN s.ms_track_id 
            END) as totalResiRedline,

            COUNT(DISTINCT CASE 
                WHEN s.redline_created_at!='0000-00-00 00:00:00' 
                AND c.cust_type_id='IND'
                THEN s.ms_track_id 
            END) as totalResiRedlineInd,

            COUNT(DISTINCT CASE 
                WHEN s.redline_created_at!='0000-00-00 00:00:00' 
                AND c.cust_type_id='COR'
                THEN s.ms_track_id 
            END) as totalResiRedlineCor
        ")->first();

        $rows = (clone $baseAR)
            ->selectRaw("
                $selectDate as tgl,
                COUNT(DISTINCT s.ms_track_id) as totalResi,
                COUNT(DISTINCT CASE 
                    WHEN s.redline_created_at!='0000-00-00 00:00:00' 
                    THEN s.ms_track_id 
                END) as totalRedline
            ")
            ->groupBy("tgl")
            ->orderBy("tgl")
            ->get()
            ->keyBy("tgl");

        [$dataLabel, $dataTotalResi, $dataTotalRedline] = $this->buildDailyChart(
            $tanggalAwal,
            $tanggalAkhir,
            $rows,
            $mode,
            'totalResi',
            'totalRedline'
        );

        return response()->json([
            "totalResi" => $summary->totalResi ?? 0,
            "totalResiInd" => $summary->totalResiInd ?? 0,
            "totalResiCor" => $summary->totalResiCor ?? 0,
            "totalResiRedline" => $summary->totalResiRedline ?? 0,
            "totalResiRedlineInd" => $summary->totalResiRedlineInd ?? 0,
            "totalResiRedlineCor" => $summary->totalResiRedlineCor ?? 0,

            "dataKind" => $kind,
            "dateRangeTitle" => $mode,
            "dateRange" => $this->formatDateRange($tanggalAwal, $tanggalAkhir),

            "dataLabel" => $dataLabel,
            "dataTotalResi" => $dataTotalResi,
            "dataTotalRedline" => $dataTotalRedline
        ]);
    }

    // =========================================
    // 🔹 KIND PS (PROSES & SUKSES)
    // =========================================
    if ($kind == "PS") {



        $sub = DB::table("shiptrip_list as s")
    ->join("service_list as sv", "sv.id", "=", "s.service_id")
    ->join("warehouse_list as w", "w.id", "=", "sv.warehouse_id")
    ->join("cust_list as c", "c.id", "=", "s.cust_id")
    ->leftJoin("order_list as o", "o.ms_track_id", "=", "s.ms_track_id")
    ->leftJoin(DB::raw("
        (
            SELECT  
                mismass_invoice_id,

                SUBSTRING_INDEX(
                    GROUP_CONCAT(shipping_number ORDER BY id ASC),
                    ',', 1
                ) as first_shipping_number,

                SUBSTRING_INDEX(
                    GROUP_CONCAT(track_status_id ORDER BY id ASC),
                    ',', 1
                ) as first_status,

                SUM(CASE WHEN track_status_id IN (13,22) THEN 1 ELSE 0 END) as totalSuccess,
                COUNT(*) as totalShip

            FROM data_list
            GROUP BY mismass_invoice_id
        ) d
    "), "d.mismass_invoice_id", "=", "o.invoice_id")
    ->whereBetween("s.to_sg_man_created_at", [$start, $end])
    ->when($warehouse, fn($q) => $q->where("sv.warehouse_id", $warehouse))
    ->when($country, fn($q) => $q->where("w.country_id", $country))
    ->selectRaw("
        s.ms_track_id,
        $selectDate as tgl,
        c.cust_type_id,
        o.invoice_id,

        COALESCE(d.first_shipping_number, '') as first_shipping_number,
        COALESCE(d.first_status, 0) as first_status,
        COALESCE(d.totalSuccess, 0) as totalSuccess,
        COALESCE(d.totalShip, 0) as totalShip
    ");

    $summary = DB::query()->fromSub($sub, "x")
    ->selectRaw("
        SUM(
            CASE 
                WHEN (
                    x.invoice_id IS NOT NULL 
                    AND x.invoice_id != ''
                    AND x.first_shipping_number != ''
                    AND (
                        (x.cust_type_id='IND' AND x.first_status IN (13,22))
                        OR (x.cust_type_id='COR' AND x.totalSuccess = x.totalShip)
                    )
                )
                THEN 1 ELSE 0 
            END
        ) as totalSukses,

        SUM(
            CASE 
                WHEN NOT (
                    x.invoice_id IS NOT NULL 
                    AND x.invoice_id != ''
                    AND x.first_shipping_number != ''
                    AND (
                        (x.cust_type_id='IND' AND x.first_status IN (13,22))
                        OR (x.cust_type_id='COR' AND x.totalSuccess = x.totalShip)
                    )
                )
                THEN 1 ELSE 0 
            END
        ) as totalProses,

        SUM(
            CASE 
                WHEN (
                    x.invoice_id IS NOT NULL 
                    AND x.invoice_id != ''
                    AND x.first_shipping_number != ''
                    AND x.first_status IN (13,22)
                ) AND x.cust_type_id='IND'
                THEN 1 ELSE 0 
            END
        ) as totalSuksesInd,

        SUM(
            CASE 
                WHEN (
                    x.invoice_id IS NOT NULL 
                    AND x.invoice_id != ''
                    AND x.first_shipping_number != ''
                    AND x.totalSuccess = x.totalShip
                ) AND x.cust_type_id='COR'
                THEN 1 ELSE 0 
            END
        ) as totalSuksesCor,

        SUM(
            CASE 
                WHEN NOT (
                    x.invoice_id IS NOT NULL 
                    AND x.invoice_id != ''
                    AND x.first_shipping_number != ''
                    AND (
                        (x.cust_type_id='IND' AND x.first_status IN (13,22))
                        OR (x.cust_type_id='COR' AND x.totalSuccess = x.totalShip)
                    )
                ) AND x.cust_type_id='IND'
                THEN 1 ELSE 0 
            END
        ) as totalProsesInd,

        SUM(
            CASE 
                WHEN NOT (
                    x.invoice_id IS NOT NULL 
                    AND x.invoice_id != ''
                    AND x.first_shipping_number != ''
                    AND (
                        (x.cust_type_id='IND' AND x.first_status IN (13,22))
                        OR (x.cust_type_id='COR' AND x.totalSuccess = x.totalShip)
                    )
                ) AND x.cust_type_id='COR'
                THEN 1 ELSE 0 
            END
        ) as totalProsesCor
    ")
    ->first();

    $rows = DB::query()->fromSub($sub, "x")
    ->selectRaw("
        x.tgl,

        SUM(
            CASE 
                WHEN (
                    x.invoice_id IS NOT NULL 
                    AND x.invoice_id != ''
                    AND x.first_shipping_number != ''
                    AND (
                        (x.cust_type_id='IND' AND x.first_status IN (13,22))
                        OR (x.cust_type_id='COR' AND x.totalSuccess = x.totalShip)
                    )
                )
                THEN 1 ELSE 0 
            END
        ) as totalSukses,

        SUM(
            CASE 
                WHEN NOT (
                    x.invoice_id IS NOT NULL 
                    AND x.invoice_id != ''
                    AND x.first_shipping_number != ''
                    AND (
                        (x.cust_type_id='IND' AND x.first_status IN (13,22))
                        OR (x.cust_type_id='COR' AND x.totalSuccess = x.totalShip)
                    )
                )
                THEN 1 ELSE 0 
            END
        ) as totalProses
    ")
    ->groupBy("x.tgl")
    ->orderBy("x.tgl")
    ->get()
    ->keyBy("tgl");

        [$dataLabel, $dataTotalProses, $dataTotalSukses] = $this->buildDailyChart(
            $tanggalAwal,
            $tanggalAkhir,
            $rows,
            $mode,
            'totalProses',
            'totalSukses'
        );

        return response()->json([
            "totalResiSukses" => $summary->totalSukses ?? 0,
            "totalResiSuksesInd" => $summary->totalSuksesInd ?? 0,
            "totalResiSuksesCor" => $summary->totalSuksesCor ?? 0,

            "totalResiProses" => $summary->totalProses ?? 0,
            "totalResiProsesInd" => $summary->totalProsesInd ?? 0,
            "totalResiProsesCor" => $summary->totalProsesCor ?? 0,

            "dataKind" => $kind,
            "dateRangeTitle" => $mode,
            "dateRange" => $this->formatDateRange($tanggalAwal, $tanggalAkhir),

            "dataLabel" => $dataLabel,
            "dataTotalProses" => $dataTotalProses,
            "dataTotalSukses" => $dataTotalSukses
        ]);
    }
}

private function buildDailyChart($start, $end, $rows, $mode, $field1, $field2)
{
    $labels = [];
    $data1 = [];
    $data2 = [];

    // konfigurasi mode
    if ($mode == 'Harian') {
        $interval = 'P1D';
        $keyFormat = 'Y-m-d';
        $labelFormat = 'd-m-Y';
        $startDate = new \DateTime($start);
        $endDate = (new \DateTime($end))->modify('+1 day');

    } elseif ($mode == 'Bulanan') {
        $interval = 'P1M';
        $keyFormat = 'Y-m';
        $labelFormat = 'M Y';
        $startDate = new \DateTime(date('Y-m-01', strtotime($start)));
        $endDate = (new \DateTime(date('Y-m-01', strtotime($end))))->modify('+1 month');

    } else {
        $interval = 'P1Y';
        $keyFormat = 'Y';
        $labelFormat = 'Y';
        $startDate = new \DateTime(date('Y-01-01', strtotime($start)));
        $endDate = (new \DateTime(date('Y-01-01', strtotime($end))))->modify('+1 year');

    }

    $period = new \DatePeriod(
        $startDate,
        new \DateInterval($interval),
        $endDate
    );

    foreach ($period as $date) {
        $key = $date->format($keyFormat);

        $labels[] = $date->format($labelFormat);
        $data1[] = $rows[$key]->$field1 ?? 0;
        $data2[] = $rows[$key]->$field2 ?? 0;
    }

    return [$labels, $data1, $data2];
}

private function formatDateRange($start, $end)
{
    return date("d M Y", strtotime($start)) . " - " . date("d M Y", strtotime($end));
}




}
