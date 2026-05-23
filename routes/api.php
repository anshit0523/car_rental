<?php

use App\Http\Controllers\Api\AuthUserApiController;
use App\Http\Controllers\Api\UserBrowseCarApiController;
use Illuminate\Support\Facades\Route;

Route::post('/user/login', [AuthUserApiController::class, 'login']);
Route::post('/user/register', [AuthUserApiController::class, 'register']);
Route::post('/user/register/verify-otp', [AuthUserApiController::class, 'verifyRegisterOtp']);

Route::middleware('auth:sanctum')->group(function () {
   Route::get('/user/me', [AuthUserApiController::class, 'me']);
   Route::post('/user/logout', [AuthUserApiController::class, 'logout']);

   Route::get('/cars', [UserBrowseCarApiController::class, 'index']);
   Route::get('/cars/search', [UserBrowseCarApiController::class, 'search']);
   Route::get('/cars/{id}', [UserBrowseCarApiController::class, 'show']);

   
});
