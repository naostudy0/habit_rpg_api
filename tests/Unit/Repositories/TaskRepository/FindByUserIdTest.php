<?php

namespace Tests\Unit\Repositories\TaskRepository;

use App\Models\Task;
use App\Models\User;
use App\Repositories\TaskRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FindByUserIdTest extends TestCase
{
    use RefreshDatabase;

    private TaskRepository $task_repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->task_repository = app(TaskRepository::class);
    }

    /**
     * 存在するユーザーIDで予定一覧を取得できること
     */
    public function testFindByUserIdReturnsTasksWhenUserExists(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create();

        // テストユーザーに紐づく予定を作成
        $task1 = Task::factory()->create([
            'user_id' => $user->user_id,
            'scheduled_date' => '2025-12-20',
            'scheduled_time' => '10:00:00',
        ]);
        $task2 = Task::factory()->create([
            'user_id' => $user->user_id,
            'scheduled_date' => '2025-12-19',
            'scheduled_time' => '14:00:00',
        ]);
        $task3 = Task::factory()->create([
            'user_id' => $user->user_id,
            'scheduled_date' => '2025-12-20',
            'scheduled_time' => '09:00:00',
        ]);

        // ユーザーIDで予定一覧を取得
        $result = $this->task_repository->findByUserId($user->user_id);

        // 検証
        $this->assertCount(3, $result);
        // ソート順の検証: scheduled_date昇順、scheduled_time昇順
        $this->assertEquals($task2->task_id, $result[0]->task_id); // 2025-12-19 14:00:00
        $this->assertEquals($task3->task_id, $result[1]->task_id); // 2025-12-20 09:00:00
        $this->assertEquals($task1->task_id, $result[2]->task_id); // 2025-12-20 10:00:00
    }

    /**
     * 存在しないユーザーIDの場合は空のコレクションが返ること
     */
    public function testFindByUserIdReturnsEmptyCollectionWhenUserNotExists(): void
    {
        // 存在しないユーザーIDで予定一覧を取得
        $result = $this->task_repository->findByUserId(0);

        // 検証
        $this->assertCount(0, $result);
        $this->assertTrue($result->isEmpty());
    }

    /**
     * 他のユーザーの予定は取得されないこと
     */
    public function testFindByUserIdReturnsOnlyTasksForSpecifiedUser(): void
    {
        // テストユーザーを2人作成
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // 各ユーザーに予定を作成
        $task1 = Task::factory()->create([
            'user_id' => $user1->user_id,
        ]);
        $task2 = Task::factory()->create([
            'user_id' => $user2->user_id,
        ]);

        // user1の予定一覧を取得
        $result = $this->task_repository->findByUserId($user1->user_id);

        // 検証
        $this->assertCount(1, $result);
        $this->assertEquals($task1->task_id, $result[0]->task_id);
        $this->assertNotEquals($task2->task_id, $result[0]->task_id);
    }

    /**
     * 予定がない場合は空のコレクションが返ること
     */
    public function testFindByUserIdReturnsEmptyCollectionWhenNoTasks(): void
    {
        // テストユーザーを作成（予定なし）
        $user = User::factory()->create();

        // ユーザーIDで予定一覧を取得
        $result = $this->task_repository->findByUserId($user->user_id);

        // 検証
        $this->assertCount(0, $result);
        $this->assertTrue($result->isEmpty());
    }
}
