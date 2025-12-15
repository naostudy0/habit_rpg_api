<?php

namespace Tests\Unit\Requests\Auth;

use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class LoginRequestTest extends TestCase
{
    /**
     * バリデーション成功のテスト
     */
    #[DataProvider('validDataProvider')]
    public function testValidationPasses(array $data, bool $expected): void
    {
        $request = new LoginRequest();
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
        $request = new LoginRequest();
        $rules = $request->rules();

        $validator = Validator::make($data, $rules, $request->messages());

        $this->assertFalse($validator->passes());
        $this->assertEquals($expected_errors, $validator->errors()->toArray());
    }

    /**
     * authorizeメソッドがtrueを返すことを確認
     */
    public function testAuthorizeReturnsTrue(): void
    {
        $request = new LoginRequest();

        $this->assertTrue($request->authorize());
    }

    /**
     * バリデーション成功のテストケース
     *
     * @return array<string, array<string, mixed>>
     */
    public static function validDataProvider(): array
    {
        return [
            '正常なメールアドレスとパスワード' => [
                [
                    'email' => 'test@example.com',
                    'password' => 'password123',
                ],
                true,
            ],
            'パスワードが8文字' => [
                [
                    'email' => 'test@example.com',
                    'password' => '12345678',
                ],
                true,
            ],
            'メールアドレスが255文字' => [
                [
                    'email' => str_repeat('a', 243) . '@example.com', // 合計255文字
                    'password' => 'password123',
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
            '値が空' => [
                [],
                [
                    'email' => ['メールアドレスは必須です。'],
                    'password' => ['パスワードは必須です。'],
                ],
            ],
            '値がnull' => [
                [
                    'email' => null,
                    'password' => null,
                ],
                [
                    'email' => ['メールアドレスは必須です。'],
                    'password' => ['パスワードは必須です。'],
                ],
            ],
            'passwordが7文字以下' => [
                [
                    'email' => 'test@example.com',
                    'password' => '1234567',
                ],
                [
                    'password' => ['パスワードは8文字以上で入力してください。'],
                ],
            ],
            'メールアドレスが不正' => [
                [
                    'email' => 'invalid-email',
                    'password' => 'password123',
                ],
                [
                    'email' => ['正しいメールアドレスの形式で入力してください。'],
                ],
            ],
            'メールアドレスが256文字以上' => [
                [
                    'email' => str_repeat('a', 245) . '@example.com',
                    'password' => 'password123',
                ],
                [
                    'email' => ['メールアドレスは255文字以内で入力してください。'],
                ],
            ],
        ];
    }
}
