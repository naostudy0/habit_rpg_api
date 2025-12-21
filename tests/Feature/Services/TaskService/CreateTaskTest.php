<?php

namespace Tests\Feature\Services\TaskService;

use App\Models\User;
use App\Services\TaskService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateTaskTest extends TestCase
{
    use RefreshDatabase;

    private TaskService $task_service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->task_service = app(TaskService::class);
    }

    /**
     * 予定を作成できること
     */
    public function testCreateTaskSuccessfully(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create();

        // 予定データを作成
        $task_data = [
            'title' => 'テスト予定',
            'scheduled_date' => '2025-12-20',
            'scheduled_time' => '10:00:00',
            'memo' => 'テストメモ',
        ];

        // 予定を作成
        $result = $this->task_service->createTask($user->user_id, $task_data);

        // 検証
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertEquals($task_data['title'], $result['title']);
        $this->assertEquals($task_data['scheduled_date'], $result['scheduled_date']);
        $this->assertEquals($task_data['scheduled_time'], $result['scheduled_time']);
        $this->assertEquals($task_data['memo'], $result['memo']);
        $this->assertFalse($result['is_completed']);
        $this->assertNotNull($result['uuid']);
    }

    /**
     * APIレスポンス形式に整形されること
     */
    public function testCreateTaskReturnsFormattedArray(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create();

        // 予定データを作成
        $task_data = [
            'title' => 'テスト予定',
            'scheduled_date' => '2025-12-20',
            'scheduled_time' => '10:00:00',
        ];

        // 予定を作成
        $result = $this->task_service->createTask($user->user_id, $task_data);

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
     * メモがnullでも作成できること
     */
    public function testCreateTaskWithNullMemo(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create();

        // 予定データを作成（メモなし）
        $task_data = [
            'title' => 'テスト予定',
            'scheduled_date' => '2025-12-20',
            'scheduled_time' => '10:00:00',
        ];

        // 予定を作成
        $result = $this->task_service->createTask($user->user_id, $task_data);

        // 検証
        $this->assertIsArray($result);
        $this->assertNull($result['memo']);
    }

    /**
     * is_completedがfalseで作成されること
     */
    public function testCreateTaskSetsIsCompletedToFalse(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create();

        // 予定データを作成
        $task_data = [
            'title' => 'テスト予定',
            'scheduled_date' => '2025-12-20',
            'scheduled_time' => '10:00:00',
        ];

        // 予定を作成
        $result = $this->task_service->createTask($user->user_id, $task_data);

        // 検証
        $this->assertFalse($result['is_completed']);
    }

    /**
     * 日付フォーマットが正しいこと
     */
    public function testCreateTaskFormatsDateCorrectly(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create();

        // 予定データを作成
        $task_data = [
            'title' => 'テスト予定',
            'scheduled_date' => '2025-12-20',
            'scheduled_time' => '10:00:00',
        ];

        // 予定を作成
        $result = $this->task_service->createTask($user->user_id, $task_data);

        // 検証
        $this->assertEquals($task_data['scheduled_date'], $result['scheduled_date']);
        $this->assertEquals($task_data['scheduled_time'], $result['scheduled_time']);
    }

    /**
     * ISO8601形式のタイムスタンプが正しいこと
     */
    public function testCreateTaskFormatsTimestampsAsIso8601(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create();

        // 予定データを作成
        $task_data = [
            'title' => 'テスト予定',
            'scheduled_date' => '2025-12-15',
            'scheduled_time' => '10:30:00',
        ];

        // 予定を作成
        $result = $this->task_service->createTask($user->user_id, $task_data);

        // 検証
        // ISO8601形式の検証（例: 2025-12-15T10:30:00+00:00）
        $this->assertMatchesRegularExpression(
            '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}[+-]\d{2}:\d{2}$/',
            $result['created_at']
        );
        $this->assertMatchesRegularExpression(
            '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}[+-]\d{2}:\d{2}$/',
            $result['updated_at']
        );
    }

    /**
     * データベースに正しく保存されること
     */
    public function testCreateTaskSavesToDatabase(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create();

        // 予定データを作成
        $task_data = [
            'title' => 'テスト予定',
            'scheduled_date' => '2025-12-20',
            'scheduled_time' => '10:00:00',
            'memo' => 'テストメモ',
        ];

        // 予定を作成
        $result = $this->task_service->createTask($user->user_id, $task_data);

        // データベースに正しく保存されていることを確認
        $this->assertDatabaseHas('tasks', [
            'task_uuid' => $result['uuid'],
            'user_id' => $user->user_id,
            'title' => $task_data['title'],
            'scheduled_date' => $task_data['scheduled_date'],
            'scheduled_time' => $task_data['scheduled_time'],
            'memo' => $task_data['memo'],
            'is_completed' => false,
        ]);
    }
}
