<?php

use Illuminate\Http\Request;
use App\Http\Controllers\WebhookController;

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
