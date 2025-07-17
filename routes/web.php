<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

Route::match(['get', 'post'], '/', function (Request $request) {
    $question = $request->input('question');
    $answer = '';

    if ($request->isMethod('post') && $question) {
        try {
            $response = Http::timeout(60)->post('http://ollama:11434/api/generate', [
                'model' => 'llama3',
                'prompt' => $question,
                'system' => 'あなたは丁寧な日本語で回答するアシスタントです。',
            ]);

            if ($response->successful()) {
                $stream = $response->body();
                $answer = '';

                // ストリーミングされたデータを処理
                foreach (explode("\n", $stream) as $line) {
                    if (trim($line)) {
                        $data = json_decode($line, true);
                        if (isset($data['response'])) {
                            $answer .= $data['response'];
                        }
                    }
                }
            } else {
                Log::error('API request failed', ['status' => $response->status(), 'body' => $response->body()]);
                $answer = 'API request failed. Please try again later.';
            }
        } catch (\Exception $e) {
            Log::error('Exception occurred', ['message' => $e->getMessage()]);
            $answer = 'An error occurred: ' . $e->getMessage();
        }
    }

    return view('welcome', compact('question', 'answer'));
})->name('home');
