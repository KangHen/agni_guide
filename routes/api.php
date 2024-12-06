<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\Register\RegisterController;

/** Default Route */
Route::get('/', function () {
    return response()->json(['message' => 'Agni Guide API v1.0']);
});

/** Register Routes */
Route::post('register', RegisterController::class)->name('api.register');

/** Auth Route */
Route::post('auth', LoginController::class)->name('api.auth');

/** User Routes */
Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', fn (Request $request) => $request->user())->name('api.user');
    Route::get('categories', [CategoryController::class, 'index'])->name('api.categories');
});
