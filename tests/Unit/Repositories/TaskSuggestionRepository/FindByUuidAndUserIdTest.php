<?php

namespace Tests\Unit\Repositories\TaskSuggestionRepository;

use App\Models\TaskSuggestion;
use App\Models\User;
use App\Repositories\TaskSuggestionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FindByUuidAndUserIdTest extends TestCase
{
    use RefreshDatabase;

    private TaskSuggestionRepository $task_suggestion_repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->task_suggestion_repository = app(TaskSuggestionRepository::class);
    }

    /**
     * 存在するUUIDとユーザーIDで提案を取得できること
     */
    public function testFindByUuidAndUserIdReturnsSuggestionWhenExists(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create();

        // テストユーザーに紐づく提案を作成
        $suggestion = TaskSuggestion::factory()->create([
            'user_id' => $user->user_id,
        ]);

        // UUIDとユーザーIDで提案を取得
        $result = $this->task_suggestion_repository->findByUuidAndUserId(
            $suggestion->task_suggestion_uuid,
            $user->user_id
        );

        // 検証
        $this->assertNotNull($result);
        $this->assertInstanceOf(TaskSuggestion::class, $result);
        $this->assertEquals($suggestion->task_suggestion_id, $result->task_suggestion_id);
        $this->assertEquals($suggestion->task_suggestion_uuid, $result->task_suggestion_uuid);
        $this->assertEquals($user->user_id, $result->user_id);
    }

    /**
     * 存在しないUUIDの場合はnullが返ること
     */
    public function testFindByUuidAndUserIdReturnsNullWhenUuidNotExists(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create();

        // 存在しないUUIDで提案を取得
        $result = $this->task_suggestion_repository->findByUuidAndUserId('non-existent-uuid', $user->user_id);

        // 検証
        $this->assertNull($result);
    }

    /**
     * 他のユーザーの提案は取得されないこと
     */
    public function testFindByUuidAndUserIdReturnsNullForOtherUserSuggestion(): void
    {
        // テストユーザーを2人作成
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // user1に紐づく提案を作成
        $suggestion = TaskSuggestion::factory()->create([
            'user_id' => $user1->user_id,
        ]);

        // user2でuser1の提案を取得しようとする
        $result = $this->task_suggestion_repository->findByUuidAndUserId(
            $suggestion->task_suggestion_uuid,
            $user2->user_id
        );

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

        // user1に紐づく提案を作成
        $suggestion1 = TaskSuggestion::factory()->create([
            'user_id' => $user1->user_id,
        ]);

        // user2に紐づく提案を作成
        $suggestion2 = TaskSuggestion::factory()->create([
            'user_id' => $user2->user_id,
        ]);

        // user1でuser1の提案を取得（成功）
        $result1 = $this->task_suggestion_repository->findByUuidAndUserId(
            $suggestion1->task_suggestion_uuid,
            $user1->user_id
        );
        $this->assertNotNull($result1);
        $this->assertEquals($suggestion1->task_suggestion_id, $result1->task_suggestion_id);

        // user1でuser2の提案を取得（失敗）
        $result2 = $this->task_suggestion_repository->findByUuidAndUserId(
            $suggestion2->task_suggestion_uuid,
            $user1->user_id
        );
        $this->assertNull($result2);
    }
}
