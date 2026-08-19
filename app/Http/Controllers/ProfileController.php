<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Users;

class ProfileController extends Controller
{
    public function index()
    {
        $username = Auth::user()->username;

        $role = DB::table('users')
            ->join('role_list', 'role_list.id', '=', 'users.role_id')
            ->select('role_list.name')
            ->where('users.username', '=', $username)
            ->first();

        $data = [
            'username' => $this->ifEmpty($username),
            'role_name' => $role->name,
            'fullname' => $this->ifEmpty(Auth::user()->fullname),
            'phone' => $this->ifEmpty(Auth::user()->phone),
            'address' => $this->ifEmpty(Auth::user()->address),
            'email' => $this->ifEmpty(Auth::user()->email),
            'referral' => Auth::user()->ref_code
        ];
        return view('pages.profile', $data);
    }

    public function gantipass(Request $request)
    {
        $username = Auth::user()->username;
        $passlama = $this->saltThis($request->input("passlama"));
        $passbaru = Hash::make($this->saltThis($request->input("passbaru")));

        $userModel = new Users;
        $result = $userModel::where("username", "=", $username)->get();
        foreach ($result as $r) {
            $hashedpassword = $r->password;
        }

        $encode = array("status" => "Gagal", "text" => "Gagal Ganti Password");
        if (Hash::check($passlama, $hashedpassword)) {
            $update = $userModel::where("username", "=", $username)->update(['password' => $passbaru]);

            if ($update) {
                $encode = array("status" => 200, "text" => "Berhasil Ganti Password", "url" => url('/profile'));
            }
        }

        return json_encode($encode);
    }
}
