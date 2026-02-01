<?php

namespace Tests\Unit\Repositories\UserRepository;

use App\Domain\Entities\User as DomainUser;
use App\Infrastructure\Repositories\EloquentUserRepository;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FindByEmailTest extends TestCase
{
    use RefreshDatabase;

    private EloquentUserRepository $user_repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user_repository = app(EloquentUserRepository::class);
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
