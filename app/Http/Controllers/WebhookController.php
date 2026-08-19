<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\SentralCargoWebhookService;
use App\Services\JneWebhookService;
use App\Services\WebhookService;

class WebhookController extends Controller
{
    protected $sentralkar,$jne,$webhook;

    public function __construct()
    {
        $this->sentralkar = new SentralCargoWebhookService;
        $this->jne = new JneWebhookService;
        $this->webhook = new WebhookService;
    }

    public function table(Request $request){        
        $array = [
            "request" => $request,
            "search" => $request->input('search')['value'],
            "no" => $request->input('start'),
            "id" => $request->input("id")
        ];

        $data = $this->webhook->getDT($array);

        $output = [
            'draw' => $request->input('draw'),
            'recordsTotal' => $this->webhook->countAll($request->input("id")),
            'recordsFiltered' => $this->webhook->countFiltered($array),
            'data' => $data
        ];

        return json_encode($output);
    }

    public function list(Request $request,string $id){
        $vendor = $request->input("vendor");
        $data['vendor'] = strtoupper($vendor);
        $data['webhook'] = $this->webhook->getDataById($vendor,$id);
        if(count($data['webhook'])<1){
            abort(404);
        }
        return view('printout.webhook', $data);
    }

    public function SBSentralCargo(Request $request)
    {
        // if(!$this->sentralkar->checkIp($request)){
        //     return response()->json(['status' => 'unauthorized'], 401);
        // }

        // if (!$this->sentralkar->validatePayload($request)) {
        //     return response()->json(['status' => 'invalid'], 422);
        // }

        // if ($this->sentralkar->isDuplicate($request)) {
        //     return response()->json([
        //         'status' => 'ignored',
        //         'reason' => 'duplicate'
        //     ], 200);
        // }

        // $this->sentralkar->saveLog($request);

        // $updateStatus = $this->sentralkar->updateStatusTracking($request);
        // if(!$updateStatus['status']){
        //     return response()->json(['message' => $updateStatus['message']], 200);
        // }

        // return response()->json(['status' => 'ok'], 200);
    }

    public function sentralCargo(Request $request)
    {
        if(!$this->sentralkar->checkIp($request)){
            return response()->json(['status' => 'unauthorized'], 401);
        }

        if (!$this->sentralkar->validatePayload($request)) {
            return response()->json(['status' => 'invalid'], 422);
        }

        if ($this->sentralkar->isDuplicate($request)) {
            return response()->json([
                'status' => 'ignored',
                'reason' => 'duplicate'
            ], 200);
        }

        $this->sentralkar->saveLog($request);

        $updateStatus = $this->sentralkar->updateStatusTracking($request);
        if(!$updateStatus['status']){
            return response()->json(['message' => $updateStatus['message']], 200);
        }

        return response()->json(['status' => 'ok'], 200);
    }

    public function SBJne(Request $request)
    {
        // if(!$this->jne->checkIp($request)){
        //     return response()->json(['status' => 'unauthorized'], 401);
        // }

        // if (!$this->jne->validatePayload($request)) {
        //     return response()->json(['status' => 'invalid'], 422);
        // }

        // if ($this->jne->isDuplicate($request)) {
        //     return response()->json([
        //         'status' => 'ignored',
        //         'reason' => 'duplicate'
        //     ], 200);
        // }

        // $this->jne->saveLog($request);

        // $updateStatus = $this->jne->updateStatusTracking($request);
        // if(!$updateStatus['status']){
        //     return response()->json(['message' => $updateStatus['message']], 200);
        // }

        // return response()->json(['status' => 'ok'], 200);
    }

    public function Jne(Request $request)
    {
        // $ip = $request->header('CF-Connecting-IP') ?? $request->header('X-Forwarded-For') ?? $request->ip();
        // $headers = $request->headers->all();
        // $body = $request->getContent();
    
        // $content  = "=== CLIENT INFO ===\n";
        // $content .= "IP Address: {$ip}\n";
        // $content .= "Received At: " . now() . "\n";
    
        // $content .= "\n=== HEADERS ===\n";
        // foreach ($headers as $key => $value) {
        //     $content .= $key . ': ' . implode(', ', $value) . "\n";
        // }
    
        // $content .= "\n=== BODY ===\n";
        // $content .= $body;
    
        // $filename = 'live_jne_' . now()->format('Ymd_His') . '.txt';
    
        // Storage::disk('local')->put(
        //     'jne/' . $filename,
        //     $content
        // );
    
        // return response()->json(['status' => true], 200);

        if(!$this->jne->checkIp($request)){
            return response()->json(['status' => 'unauthorized'], 401);
        }

        if (!$this->jne->validatePayload($request)) {
            return response()->json(['status' => 'invalid'], 422);
        }

        if ($this->jne->isDuplicate($request)) {
            return response()->json([
                'status' => 'ignored',
                'reason' => 'duplicate'
            ], 200);
        }

        $this->jne->saveLog($request);

        $updateStatus = $this->jne->updateStatusTracking($request);
        if(!$updateStatus['status']){
            return response()->json(['message' => $updateStatus['message']], 200);
        }

        return response()->json(['status' => 'ok'], 200);
    }
}
