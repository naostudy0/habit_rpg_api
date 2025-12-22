<?php

use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;

// LLMの動作テスト用ルート（本番環境以外）
if (!app()->environment('production')) {
    Route::get('/', [TestController::class, 'index'])->name('test.index');
    Route::post('/', [TestController::class, 'store'])->name('test.store');
}
