<?php

namespace Tests\Unit\Repositories\TaskRepository;

use App\Domain\Entities\Task;
use App\Infrastructure\Repositories\EloquentTaskRepository;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    private EloquentTaskRepository $task_repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->task_repository = app(EloquentTaskRepository::class);
    }

    /**
     * 予定を作成できること
     */
    public function testCreateTaskSuccessfully(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create();

        $task_data = [
            'title' => 'テスト予定',
            'scheduled_date' => '2025-12-20',
            'scheduled_time' => '10:00:00',
            'memo' => 'テストメモ',
        ];

        // 予定を作成
        $task = $this->task_repository->create(new Task(
            null,
            '',
            $user->user_id,
            $task_data['title'],
            $task_data['scheduled_date'],
            $task_data['scheduled_time'],
            $task_data['memo'],
            false,
            null,
            null
        ));

        // 検証
        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals($task_data['title'], $task->getTitle());
        $this->assertEquals($task_data['scheduled_date'], $task->getScheduledDate());
        $this->assertEquals($task_data['scheduled_time'], $task->getScheduledTime());
        $this->assertEquals($task_data['memo'], $task->getMemo());
        $this->assertFalse($task->isCompleted());
        $this->assertNotNull($task->getTaskUuid());
    }

    /**
     * メモがnullでも作成できること
     */
    public function testCreateTaskWithNullMemo(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create();

        $task_data = [
            'title' => 'テスト予定',
            'scheduled_date' => '2025-12-20',
            'scheduled_time' => '10:00:00',
            'memo' => null,
        ];

        // 予定を作成
        $task = $this->task_repository->create(new Task(
            null,
            '',
            $user->user_id,
            $task_data['title'],
            $task_data['scheduled_date'],
            $task_data['scheduled_time'],
            $task_data['memo'],
            false,
            null,
            null
        ));

        // 検証
        $this->assertInstanceOf(Task::class, $task);
        $this->assertNull($task->getMemo());
    }

    /**
     * データベースに正しく保存されること
     */
    public function testCreateTaskSavesToDatabase(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create();

        $task_data = [
            'title' => 'テスト予定',
            'scheduled_date' => '2025-12-20',
            'scheduled_time' => '10:00:00',
            'memo' => 'テストメモ',
        ];

        // 予定を作成
        $task = $this->task_repository->create(new Task(
            null,
            '',
            $user->user_id,
            $task_data['title'],
            $task_data['scheduled_date'],
            $task_data['scheduled_time'],
            $task_data['memo'],
            false,
            null,
            null
        ));

        // データベースに正しく保存されていることを確認
        $this->assertDatabaseHas('tasks', [
            'task_id' => $task->getTaskId(),
            'user_id' => $user->user_id,
            'title' => $task_data['title'],
            'scheduled_date' => $task_data['scheduled_date'],
            'scheduled_time' => $task_data['scheduled_time'],
            'memo' => $task_data['memo'],
            'is_completed' => false,
        ]);
    }
}
