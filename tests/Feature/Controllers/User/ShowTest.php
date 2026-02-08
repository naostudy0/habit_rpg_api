<?php

namespace Tests\Feature\Controllers\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ShowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ユーザー情報を取得できること
     */
    public function testShowReturnsUserData(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson(route('user.show'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'result',
            'data' => [
                'user_uuid',
                'name',
                'email',
                'is_dark_mode',
                'is_24_hour_format',
            ],
        ]);
        $this->assertTrue($response->json('result'));
        $this->assertEquals($user->user_uuid, $response->json('data.user_uuid'));
        $this->assertEquals($user->name, $response->json('data.name'));
        $this->assertEquals($user->email, $response->json('data.email'));
        $this->assertEquals($user->is_dark_mode, $response->json('data.is_dark_mode'));
        $this->assertEquals($user->is_24_hour_format, $response->json('data.is_24_hour_format'));
    }
}
