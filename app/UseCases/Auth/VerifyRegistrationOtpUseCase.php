<?php

namespace App\UseCases\Auth;

use App\Services\RegistrationService;
use App\UseCases\Inputs\Input;
use App\UseCases\Results\Result;
use App\UseCases\UseCaseInterface;

class VerifyRegistrationOtpUseCase implements UseCaseInterface
{
    private RegistrationService $registration_service;

    public function __construct(RegistrationService $registration_service)
    {
        $this->registration_service = $registration_service;
    }

    public function handle(Input $input): Result
    {
        if (!$input instanceof VerifyRegistrationOtpInput) {
            return Result::failure('INVALID_INPUT', 'ワンタイムパスワードの検証に失敗しました。');
        }

        $result = $this->registration_service->verifyOtp($input->getEmail(), $input->getOtpCode());
        if (!$result['success']) {
            return Result::failure($result['error_code'], $result['message']);
        }

        return Result::success(new VerifyRegistrationOtpOutput(
            $result['registration_token'],
            $result['registration_token_expires_at']
        ));
    }
}
