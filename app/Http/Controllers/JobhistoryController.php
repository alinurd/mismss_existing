<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class JobhistoryController extends Controller
{
    public function index(){
        return view('pages.jobhistory');
    }
}
