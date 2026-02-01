<?php

namespace Tests\Unit\Repositories\TaskRepository;

use App\Domain\Entities\Task as DomainTask;
use App\Infrastructure\Repositories\EloquentTaskRepository;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateCompletionTest extends TestCase
{
    use RefreshDatabase;

    private EloquentTaskRepository $task_repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->task_repository = app(EloquentTaskRepository::class);
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

        $domain_task = $this->task_repository->findByUuidAndUserId($task->task_uuid, $user->user_id);
        $this->assertNotNull($domain_task);

        // 完了状態をtrueに更新
        $result = $this->task_repository->updateCompletion($domain_task, true);

        // 検証
        $this->assertInstanceOf(DomainTask::class, $result);
        $this->assertTrue($result->isCompleted());
        $this->assertEquals($task->task_id, $result->getTaskId());

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

        $domain_task = $this->task_repository->findByUuidAndUserId($task->task_uuid, $user->user_id);
        $this->assertNotNull($domain_task);

        // 完了状態をfalseに更新
        $result = $this->task_repository->updateCompletion($domain_task, false);

        // 検証
        $this->assertInstanceOf(DomainTask::class, $result);
        $this->assertFalse($result->isCompleted());
        $this->assertEquals($task->task_id, $result->getTaskId());

        // データベースに正しく保存されていることを確認
        $this->assertDatabaseHas('tasks', [
            'task_id' => $task->task_id,
            'is_completed' => false,
        ]);
    }

    /**
     * 更新されたTaskオブジェクトが返されること
     */
    public function testUpdateCompletionReturnsUpdatedTask(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create();

        // 予定を作成
        $task = Task::factory()->create([
            'user_id' => $user->user_id,
            'is_completed' => false,
        ]);

        $domain_task = $this->task_repository->findByUuidAndUserId($task->task_uuid, $user->user_id);
        $this->assertNotNull($domain_task);

        // 完了状態を更新
        $result = $this->task_repository->updateCompletion($domain_task, true);

        // 検証
        $this->assertTrue($result->isCompleted());
    }

    /**
     * 他のフィールドは変更されないこと
     */
    public function testUpdateCompletionDoesNotChangeOtherFields(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create();

        // 予定を作成
        $task = Task::factory()->create([
            'user_id' => $user->user_id,
            'is_completed' => false,
        ]);

        $domain_task = $this->task_repository->findByUuidAndUserId($task->task_uuid, $user->user_id);
        $this->assertNotNull($domain_task);

        $original_title = $domain_task->getTitle();
        $original_scheduled_date = $domain_task->getScheduledDate();
        $original_scheduled_time = $domain_task->getScheduledTime();
        $original_memo = $domain_task->getMemo();

        // 完了状態を更新
        $result = $this->task_repository->updateCompletion($domain_task, true);

        // 検証
        $this->assertEquals($original_title, $result->getTitle());
        $this->assertEquals($original_scheduled_date, $result->getScheduledDate());
        $this->assertEquals($original_scheduled_time, $result->getScheduledTime());
        $this->assertEquals($original_memo, $result->getMemo());
        $this->assertTrue($result->isCompleted());
    }
}
