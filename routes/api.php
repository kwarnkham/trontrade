<?php

use App\Constants\Endpoint;
use App\Http\Controllers\AgentApiController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\CryptoNetworkController;
use App\Http\Controllers\CryptoWalletController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\IdentifierController;
use App\Http\Controllers\NetworkTransactionController;
use App\Http\Controllers\OneTimePasswordController;
use App\Http\Controllers\OtpAbilityController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WithdrawController;
use App\Http\Controllers\XController;
use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/test', function (Request $request) {
    return new App\Mail\ResetPassword(123);
});

Route::post(Endpoint::REGISTER, [UserController::class, 'store']);
Route::post(Endpoint::LOGIN, [UserController::class, 'login']);
Route::get(Endpoint::GET_TOKEN, [TokenController::class, 'index']);
Route::get(Endpoint::GET_CRYPTO_NETWORK, [CryptoNetworkController::class, 'index']);
Route::get(Endpoint::GET_CURRENCY, [CurrencyController::class, 'index']);
Route::get(Endpoint::PAYMENT, [PaymentController::class, 'index']);
Route::get(Endpoint::GET_ALL_SALE, [SaleController::class, 'index']);

// Route::middleware(['onlySameIP'])->group(function () {
//     Route::post(Endpoint::TRON_EVENT, [XController::class, 'tronEvent']);
//     Route::get(Endpoint::HANDSHAKE, function (Request $request) {
//         return response()->json($request->ip());
//     });
// });

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post(Endpoint::LOGOUT, [UserController::class, 'logout']);
    Route::post(Endpoint::VERIFY_EMAIL_OTP, [UserController::class, 'verifyEmailOTP']);
    Route::get(Endpoint::GET_OTP_ABILITY, [OtpAbilityController::class, 'index']);
    Route::get(Endpoint::GET_IDENTIFIERS, [IdentifierController::class, 'index']);
    Route::get(Endpoint::ME, [UserController::class, 'me']);
    Route::post(Endpoint::SET_SETTING, [UserController::class, 'setSetting']);
    Route::put(Endpoint::UPDATE_SETTING, [UserController::class, 'updateSetting']);
    // todo
    // check otp ability and conditional middleware
    Route::post(Endpoint::REQUEST_OTP, [OneTimePasswordController::class, 'store']);
    Route::post(Endpoint::EMAIL_VERIFICATION_LINK, [UserController::class, 'requestEmailVerificationLink']);
    Route::get(Endpoint::GET_SALE, [SaleController::class, 'index']);

    Route::middleware(['verified'])->group(function () {
        Route::post(Endpoint::CHANGE_PASSWORD, [UserController::class, 'changePassword']);
        Route::get(Endpoint::GOOGLE_2FA, [UserController::class, 'getGoogle2FA']);
        Route::post(Endpoint::VERIFY_GOOGLE_2FA, [UserController::class, 'verifyGoogle2FA']);
        Route::post(Endpoint::PROPOSE_VERIFY_USER, [UserController::class, 'verifyUser']);
        Route::get(Endpoint::GET_GOOGLE_FORM_FIELDS, [XController::class, 'getGoogleFormFields']);
        Route::get(Endpoint::GET_GOOGLE_FORM_FIELDS_QR, [XController::class, 'getGoogleFormFieldsQr']);
        Route::get(Endpoint::GET_SELF_SALE, [SaleController::class, 'selfSale']);
        Route::post(Endpoint::ADD_PAYMENT . '/{payment}', [PaymentController::class, 'addPayment']);
        Route::delete(Endpoint::REMOVE_PAYMENT . "/{payment}", [PaymentController::class, 'removePayment']);
        Route::put(Endpoint::UPDATE_PAYMENT . '/{payment}', [PaymentController::class, 'updatePayment']);
        Route::get(Endpoint::USABLE_PAYMENTS, [PaymentController::class, 'usablePayments']);
        Route::get(Endpoint::LIST_NETWORK_TRANSACTIONS, [NetworkTransactionController::class, 'index']);
    });

    Route::middleware(['idVerified', '2FA'])->group(function () {
        Route::post(Endpoint::SYNC_WALLET, [CryptoWalletController::class, 'syncWallet'])->middleware(['throttle:syncWallet']);
        Route::post(Endpoint::CREATE_SALE, [SaleController::class, 'store']);
        Route::put(Endpoint::UPDATE_SALE . "/{sale}", [SaleController::class, 'update']);
        Route::post(Endpoint::CREATE_PURCHASE, [PurchaseController::class, 'store']);
        Route::post(Endpoint::PURCHASE_PAID . "/{purchase}", [PurchaseController::class, 'paid']);
        Route::post(Endpoint::PURCHASE_DEALT . "/{purchase}", [PurchaseController::class, 'dealt']);
        Route::post(Endpoint::CANCEL_PURCHASE . "/{purchase}", [PurchaseController::class, 'cancel']);
        Route::post(Endpoint::REJECT_PURCHASE . "/{purchase}", [PurchaseController::class, 'reject']);
        Route::get(Endpoint::GET_WALLET_TOKEN . "/{cryptoNetwork}", [UserController::class, 'getWalletToken']);
        Route::post(Endpoint::WITHDRAW . "/{token}", [WithdrawController::class, 'store']);
        Route::get(Endpoint::GET_A_PURCHASE . "/{purchase}", [PurchaseController::class, 'getPurchase']);
        Route::get(Endpoint::GET_WALLETS, [CryptoWalletController::class, 'getWallets']);
        Route::get(Endpoint::GET_PURCHASES, [PurchaseController::class, 'getPurchases']);
        Route::get(Endpoint::GET_SALE_PURCHASES . "/{sale}", [PurchaseController::class, 'getSalePurchases']);
    });
});

