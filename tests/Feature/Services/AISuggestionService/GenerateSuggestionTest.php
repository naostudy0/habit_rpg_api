<?php

namespace Tests\Feature\Services\AISuggestionService;

use App\Services\AISuggestionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GenerateSuggestionTest extends TestCase
{
    use RefreshDatabase;

    private AISuggestionService $ai_suggestion_service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ai_suggestion_service = app(AISuggestionService::class);
    }

    /**
     * 正常なレスポンスから提案を生成できること
     */
    public function testGenerateSuggestionSuccessfully(): void
    {
        // Ollama APIのレスポンスをモック
        $mock_response = "{\"response\":\"筋トレ\"}\n"
            . "{\"response\":\"：\"}\n"
            . "{\"response\":\"週3回を目標に習慣化するため\"}\n";

        Http::fake([
            config('ollama.url') => Http::response($mock_response, 200),
        ]);

        // 予定データのサマリー
        $tasks_summary = "過去の予定:\n- 読書\n- 散歩";

        // 提案を生成
        $result = $this->ai_suggestion_service->generateSuggestion($tasks_summary);

        // 検証
        $this->assertIsArray($result);
        $this->assertArrayHasKey('title', $result);
        $this->assertArrayHasKey('memo', $result);
        $this->assertEquals('筋トレ', $result['title']);
        $this->assertEquals('週3回を目標に習慣化するため', $result['memo']);
    }

    /**
     * ストリーミングレスポンスを正しく処理できること
     */
    public function testGenerateSuggestionHandlesStreamingResponse(): void
    {
        // ストリーミング形式のレスポンスをモック
        $mock_response = "{\"response\":\"読書\"}\n"
            . "{\"response\":\"：\"}\n"
            . "{\"response\":\"技術書を1時間読んでスキルアップするため\"}\n";

        Http::fake([
            config('ollama.url') => Http::response($mock_response, 200),
        ]);

        // 予定データのサマリー
        $tasks_summary = "過去の予定:\n- 筋トレ";

        // 提案を生成
        $result = $this->ai_suggestion_service->generateSuggestion($tasks_summary);

        // 検証
        $this->assertIsArray($result);
        $this->assertEquals('読書', $result['title']);
        $this->assertEquals('技術書を1時間読んでスキルアップするため', $result['memo']);
    }

    /**
     * APIエラー時はnullが返ること
     */
    public function testGenerateSuggestionReturnsNullOnApiError(): void
    {
        // APIエラーレスポンスをモック
        Http::fake([
            config('ollama.url') => Http::response(['error' => 'Internal Server Error'], 500),
        ]);

        // 予定データのサマリー
        $tasks_summary = "過去の予定:\n- 読書";

        // 提案を生成
        $result = $this->ai_suggestion_service->generateSuggestion($tasks_summary);

        // 検証
        $this->assertNull($result);
    }

    /**
     * 空のレスポンス時はnullが返ること
     */
    public function testGenerateSuggestionReturnsNullOnEmptyResponse(): void
    {
        // 空のレスポンスをモック
        Http::fake([
            config('ollama.url') => Http::response('', 200),
        ]);

        // 予定データのサマリー
        $tasks_summary = "過去の予定:\n- 読書";

        // 提案を生成
        $result = $this->ai_suggestion_service->generateSuggestion($tasks_summary);

        // 検証
        $this->assertNull($result);
    }

    /**
     * パースできない形式のレスポンス時はnullが返ること
     */
    public function testGenerateSuggestionReturnsNullOnInvalidFormat(): void
    {
        // パースできない形式のレスポンスをモック
        $mock_response = "{\"response\":\"これは予定名と提案理由の形式ではありません\"}\n";

        Http::fake([
            config('ollama.url') => Http::response($mock_response, 200),
        ]);

        // 予定データのサマリー
        $tasks_summary = "過去の予定:\n- 読書";

        // 提案を生成
        $result = $this->ai_suggestion_service->generateSuggestion($tasks_summary);

        // 検証
        $this->assertNull($result);
    }

    /**
     * 接続エラー時はnullが返ること
     */
    public function testGenerateSuggestionReturnsNullOnConnectionError(): void
    {
        // 接続エラーをモック
        Http::fake(function () {
            throw new \Illuminate\Http\Client\ConnectionException('Connection timeout');
        });

        // 予定データのサマリー
        $tasks_summary = "過去の予定:\n- 読書";

        // 提案を生成
        $result = $this->ai_suggestion_service->generateSuggestion($tasks_summary);

        // 検証
        $this->assertNull($result);
    }

    /**
     * プロンプトに予定データのサマリーが含まれること
     */
    public function testGenerateSuggestionIncludesTasksSummaryInPrompt(): void
    {
        // 予定データのサマリー
        $tasks_summary = "過去の予定:\n- 読書\n- 散歩";

        // リクエストをキャプチャするためのモック
        Http::fake([
            config('ollama.url') => function ($request) use ($tasks_summary) {
                // プロンプトに予定データのサマリーが含まれていることを確認
                $request_data = $request->data();
                $this->assertStringContainsString($tasks_summary, $request_data['prompt']);

                return Http::response("{\"response\":\"筋トレ：週3回を目標に習慣化するため\"}\n", 200);
            },
        ]);

        // 提案を生成
        $result = $this->ai_suggestion_service->generateSuggestion($tasks_summary);

        // 検証
        $this->assertIsArray($result);
    }

    /**
     * マークダウン記号が除去されること
     */
    public function testGenerateSuggestionRemovesMarkdownSymbols(): void
    {
        // マークダウン記号を含むレスポンスをモック
        $mock_response = "{\"response\":\"*\"}\n"
            . "{\"response\":\"筋トレ\"}\n"
            . "{\"response\":\"：\"}\n"
            . "{\"response\":\"週3回を目標に習慣化するため\"}\n";

        Http::fake([
            config('ollama.url') => Http::response($mock_response, 200),
        ]);

        // 予定データのサマリー
        $tasks_summary = "過去の予定:\n- 読書";

        // 提案を生成
        $result = $this->ai_suggestion_service->generateSuggestion($tasks_summary);

        // 検証
        $this->assertIsArray($result);
        $this->assertEquals('筋トレ', $result['title']);
    }
}
