<?php

namespace Tests\Unit\Repositories\TaskSuggestionRepository;

use App\Models\TaskSuggestion;
use App\Models\User;
use App\Repositories\TaskSuggestionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    private TaskSuggestionRepository $task_suggestion_repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->task_suggestion_repository = app(TaskSuggestionRepository::class);
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
            'user_id' => $user->user_id,
            'title' => 'テスト提案',
            'memo' => 'テストメモ',
        ];

        // 提案を作成
        $suggestion = $this->task_suggestion_repository->create($suggestion_data);

        // 検証
        $this->assertInstanceOf(TaskSuggestion::class, $suggestion);
        $this->assertEquals($suggestion_data['title'], $suggestion->title);
        $this->assertEquals($suggestion_data['memo'], $suggestion->memo);
        $this->assertNotNull($suggestion->task_suggestion_uuid);
    }


    /**
     * データベースに正しく保存されること
     */
    public function testCreateSuggestionSavesToDatabase(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create();

        // 提案データを作成
        $suggestion_data = [
            'user_id' => $user->user_id,
            'title' => 'テスト提案',
            'memo' => 'テストメモ',
        ];

        // 提案を作成
        $suggestion = $this->task_suggestion_repository->create($suggestion_data);

        // データベースに正しく保存されていることを確認
        $this->assertDatabaseHas('task_suggestions', [
            'task_suggestion_id' => $suggestion->task_suggestion_id,
            'user_id' => $user->user_id,
            'title' => $suggestion_data['title'],
            'memo' => $suggestion_data['memo'],
        ]);
    }
}
