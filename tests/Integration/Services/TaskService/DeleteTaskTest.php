<?php

namespace Tests\Integration\Services\TaskService;

use App\Models\Task;
use App\Models\User;
use App\Services\TaskService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteTaskTest extends TestCase
{
    use RefreshDatabase;

    private TaskService $task_service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->task_service = app(TaskService::class);
    }

    /**
     * 予定を削除できること
     */
    public function testDeleteTaskSuccessfully(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create();

        // 予定を作成
        $task = Task::factory()->create([
            'user_id' => $user->user_id,
        ]);

        // 予定を削除
        $result = $this->task_service->deleteTask($task->task_uuid, $user->user_id);

        // 検証
        $this->assertTrue($result);

        // データベースから削除されていることを確認
        $this->assertDatabaseMissing('tasks', [
            'task_id' => $task->task_id,
        ]);
    }

    /**
     * 存在しない予定の場合はfalseが返ること
     */
    public function testDeleteTaskReturnsFalseWhenTaskNotExists(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create();

        // 存在しないUUIDで予定を削除
        $result = $this->task_service->deleteTask('non-existent-uuid', $user->user_id);

        // 検証
        $this->assertFalse($result);
    }

    /**
     * 他のユーザーの予定は削除できないこと
     */
    public function testDeleteTaskReturnsFalseForOtherUserTask(): void
    {
        // テストユーザーを2人作成
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // user1に紐づく予定を作成
        $task = Task::factory()->create([
            'user_id' => $user1->user_id,
        ]);

        // user2でuser1の予定を削除しようとする
        $result = $this->task_service->deleteTask($task->task_uuid, $user2->user_id);

        // 検証
        $this->assertFalse($result);

        // user1の予定は削除されていないことを確認
        $this->assertDatabaseHas('tasks', [
            'task_id' => $task->task_id,
        ]);
    }

    /**
     * 削除された予定は一覧に表示されないこと
     */
    public function testDeletedTaskDoesNotAppearInList(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create();

        // 予定を作成
        $task1 = Task::factory()->create([
            'user_id' => $user->user_id,
            'title' => '予定1',
        ]);
        $task2 = Task::factory()->create([
            'user_id' => $user->user_id,
            'title' => '予定2',
        ]);

        // 予定1を削除
        $this->task_service->deleteTask($task1->task_uuid, $user->user_id);

        // 予定一覧を取得
        $tasks = $this->task_service->getTasksForApi($user->user_id);

        // 検証
        $this->assertCount(1, $tasks);
        $this->assertEquals($task2->title, $tasks[0]['title']);
    }
}
