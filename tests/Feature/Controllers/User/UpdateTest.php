<?php

namespace Tests\Feature\Controllers\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ユーザー情報を更新できること
     */
    public function testUpdateReturnsUpdatedUserData(): void
    {
        $user = User::factory()->create([
            'name' => '元の名前',
            'is_dark_mode' => false,
            'is_24_hour_format' => false,
        ]);
        Sanctum::actingAs($user);

        $updated_name = '更新後の名前';

        $response = $this->putJson(route('user.update'), [
            'name' => $updated_name,
            'is_dark_mode' => true,
            'is_24_hour_format' => true,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'result',
            'message',
            'data' => [
                'user_uuid',
                'name',
                'email',
                'is_dark_mode',
                'is_24_hour_format',
            ],
        ]);
        $this->assertTrue($response->json('result'));
        $this->assertEquals($updated_name, $response->json('data.name'));
        $this->assertTrue($response->json('data.is_dark_mode'));
        $this->assertTrue($response->json('data.is_24_hour_format'));
        $this->assertEquals($user->email, $response->json('data.email'));
        $this->assertEquals($user->user_uuid, $response->json('data.user_uuid'));
    }
}
