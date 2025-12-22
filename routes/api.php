<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskSuggestionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// 認証関連のルート
Route::group([
    'prefix' => 'auth',
    'middleware' => 'throttle:5,1',
    'as' => 'auth.',
], function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});

// ユーザー関連のルート
Route::group([
    'prefix' => 'user',
    'middleware' => ['auth:sanctum'],
    'as' => 'user.',
], function () {
    Route::get('/', [UserController::class, 'show'])->name('show');
    Route::put('/', [UserController::class, 'update'])->name('update');
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

// 提案関連のルート
Route::group([
    'prefix' => 'task-suggestions',
    'middleware' => ['auth:sanctum'],
    'as' => 'task-suggestions.',
], function () {
    Route::get('/', [TaskSuggestionController::class, 'index'])->name('index');
    Route::delete('/{uuid}', [TaskSuggestionController::class, 'destroy'])->name('destroy');
});
