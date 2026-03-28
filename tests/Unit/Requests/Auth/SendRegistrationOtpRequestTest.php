<?php

namespace Tests\Unit\Requests\Auth;

use App\Http\Requests\Auth\SendRegistrationOtpRequest;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SendRegistrationOtpRequestTest extends TestCase
{
    #[DataProvider('validDataProvider')]
    public function testValidationPasses(array $data, bool $expected): void
    {
        $request = new SendRegistrationOtpRequest();
        $validator = Validator::make($data, $request->rules(), $request->messages());

        $this->assertEquals($expected, $validator->passes());
    }

    #[DataProvider('invalidDataProvider')]
    public function testValidationFails(array $data, array $expected_errors): void
    {
        $request = new SendRegistrationOtpRequest();
        $validator = Validator::make($data, $request->rules(), $request->messages());

        $this->assertFalse($validator->passes());
        $this->assertEquals($expected_errors, $validator->errors()->toArray());
    }

    public function testAuthorizeReturnsTrue(): void
    {
        $request = new SendRegistrationOtpRequest();

        $this->assertTrue($request->authorize());
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function validDataProvider(): array
    {
        return [
            '正常なメールアドレス' => [
                ['email' => 'new-user@example.com'],
                true,
            ],
            'メールアドレスが255文字' => [
                ['email' => str_repeat('a', 243) . '@example.com'],
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
                ['email' => ['メールアドレスは必須です。']],
            ],
            '値がnull' => [
                ['email' => null],
                ['email' => ['メールアドレスは必須です。']],
            ],
            'メールアドレスが不正' => [
                ['email' => 'invalid-email'],
                ['email' => ['正しいメールアドレスの形式で入力してください。']],
            ],
            'メールアドレスが256文字以上' => [
                ['email' => str_repeat('a', 245) . '@example.com'],
                ['email' => ['メールアドレスは255文字以内で入力してください。']],
            ],
        ];
    }
}
