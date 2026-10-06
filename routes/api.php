<?php

use Illuminate\Http\Request;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\AppController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ApiTrackingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::post('/apijne/webhook', [WebhookController::class, 'Jne']);
Route::post('/apijne/webhook/sandbox', [WebhookController::class, 'SBJne']);

Route::post('/sentralkar/webhook', [WebhookController::class, 'sentralCargo']);
Route::post('/sentralkar/webhook/sandbox', [WebhookController::class, 'SBSentralCargo']);


/*
|--------------------------------------------------------------------------
| Shipment tracking (read-only, API key `key-tracking-...` via X-API-Key)
|--------------------------------------------------------------------------
| GET /api/tracking?id=MMS45219783EUID  (header X-API-Key: key-tracking-xxxx)
| Pure JSON (no HTML), served by ApiTrackingController. Does not modify or share
| code paths with the existing /shiptrip/tracking/system web endpoint.
*/
Route::get('/tracking', [ApiTrackingController::class, 'show'])->middleware('api.key:tracking');


/*
|--------------------------------------------------------------------------
| Internal API (static API key required, header X-API-Key)
|--------------------------------------------------------------------------
| Keys are set in .env as API_INTERNAL_KEYS (comma-separated).
*/
Route::middleware('api.key:internal')->group(function () {
    // GET /api/dashboard?type=F|T&... (reuses AppController::loadDashboard / Dashboard model)
    Route::get('/dashboard', [AppController::class, 'loadDashboard']);

    // GET /api/customers?cust_type_id=IND|COR&search=...&per_page=25&page=1
    Route::get('/customers', [CustomerController::class, 'apiIndex']);

    // GET /api/customers/{id}
    Route::get('/customers/{id}', [CustomerController::class, 'apiShow']);
});


// Route::middleware('auth:sanctum')->post('/dokunotif', function (Request $request) {
//     $notificationHeader = getallheaders();
//     $notificationBody = file_get_contents('php://input');
//     $notificationPath = '/dokunotif'; // Adjust according to your notification path
//     $secretKey = 'SK-ApIIfVr1fIBcymgDPj4D'; // Adjust according to your secret key

//     $digest = base64_encode(hash('sha256', $notificationBody, true));
//     $rawSignature = "Client-Id:" . $notificationHeader['Client-Id'] . "\n"
//         . "Request-Id:" . $notificationHeader['Request-Id'] . "\n"
//         . "Request-Timestamp:" . $notificationHeader['Request-Timestamp'] . "\n"
//         . "Request-Target:" . $notificationPath . "\n"
//         . "Digest:" . $digest;

//     $signature = base64_encode(hash_hmac('sha256', $rawSignature, $secretKey, true));
//     $finalSignature = 'HMACSHA256=' . $signature;

//     if ($finalSignature == $notificationHeader['Signature']) {
//         // TODO: Process if Signature is Valid
//         return response('OK', 200)->header('Content-Type', 'text/plain');

//         // TODO: Do update the transaction status based on the `transaction.status`
//         DB::table('ztest_list')->update(['id' => 1]);
//     } else {
//         // TODO: Response with 400 errors for Invalid Signature
//         return response('Invalid Signature', 300)->header('Content-Type', 'text/plain');
//     }
// });
