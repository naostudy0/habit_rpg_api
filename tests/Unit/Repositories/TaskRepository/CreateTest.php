<?php

namespace Tests\Unit\Repositories\TaskRepository;

use App\Models\Task;
use App\Models\User;
use App\Repositories\TaskRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    private TaskRepository $task_repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->task_repository = app(TaskRepository::class);
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
            'user_id' => $user->user_id,
            'title' => 'テスト予定',
            'scheduled_date' => '2025-12-20',
            'scheduled_time' => '10:00:00',
            'memo' => 'テストメモ',
            'is_completed' => false,
        ];

        // 予定を作成
        $task = $this->task_repository->create($task_data);

        // 検証
        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals($task_data['title'], $task->title);
        $this->assertEquals($task_data['scheduled_date'], $task->scheduled_date->format('Y-m-d'));
        $this->assertEquals($task_data['scheduled_time'], $task->scheduled_time);
        $this->assertEquals($task_data['memo'], $task->memo);
        $this->assertFalse($task->is_completed);
        $this->assertNotNull($task->task_uuid);
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
            'user_id' => $user->user_id,
            'title' => 'テスト予定',
            'scheduled_date' => '2025-12-20',
            'scheduled_time' => '10:00:00',
            'memo' => null,
        ];

        // 予定を作成
        $task = $this->task_repository->create($task_data);

        // 検証
        $this->assertInstanceOf(Task::class, $task);
        $this->assertNull($task->memo);
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
            'user_id' => $user->user_id,
            'title' => 'テスト予定',
            'scheduled_date' => '2025-12-20',
            'scheduled_time' => '10:00:00',
            'memo' => 'テストメモ',
            'is_completed' => false,
        ];

        // 予定を作成
        $task = $this->task_repository->create($task_data);

        // データベースに正しく保存されていることを確認
        $this->assertDatabaseHas('tasks', [
            'task_id' => $task->task_id,
            'user_id' => $user->user_id,
            'title' => $task_data['title'],
            'scheduled_date' => $task_data['scheduled_date'],
            'scheduled_time' => $task_data['scheduled_time'],
            'memo' => $task_data['memo'],
            'is_completed' => $task_data['is_completed'],
        ]);
    }
}
