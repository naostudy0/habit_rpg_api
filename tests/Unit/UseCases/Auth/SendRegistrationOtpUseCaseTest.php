<?php

namespace Tests\Unit\UseCases\Auth;

use App\Services\RegistrationService;
use App\UseCases\Auth\SendRegistrationOtpInput;
use App\UseCases\Auth\SendRegistrationOtpOutput;
use App\UseCases\Auth\SendRegistrationOtpUseCase;
use App\UseCases\Inputs\Input;
use Carbon\CarbonImmutable;
use Tests\TestCase;

class SendRegistrationOtpUseCaseTest extends TestCase
{
    public function testHandleReturnsSuccessWhenOtpIsSent(): void
    {
        $expires_at = CarbonImmutable::parse('2026-03-26 12:00:00');
        $resend_available_at = CarbonImmutable::parse('2026-03-26 12:01:00');

        $registration_service = $this->createMock(RegistrationService::class);
        $registration_service->expects($this->once())
            ->method('sendOtp')
            ->with('new-user@example.com')
            ->willReturn([
                'success' => true,
                'email' => 'new-user@example.com',
                'expires_at' => $expires_at,
                'resend_available_at' => $resend_available_at,
            ]);

        $use_case = new SendRegistrationOtpUseCase($registration_service);
        $result = $use_case->handle(new SendRegistrationOtpInput('new-user@example.com'));

        $this->assertTrue($result->isSuccess());
        /** @var SendRegistrationOtpOutput $output */
        $output = $result->getOutput();
        $this->assertInstanceOf(SendRegistrationOtpOutput::class, $output);
        $this->assertSame('new-user@example.com', $output->getEmail());
        $this->assertSame($expires_at->toIso8601String(), $output->getExpiresAt());
        $this->assertSame($resend_available_at->toIso8601String(), $output->getResendAvailableAt());
    }

    public function testHandleReturnsFailureWhenServiceFails(): void
    {
        $registration_service = $this->createMock(RegistrationService::class);
        $registration_service->expects($this->once())
            ->method('sendOtp')
            ->with('new-user@example.com')
            ->willReturn([
                'success' => false,
                'error_code' => RegistrationService::ERROR_EMAIL_ALREADY_REGISTERED,
                'message' => 'このメールアドレスはすでに登録されています。',
            ]);

        $use_case = new SendRegistrationOtpUseCase($registration_service);
        $result = $use_case->handle(new SendRegistrationOtpInput('new-user@example.com'));

        $this->assertFalse($result->isSuccess());
        $this->assertNull($result->getOutput());
        $this->assertSame(RegistrationService::ERROR_EMAIL_ALREADY_REGISTERED, $result->getErrorCode());
    }

    public function testHandleReturnsFailureWhenInputIsInvalid(): void
    {
        $registration_service = $this->createMock(RegistrationService::class);
        $use_case = new SendRegistrationOtpUseCase($registration_service);
        $invalid_input = new class () implements Input {
        };

        $result = $use_case->handle($invalid_input);

        $this->assertFalse($result->isSuccess());
        $this->assertNull($result->getOutput());
    }
}
