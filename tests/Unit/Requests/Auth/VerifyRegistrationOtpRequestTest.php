<?php

namespace Tests\Unit\Requests\Auth;

use App\Http\Requests\Auth\VerifyRegistrationOtpRequest;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class VerifyRegistrationOtpRequestTest extends TestCase
{
    #[DataProvider('validDataProvider')]
    public function testValidationPasses(array $data, bool $expected): void
    {
        $request = new VerifyRegistrationOtpRequest();
        $validator = Validator::make($data, $request->rules(), $request->messages());

        $this->assertEquals($expected, $validator->passes());
    }

    #[DataProvider('invalidDataProvider')]
    public function testValidationFails(array $data, array $expected_errors): void
    {
        $request = new VerifyRegistrationOtpRequest();
        $validator = Validator::make($data, $request->rules(), $request->messages());

        $this->assertFalse($validator->passes());
        $this->assertEquals($expected_errors, $validator->errors()->toArray());
    }

    public function testAuthorizeReturnsTrue(): void
    {
        $request = new VerifyRegistrationOtpRequest();

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
                    'email' => 'new-user@example.com',
                    'otp' => '123456',
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
                    'email' => ['メールアドレスは必須です。'],
                    'otp' => ['ワンタイムパスワードは必須です。'],
                ],
            ],
            'メールアドレスが不正' => [
                [
                    'email' => 'invalid-email',
                    'otp' => '123456',
                ],
                [
                    'email' => ['正しいメールアドレスの形式で入力してください。'],
                ],
            ],
            'ワンタイムパスワードが6桁未満' => [
                [
                    'email' => 'new-user@example.com',
                    'otp' => '12345',
                ],
                [
                    'otp' => ['ワンタイムパスワードは6桁で入力してください。'],
                ],
            ],
            'ワンタイムパスワードが数字以外' => [
                [
                    'email' => 'new-user@example.com',
                    'otp' => '12ab56',
                ],
                [
                    'otp' => ['ワンタイムパスワードは6桁で入力してください。'],
                ],
            ],
        ];
    }
}
