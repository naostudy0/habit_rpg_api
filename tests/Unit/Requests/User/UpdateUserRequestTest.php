<?php

namespace Tests\Unit\Requests\User;

use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class UpdateUserRequestTest extends TestCase
{
    use RefreshDatabase;

    /**
     * バリデーション成功のテスト
     */
    #[DataProvider('validDataProvider')]
    public function testValidationPasses(array $data, bool $expected): void
    {
        $user = User::factory()->create();

        $request = new UpdateUserRequest();
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $rules = $request->rules();
        $validator = Validator::make($data, $rules, $request->messages());

        $this->assertEquals($expected, $validator->passes());
    }

    /**
     * バリデーション失敗のテスト
     */
    #[DataProvider('invalidDataProvider')]
    public function testValidationFails(array $data, array $expected_errors): void
    {
        $user = User::factory()->create();

        $request = new UpdateUserRequest();
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $rules = $request->rules();
        $validator = Validator::make($data, $rules, $request->messages());

        $this->assertFalse($validator->passes());
        $this->assertEquals($expected_errors, $validator->errors()->toArray());
    }

    /**
     * authorizeメソッドが認証済みユーザーの場合trueを返すことを確認
     */
    public function testAuthorizeReturnsTrueWhenUserAuthenticated(): void
    {
        $user = User::factory()->create();

        $request = new UpdateUserRequest();
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $this->assertTrue($request->authorize());
    }

    /**
     * authorizeメソッドが未認証ユーザーの場合falseを返すことを確認
     */
    public function testAuthorizeReturnsFalseWhenUserNotAuthenticated(): void
    {
        $request = new UpdateUserRequest();
        $request->setUserResolver(function () {
            return null;
        });

        $this->assertFalse($request->authorize());
    }

    /**
     * バリデーション成功のテストケース
     *
     * @return array<string, array<string, mixed>>
     */
    public static function validDataProvider(): array
    {
        return [
            '名前のみ更新' => [
                [
                    'name' => '新しい名前',
                ],
                true,
            ],
            'パスワードのみ更新' => [
                [
                    'password' => 'newpassword123',
                ],
                true,
            ],
            'ダークモード設定のみ更新' => [
                [
                    'is_dark_mode' => true,
                ],
                true,
            ],
            '24時間形式設定のみ更新' => [
                [
                    'is_24_hour_format' => true,
                ],
                true,
            ],
            '複数項目を更新' => [
                [
                    'name' => '新しい名前',
                    'is_dark_mode' => true,
                    'is_24_hour_format' => false,
                ],
                true,
            ],
            'すべての項目を更新' => [
                [
                    'name' => '新しい名前',
                    'password' => 'newpassword123',
                    'is_dark_mode' => true,
                    'is_24_hour_format' => true,
                ],
                true,
            ],
            '空の配列（更新なし）' => [
                [],
                true,
            ],
            '名前が255文字' => [
                [
                    'name' => str_repeat('a', 255),
                ],
                true,
            ],
        ];
    }

    /**
     * バリデーション失敗のテストケース
     *
     * @return array<string, array<string, mixed>>
     */
    public static function invalidDataProvider(): array
    {
        return [
            '名前が256文字以上' => [
                [
                    'name' => str_repeat('a', 256),
                ],
                [
                    'name' => ['名前は255文字以内で入力してください。'],
                ],
            ],
            'パスワードが7文字以下' => [
                [
                    'password' => '1234567',
                ],
                [
                    'password' => ['パスワードは8文字以上で入力してください。'],
                ],
            ],
            'ダークモード設定が真偽値以外' => [
                [
                    'is_dark_mode' => 'true',
                ],
                [
                    'is_dark_mode' => ['ダークモード設定は真偽値で入力してください。'],
                ],
            ],
            '24時間形式設定が真偽値以外' => [
                [
                    'is_24_hour_format' => 'false',
                ],
                [
                    'is_24_hour_format' => ['24時間形式設定は真偽値で入力してください。'],
                ],
            ],
        ];
    }
}
