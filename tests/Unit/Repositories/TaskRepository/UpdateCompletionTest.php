<?php

namespace Tests\Unit\Repositories\TaskRepository;

use App\Models\Task;
use App\Models\User;
use App\Repositories\TaskRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateCompletionTest extends TestCase
{
    use RefreshDatabase;

    private TaskRepository $task_repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->task_repository = app(TaskRepository::class);
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
        $result = $this->task_repository->updateCompletion($task, true);

        // 検証
        $this->assertInstanceOf(Task::class, $result);
        $this->assertTrue($result->is_completed);
        $this->assertEquals($task->task_id, $result->task_id);

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
        $result = $this->task_repository->updateCompletion($task, false);

        // 検証
        $this->assertInstanceOf(Task::class, $result);
        $this->assertFalse($result->is_completed);
        $this->assertEquals($task->task_id, $result->task_id);

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

        // 完了状態を更新
        $result = $this->task_repository->updateCompletion($task, true);

        // 検証
        $this->assertSame($task, $result);
        $this->assertTrue($result->is_completed);
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

        $original_title = $task->title;
        $original_scheduled_date = $task->scheduled_date;
        $original_scheduled_time = $task->scheduled_time;
        $original_memo = $task->memo;

        // 完了状態を更新
        $result = $this->task_repository->updateCompletion($task, true);

        // 検証
        $this->assertEquals($original_title, $result->title);
        $this->assertEquals($original_scheduled_date->format('Y-m-d'), $result->scheduled_date->format('Y-m-d'));
        $this->assertEquals($original_scheduled_time, $result->scheduled_time);
        $this->assertEquals($original_memo, $result->memo);
        $this->assertTrue($result->is_completed);
    }
}
