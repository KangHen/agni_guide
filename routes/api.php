<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\Register\RegisterController;
use App\Http\Controllers\API\Auth\GoogleSignInController;
use App\Http\Controllers\API\Auth\UpdateTokenController;
use App\Http\Controllers\API\PageController;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\API\SaleController;
use App\Http\Controllers\API\HistoricSiteController;

/** Default Route */
Route::get('/', function () {
    return response()->json(['message' => 'Agni Guide API v1.0']);
});
Route::get('terms', [PageController::class, 'terms'])->name('api.page');

/** Register Routes */
Route::post('register', RegisterController::class)->name('api.register');
Route::post('google-sign', GoogleSignInController::class)->name('api.google-sign');

/** Auth Route */
Route::post('auth', LoginController::class)->name('api.auth');

/**
 * Authenticated Route
 */
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('user')->group(function () {
        Route::get('/', fn (Request $request) => $request->user())->name('api.user');
        Route::put('profile', fn (Request $request) => $request->user())->name('api.user.update');
        Route::put('password', fn (Request $request) => $request->user())->name('api.user.password');
    });
    Route::post('update-token', UpdateTokenController::class)->name('api.update-token');
    Route::get('categories', [CategoryController::class, 'index'])->name('api.categories');
    Route::get('page/{slug}', [PageController::class, 'index'])->name('api.page');
    Route::get('posts', [PostController::class, 'index'])->name('api.posts');
    Route::get('posts/{slug}', [PostController::class, 'show'])->name('api.posts.show');
    Route::get('sales', [SaleController::class, 'index'])->name('api.sales');
    Route::get('sales/{id}', [SaleController::class, 'show'])->name('api.sales.show');
    Route::get('historic-sites/showcase', [HistoricSiteController::class, 'showcase'])->name('api.historic-sites.showcase');
    Route::apiResource('historic-sites', HistoricSiteController::class)->only(['index', 'show']);
});
