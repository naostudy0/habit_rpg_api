<?php

namespace Tests\Unit\Repositories\TaskSuggestionRepository;

use App\Domain\Entities\TaskSuggestion;
use App\Infrastructure\Repositories\EloquentTaskSuggestionRepository;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    private EloquentTaskSuggestionRepository $task_suggestion_repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->task_suggestion_repository = app(EloquentTaskSuggestionRepository::class);
    }

    /**
     * 提案を作成できること
     */
    public function testCreateSuggestionSuccessfully(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create();

        $suggestion_data = [
            'title' => 'テスト提案',
            'memo' => 'テストメモ',
        ];

        // 提案を作成
        $suggestion = $this->task_suggestion_repository->create(new TaskSuggestion(
            null,
            '',
            $user->user_id,
            $suggestion_data['title'],
            $suggestion_data['memo'],
            null,
            null
        ));

        // 検証
        $this->assertInstanceOf(TaskSuggestion::class, $suggestion);
        $this->assertEquals($suggestion_data['title'], $suggestion->getTitle());
        $this->assertEquals($suggestion_data['memo'], $suggestion->getMemo());
        $this->assertNotNull($suggestion->getTaskSuggestionUuid());
    }


    /**
     * データベースに正しく保存されること
     */
    public function testCreateSuggestionSavesToDatabase(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create();

        $suggestion_data = [
            'title' => 'テスト提案',
            'memo' => 'テストメモ',
        ];

        // 提案を作成
        $suggestion = $this->task_suggestion_repository->create(new TaskSuggestion(
            null,
            '',
            $user->user_id,
            $suggestion_data['title'],
            $suggestion_data['memo'],
            null,
            null
        ));

        // データベースに正しく保存されていることを確認
        $this->assertDatabaseHas('task_suggestions', [
            'task_suggestion_id' => $suggestion->getTaskSuggestionId(),
            'user_id' => $user->user_id,
            'title' => $suggestion_data['title'],
            'memo' => $suggestion_data['memo'],
        ]);
    }
}
