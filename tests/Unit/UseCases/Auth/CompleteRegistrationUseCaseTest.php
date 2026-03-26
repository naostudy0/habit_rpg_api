<?php

namespace Tests\Unit\UseCases\Auth;

use App\Services\RegistrationService;
use App\UseCases\Auth\CompleteRegistrationInput;
use App\UseCases\Auth\CompleteRegistrationOutput;
use App\UseCases\Auth\CompleteRegistrationUseCase;
use App\UseCases\Inputs\Input;
use Tests\TestCase;

class CompleteRegistrationUseCaseTest extends TestCase
{
    public function testHandleReturnsSuccessWhenRegistrationCompleted(): void
    {
        $user = [
            'user_uuid' => 'user-uuid',
            'name' => 'テストユーザー',
            'email' => 'new-user@example.com',
            'is_dark_mode' => false,
            'is_24_hour_format' => true,
        ];

        $registration_service = $this->createMock(RegistrationService::class);
        $registration_service->expects($this->once())
            ->method('completeRegistration')
            ->with('registration-token', 'テストユーザー', 'password123')
            ->willReturn([
                'success' => true,
                'user' => $user,
            ]);

        $use_case = new CompleteRegistrationUseCase($registration_service);
        $result = $use_case->handle(new CompleteRegistrationInput('registration-token', 'テストユーザー', 'password123'));

        $this->assertTrue($result->isSuccess());
        /** @var CompleteRegistrationOutput $output */
        $output = $result->getOutput();
        $this->assertInstanceOf(CompleteRegistrationOutput::class, $output);
        $this->assertSame($user, $output->getUser());
    }

    public function testHandleReturnsFailureWhenServiceFails(): void
    {
        $registration_service = $this->createMock(RegistrationService::class);
        $registration_service->expects($this->once())
            ->method('completeRegistration')
            ->with('invalid-token', 'テストユーザー', 'password123')
            ->willReturn([
                'success' => false,
                'error_code' => RegistrationService::ERROR_REGISTRATION_TOKEN_INVALID,
                'message' => '本登録トークンが不正です。',
            ]);

        $use_case = new CompleteRegistrationUseCase($registration_service);
        $result = $use_case->handle(new CompleteRegistrationInput('invalid-token', 'テストユーザー', 'password123'));

        $this->assertFalse($result->isSuccess());
        $this->assertNull($result->getOutput());
        $this->assertSame(RegistrationService::ERROR_REGISTRATION_TOKEN_INVALID, $result->getErrorCode());
    }

    public function testHandleReturnsFailureWhenInputIsInvalid(): void
    {
        $registration_service = $this->createMock(RegistrationService::class);
        $use_case = new CompleteRegistrationUseCase($registration_service);
        $invalid_input = new class () implements Input {
        };

        $result = $use_case->handle($invalid_input);

        $this->assertFalse($result->isSuccess());
        $this->assertNull($result->getOutput());
    }
}
