<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ApiJneController extends Controller
{
  
  public function webhook()
  {
      //cek secret id apakah sama
      
      //
  }
  
  public function uat(Request $request)
  {
    $ip = $request->header('CF-Connecting-IP') ?? $request->header('X-Forwarded-For') ?? $request->ip();
    $headers = $request->headers->all();
    $body = $request->getContent();

    $content  = "=== CLIENT INFO ===\n";
    $content .= "IP Address: {$ip}\n";
    $content .= "Received At: " . now() . "\n";

    $content .= "\n=== HEADERS ===\n";
    foreach ($headers as $key => $value) {
        $content .= $key . ': ' . implode(', ', $value) . "\n";
    }

    $content .= "\n=== BODY ===\n";
    $content .= $body;

    $filename = 'jne_' . now()->format('Ymd_His') . '.txt';

    Storage::disk('local')->put(
        'jne/' . $filename,
        $content
    );

    return response()->json(['status' => 'ok'], 200);
  }
    
}

?>