<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::apiResource('travels', \App\Http\Controllers\Api\V1\TravelApiController::class);
Route::apiResource('travels/{travel}/tours', \App\Http\Controllers\Api\V1\TourApiController::class);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
