<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PaketCuciController;
use App\Http\Controllers\StatusPesananController;
use App\Http\Controllers\TransaksiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');





// Route::get('order',[TransaksiController::class, 'index']);


//Route Customer 


Route::post('/register', [CustomerController::class, 'createCustomer']);
Route::middleware('auth:sanctum')->put('/profile', [CustomerController::class, 'updateCustomer']);
Route::post('/customer/login', [AuthController::class, 'login'])->name('login');
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::get('/profile', [CustomerController::class, 'getProfile'])->middleware('auth:sanctum');
Route::middleware('auth:sanctum')->put('/profile', [CustomerController::class, 'updateCustomer']);
Route::middleware('auth:sanctum')->post('/transaksi', [TransaksiController::class, 'createTransaksi']);
Route::post('/admin/login', [AuthController::class, 'adminLogin']);
Route::get('/admin/total-customer', [CustomerController::class, 'totalCustomer']);


//Route status pesanan
Route::get('/status', [StatusPesananController::class,'readAllStatusPesanans']);
Route::post('/status', [StatusPesananController::class, 'createStatusPesanan']);
Route::get('/admin/status-pesanan', [StatusPesananController::class, 'getStatusPesanan']);


//Route Transaksi
Route::get('/orders',[TransaksiController::class, 'readAllTransaksi']);
Route::middleware('auth:sanctum')->get('/orderhistory', [TransaksiController::class,'getOrderHistory']);
Route::get('/admin/total-transaksi', [TransaksiController::class, 'totalTransaksi']);
Route::get('/admin/orders', [TransaksiController::class, 'getAllOrders']);
Route::post('/admin/orders/update', [TransaksiController::class, 'updateOrderStatus']);





