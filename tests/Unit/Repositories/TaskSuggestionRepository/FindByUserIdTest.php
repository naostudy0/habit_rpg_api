<?php

namespace Tests\Unit\Repositories\TaskSuggestionRepository;

use App\Models\TaskSuggestion;
use App\Models\User;
use App\Repositories\TaskSuggestionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FindByUserIdTest extends TestCase
{
    use RefreshDatabase;

    private TaskSuggestionRepository $task_suggestion_repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->task_suggestion_repository = app(TaskSuggestionRepository::class);
    }

    /**
     * 存在するユーザーIDで提案一覧を取得できること
     */
    public function testFindByUserIdReturnsSuggestionsWhenUserExists(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create();

        // テストユーザーに紐づく提案を作成
        $suggestion1 = TaskSuggestion::factory()->create([
            'user_id' => $user->user_id,
            'created_at' => now()->subSeconds(2),
        ]);
        $suggestion2 = TaskSuggestion::factory()->create([
            'user_id' => $user->user_id,
            'created_at' => now()->subSeconds(1),
        ]);
        $suggestion3 = TaskSuggestion::factory()->create([
            'user_id' => $user->user_id,
            'created_at' => now(),
        ]);

        // ユーザーIDで提案一覧を取得
        $result = $this->task_suggestion_repository->findByUserId($user->user_id);

        // 検証
        $this->assertCount(3, $result);
        // 作成日時の降順でソートされていること
        $this->assertEquals($suggestion3->task_suggestion_id, $result[0]->task_suggestion_id);
        $this->assertEquals($suggestion2->task_suggestion_id, $result[1]->task_suggestion_id);
        $this->assertEquals($suggestion1->task_suggestion_id, $result[2]->task_suggestion_id);
    }

    /**
     * 存在しないユーザーIDの場合は空のコレクションが返ること
     */
    public function testFindByUserIdReturnsEmptyCollectionWhenUserNotExists(): void
    {
        // 存在しないユーザーIDで提案一覧を取得
        $result = $this->task_suggestion_repository->findByUserId(0);

        // 検証
        $this->assertCount(0, $result);
        $this->assertTrue($result->isEmpty());
    }

    /**
     * 他のユーザーの提案は取得されないこと
     */
    public function testFindByUserIdReturnsOnlySuggestionsForSpecifiedUser(): void
    {
        // テストユーザーを2人作成
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // 各ユーザーに提案を作成
        $suggestion1 = TaskSuggestion::factory()->create([
            'user_id' => $user1->user_id,
        ]);
        $suggestion2 = TaskSuggestion::factory()->create([
            'user_id' => $user2->user_id,
        ]);

        // user1の提案一覧を取得
        $result = $this->task_suggestion_repository->findByUserId($user1->user_id);

        // 検証
        $this->assertCount(1, $result);
        $this->assertEquals($suggestion1->task_suggestion_id, $result[0]->task_suggestion_id);
        $this->assertNotEquals($suggestion2->task_suggestion_id, $result[0]->task_suggestion_id);
    }

    /**
     * 提案がない場合は空のコレクションが返ること
     */
    public function testFindByUserIdReturnsEmptyCollectionWhenNoSuggestions(): void
    {
        // テストユーザーを作成（提案なし）
        $user = User::factory()->create();

        // ユーザーIDで提案一覧を取得
        $result = $this->task_suggestion_repository->findByUserId($user->user_id);

        // 検証
        $this->assertCount(0, $result);
        $this->assertTrue($result->isEmpty());
    }
}
