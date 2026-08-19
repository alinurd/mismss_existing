<?php

namespace App\Exports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use App\Http\Controllers\Controller;
use App\Models\Afunction;

class DataListExport implements FromQuery, WithMapping, WithHeadings, WithEvents
{
    protected $filters;
    protected $rowNumbers = 0;
    protected $controller;
    protected $afunction;
    protected $order = ["data_list.mismass_invoice_date","asc"];
    protected $groupBy = "data_list.mismass_invoice_id";

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
        $this->controller = new Controller;
        $this->afunction = new Afunction;
    }

    public function query()
    {
        $query = Invoice::query();
        $custTypeId = $this->filters['custTypeId'];
        $warehouseId = $this->filters['warehouseId'];
        $tanggalAwal = $this->filters['tanggalAwal'];
        $tanggalAkhir = $this->filters['tanggalAkhir'];

        $fltrs = [
            $custTypeId,
            $warehouseId,
            $tanggalAwal,
            $tanggalAkhir
        ];

        $query->selectRaw(
            "data_list.*,
            warehouse_list.id as wareid,
            warehouse_list.name as warename,
            warehouse_list.location as wareloc,
            cust_list.reference,
            (SELECT SUM(data_list.weight) FROM data_list WHERE data_list.mismass_invoice_id=order_list.invoice_id) as total_berat,
            (SELECT SUM(data_list.item) FROM data_list WHERE data_list.mismass_invoice_id=order_list.invoice_id) as total_item,
            (SELECT SUM(data_list.cbm) FROM data_list WHERE data_list.mismass_invoice_id=order_list.invoice_id) as total_cbm,
            (SELECT SUM(data_list.discount) FROM data_list WHERE data_list.mismass_invoice_id=order_list.invoice_id) as total_diskon,
            (SELECT SUM(data_list.sub_total) FROM data_list WHERE data_list.mismass_invoice_id=order_list.invoice_id) as total_subtotal,
            (SELECT SUM(data_list.additional_nom) FROM data_list WHERE data_list.mismass_invoice_id=order_list.invoice_id) as additionalNom,
            (SELECT SUM(data_list.packing_total) FROM data_list WHERE data_list.mismass_invoice_id=order_list.invoice_id) as packingTotal,
            (SELECT SUM(data_list.import_permit_total) FROM data_list WHERE data_list.mismass_invoice_id=order_list.invoice_id) as importPermit,
            (SELECT SUM(data_list.document_total) FROM data_list WHERE data_list.mismass_invoice_id=order_list.invoice_id) as documentTotal,
            (SELECT SUM(data_list.dr_medicine_total) FROM data_list WHERE data_list.mismass_invoice_id=order_list.invoice_id) as medicineTotal,
            (SELECT SUM(data_list.insurance_total) FROM data_list WHERE data_list.mismass_invoice_id=order_list.invoice_id) as insuranceTotal,
            (SELECT SUM(data_list.fee_total) FROM data_list WHERE data_list.mismass_invoice_id=order_list.invoice_id) as feeTotal,
            (SELECT SUM(data_list.tax_total) FROM data_list WHERE data_list.mismass_invoice_id=order_list.invoice_id) as taxTotal,
            (SELECT SUM(data_list.extra_cost_price) FROM data_list WHERE data_list.mismass_invoice_id=order_list.invoice_id) as extraCost,
            (SELECT SUM(data_list.pickup_charge) FROM data_list WHERE data_list.mismass_invoice_id=order_list.invoice_id) as pickupCharge,
            (SELECT COUNT(data_list.cust_id) FROM data_list WHERE data_list.cust_id=cust_list.id) as jumlah_kirim,
            CONCAT_WS(' ',data_list.cons_first_name,data_list.cons_middle_name,data_list.cons_last_name) as cons_full_name,
            CONCAT_WS(', ',data_list.cons_district,data_list.cons_city) as address"
        );
        $query->join("cust_list","cust_list.id","=","data_list.cust_id");
        $query->join("warehouse_list","warehouse_list.id","=","data_list.warehouse_id");
        $query->join("order_list","order_list.id","=","data_list.mismass_order_id");
        $query->where("data_list.cust_type_id",$custTypeId);
        $query->where("data_list.warehouse_id",$warehouseId);
        $query->whereRaw("mismass_invoice_date BETWEEN '$tanggalAwal' AND '$tanggalAkhir'");
        $query->where("doku_link","!=","");
        $query->orWhere(function ($query) use ($fltrs) {
            $query->where("data_list.cust_type_id",$fltrs[0])
                ->where("data_list.warehouse_id",$fltrs[1])
                ->whereRaw("mismass_invoice_date BETWEEN '$fltrs[2]' AND '$fltrs[3]'")
                ->where("bank_name","!=","");
        });
        $query->orderBy($this->order[0],$this->order[1]);
        $query->groupBy($this->groupBy);

        return $query;
    }

    public function map($row): array
    {
        $this->rowNumbers++;
        $additional = $row->additionalNom+$row->packingTotal+$row->importPermit+$row->documentTotal+$row->medicineTotal+$row->insuranceTotal+$row->feeTotal+$row->taxTotal+$row->extraCost+$row->pickupCharge;

        return [
            [
                $this->rowNumbers, //1
                $row->mismass_invoice_id,
                $row->created_by,
                $row->invoice_status,
                DataListExport::shippingCreatedBy($row->shipping_created_by),
                DataListExport::getForwarder($row->forwarder_id,$row->forwarder_name),
                DataListExport::getReference($row->reference), //7
                $row->cons_full_name,
                $row->service_name,
                round($row->total_berat,2),
                $row->total_item,
                $row->total_cbm, //12
                $row->total_cbm*100,
                $this->controller->rupiah($additional),
                $this->controller->rupiah($row->total_diskon),
            ],
            [
                "", //1
                $this->controller->dateFormatIndo($row->mismass_invoice_date,3),
                $this->controller->dateFormatIndo($row->created_at,1),
                $this->controller->getSuccessTime($row->mismass_invoice_id),
                DataListExport::shippingCreatedAt($row->shipping_created_at),
                $row->shipping_number ?? "-",
                DataListExport::getCustStatus($row->jumlah_kirim),//7
                "'".$row->cons_phone,
                "Total ".$this->afunction->getTotalServiceByInvoice($row->mismass_invoice_id)." Invoice",
                "",
                "",
                "", //12
                "",
                "",
                "",
            ],
            [
                "", //1
                "Link Invoice",
                "",
                "",
                "",
                DataListExport::shippingCreatedAt($row->shipping_created_at),
                "", //7
                $row->address,
                "",
                "",
                "",
                "", //12
                "",
                "",
                "",
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'No.',
            'No. Invoice',
            'Create Invoice By',
            'Pembayaran',
            'Create Resi By',
            'No. Resi',
            'Reference',
            'Data Customer',
            'Service',
            'Berat(Kg)',
            'Item/Box',
            'CBM',
            'CBM(kgs)',
            'Additional',
            'Diskon',
            'Jumlah',
            'Total Biaya',
            'Komisi Packer',
            'Komisi Driver',
            'Profit',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                for($i=0;$i<8;$i++){
                    $event->sheet->insertNewRowBefore(1, 1);
                }
                $event->sheet->setCellValue("A1", DataListExport::titleMaker($this->filters));
                $event->sheet->mergeCells("A1:L1");
                $event->sheet->getStyle("A1")->applyFromArray([
                    "font" => [
                        "bold" => true,
                        "size" => 20,
                    ],
                    "alignment" => [
                        "horizontal" => Alignment::HORIZONTAL_CENTER,
                    ],
                    // "fill" => [
                    //     "fillType" => Fill::FILL_SOLID,
                        // "color" => ["argb" => "FFE0E0E0"], // Light grey background
                    // ],
                ]);
                $event->sheet->setCellValue("A3","Total Pendapatan");
                $event->sheet->setCellValue("B3","Rp.1.000.000");
                $event->sheet->setCellValue("A4","Total PAID");
                $event->sheet->setCellValue("B4","Rp.1.000.000");
                $event->sheet->setCellValue("A5","Total UNPAID");
                $event->sheet->setCellValue("B5","Rp.1.000.000");
                $event->sheet->setCellValue("A6","Total Diskon");
                $event->sheet->setCellValue("B6","Rp.1.000.000");

                $event->sheet->setCellValue("C3","Total Komisi Packer");
                $event->sheet->setCellValue("D3","Rp.1.000.000");
                $event->sheet->setCellValue("C4","Total Komisi Driver");
                $event->sheet->setCellValue("D4","Rp.1.000.000");

                $event->sheet->setCellValue("E3","Total Berat (Kg)");
                $event->sheet->setCellValue("F3","20");
                $event->sheet->setCellValue("E4","Total Item/Box");
                $event->sheet->setCellValue("F4","14");
                $event->sheet->setCellValue("E5","Total CBM");
                $event->sheet->setCellValue("F5","0");
                $event->sheet->setCellValue("E6","Total CBM (kgs)");
                $event->sheet->setCellValue("F6","0");

                $event->sheet->setCellValue("G3","Total Profit");
                $event->sheet->setCellValue("H3","Rp.1.000.000");
                $event->sheet->setCellValue("G4","Profit sudah dikurangi UNPAID dan Diskon, belum dikurangi Komisi AE dan biaya lainnya.");
                $event->sheet->mergeCells("G4:H6");

                $event->sheet->setCellValue("A8","Data Shipment Periode ".$this->controller->dateFormatIndo($this->filters['tanggalAwal'])." - ".$this->controller->dateFormatIndo($this->filters['tanggalAkhir'])." (Berdasarkan Tanggal Invoice)");
                $event->sheet->mergeCells("A8:L8");
                $event->sheet->getStyle("A8")->applyFromArray([
                    "font" => [
                        "bold" => true,
                        "size" => 14,
                    ],
                    "alignment" => [
                        "horizontal" => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);
                $event->sheet->getStyle('A9:T9')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                ]);

                $n = 10;
                for($i=1;$i<=$this->rowNumbers;$i++){
                    $event->sheet->mergeCells("A".$n.":A".($n+2));
                    $event->sheet->mergeCells("J".$n.":J".($n+2));
                    $event->sheet->mergeCells("K".$n.":K".($n+2));
                    $event->sheet->mergeCells("L".$n.":L".($n+2));
                    $event->sheet->mergeCells("M".$n.":M".($n+2));
                    $event->sheet->mergeCells("N".$n.":N".($n+2));
                    $event->sheet->mergeCells("O".$n.":O".($n+2));
                    $event->sheet->mergeCells("P".$n.":P".($n+2));
                    $event->sheet->mergeCells("Q".$n.":Q".($n+2));
                    $event->sheet->getStyle("A".$n)->applyFromArray([
                        "alignment" => [
                            "horizontal" => Alignment::HORIZONTAL_CENTER,
                            "vertical" => Alignment::VERTICAL_CENTER,
                        ],
                    ]);
                    $n+=3;
                }
            },
        ];
    }

    private function titleMaker($filters)
    {
        if($filters['custTypeId']=="IND"){
            return "INDIVIDUAL SHIPMENT BY WAREHOUSE | ".$filters['warehouseId'];
        }

        return "CORPORATE SHIPMENT BY WAREHOUSE | ".$filters['warehouseId'];
    }

    private function shippingCreatedAt($v){
        if($v!="0000-00-00 00:00:00"){
            return $this->controller->dateFormatIndo($v,3);
        }
        return "-";
    }

    private function shippingCreatedBy($v){
        if($v!=""){
            return $v;
        }
        return "-";
    }

    private function getForwarder($id,$name){
        $value = $id!="" ? ( $id=="PICK-UP" ? "PICKUP SENDIRI" : ( $id=="MISMASS" ? $id : $name )) : "-";
        return $value;
    }

    private function getReference($r){
        if($r!=0){
            $value = $this->controller->getReferenceFullName($r);
            return $value;
        }
        return "-";
    }

    private function getCustStatus($jk){
        if($jk>1){
            $value = "Customer : OC";
            return $value;
        }
        return "Customer : NC";
    }
}