Route::middleware(['verified'])->group(function () {
    Route::post(Endpoint::VERIFY_FORGOT_PASSWORD, [UserController::class, 'verifyResetPasswordOTP']);
    Route::post(Endpoint::FORGOT_PASSWORD, [UserController::class, 'requestPasswordResetOTP']);
    Route::post(Endpoint::RESET_PASSWORD, [UserController::class, 'resetPassword'])->name('resetPassword')->middleware('signed');
});

Route::middleware(['auth:sanctum', 'isAdmin'])->group(function () {
    Route::post(Endpoint::REJECT_IDENTIFIER . "/{identifier}", [IdentifierController::class, 'rejectIdentifier']);
    Route::post(Endpoint::CONFIRM_IDENTIFIER . "/{identifier}", [IdentifierController::class, 'confirmIdentifier']);
    Route::post(Endpoint::REVIEW_PURCHASE . "/{purchase}", [PurchaseController::class, 'review']);
    Route::post(Endpoint::PURCHASE_REVIEWED . "/{purchase}", [PurchaseController::class, 'reviewed']);
    Route::post(Endpoint::CREATE_PAYMENT, [PaymentController::class, 'store']);
    Route::post(Endpoint::CREATE_NETWORK, [CryptoNetworkController::class, 'store']);
    Route::post(Endpoint::CREATE_TOKEN, [TokenController::class, 'store']);
    Route::post(Endpoint::CREATE_CURRENCY, [CurrencyController::class, 'store']);
    Route::post(Endpoint::CREATE_IDENTIFIER, [IdentifierController::class, 'store']);
    Route::get(Endpoint::GET_USER_IDENTITIES, [IdentifierController::class, 'userIdentifiers']);
    Route::get(Endpoint::GET_TOKEN_SUMMERY . '/{token}', [TokenController::class, 'summery']);

    Route::post(Endpoint::BAN . "/{user}", [UserController::class, 'ban']);
    Route::post(Endpoint::UNBAN . "/{user}", [UserController::class, 'unban']);
    Route::post(Endpoint::RESET_2FA . "/{user}", [UserController::class, 'reset2FA']);
    Route::get(Endpoint::LIST_PURCHASES, [PurchaseController::class, 'index']);
    Route::post(Endpoint::CREATE_AGENT, [AgentController::class, 'store']);
    Route::post(Endpoint::TOGGLE_BLOCK_AGENT . '/{agent}', [AgentController::class, 'toggleBlock']);
    Route::get(Endpoint::LIST_AGENT, [AgentController::class, 'index']);
    Route::post(Endpoint::RESET_AGENT_KEY . '/{agent}', [AgentController::class, 'resetAgentKey']);
});

Route::middleware(['auth:sanctum', 'isAgent'])->group(function () {
    Route::get(Endpoint::GET_USERS, [UserController::class, 'index']);
});

Route::middleware(['agent'])->group(function () {
    Route::get(Endpoint::GET_USD_RATE, [AgentApiController::class, 'getUSDRate']);
    Route::post(Endpoint::AGENT_CREATE_USER, [AgentApiController::class, 'createUser']);
    Route::get(Endpoint::AGENT_GET_USER_WALLETS, [AgentApiController::class, 'getUserWallets']);
    Route::get(Endpoint::AGENT_GET_NETWORK_TRANSACTIONS, [AgentApiController::class, 'getNetworkTransaction']);
    Route::get(Endpoint::AGENT_GET_TOKEN_SUMMERY . '/{token}', [AgentApiController::class, 'getTokenSummery']);
    Route::post(Endpoint::AGENT_USER_WITHDRAW . '/{token}/{user}', [AgentApiController::class, 'withdraw']);
});
