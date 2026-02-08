<?php

namespace Tests\Feature\Controllers\Task;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 予定を削除できること
     */
    public function testDestroyDeletesTask(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $task = Task::factory()->create([
            'user_id' => $user->user_id,
        ]);

        $response = $this->deleteJson(route('tasks.destroy', ['uuid' => $task->task_uuid]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'result',
            'message',
        ]);
        $this->assertTrue($response->json('result'));
        $this->assertEquals('予定を削除しました', $response->json('message'));
    }
}
