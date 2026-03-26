<?php

namespace Tests\Unit\UseCases\Auth;

use App\Services\RegistrationService;
use App\UseCases\Auth\VerifyRegistrationOtpInput;
use App\UseCases\Auth\VerifyRegistrationOtpOutput;
use App\UseCases\Auth\VerifyRegistrationOtpUseCase;
use App\UseCases\Inputs\Input;
use Carbon\CarbonImmutable;
use Tests\TestCase;

class VerifyRegistrationOtpUseCaseTest extends TestCase
{
    public function testHandleReturnsSuccessWhenOtpIsVerified(): void
    {
        $token_expires_at = CarbonImmutable::parse('2026-03-26 12:15:00');

        $registration_service = $this->createMock(RegistrationService::class);
        $registration_service->expects($this->once())
            ->method('verifyOtp')
            ->with('new-user@example.com', '123456')
            ->willReturn([
                'success' => true,
                'registration_token' => 'registration-token',
                'registration_token_expires_at' => $token_expires_at,
            ]);

        $use_case = new VerifyRegistrationOtpUseCase($registration_service);
        $result = $use_case->handle(new VerifyRegistrationOtpInput('new-user@example.com', '123456'));

        $this->assertTrue($result->isSuccess());
        /** @var VerifyRegistrationOtpOutput $output */
        $output = $result->getOutput();
        $this->assertInstanceOf(VerifyRegistrationOtpOutput::class, $output);
        $this->assertSame('registration-token', $output->getRegistrationToken());
        $this->assertSame($token_expires_at, $output->getRegistrationTokenExpiresAt());
    }

    public function testHandleReturnsFailureWhenServiceFails(): void
    {
        $registration_service = $this->createMock(RegistrationService::class);
        $registration_service->expects($this->once())
            ->method('verifyOtp')
            ->with('new-user@example.com', '000000')
            ->willReturn([
                'success' => false,
                'error_code' => RegistrationService::ERROR_OTP_INVALID,
                'message' => 'ワンタイムパスワードが正しくありません。',
            ]);

        $use_case = new VerifyRegistrationOtpUseCase($registration_service);
        $result = $use_case->handle(new VerifyRegistrationOtpInput('new-user@example.com', '000000'));

        $this->assertFalse($result->isSuccess());
        $this->assertNull($result->getOutput());
        $this->assertSame(RegistrationService::ERROR_OTP_INVALID, $result->getErrorCode());
    }

    public function testHandleReturnsFailureWhenInputIsInvalid(): void
    {
        $registration_service = $this->createMock(RegistrationService::class);
        $use_case = new VerifyRegistrationOtpUseCase($registration_service);
        $invalid_input = new class () implements Input {
        };

        $result = $use_case->handle($invalid_input);

        $this->assertFalse($result->isSuccess());
        $this->assertNull($result->getOutput());
    }
}
