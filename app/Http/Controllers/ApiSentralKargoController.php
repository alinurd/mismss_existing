<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\ApiSentralKargo;

class ApiSentralKargoController extends Controller
{
    private $apiSentralKargo;

    public function __construct()
    {
        $this->apiSentralKargo = new ApiSentralKargo;
    }
  
    public function webhook(Request $request)
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
    
        $filename = 'live_sk_' . now()->format('Ymd_His') . '.txt';
    
        Storage::disk('local')->put(
            'sentralkargo/' . $filename,
            $content
        );
    
        return response()->json(['status' => 'ok'], 200);
      
    }
    
    public function sb(Request $request)
    {
        //Cek ip pengirim
        // $ip = $request->header('CF-Connecting-IP') ?? $request->header('X-Forwarded-For') ?? $request->ip();
        // $checkIp = $this->apiSentralKargo->checkIp($ip);
        // if(!$checkIp){
        //     return response()->json(['status' => 'Unauthorized'], 401);
        // }
        
        //Validation
        $checkValid = $this->apiSentralKargo->validation($request);
        if(!$checkValid){
            return response()->json(['status' => 'Invalid Data'], 422);
        }
        
        //cek sentralkar_webhook_list apakah ada uniqid (SHA256)
        $checkLog = $this->apiSentralKargo->checkLog($request);
        if(!$checkLog){
            return response()->json([
                        'status' => 'ignored',
                        'reason' => 'duplicate'
                    ], 200);
        }
        
        //Update Data Tracking
        // $updateTracking = $this->apiSentralKargo->updateStatusTracking($request);
        
        return response()->json(['status' => 'ok'], 200);
    }
    
}

?>