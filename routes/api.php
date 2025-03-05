<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::match(['get', 'post'], '/admin-login', [App\Http\Controllers\Admin\LoginAdminController::class, 'login'])->name('admin-login');
Route::get('/admin-information', [App\Http\Controllers\Admin\LoginAdminController::class, 'information']);

Route::group(['middleware' => 'admin', 'prefix' => 'admin'], function () {
    Route::resource('information', App\Http\Controllers\Admin\AdminController::class);
});

Route::resource('theory-category', App\Http\Controllers\Admin\TheOryCategoryController::class);
Route::delete('theory-categorys/delete', [App\Http\Controllers\Admin\TheOryCategoryController::class, 'delete']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
