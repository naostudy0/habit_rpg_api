<?php

namespace Tests\Unit\Repositories\UserRepository;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    private UserRepository $user_repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user_repository = app(UserRepository::class);
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

        // 更新データ
        $update_data = [
            'name' => '更新後の名前',
            'is_dark_mode' => true,
            'is_24_hour_format' => true,
        ];

        // ユーザーを更新
        $result = $this->user_repository->update($user, $update_data);

        // 検証
        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($update_data['name'], $result->name);
        $this->assertEquals($update_data['is_dark_mode'], $result->is_dark_mode);
        $this->assertEquals($update_data['is_24_hour_format'], $result->is_24_hour_format);
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

        // 更新データ
        $update_data = [
            'name' => '更新後の名前',
        ];

        // ユーザーを更新
        $result = $this->user_repository->update($user, $update_data);

        // 検証
        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($update_data['name'], $result->name);
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

        // 更新データ
        $update_data = [
            'is_dark_mode' => true,
        ];

        // ユーザーを更新
        $result = $this->user_repository->update($user, $update_data);

        // 検証
        $this->assertInstanceOf(User::class, $result);
        $this->assertTrue($result->is_dark_mode);
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

        // 更新データ
        $update_data = [
            'is_24_hour_format' => true,
        ];

        // ユーザーを更新
        $result = $this->user_repository->update($user, $update_data);

        // 検証
        $this->assertInstanceOf(User::class, $result);
        $this->assertTrue($result->is_24_hour_format);
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

        // 更新データ
        $update_data = [
            'name' => '更新後の名前',
            'is_dark_mode' => true,
            'is_24_hour_format' => true,
        ];

        // ユーザーを更新
        $this->user_repository->update($user, $update_data);

        // データベースに正しく保存されていることを確認
        $this->assertDatabaseHas('users', [
            'user_id' => $user->user_id,
            'name' => $update_data['name'],
            'is_dark_mode' => $update_data['is_dark_mode'],
            'is_24_hour_format' => $update_data['is_24_hour_format'],
        ]);
    }
}
