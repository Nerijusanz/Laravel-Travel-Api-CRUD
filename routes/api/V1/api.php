<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::post('login', App\Http\Controllers\Api\V1\Auth\LoginApiController::class);
Route::apiResource('travels', \App\Http\Controllers\Api\V1\TravelApiController::class);
Route::apiResource('travels/{travel}/tours', \App\Http\Controllers\Api\V1\TourApiController::class);


Route::prefix('admin')->middleware(['auth:sanctum','AdminRole'])->group(function () {

    Route::apiResource('travels', \App\Http\Controllers\Api\V1\Admin\TravelApiController::class);
    Route::apiResource('travels/{travel}/tours', \App\Http\Controllers\Api\V1\Admin\TourApiController::class);

});


Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('logout', App\Http\Controllers\Api\V1\Auth\LogoutApiController::class);

});