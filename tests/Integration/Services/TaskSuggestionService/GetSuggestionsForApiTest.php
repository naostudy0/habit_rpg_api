<?php

namespace Tests\Integration\Services\TaskSuggestionService;

use App\Models\TaskSuggestion;
use App\Models\User;
use App\Services\TaskSuggestionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetSuggestionsForApiTest extends TestCase
{
    use RefreshDatabase;

    private TaskSuggestionService $task_suggestion_service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->task_suggestion_service = app(TaskSuggestionService::class);
    }

    /**
     * APIレスポンス形式に整形できること
     */
    public function testGetSuggestionsForApiReturnsFormattedArray(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create();

        // テストユーザーに紐づく提案を作成
        TaskSuggestion::factory()->create([
            'user_id' => $user->user_id,
        ]);

        // APIレスポンス形式で提案一覧を取得
        $result = $this->task_suggestion_service->getSuggestionsForApi($user->user_id);

        // 検証
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertArrayHasKey('uuid', $result[0]);
        $this->assertArrayHasKey('title', $result[0]);
        $this->assertArrayHasKey('memo', $result[0]);
        $this->assertArrayHasKey('created_at', $result[0]);
        $this->assertArrayHasKey('updated_at', $result[0]);
    }

    /**
     * UUIDが正しくマッピングされること
     */
    public function testGetSuggestionsForApiMapsUuidCorrectly(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create();

        // テストユーザーに紐づく提案を作成
        $suggestion = TaskSuggestion::factory()->create([
            'user_id' => $user->user_id,
        ]);

        // APIレスポンス形式で提案一覧を取得
        $result = $this->task_suggestion_service->getSuggestionsForApi($user->user_id);

        // 検証
        $this->assertEquals($suggestion->task_suggestion_uuid, $result[0]['uuid']);
    }

    /**
     * ISO8601形式のタイムスタンプが正しいこと
     */
    public function testGetSuggestionsForApiFormatsTimestampsAsIso8601(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create();

        // テストユーザーに紐づく提案を作成
        $suggestion = TaskSuggestion::factory()->create([
            'user_id' => $user->user_id,
        ]);

        // APIレスポンス形式で提案一覧を取得
        $result = $this->task_suggestion_service->getSuggestionsForApi($user->user_id);

        // 検証
        $this->assertEquals($suggestion->created_at->toIso8601String(), $result[0]['created_at']);
        $this->assertEquals($suggestion->updated_at->toIso8601String(), $result[0]['updated_at']);
        // ISO8601形式の検証（例: 2025-12-15T10:30:00+00:00）
        $this->assertMatchesRegularExpression(
            '/^\\d{4}-\\d{2}-\\d{2}T\\d{2}:\\d{2}:\\d{2}[+-]\\d{2}:\\d{2}$/',
            $result[0]['created_at']
        );
    }

    /**
     * 複数の提案が正しく整形されること
     */
    public function testGetSuggestionsForApiFormatsMultipleSuggestions(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create();

        // テストユーザーに紐づく提案を複数作成
        $suggestion1 = TaskSuggestion::factory()->create([
            'user_id' => $user->user_id,
            'title' => '提案1',
            'created_at' => now()->subSeconds(1),
        ]);
        $suggestion2 = TaskSuggestion::factory()->create([
            'user_id' => $user->user_id,
            'title' => '提案2',
            'created_at' => now(),
        ]);

        // APIレスポンス形式で提案一覧を取得
        $result = $this->task_suggestion_service->getSuggestionsForApi($user->user_id);

        // 検証
        $this->assertCount(2, $result);
        // 作成日時の降順でソートされていること
        $this->assertEquals($suggestion2->task_suggestion_uuid, $result[0]['uuid']);
        $this->assertEquals($suggestion1->task_suggestion_uuid, $result[1]['uuid']);
    }

    /**
     * 提案がない場合は空の配列が返ること
     */
    public function testGetSuggestionsForApiReturnsEmptyArrayWhenNoSuggestions(): void
    {
        // テストユーザーを作成（提案なし）
        $user = User::factory()->create();

        // APIレスポンス形式で提案一覧を取得
        $result = $this->task_suggestion_service->getSuggestionsForApi($user->user_id);

        // 検証
        $this->assertIsArray($result);
        $this->assertCount(0, $result);
    }
}
