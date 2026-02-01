<?php

namespace Tests\Feature\Services\AuthService;

use App\Domain\Entities\User as DomainUser;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticateTest extends TestCase
{
    use RefreshDatabase;

    private AuthService $auth_service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->auth_service = app(AuthService::class);
    }

    /**
     * 認証が成功すること
     */
    public function testAuthenticateReturnsUserWhenCredentialsAreValid(): void
    {
        // テストユーザーを作成
        $password = 'password123';
        $user = User::factory()->create([
            'password' => $password,
        ]);

        // 認証処理を実行
        $result = $this->auth_service->authenticate($user->email, $password);

        // 検証
        $this->assertNotNull($result);
        $this->assertInstanceOf(DomainUser::class, $result);
        $this->assertEquals($user->user_id, $result->getUserId());
        $this->assertEquals($user->user_uuid, $result->getUserUuid());
        $this->assertEquals($user->name, $result->getName());
        $this->assertEquals($user->email, $result->getEmail());
        $this->assertEquals((bool) $user->is_dark_mode, $result->isDarkMode());
        $this->assertEquals((bool) $user->is_24_hour_format, $result->is24HourFormat());
    }

    /**
     * 存在しないメールアドレスの場合、nullが返ること
     */
    public function testAuthenticateReturnsNullWhenEmailNotExists(): void
    {
        // 存在しないメールアドレスで認証処理を実行
        $result = $this->auth_service->authenticate('notfound@example.com', 'password123');

        // 検証
        $this->assertNull($result);
    }

    /**
     * パスワードが間違っている場合、nullが返ること
     */
    public function testAuthenticateReturnsNullWhenPasswordIsIncorrect(): void
    {
        // テストユーザーを作成
        $password = 'correct_password';
        $user = User::factory()->create([
            'password' => $password,
        ]);

        // 間違ったパスワードで認証処理を実行
        $result = $this->auth_service->authenticate($user->email, 'invalid_' . $password);

        // 検証
        $this->assertNull($result);
    }

    /**
     * 認証成功時にパスワードがハッシュ化されていること
     */
    public function testAuthenticateReturnsHashedPasswordWhenSuccessful(): void
    {
        // テストユーザーを作成
        $password = 'password123';
        $user = User::factory()->create([
            'password' => $password,
        ]);

        // 認証処理を実行
        $result = $this->auth_service->authenticate($user->email, $password);

        // 検証
        $this->assertNotNull($result);
        $this->assertNotNull($result->getPasswordHash());
        $this->assertNotEquals($password, $result->getPasswordHash());
    }
}
