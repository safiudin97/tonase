<?php

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

//route untuk soal pre-test A
Route::get('/', [App\Http\Controllers\PretestController::class, 'index'])->name('pre_test');
Route::post('/check', [App\Http\Controllers\PretestController::class, 'check'])->name('check');

