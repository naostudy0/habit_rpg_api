<?php

namespace Tests\Unit\Repositories\TaskRepository;

use App\Domain\Entities\Task as DomainTask;
use App\Infrastructure\Repositories\EloquentTaskRepository;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    private EloquentTaskRepository $task_repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->task_repository = app(EloquentTaskRepository::class);
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

        $domain_task = $this->task_repository->findByUuidAndUserId($task->task_uuid, $user->user_id);
        $this->assertNotNull($domain_task);

        $updated_task = new DomainTask(
            $domain_task->getTaskId(),
            $domain_task->getTaskUuid(),
            $domain_task->getUserId(),
            $update_data['title'],
            $update_data['scheduled_date'],
            $update_data['scheduled_time'],
            $update_data['memo'],
            $domain_task->isCompleted(),
            $domain_task->getCreatedAt(),
            $domain_task->getUpdatedAt()
        );

        // 予定を更新
        $result = $this->task_repository->update($updated_task);

        // 検証
        $this->assertInstanceOf(DomainTask::class, $result);
        $this->assertEquals($update_data['title'], $result->getTitle());
        $this->assertEquals($update_data['scheduled_date'], $result->getScheduledDate());
        $this->assertEquals($update_data['scheduled_time'], $result->getScheduledTime());
        $this->assertEquals($update_data['memo'], $result->getMemo());
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

        $update_data = [
            'title' => $task->title,
            'scheduled_date' => $task->scheduled_date->format('Y-m-d'),
            'scheduled_time' => $task->scheduled_time,
            'memo' => null,
        ];

        $domain_task = $this->task_repository->findByUuidAndUserId($task->task_uuid, $user->user_id);
        $this->assertNotNull($domain_task);

        $updated_task = new DomainTask(
            $domain_task->getTaskId(),
            $domain_task->getTaskUuid(),
            $domain_task->getUserId(),
            $update_data['title'],
            $update_data['scheduled_date'],
            $update_data['scheduled_time'],
            $update_data['memo'],
            $domain_task->isCompleted(),
            $domain_task->getCreatedAt(),
            $domain_task->getUpdatedAt()
        );

        // 予定を更新
        $result = $this->task_repository->update($updated_task);

        // 検証
        $this->assertInstanceOf(DomainTask::class, $result);
        $this->assertNull($result->getMemo());
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

        $domain_task = $this->task_repository->findByUuidAndUserId($task->task_uuid, $user->user_id);
        $this->assertNotNull($domain_task);

        $updated_task = new DomainTask(
            $domain_task->getTaskId(),
            $domain_task->getTaskUuid(),
            $domain_task->getUserId(),
            $update_data['title'],
            $update_data['scheduled_date'],
            $update_data['scheduled_time'],
            $update_data['memo'],
            $domain_task->isCompleted(),
            $domain_task->getCreatedAt(),
            $domain_task->getUpdatedAt()
        );

        // 予定を更新
        $this->task_repository->update($updated_task);

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
