<?php

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

Route::prefix('v1')->group(function () { 
    // auth user
    Route::post('login', [App\Http\Controllers\Api\AuthController::class, 'login']);
    Route::post('register', [App\Http\Controllers\Api\AuthController::class, 'register']);

    Route::middleware(['auth:api'])->group(function () {
        Route::post('logout', [App\Http\Controllers\Api\AuthController::class, 'logout']);
        Route::post('reset_password', [App\Http\Controllers\Api\AuthController::class, 'resetPassword']);

        //e wallet
        Route::post('add_bank_account', [App\Http\Controllers\Api\EwalletController::class, 'addbankAccount']);
        Route::post('topup', [App\Http\Controllers\Api\EwalletController::class, 'topup']);
        Route::post('withdraw', [App\Http\Controllers\Api\EwalletController::class, 'withdraw']);
        Route::post('transfer', [App\Http\Controllers\Api\EwalletController::class, 'transfer']);
        Route::get('mutasi', [App\Http\Controllers\Api\EwalletController::class, 'mutasi']);

        //one bill
        Route::post('create_invoice', [App\Http\Controllers\Api\InvoiceController::class, 'create']);
        Route::get('list_invoice', [App\Http\Controllers\Api\InvoiceController::class, 'list']);
        Route::post('create_billing_id', [App\Http\Controllers\Api\OneBillController::class, 'create']);
        Route::post('payment_one_billing', [App\Http\Controllers\Api\OneBillController::class, 'payment']);



    }); 
});
