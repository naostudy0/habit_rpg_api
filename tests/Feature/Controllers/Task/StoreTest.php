<?php

namespace Tests\Feature\Controllers\Task;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 予定を作成できること
     */
    public function testStoreCreatesTask(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $title = 'テスト予定';
        $scheduled_date = '2025-12-20';
        $scheduled_time = '10:00:00';
        $memo = 'テストメモ';

        $response = $this->postJson(route('tasks.store'), [
            'title' => $title,
            'scheduled_date' => $scheduled_date,
            'scheduled_time' => $scheduled_time,
            'memo' => $memo,
        ]);

        $response->assertStatus(201);
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
        $this->assertEquals($title, $response->json('data.title'));
        $this->assertEquals($scheduled_date, $response->json('data.scheduled_date'));
        $this->assertEquals($scheduled_time, $response->json('data.scheduled_time'));
        $this->assertEquals($memo, $response->json('data.memo'));
        $this->assertFalse($response->json('data.is_completed'));
        $this->assertNotEmpty($response->json('data.uuid'));
    }
}
