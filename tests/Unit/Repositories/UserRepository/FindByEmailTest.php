<?php

namespace Tests\Unit\Repositories\UserRepository;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FindByEmailTest extends TestCase
{
    use RefreshDatabase;

    private UserRepository $user_repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user_repository = app(UserRepository::class);
    }

    /**
     * 存在するメールアドレスでユーザーを取得できること
     */
    public function testFindByEmailReturnsUserWhenEmailExists(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create();

        // メールアドレスでユーザーを取得
        $result = $this->user_repository->findByEmail($user->email);

        $expected = $user->only(['user_id', 'user_uuid', 'name', 'email', 'is_dark_mode', 'is_24_hour_format']);

        // 検証
        $this->assertNotNull($result);
        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * 存在しないメールアドレスの場合はnullが返ること
     */
    public function testFindByEmailReturnsNullWhenEmailNotExists(): void
    {
        // 存在しないメールアドレスでユーザーを取得
        $result = $this->user_repository->findByEmail('notfound@example.com');

        // 検証
        $this->assertNull($result);
    }
}
