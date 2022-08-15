<?php

use App\Constants\Endpoint;
use App\Http\Controllers\UserController;
use App\Utility\TronWeb;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    $disk = Storage::build([
        'driver' => 'local',
        'root' => base_path(),
    ]);
    $disk->exists('service-account-file.json');
    $token = DB::table('tokens')->count();
    $network = DB::table('crypto_networks')->count();
    $identifier = DB::table('identifiers')->count();
    $currency = DB::table('currencies')->count();
    $payment = DB::table('payments')->count();
    $nodeTron = Redis::exists('tron_network');
    $nodeTronUsdt = Redis::exists('tron_network');
    $systemTrx = TronWeb::getSystemTrxBalance();
    $systemResource = TronWeb::getSystemResources();
    $checklists = [
        'APP_ENV' => env('APP_ENV'),
        'service-account-file' => $disk->exists('service-account-file.json'),
        'GOOGLE_BUCKET_NAME' => env('GOOGLE_BUCKET_NAME'),
        'APP_URL' => env('APP_URL'),
        'CLIENT_URL' => env('CLIENT_URL'),
        'NODE_URL' => env('NODE_URL'),
        'NOTICE_EMAIL' => env('NOTICE_EMAIL'),
        'token' => $token,
        'network' => $network,
        'identifier' => $identifier,
        'currency' => $currency,
        'payment' => $payment,
        'node tron' => $nodeTron,
        'node tron usdt' => $nodeTronUsdt,
        'System trx' => $systemTrx,
        'System resource' => json_encode($systemResource)
    ];
    return $checklists;
    return view('welcome', ['checklists' => $checklists]);
})->middleware(['auth.basic', 'isAdmin']);
Route::get(Endpoint::VERIFY_EMAIL_LINK . "/{user}", [UserController::class, 'verifyEmailLink'])->middleware('signed')->name('verifyEmail');
