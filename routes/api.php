<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Auth\LoginController;
/**
 * Auth Routes
 */
Route::post('auth', LoginController::class);
Route::get('/', function () {
    return response()->json(['message' => 'Agni Guide API v1.0']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
