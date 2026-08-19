<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
use Alert;

class LoginController extends Controller
{
    public function login()
    {
        return view('login');
    }

    public function auth(Request $request)
    {
        $data = [
            'username' => $request->input('username'),
            'password' => $this->saltThis($request->input('password')),
        ];

        $remember = $request->input('remember') == "on" ? true : false;

        if (!Auth::attempt($data, $remember)) {
            return redirect('/login')->with('status', 'errors');
        }

        if(Auth::user()->role_id=="876384"){
            return redirect('/login')->with('status', 'errors');
        }
        
        return redirect('/');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
}
