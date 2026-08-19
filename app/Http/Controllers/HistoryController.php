<?php

namespace App\Http\Controllers;

use App\Models\History;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index()
    {
        if(!env('HISTORY_LIST')){
            if(Auth::user()->username!="dev8th"){
                return view('pages.maintenance');
            }
        }
        return view('pages.history');
    }

    public function table(Request $request)
    {
        $data = [];
        $no = $request->input('start');
        $search = $request->input('search')['value'];
        $historyModel = new History();
        $lists = $historyModel->getDT($request, $search);

        foreach ($lists as $list) {
            $getRank = DB::table('users')->where("username",$list->created_by)->value("rank");
            $jabatan = $getRank != null ? "<div class='bg-mismass' style='padding:1px 5px'>".$getRank."</div>" : "";

            $no++;
            $row = [];
            $row[] = $no;
            $row[] = "<div style='font-weight:700'>" . $list->created_by . "</div>".$jabatan."<div>" . $this->dateFormatIndo($list->created_at,2) . "</div>";
            $row[] = $this->simpleDesc($list->description,$list->id);
            $row[] = $list->whatsapp_desc==""?"-":$list->whatsapp_desc;
            $row[] = "<a href='".url("/history/list/".$list->id)."' target='_blank'>Lihat Detail</a>";

            $data[] = $row;
        }

        $output = [
            'draw' => $request->input('draw'),
            'recordsTotal' => $historyModel->countAll(),
            'recordsFiltered' => $historyModel->countFiltered($request, $search),
            'data' => $data
        ];

        return json_encode($output);
    }

    public function list(string $id){
        $data['history'] = DB::table("history_list")->where("id",$id)->get();
        if(count($data['history'])<1){
            abort(404);
        }
        return view('printout.history', $data);
    }
}
