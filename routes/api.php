<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

Route::match(['get', 'post'], '/test', function (Request $request) {
    Log::info('API test', ['params' => $request->all()]);
    return response()->json([
        'message' => 'API test',
        'result' => true,
    ]);
});
