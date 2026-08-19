<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TroubleshootController extends Controller
{
    public function index(){
        return view("pages.maintenance");
    }
}
