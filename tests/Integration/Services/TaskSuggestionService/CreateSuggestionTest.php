<?php

namespace Tests\Integration\Services\TaskSuggestionService;

use App\Models\TaskSuggestion;
use App\Models\User;
use App\Services\TaskSuggestionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateSuggestionTest extends TestCase
{
    use RefreshDatabase;

    private TaskSuggestionService $task_suggestion_service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->task_suggestion_service = app(TaskSuggestionService::class);
    }

    /**
     * 提案を作成できること
     */
    public function testCreateSuggestionSuccessfully(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create();

        // 提案データを作成
        $suggestion_data = [
            'title' => 'テスト提案',
            'memo' => 'テストメモ',
        ];

        // 提案を作成
        $this->task_suggestion_service->createSuggestion($user->user_id, $suggestion_data);

        // データベースに正しく保存されていることを確認
        $this->assertDatabaseHas('task_suggestions', [
            'user_id' => $user->user_id,
            'title' => $suggestion_data['title'],
            'memo' => $suggestion_data['memo'],
        ]);
    }

    /**
     * UUIDが自動生成されること
     */
    public function testCreateSuggestionGeneratesUuid(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create();

        // 提案データを作成
        $suggestion_data = [
            'title' => 'テスト提案',
            'memo' => 'テストメモ',
        ];

        // 提案を作成
        $this->task_suggestion_service->createSuggestion($user->user_id, $suggestion_data);

        // UUIDが生成されていることを確認
        $suggestion = TaskSuggestion::where('user_id', $user->user_id)
            ->where('title', $suggestion_data['title'])
            ->first();

        $this->assertNotNull($suggestion);
        $this->assertNotNull($suggestion->task_suggestion_uuid);
    }
}
