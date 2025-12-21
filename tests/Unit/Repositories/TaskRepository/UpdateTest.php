<?php

namespace Tests\Unit\Repositories\TaskRepository;

use App\Models\Task;
use App\Models\User;
use App\Repositories\TaskRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    private TaskRepository $task_repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->task_repository = app(TaskRepository::class);
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
        $result = $this->task_repository->update($task, $update_data);

        // 検証
        $this->assertInstanceOf(Task::class, $result);
        $this->assertEquals($update_data['title'], $result->title);
        $this->assertEquals($update_data['scheduled_date'], $result->scheduled_date->format('Y-m-d'));
        $this->assertEquals($update_data['scheduled_time'], $result->scheduled_time);
        $this->assertEquals($update_data['memo'], $result->memo);
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
            'memo' => null,
        ];

        // 予定を更新
        $result = $this->task_repository->update($task, $update_data);

        // 検証
        $this->assertInstanceOf(Task::class, $result);
        $this->assertNull($result->memo);
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
        $this->task_repository->update($task, $update_data);

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
