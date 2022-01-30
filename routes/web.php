<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomAuthController;
use App\Http\Controllers\CustomDataController;

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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [CustomAuthController::class, 'index']);
Route::get('login', [CustomAuthController::class, 'index']);

Route::post('custom-login', [CustomAuthController::class, 'customLogin'])->name('login.custom');
Route::get('custom-logout', [CustomAuthController::class, 'customLogout'])->name('logout.custom');

Route::post('getPanel', [CustomDataController::class, 'getPanel'])->name('data.getPanel');
Route::post('getBorder', [CustomDataController::class, 'getBorder'])->name('data.getBorder');
Route::get('history', [CustomDataController::class, 'history'])->name('data.history');
Route::post('addToCart', [CustomDataController::class, 'addToCart'])->name('data.addToCart');
Route::post('removeFromCart', [CustomDataController::class, 'removeFromCart'])->name('data.removeFromCart');
Route::post('sendOrder', [CustomDataController::class, 'sendOrder'])->name('data.sendOrder');
