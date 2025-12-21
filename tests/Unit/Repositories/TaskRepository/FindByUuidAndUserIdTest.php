<?php

namespace Tests\Unit\Repositories\TaskRepository;

use App\Models\Task;
use App\Models\User;
use App\Repositories\TaskRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FindByUuidAndUserIdTest extends TestCase
{
    use RefreshDatabase;

    private TaskRepository $task_repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->task_repository = app(TaskRepository::class);
    }

    /**
     * 存在するUUIDとユーザーIDで予定を取得できること
     */
    public function testFindByUuidAndUserIdReturnsTaskWhenExists(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create();

        // テストユーザーに紐づく予定を作成
        $task = Task::factory()->create([
            'user_id' => $user->user_id,
        ]);

        // UUIDとユーザーIDで予定を取得
        $result = $this->task_repository->findByUuidAndUserId($task->task_uuid, $user->user_id);

        // 検証
        $this->assertNotNull($result);
        $this->assertInstanceOf(Task::class, $result);
        $this->assertEquals($task->task_id, $result->task_id);
        $this->assertEquals($task->task_uuid, $result->task_uuid);
        $this->assertEquals($user->user_id, $result->user_id);
    }

    /**
     * 存在しないUUIDの場合はnullが返ること
     */
    public function testFindByUuidAndUserIdReturnsNullWhenUuidNotExists(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create();

        // 存在しないUUIDで予定を取得
        $result = $this->task_repository->findByUuidAndUserId('non-existent-uuid', $user->user_id);

        // 検証
        $this->assertNull($result);
    }

    /**
     * 他のユーザーの予定は取得されないこと
     */
    public function testFindByUuidAndUserIdReturnsNullForOtherUserTask(): void
    {
        // テストユーザーを2人作成
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // user1に紐づく予定を作成
        $task = Task::factory()->create([
            'user_id' => $user1->user_id,
        ]);

        // user2でuser1の予定を取得しようとする
        $result = $this->task_repository->findByUuidAndUserId($task->task_uuid, $user2->user_id);

        // 検証
        $this->assertNull($result);
    }

    /**
     * UUIDとユーザーIDの両方が一致する場合のみ取得できること
     */
    public function testFindByUuidAndUserIdRequiresBothUuidAndUserId(): void
    {
        // テストユーザーを2人作成
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // user1に紐づく予定を作成
        $task1 = Task::factory()->create([
            'user_id' => $user1->user_id,
        ]);

        // user2に紐づく予定を作成
        $task2 = Task::factory()->create([
            'user_id' => $user2->user_id,
        ]);

        // user1でuser1の予定を取得（成功）
        $result1 = $this->task_repository->findByUuidAndUserId($task1->task_uuid, $user1->user_id);
        $this->assertNotNull($result1);
        $this->assertEquals($task1->task_id, $result1->task_id);

        // user1でuser2の予定を取得（失敗）
        $result2 = $this->task_repository->findByUuidAndUserId($task2->task_uuid, $user1->user_id);
        $this->assertNull($result2);
    }
}
