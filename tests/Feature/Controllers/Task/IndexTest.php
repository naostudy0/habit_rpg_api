<?php

namespace Tests\Feature\Controllers\Task;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 予定一覧を取得できること
     */
    public function testIndexReturnsTasks(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $task = Task::factory()->create([
            'user_id' => $user->user_id,
            'title' => '朝の運動',
            'scheduled_date' => '2025-12-20',
            'scheduled_time' => '08:30:00',
            'memo' => '30分のジョギング',
            'is_completed' => true,
        ]);

        $response = $this->getJson(route('tasks.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'result',
            'data' => [
                [
                    'uuid',
                    'title',
                    'scheduled_date',
                    'scheduled_time',
                    'memo',
                    'is_completed',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
        $response_data = $response->json('data.0');
        $this->assertEquals($task->task_uuid, $response_data['uuid']);
        $this->assertEquals($task->title, $response_data['title']);
        $this->assertEquals($task->scheduled_date->toDateString(), $response_data['scheduled_date']);
        $this->assertEquals($task->scheduled_time, $response_data['scheduled_time']);
        $this->assertEquals($task->memo, $response_data['memo']);
        $this->assertEquals($task->is_completed, $response_data['is_completed']);
    }
}
