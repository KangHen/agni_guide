<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Register\RegisterVerifyController;
use App\Http\Controllers\TestController;

Route::view('/', 'welcome')->name('welcome');
Route::get('register-verify', RegisterVerifyController::class)->name('register.verify');
Route::view('order-form', 'order-form')->name('order-form');
Route::view('order-form-detail/{code}', 'order-form-detail')->name('order-form.show');
//Route::get('test', [TestController::class, 'index']);

Route::middleware(['auth', 'verified'])
    ->group(function () {
        Route::view('dashboard', 'dashboard')->name('dashboard');
        Route::view('profile', 'profile')->name('profile');
        Route::view('basic', 'basic')->name('setting.basic');
        Route::view('user', 'user')->name('setting.user');
        Route::view('category', 'category')->name('setting.category');
        Route::view('page', 'page')->name('page');
        Route::view('post', 'post')->name('post');
        Route::view('historic-site', 'historic-site')->name('historic-site');
        Route::view('sale', 'sale')->name('sale');
        Route::view('product-category', 'product-category')->name('product-category');
        Route::view('order', 'order')->name('order');
    });

require __DIR__.'/auth.php';
