<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class TestController extends Controller
{
    /**
     * LLMの動作テスト用ページを表示
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        // storeからのリダイレクトの場合は、質問と回答を取得
        $question = $request->session()->get('question', '');
        $answer   = $request->session()->get('answer', '');

        return view('test', compact('question', 'answer'));
    }

    /**
     * LLMへのリクエストを処理
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $question = $request->input('question');
        if (!$question) {
            return redirect()->route('home');
        }

        $ollama_url   = config('ollama.url');
        $ollama_model = config('ollama.model');

        try {
            $response = Http::timeout(600)->post($ollama_url, [
                'model' => $ollama_model,
                'prompt' => $question,
                'system' => 'あなたは丁寧な日本語で回答するアシスタントです。',
                'stream' => true,
            ]);

            if (!$response->successful()) {
                $error_body = $response->body();
                $status_code = $response->status();

                Log::error('Ollama API request failed', [
                    'status' => $status_code,
                    'body' => $error_body,
                    'url' => $ollama_url,
                    'model' => $ollama_model
                ]);

                return redirect()->route('test.index')->with([
                    'question' => $question,
                    'answer' => ($status_code === 0 || $status_code === 500)
                        ? 'Ollamaコンテナに接続できませんでした。コンテナが起動しているか確認してください。'
                        : "APIリクエストが失敗しました（ステータス: {$status_code}）。Ollamaコンテナの状態を確認してください。",
                ]);
            }

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

            if (empty($answer)) {
                Log::warning('Empty response from Ollama', ['body' => $stream]);
                $answer = '応答が空でした。Ollamaコンテナが正しく動作しているか確認してください。';
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Connection exception', [
                'message' => $e->getMessage(),
                'url' => $ollama_url
            ]);
            $answer = 'Ollamaコンテナへの接続に失敗しました。コンテナが起動しているか確認してください。エラー: ' . $e->getMessage();
        } catch (\Exception $e) {
            Log::error('Exception occurred', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $answer = 'エラーが発生しました: ' . $e->getMessage();
        }

        return redirect()->route('test.index')->with([
            'question' => $question,
            'answer' => $answer,
        ]);
    }
}
