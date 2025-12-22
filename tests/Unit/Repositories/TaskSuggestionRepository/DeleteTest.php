<?php

namespace Tests\Unit\Repositories\TaskSuggestionRepository;

use App\Models\TaskSuggestion;
use App\Models\User;
use App\Repositories\TaskSuggestionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    use RefreshDatabase;

    private TaskSuggestionRepository $task_suggestion_repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->task_suggestion_repository = app(TaskSuggestionRepository::class);
    }

    /**
     * 提案を削除できること
     */
    public function testDeleteSuggestionSuccessfully(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create();

        // 提案を作成
        $suggestion = TaskSuggestion::factory()->create([
            'user_id' => $user->user_id,
        ]);

        // 提案を削除
        $result = $this->task_suggestion_repository->delete($suggestion);

        // 検証
        $this->assertTrue($result);

        // データベースから削除されていることを確認
        $this->assertDatabaseMissing('task_suggestions', [
            'task_suggestion_id' => $suggestion->task_suggestion_id,
        ]);
    }

    /**
     * 削除された提案はデータベースに存在しないこと
     */
    public function testDeletedSuggestionDoesNotExistInDatabase(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create();

        // 提案を作成
        $suggestion = TaskSuggestion::factory()->create([
            'user_id' => $user->user_id,
        ]);

        // 提案を削除
        $this->task_suggestion_repository->delete($suggestion);

        // 削除された提案はデータベースに存在しないことを確認
        $this->assertDatabaseMissing('task_suggestions', [
            'task_suggestion_id' => $suggestion->task_suggestion_id,
        ]);
    }
}
