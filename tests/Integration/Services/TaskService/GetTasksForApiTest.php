<?php

namespace Tests\Integration\Services\TaskService;

use App\Models\Task;
use App\Models\User;
use App\Services\TaskService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetTasksForApiTest extends TestCase
{
    use RefreshDatabase;

    private TaskService $task_service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->task_service = app(TaskService::class);
    }

    /**
     * APIレスポンス形式に整形できること
     */
    public function testGetTasksForApiReturnsFormattedArray(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create();

        // テストユーザーに紐づく予定を作成
        Task::factory()->create([
            'user_id' => $user->user_id,
            'is_completed' => false,
        ]);

        // APIレスポンス形式で予定一覧を取得
        $result = $this->task_service->getTasksForApi($user->user_id);

        // 検証
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertArrayHasKey('uuid', $result[0]);
        $this->assertArrayHasKey('title', $result[0]);
        $this->assertArrayHasKey('scheduled_date', $result[0]);
        $this->assertArrayHasKey('scheduled_time', $result[0]);
        $this->assertArrayHasKey('memo', $result[0]);
        $this->assertArrayHasKey('is_completed', $result[0]);
        $this->assertArrayHasKey('created_at', $result[0]);
        $this->assertArrayHasKey('updated_at', $result[0]);
    }

    /**
     * 日付フォーマットが正しいこと
     */
    public function testGetTasksForApiFormatsDateCorrectly(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create();

        // テストユーザーに紐づく予定を作成
        Task::factory()->create([
            'user_id' => $user->user_id,
            'scheduled_date' => '2025-12-20',
            'scheduled_time' => '10:00:00',
        ]);

        // APIレスポンス形式で予定一覧を取得
        $result = $this->task_service->getTasksForApi($user->user_id);

        // 検証
        $this->assertEquals('2025-12-20', $result[0]['scheduled_date']);
        $this->assertEquals('10:00:00', $result[0]['scheduled_time']);
    }

    /**
     * UUIDが正しくマッピングされること
     */
    public function testGetTasksForApiMapsUuidCorrectly(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create();

        // テストユーザーに紐づく予定を作成
        $task = Task::factory()->create([
            'user_id' => $user->user_id,
        ]);

        // APIレスポンス形式で予定一覧を取得
        $result = $this->task_service->getTasksForApi($user->user_id);

        // 検証
        $this->assertEquals($task->task_uuid, $result[0]['uuid']);
    }

    /**
     * ISO8601形式のタイムスタンプが正しいこと
     */
    public function testGetTasksForApiFormatsTimestampsAsIso8601(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create();

        // テストユーザーに紐づく予定を作成
        $task = Task::factory()->create([
            'user_id' => $user->user_id,
        ]);

        // APIレスポンス形式で予定一覧を取得
        $result = $this->task_service->getTasksForApi($user->user_id);

        // 検証
        $this->assertEquals($task->created_at->toIso8601String(), $result[0]['created_at']);
        $this->assertEquals($task->updated_at->toIso8601String(), $result[0]['updated_at']);
        // ISO8601形式の検証（例: 2025-12-15T10:30:00+00:00）
        $this->assertMatchesRegularExpression(
            '/^\\d{4}-\\d{2}-\\d{2}T\\d{2}:\\d{2}:\\d{2}[+-]\\d{2}:\\d{2}$/',
            $result[0]['created_at']
        );
    }

    /**
     * 複数の予定が正しく整形されること
     */
    public function testGetTasksForApiFormatsMultipleTasks(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create();

        // テストユーザーに紐づく予定を複数作成（ソート順を保証するためscheduled_dateとscheduled_timeを指定）
        Task::factory()->create([
            'user_id' => $user->user_id,
            'title' => '予定1',
            'scheduled_date' => '2025-12-20',
            'scheduled_time' => '10:00:00',
            'is_completed' => false,
        ]);
        Task::factory()->create([
            'user_id' => $user->user_id,
            'title' => '予定2',
            'scheduled_date' => '2025-12-21',
            'scheduled_time' => '10:00:00',
            'is_completed' => true,
        ]);

        // APIレスポンス形式で予定一覧を取得
        $result = $this->task_service->getTasksForApi($user->user_id);

        // 検証
        $this->assertCount(2, $result);
        $this->assertEquals('予定1', $result[0]['title']);
        $this->assertEquals('予定2', $result[1]['title']);
        $this->assertFalse($result[0]['is_completed']);
        $this->assertTrue($result[1]['is_completed']);
    }

    /**
     * 予定がない場合は空の配列が返ること
     */
    public function testGetTasksForApiReturnsEmptyArrayWhenNoTasks(): void
    {
        // テストユーザーを作成（予定なし）
        $user = User::factory()->create();

        // APIレスポンス形式で予定一覧を取得
        $result = $this->task_service->getTasksForApi($user->user_id);

        // 検証
        $this->assertIsArray($result);
        $this->assertCount(0, $result);
        $this->assertEmpty($result);
    }

    /**
     * メモがnullの場合も正しく処理されること
     */
    public function testGetTasksForApiHandlesNullMemo(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create();

        // メモがnullの予定を作成
        Task::factory()->create([
            'user_id' => $user->user_id,
            'memo' => null,
        ]);

        // APIレスポンス形式で予定一覧を取得
        $result = $this->task_service->getTasksForApi($user->user_id);

        // 検証
        $this->assertNull($result[0]['memo']);
    }
}
