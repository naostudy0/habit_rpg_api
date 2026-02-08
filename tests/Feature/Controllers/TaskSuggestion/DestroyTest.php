<?php

namespace Tests\Feature\Controllers\TaskSuggestion;

use App\Models\TaskSuggestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 提案を削除できること
     */
    public function testDestroyDeletesSuggestion(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $suggestion = TaskSuggestion::factory()->create([
            'user_id' => $user->user_id,
        ]);

        $response = $this->deleteJson(route('task-suggestions.destroy', ['uuid' => $suggestion->task_suggestion_uuid]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'result',
            'message',
        ]);
        $this->assertTrue($response->json('result'));
        $this->assertEquals('提案を削除しました', $response->json('message'));
    }
}
