<?php

namespace Tests\Integration\Services\TaskService;

use App\Models\Task;
use App\Models\User;
use App\Services\TaskService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateCompletionTest extends TestCase
{
    use RefreshDatabase;

    private TaskService $task_service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->task_service = app(TaskService::class);
    }

    /**
     * 予定の完了状態をtrueに更新できること
     */
    public function testUpdateCompletionUpdatesToTrue(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create();

        // 未完了の予定を作成
        $task = Task::factory()->create([
            'user_id' => $user->user_id,
            'is_completed' => false,
        ]);

        // 完了状態をtrueに更新
        $result = $this->task_service->updateCompletion($task->task_uuid, $user->user_id, true);

        // 検証
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertTrue($result['is_completed']);
        $this->assertEquals($task->task_uuid, $result['uuid']);
        $this->assertEquals($task->title, $result['title']);
        $this->assertEquals($task->scheduled_date->format('Y-m-d'), $result['scheduled_date']);
        $this->assertEquals($task->scheduled_time, $result['scheduled_time']);
        $this->assertEquals($task->memo, $result['memo']);
        $this->assertTrue($result['is_completed']);

        // データベースに正しく保存されていることを確認
        $this->assertDatabaseHas('tasks', [
            'task_id' => $task->task_id,
            'is_completed' => true,
        ]);
    }

    /**
     * 予定の完了状態をfalseに更新できること
     */
    public function testUpdateCompletionUpdatesToFalse(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create();

        // 完了済みの予定を作成
        $task = Task::factory()->create([
            'user_id' => $user->user_id,
            'is_completed' => true,
        ]);

        // 完了状態をfalseに更新
        $result = $this->task_service->updateCompletion($task->task_uuid, $user->user_id, false);

        // 検証
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertFalse($result['is_completed']);

        // データベースに正しく保存されていることを確認
        $this->assertDatabaseHas('tasks', [
            'task_id' => $task->task_id,
            'is_completed' => false,
        ]);
    }

    /**
     * APIレスポンス形式に整形されること
     */
    public function testUpdateCompletionReturnsFormattedArray(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create();

        // 予定を作成
        $task = Task::factory()->create([
            'user_id' => $user->user_id,
        ]);

        // 完了状態を更新
        $result = $this->task_service->updateCompletion($task->task_uuid, $user->user_id, true);

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
     * 日付フォーマットが正しいこと
     */
    public function testUpdateCompletionFormatsDateCorrectly(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create();

        // 予定を作成
        $task = Task::factory()->create([
            'user_id' => $user->user_id,
            'scheduled_date' => '2025-12-20',
            'scheduled_time' => '10:00:00',
        ]);

        // 完了状態を更新
        $result = $this->task_service->updateCompletion($task->task_uuid, $user->user_id, true);

        // 検証
        $this->assertEquals('2025-12-20', $result['scheduled_date']);
        $this->assertEquals('10:00:00', $result['scheduled_time']);
    }

    /**
     * ISO8601形式のタイムスタンプが正しいこと
     */
    public function testUpdateCompletionFormatsTimestampsAsIso8601(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create();

        // 予定を作成
        $task = Task::factory()->create([
            'user_id' => $user->user_id,
        ]);

        // 完了状態を更新
        $result = $this->task_service->updateCompletion($task->task_uuid, $user->user_id, true);

        // 検証
        $task->refresh();
        $this->assertEquals($task->created_at->toIso8601String(), $result['created_at']);
        $this->assertEquals($task->updated_at->toIso8601String(), $result['updated_at']);
        // ISO8601形式の検証（例: 2025-12-15T10:30:00+00:00）
        $this->assertMatchesRegularExpression(
            '/^\\d{4}-\\d{2}-\\d{2}T\\d{2}:\\d{2}:\\d{2}[+-]\\d{2}:\\d{2}$/',
            $result['created_at']
        );
    }

    /**
     * 存在しない予定の場合は空の配列が返ること
     */
    public function testUpdateCompletionReturnsEmptyArrayWhenTaskNotExists(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create();

        // 存在しないUUIDで完了状態を更新
        $result = $this->task_service->updateCompletion('non-existent-uuid', $user->user_id, true);

        // 検証
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /**
     * 他のユーザーの予定は更新できないこと
     */
    public function testUpdateCompletionReturnsEmptyArrayForOtherUserTask(): void
    {
        // テストユーザーを2人作成
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // user1に紐づく予定を作成
        $task = Task::factory()->create([
            'user_id' => $user1->user_id,
            'is_completed' => false,
        ]);

        // user2でuser1の予定を更新しようとする
        $result = $this->task_service->updateCompletion($task->task_uuid, $user2->user_id, true);

        // 検証
        $this->assertIsArray($result);
        $this->assertEmpty($result);

        // user1の予定は変更されていないことを確認
        $this->assertDatabaseHas('tasks', [
            'task_id' => $task->task_id,
            'is_completed' => false,
        ]);
    }
}
