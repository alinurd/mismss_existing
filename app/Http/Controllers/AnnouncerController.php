<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AnnouncerController extends Controller
{
    public function updateAnnouncer(Request $request){
        $text = $request->input("text");

        $affected = DB::table("announcer")
        ->update([
            "text" => $text,
        ]);

        if($affected==0){
            $encode = array("status" => 500, "title" => "Tidak Ada Perubahan", "text" => "Announcer Text Tidak Mengalami Perubahan");
            return json_encode($encode);
        }

        $encode = array(
            "status" => 200, 
            "title" => "Berhasil", 
            "text" => "Update Announcer Sukses!", 
            "data" => $text
        );
        return json_encode($encode);
    }
}
