<?php

namespace Tests\Unit\Repositories\TaskRepository;

use App\Models\Task;
use App\Models\User;
use App\Repositories\TaskRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    use RefreshDatabase;

    private TaskRepository $task_repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->task_repository = app(TaskRepository::class);
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
        $result = $this->task_repository->delete($task);

        // 検証
        $this->assertTrue($result);

        // データベースから削除されていることを確認
        $this->assertDatabaseMissing('tasks', [
            'task_id' => $task->task_id,
        ]);
    }

    /**
     * 削除された予定は取得できないこと
     */
    public function testDeletedTaskCannotBeFound(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create();

        // 予定を作成
        $task = Task::factory()->create([
            'user_id' => $user->user_id,
        ]);

        $task_uuid = $task->task_uuid;

        // 予定を削除
        $this->task_repository->delete($task);

        // 削除された予定は取得できないことを確認
        $found_task = $this->task_repository->findByUuidAndUserId($task_uuid, $user->user_id);
        $this->assertNull($found_task);
    }
}
