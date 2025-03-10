<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\UserAdminController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//AUTH
Route::post('login', [AuthController::class,'postLogin'])->name('postLogout');
Route::post('logout', [AuthController::class,'postLogout'])->name('postLogout');

//PASSWORD RESET
Route::post('password/email', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::post('password/reset', [AuthController::class, 'reset'])->name('password.update');

Route::middleware(["admin"])->group(function (){
    Route::get('/user', [UserAdminController::class, 'getUser'])->name('getUser');;
    Route::post('/user/create', [UserAdminController::class, 'storeUser'])->name('storeUser');
    Route::put('/user/update/{id}', [UserAdminController::class, 'updateUser'])->name('updateUser');
    Route::delete('/user/destroy/{id}', [UserAdminController::class, 'destroyUser'])->name('destroyUser');
});
