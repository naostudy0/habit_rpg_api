<?php

namespace Tests\Unit\Repositories\UserRepository;

use App\Domain\Entities\User as DomainUser;
use App\Infrastructure\Repositories\EloquentUserRepository;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    private EloquentUserRepository $user_repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user_repository = app(EloquentUserRepository::class);
    }

    /**
     * ユーザーを更新できること
     */
    public function testUpdateUserSuccessfully(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create([
            'name' => '元の名前',
            'is_dark_mode' => false,
            'is_24_hour_format' => false,
        ]);

        $domain_user = $this->user_repository->findByUserId($user->user_id);
        $this->assertNotNull($domain_user);

        $updated_user = new DomainUser(
            $domain_user->getUserId(),
            $domain_user->getUserUuid(),
            '更新後の名前',
            $domain_user->getEmail(),
            null,
            true,
            true
        );

        // ユーザーを更新
        $result = $this->user_repository->update($updated_user);

        // 検証
        $this->assertInstanceOf(DomainUser::class, $result);
        $this->assertEquals('更新後の名前', $result->getName());
        $this->assertTrue($result->isDarkMode());
        $this->assertTrue($result->is24HourFormat());
    }

    /**
     * 名前のみ更新できること
     */
    public function testUpdateUserNameOnly(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create([
            'name' => '元の名前',
        ]);

        $domain_user = $this->user_repository->findByUserId($user->user_id);
        $this->assertNotNull($domain_user);

        $updated_user = new DomainUser(
            $domain_user->getUserId(),
            $domain_user->getUserUuid(),
            '更新後の名前',
            $domain_user->getEmail(),
            null,
            $domain_user->isDarkMode(),
            $domain_user->is24HourFormat()
        );

        // ユーザーを更新
        $result = $this->user_repository->update($updated_user);

        // 検証
        $this->assertInstanceOf(DomainUser::class, $result);
        $this->assertEquals('更新後の名前', $result->getName());
    }

    /**
     * ダークモード設定のみ更新できること
     */
    public function testUpdateUserDarkModeOnly(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create([
            'is_dark_mode' => false,
        ]);

        $domain_user = $this->user_repository->findByUserId($user->user_id);
        $this->assertNotNull($domain_user);

        $updated_user = new DomainUser(
            $domain_user->getUserId(),
            $domain_user->getUserUuid(),
            $domain_user->getName(),
            $domain_user->getEmail(),
            null,
            true,
            $domain_user->is24HourFormat()
        );

        // ユーザーを更新
        $result = $this->user_repository->update($updated_user);

        // 検証
        $this->assertInstanceOf(DomainUser::class, $result);
        $this->assertTrue($result->isDarkMode());
    }

    /**
     * 24時間形式設定のみ更新できること
     */
    public function testUpdateUser24HourFormatOnly(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create([
            'is_24_hour_format' => false,
        ]);

        $domain_user = $this->user_repository->findByUserId($user->user_id);
        $this->assertNotNull($domain_user);

        $updated_user = new DomainUser(
            $domain_user->getUserId(),
            $domain_user->getUserUuid(),
            $domain_user->getName(),
            $domain_user->getEmail(),
            null,
            $domain_user->isDarkMode(),
            true
        );

        // ユーザーを更新
        $result = $this->user_repository->update($updated_user);

        // 検証
        $this->assertInstanceOf(DomainUser::class, $result);
        $this->assertTrue($result->is24HourFormat());
    }

    /**
     * データベースに正しく保存されること
     */
    public function testUpdateUserSavesToDatabase(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create([
            'name' => '元の名前',
            'is_dark_mode' => false,
            'is_24_hour_format' => false,
        ]);

        $domain_user = $this->user_repository->findByUserId($user->user_id);
        $this->assertNotNull($domain_user);

        $updated_user = new DomainUser(
            $domain_user->getUserId(),
            $domain_user->getUserUuid(),
            '更新後の名前',
            $domain_user->getEmail(),
            null,
            true,
            true
        );

        // ユーザーを更新
        $this->user_repository->update($updated_user);

        // データベースに正しく保存されていることを確認
        $this->assertDatabaseHas('users', [
            'user_id' => $user->user_id,
            'name' => '更新後の名前',
            'is_dark_mode' => true,
            'is_24_hour_format' => true,
        ]);
    }
}
