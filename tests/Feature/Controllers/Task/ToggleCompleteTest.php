<?php

namespace Tests\Feature\Controllers\Task;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ToggleCompleteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 完了状態を切り替えできること
     */
    public function testToggleCompleteUpdatesCompletion(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $task = Task::factory()->create([
            'user_id' => $user->user_id,
            'is_completed' => false,
        ]);

        $response = $this->patchJson(route('tasks.complete', ['uuid' => $task->task_uuid]), [
            'is_completed' => true,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'result',
            'message',
            'data' => [
                'uuid',
                'title',
                'scheduled_date',
                'scheduled_time',
                'memo',
                'is_completed',
                'created_at',
                'updated_at',
            ],
        ]);
        $this->assertEquals($task->task_uuid, $response->json('data.uuid'));
        $this->assertEquals($task->title, $response->json('data.title'));
        $this->assertTrue($response->json('data.is_completed'));
    }
}
