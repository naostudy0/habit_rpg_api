<?php

namespace Tests\Feature\Services\UserService;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UpdateUserTest extends TestCase
{
    use RefreshDatabase;

    private UserService $user_service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user_service = app(UserService::class);
    }

    /**
     * ユーザー情報を更新できること
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
        $result = $this->user_service->updateUser($user->user_id, $update_data);

        // 検証
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertEquals($update_data['name'], $result['name']);
        $this->assertEquals($update_data['is_dark_mode'], $result['is_dark_mode']);
        $this->assertEquals($update_data['is_24_hour_format'], $result['is_24_hour_format']);
    }

    /**
     * APIレスポンス形式に整形されること
     */
    public function testUpdateUserReturnsFormattedArray(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create();

        // 更新データ
        $update_data = [
            'name' => '更新後の名前',
        ];

        // ユーザーを更新
        $result = $this->user_service->updateUser($user->user_id, $update_data);

        // 検証
        $this->assertArrayHasKey('user_uuid', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('is_dark_mode', $result);
        $this->assertArrayHasKey('is_24_hour_format', $result);
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
        $result = $this->user_service->updateUser($user->user_id, $update_data);

        // 検証
        $this->assertIsArray($result);
        $this->assertEquals($update_data['name'], $result['name']);
    }

    /**
     * パスワードがハッシュ化されること
     */
    public function testUpdateUserPasswordIsHashed(): void
    {
        // テストユーザーを作成
        $user = User::factory()->create();
        $original_password = $user->password;

        // 更新データ
        $update_data = [
            'password' => 'newpassword123',
        ];

        // ユーザーを更新
        $this->user_service->updateUser($user->user_id, $update_data);

        // データベースからユーザーを再取得
        $user->refresh();

        // 検証
        $this->assertNotEquals($original_password, $user->password);
        $this->assertNotEquals('newpassword123', $user->password);
        $this->assertTrue(Hash::check('newpassword123', $user->password));
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
        $result = $this->user_service->updateUser($user->user_id, $update_data);

        // 検証
        $this->assertIsArray($result);
        $this->assertTrue($result['is_dark_mode']);
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
        $result = $this->user_service->updateUser($user->user_id, $update_data);

        // 検証
        $this->assertIsArray($result);
        $this->assertTrue($result['is_24_hour_format']);
    }

    /**
     * 存在しないユーザーの場合はnullが返ること
     */
    public function testUpdateUserReturnsNullWhenUserNotExists(): void
    {
        // 更新データ
        $update_data = [
            'name' => '更新後の名前',
        ];

        // 存在しないユーザーIDで更新
        $result = $this->user_service->updateUser(99999, $update_data);

        // 検証
        $this->assertNull($result);
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
        $this->user_service->updateUser($user->user_id, $update_data);

        // データベースに正しく保存されていることを確認
        $this->assertDatabaseHas('users', [
            'user_id' => $user->user_id,
            'name' => $update_data['name'],
            'is_dark_mode' => $update_data['is_dark_mode'],
            'is_24_hour_format' => $update_data['is_24_hour_format'],
        ]);
    }
}
