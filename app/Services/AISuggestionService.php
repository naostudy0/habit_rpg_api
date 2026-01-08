<?php

namespace App\Services;

use App\Utils\StringSanitizer;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AISuggestionService
{
    /**
     * 予定データを元にAIが提案する予定を生成
     *
     * @param string $tasks_summary 予定データのサマリー（プロンプト用に整形済み）
     * @return array|null
     */
    public function generateSuggestion(string $tasks_summary): ?array
    {
        // AIプロンプトを作成
        $prompt = $this->buildPrompt($tasks_summary);

        // Ollama APIを呼び出し
        return $this->callOllamaAPI($prompt);
    }

    /**
     * AIプロンプトを作成
     *
     * @param string $tasks_summary
     * @return string
     */
    private function buildPrompt(string $tasks_summary): string
    {
        return <<<PROMPT
## 質問
過去の予定から、提案する予定を1つ考えてください。
過去の予定とは異なるものでも構いません。

## 前提条件
過去の予定：
{$tasks_summary}

## 回答形式
以下の形式で1行のみ回答してください：
予定名：提案理由

## 例
筋トレ：週3回を目標に習慣化するため
読書：技術書を1時間読んでスキルアップするため
散歩：健康維持のために毎日30分歩く

## 重要な注意事項
・「予定名：提案理由」という文字列は出力しないこと
・マークダウン形式（*、+、-など）は使わないこと
・箇条書きや改行は使わないこと
・1行のみで「予定名：提案理由」の形式で回答すること
・前置きや説明文は一切不要です
・予定名と提案理由の両方を必ず含めること
・提案理由には、なぜこの予定を提案するのかの理由を簡潔に記載すること
PROMPT;
    }

    /**
     * Ollama APIを呼び出して予定提案を取得
     *
     * @param string $prompt
     * @return array|null
     */
    private function callOllamaAPI(string $prompt): ?array
    {
        $ollama_url = config('ollama.url');
        $ollama_model = config('ollama.model');

        try {
            $response = Http::timeout(600)->post($ollama_url, [
                'model' => $ollama_model,
                'prompt' => $prompt,
                'system' => 'あなたは予定管理をサポートするAIアシスタントです。指定された形式で回答してください。',
                'stream' => true,
            ]);

            if (!$response->successful()) {
                Log::error('Ollama API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'url' => $ollama_url,
                    'model' => $ollama_model,
                ]);
                return null;
            }

            $response_body = $response->body();
            $answer = '';

            // ストリーミングレスポンスを結合
            foreach (explode("\n", $response_body) as $line) {
                if (trim($line) === '') {
                    continue;
                }

                $data = json_decode($line, true);
                if (isset($data['response'])) {
                    $answer .= $data['response'];
                }
            }

            if ($answer === '') {
                Log::warning('Empty response from Ollama', ['body' => $response_body]);
                return null;
            }

            // 回答をパース
            $suggestion = $this->parseSuggestion($answer);
            // パース失敗はnullを返す
            if ($suggestion === null) {
                return null;
            }

            // DB保存前のチェックとサニタイズ
            foreach (['title', 'memo'] as $key) {
                // nullまたは空文字列のチェック
                if (empty($suggestion[$key])) {
                    Log::warning('Empty value for DB', [
                        'field' => $key,
                        'answer' => $answer,
                    ]);
                    return null;
                }

                if (!StringSanitizer::canStoreToDb($suggestion[$key])) {
                    Log::warning('Invalid string for DB', [
                        'field' => $key,
                        'answer' => $answer,
                    ]);
                    return null;
                }

                // DB保存時にエラーを起こす可能性のある文字のみを除去
                $suggestion[$key] = StringSanitizer::removeDbUnsafeChars($suggestion[$key]);
            }

            // 文字数チェック
            if (mb_strlen($suggestion['title']) > 255) {
                Log::warning('Suggestion title too long', ['answer' => $answer]);
                return null;
            }

            return $suggestion;
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Connection exception in AISuggestionService', [
                'message' => $e->getMessage(),
                'url' => $ollama_url,
            ]);
            return null;
        } catch (\Throwable $e) {
            Log::error('Exception occurred in AISuggestionService', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'url' => $ollama_url,
            ]);
            return null;
        }
    }

    /**
     * AIの回答から「予定名：提案理由」形式をパース
     *
     * @param string $answer
     * @return array|null
     */
    private function parseSuggestion(string $answer): ?array
    {
        // 先頭の装飾文字を除去
        $answer = preg_replace('/^[・\s]+/u', '', trim($answer));

        // 「予定名：提案理由」という誤出力行を除去
        $answer = preg_replace('/^予定名[：:]提案理由\s*$/um', '', $answer);

        $lines = explode("\n", $answer);
        $title = null;
        $memo = null;

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '') {
                continue;
            }

            // マークダウン・箇条書き除去
            $line = preg_replace('/^[*+\-•\t\s]+/u', '', $line);
            if ($line === '') {
                continue;
            }

            // 予定名：提案理由の形式かチェック
            if (preg_match('/^(.+?)[：:]\s*(.+)$/u', $line, $matches)) {
                $title = trim($matches[1]);
                $memo = trim($matches[2]);

                if ($title !== '' && $memo !== '') {
                    return [
                        'title' => $title,
                        'memo' => $memo,
                    ];
                }
            }

            // 予定名のみの場合は次行を理由として扱う
            if ($title === null && !preg_match('/[：:]/u', $line)) {
                $title = $line;
                continue;
            }

            if ($title !== null) {
                $memo = preg_replace('/^(提案した理由|理由|メモ|説明)[：:]\s*/u', '', $line);
                if ($memo !== '') {
                    return [
                        'title' => $title,
                        'memo' => $memo,
                    ];
                }
            }
        }

        Log::warning('Failed to parse suggestion format', ['answer' => $answer]);
        return null;
    }
}
