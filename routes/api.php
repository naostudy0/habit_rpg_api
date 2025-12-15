<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// 認証関連のルート
Route::group([
    'prefix' => 'auth',
    'middleware' => 'throttle:5,1',
    'as' => 'auth.',
], function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});
