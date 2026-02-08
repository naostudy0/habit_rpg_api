<?php

namespace Tests\Feature\Controllers\TaskSuggestion;

use App\Models\TaskSuggestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 提案一覧を取得できること
     */
    public function testIndexReturnsSuggestions(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $suggestion = TaskSuggestion::factory()->create([
            'user_id' => $user->user_id,
            'title' => '読書',
            'memo' => '技術書を30ページ',
        ]);

        $response = $this->getJson(route('task-suggestions.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'result',
            'data' => [
                [
                    'uuid',
                    'title',
                    'memo',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
        $response_data = $response->json('data.0');
        $this->assertEquals($suggestion->task_suggestion_uuid, $response_data['uuid']);
        $this->assertEquals($suggestion->title, $response_data['title']);
        $this->assertEquals($suggestion->memo, $response_data['memo']);
    }
}
