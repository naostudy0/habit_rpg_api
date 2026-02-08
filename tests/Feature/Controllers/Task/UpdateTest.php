<?php

namespace Tests\Feature\Controllers\Task;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 予定を更新できること
     */
    public function testUpdateUpdatesTask(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $task = Task::factory()->create([
            'user_id' => $user->user_id,
        ]);

        $title = '更新後';
        $scheduled_date = '2025-12-21';
        $scheduled_time = '11:00:00';
        $memo = '更新後メモ';

        $response = $this->putJson(route('tasks.update', ['uuid' => $task->task_uuid]), [
            'title' => $title,
            'scheduled_date' => $scheduled_date,
            'scheduled_time' => $scheduled_time,
            'memo' => $memo,
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
        $this->assertEquals($title, $response->json('data.title'));
        $this->assertEquals($scheduled_date, $response->json('data.scheduled_date'));
        $this->assertEquals($scheduled_time, $response->json('data.scheduled_time'));
        $this->assertEquals($memo, $response->json('data.memo'));
    }
}
