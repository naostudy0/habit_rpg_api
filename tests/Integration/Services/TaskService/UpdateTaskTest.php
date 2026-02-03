<?php

namespace Tests\Integration\Services\TaskService;

use App\Models\Task;
use App\Models\User;
use App\Services\TaskService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateTaskTest extends TestCase
{
    use RefreshDatabase;

    private TaskService $task_service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->task_service = app(TaskService::class);
    }

    /**
     * 予定を更新できること
     */
    public function testUpdateTaskSuccessfully(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create();

        // 予定を作成
        $task = Task::factory()->create([
            'user_id' => $user->user_id,
            'title' => '元のタイトル',
            'scheduled_date' => '2025-12-20',
            'scheduled_time' => '10:00:00',
            'memo' => '元のメモ',
        ]);

        // 更新データ
        $update_data = [
            'title' => '更新後のタイトル',
            'scheduled_date' => '2025-12-21',
            'scheduled_time' => '14:00:00',
            'memo' => '更新後のメモ',
        ];

        // 予定を更新
        $result = $this->task_service->updateTask($task->task_uuid, $user->user_id, $update_data);

        // 検証
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertEquals($update_data['title'], $result['title']);
        $this->assertEquals($update_data['scheduled_date'], $result['scheduled_date']);
        $this->assertEquals($update_data['scheduled_time'], $result['scheduled_time']);
        $this->assertEquals($update_data['memo'], $result['memo']);
    }

    /**
     * APIレスポンス形式に整形されること
     */
    public function testUpdateTaskReturnsFormattedArray(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create();

        // 予定を作成
        $task = Task::factory()->create([
            'user_id' => $user->user_id,
        ]);

        // 更新データ
        $update_data = [
            'title' => '更新後のタイトル',
            'scheduled_date' => '2025-12-21',
            'scheduled_time' => '14:00:00',
        ];

        // 予定を更新
        $result = $this->task_service->updateTask($task->task_uuid, $user->user_id, $update_data);

        // 検証
        $this->assertArrayHasKey('uuid', $result);
        $this->assertArrayHasKey('title', $result);
        $this->assertArrayHasKey('scheduled_date', $result);
        $this->assertArrayHasKey('scheduled_time', $result);
        $this->assertArrayHasKey('memo', $result);
        $this->assertArrayHasKey('is_completed', $result);
        $this->assertArrayHasKey('created_at', $result);
        $this->assertArrayHasKey('updated_at', $result);
    }

    /**
     * メモをnullに更新できること
     */
    public function testUpdateTaskWithNullMemo(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create();

        // 予定を作成
        $task = Task::factory()->create([
            'user_id' => $user->user_id,
            'memo' => '元のメモ',
        ]);

        // 更新データ（メモをnullに）
        $update_data = [
            'title' => '更新後のタイトル',
            'scheduled_date' => '2025-12-21',
            'scheduled_time' => '14:00:00',
        ];

        // 予定を更新
        $result = $this->task_service->updateTask($task->task_uuid, $user->user_id, $update_data);

        // 検証
        $this->assertIsArray($result);
        $this->assertNull($result['memo']);
    }

    /**
     * 存在しない予定の場合はnullが返ること
     */
    public function testUpdateTaskReturnsNullWhenTaskNotExists(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create();

        // 更新データ
        $update_data = [
            'title' => '更新後のタイトル',
            'scheduled_date' => '2025-12-21',
            'scheduled_time' => '14:00:00',
        ];

        // 存在しないUUIDで予定を更新
        $result = $this->task_service->updateTask('non-existent-uuid', $user->user_id, $update_data);

        // 検証
        $this->assertNull($result);
    }

    /**
     * 他のユーザーの予定は更新できないこと
     */
    public function testUpdateTaskReturnsNullForOtherUserTask(): void
    {
        // テストユーザーを2人作成
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // user1に紐づく予定を作成
        $task = Task::factory()->create([
            'user_id' => $user1->user_id,
        ]);

        // 更新データ
        $update_data = [
            'title' => '更新後のタイトル',
            'scheduled_date' => '2025-12-21',
            'scheduled_time' => '14:00:00',
        ];

        // user2でuser1の予定を更新しようとする
        $result = $this->task_service->updateTask($task->task_uuid, $user2->user_id, $update_data);

        // 検証
        $this->assertNull($result);

        // user1の予定は変更されていないことを確認
        $task->refresh();
        $this->assertNotEquals($update_data['title'], $task->title);
    }

    /**
     * データベースに正しく保存されること
     */
    public function testUpdateTaskSavesToDatabase(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create();

        // 予定を作成
        $task = Task::factory()->create([
            'user_id' => $user->user_id,
            'title' => '元のタイトル',
            'scheduled_date' => '2025-12-20',
            'scheduled_time' => '10:00:00',
            'memo' => '元のメモ',
        ]);

        // 更新データ
        $update_data = [
            'title' => '更新後のタイトル',
            'scheduled_date' => '2025-12-21',
            'scheduled_time' => '14:00:00',
            'memo' => '更新後のメモ',
        ];

        // 予定を更新
        $this->task_service->updateTask($task->task_uuid, $user->user_id, $update_data);

        // データベースに正しく保存されていることを確認
        $this->assertDatabaseHas('tasks', [
            'task_id' => $task->task_id,
            'title' => $update_data['title'],
            'scheduled_date' => $update_data['scheduled_date'],
            'scheduled_time' => $update_data['scheduled_time'],
            'memo' => $update_data['memo'],
        ]);
    }
}
