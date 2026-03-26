<?php

namespace Tests\Unit\Requests\Auth;

use App\Http\Requests\Auth\CompleteRegistrationRequest;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CompleteRegistrationRequestTest extends TestCase
{
    #[DataProvider('validDataProvider')]
    public function testValidationPasses(array $data, bool $expected): void
    {
        $request = new CompleteRegistrationRequest();
        $validator = Validator::make($data, $request->rules(), $request->messages());

        $this->assertEquals($expected, $validator->passes());
    }

    #[DataProvider('invalidDataProvider')]
    public function testValidationFails(array $data, array $expected_errors): void
    {
        $request = new CompleteRegistrationRequest();
        $validator = Validator::make($data, $request->rules(), $request->messages());

        $this->assertFalse($validator->passes());
        $this->assertEquals($expected_errors, $validator->errors()->toArray());
    }

    public function testAuthorizeReturnsTrue(): void
    {
        $request = new CompleteRegistrationRequest();

        $this->assertTrue($request->authorize());
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function validDataProvider(): array
    {
        return [
            '正常なデータ' => [
                [
                    'registration_token' => str_repeat('a', 64),
                    'name' => 'テストユーザー',
                    'password' => 'password123',
                ],
                true,
            ],
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function invalidDataProvider(): array
    {
        return [
            '値が空' => [
                [],
                [
                    'registration_token' => ['本登録トークンは必須です。'],
                    'name' => ['名前は必須です。'],
                    'password' => ['パスワードは必須です。'],
                ],
            ],
            'トークンが短すぎる' => [
                [
                    'registration_token' => str_repeat('a', 31),
                    'name' => 'テストユーザー',
                    'password' => 'password123',
                ],
                [
                    'registration_token' => ['本登録トークンの形式が不正です。'],
                ],
            ],
            '名前が256文字以上' => [
                [
                    'registration_token' => str_repeat('a', 64),
                    'name' => str_repeat('a', 256),
                    'password' => 'password123',
                ],
                [
                    'name' => ['名前は255文字以内で入力してください。'],
                ],
            ],
            'パスワードが7文字以下' => [
                [
                    'registration_token' => str_repeat('a', 64),
                    'name' => 'テストユーザー',
                    'password' => '1234567',
                ],
                [
                    'password' => ['パスワードは8文字以上で入力してください。'],
                ],
            ],
        ];
    }
}
