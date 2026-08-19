<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;  
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;


class ShippingExport implements FromView, WithStyles, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $id;

    function __construct($id) {
            $this->id = $id;
    }

    public function view(): View
    {
        $data['shipping'] = DB::table("data_list")
        ->whereRaw("mismass_invoice_id LIKE '%$this->id'")
        ->get();

        if(count($data['shipping'])<1 || strlen($this->id)<8){
            abort(404);
        }

        return view('export.exampleshipimp',$data);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A2:J3')
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN)
                ->setColor(new Color('00000000'));
        $sheet->getStyle('D')
                ->getAlignment()
                ->setWrapText(true);
    }
}
