<?php

namespace Tests\Feature\Controllers\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ログインが成功すること
     */
    public function testLoginReturnsTokenAndUserData(): void
    {
        $password = 'password123';
        $user = User::factory()->create([
            'password' => $password,
        ]);

        $response = $this->postJson(route('auth.login'), [
            'email' => $user->email,
            'password' => $password,
        ]);

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
            'token',
        ]);
        $this->assertTrue($response->json('result'));
        $this->assertEquals($user->user_uuid, $response->json('data.user_uuid'));
        $this->assertEquals($user->email, $response->json('data.email'));
        $this->assertNotEmpty($response->json('token'));
    }

    /**
     * 認証失敗時に401が返ること
     */
    public function testLoginReturnsUnauthorizedWhenCredentialsInvalid(): void
    {
        $user = User::factory()->create([
            'password' => 'password123',
        ]);

        $response = $this->postJson(route('auth.login'), [
            'email' => $user->email,
            'password' => 'invalid-password',
        ]);

        $response->assertStatus(401);
        $response->assertJsonStructure([
            'message',
            'errors' => ['email'],
        ]);
    }
}
