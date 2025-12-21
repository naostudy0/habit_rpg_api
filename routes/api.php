<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

// 認証関連のルート
Route::group([
    'prefix' => 'auth',
    'middleware' => 'throttle:5,1',
    'as' => 'auth.',
], function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});

// 予定関連のルート
Route::group([
    'prefix' => 'tasks',
    'middleware' => ['auth:sanctum'],
    'as' => 'tasks.',
], function () {
    Route::get('/', [TaskController::class, 'index'])->name('index');
    Route::post('/', [TaskController::class, 'store'])->name('store');
    Route::put('/{uuid}', [TaskController::class, 'update'])->name('update');
    Route::delete('/{uuid}', [TaskController::class, 'destroy'])->name('destroy');
    Route::patch('/{uuid}/complete', [TaskController::class, 'toggleComplete'])->name('complete');
});
