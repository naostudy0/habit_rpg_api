<?php

namespace Tests\Feature\Services\TaskSuggestionService;

use App\Models\TaskSuggestion;
use App\Models\User;
use App\Services\TaskSuggestionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteSuggestionTest extends TestCase
{
    use RefreshDatabase;

    private TaskSuggestionService $task_suggestion_service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->task_suggestion_service = app(TaskSuggestionService::class);
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
        $result = $this->task_suggestion_service->deleteSuggestion($suggestion->task_suggestion_uuid, $user->user_id);

        // 検証
        $this->assertTrue($result);

        // データベースから削除されていることを確認
        $this->assertDatabaseMissing('task_suggestions', [
            'task_suggestion_id' => $suggestion->task_suggestion_id,
        ]);
    }

    /**
     * 存在しない提案の場合はfalseが返ること
     */
    public function testDeleteSuggestionReturnsFalseWhenSuggestionNotExists(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create();

        // 存在しないUUIDで提案を削除
        $result = $this->task_suggestion_service->deleteSuggestion('non-existent-uuid', $user->user_id);

        // 検証
        $this->assertFalse($result);
    }

    /**
     * 他のユーザーの提案は削除できないこと
     */
    public function testDeleteSuggestionReturnsFalseForOtherUserSuggestion(): void
    {
        // テストユーザーを2人作成
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // user1に紐づく提案を作成
        $suggestion = TaskSuggestion::factory()->create([
            'user_id' => $user1->user_id,
        ]);

        // user2でuser1の提案を削除しようとする
        $result = $this->task_suggestion_service->deleteSuggestion($suggestion->task_suggestion_uuid, $user2->user_id);

        // 検証
        $this->assertFalse($result);

        // user1の提案は削除されていないことを確認
        $this->assertDatabaseHas('task_suggestions', [
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
        $suggestion1 = TaskSuggestion::factory()->create([
            'user_id' => $user->user_id,
            'title' => '提案1',
        ]);
        $suggestion2 = TaskSuggestion::factory()->create([
            'user_id' => $user->user_id,
            'title' => '提案2',
        ]);

        // 提案1を削除
        $this->task_suggestion_service->deleteSuggestion($suggestion1->task_suggestion_uuid, $user->user_id);

        // 削除された提案はデータベースに存在しない
        $this->assertDatabaseMissing('task_suggestions', [
            'task_suggestion_id' => $suggestion1->task_suggestion_id,
        ]);

        // 削除されていない提案はデータベースに存在する
        $this->assertDatabaseHas('task_suggestions', [
            'task_suggestion_id' => $suggestion2->task_suggestion_id,
        ]);
    }
}
