<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//AUTH
Route::post('postLogin', [AuthController::class,'postLogin']);
Route::post('postLogout', [AuthController::class,'postLogout'])->name('postLogout');

