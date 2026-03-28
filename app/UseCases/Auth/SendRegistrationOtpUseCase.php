<?php

namespace App\UseCases\Auth;

use App\Services\RegistrationService;
use App\UseCases\Inputs\Input;
use App\UseCases\Results\Result;
use App\UseCases\UseCaseInterface;

class SendRegistrationOtpUseCase implements UseCaseInterface
{
    private RegistrationService $registration_service;

    public function __construct(RegistrationService $registration_service)
    {
        $this->registration_service = $registration_service;
    }

    public function handle(Input $input): Result
    {
        if (!$input instanceof SendRegistrationOtpInput) {
            return Result::failure('INVALID_INPUT', 'ワンタイムパスワードの送信に失敗しました。');
        }

        $result = $this->registration_service->sendOtp($input->getEmail());
        if (!$result['success']) {
            return Result::failure($result['error_code'], $result['message']);
        }

        return Result::success(new SendRegistrationOtpOutput(
            $result['email'],
            $result['expires_at']->toIso8601String(),
            $result['resend_available_at']->toIso8601String()
        ));
    }
}
