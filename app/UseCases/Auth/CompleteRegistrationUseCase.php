<?php

namespace App\UseCases\Auth;

use App\Services\RegistrationService;
use App\UseCases\Inputs\Input;
use App\UseCases\Results\Result;
use App\UseCases\UseCaseInterface;

class CompleteRegistrationUseCase implements UseCaseInterface
{
    private RegistrationService $registration_service;

    public function __construct(RegistrationService $registration_service)
    {
        $this->registration_service = $registration_service;
    }

    public function handle(Input $input): Result
    {
        if (!$input instanceof CompleteRegistrationInput) {
            return Result::failure('INVALID_INPUT', '会員登録に失敗しました。');
        }

        $result = $this->registration_service->completeRegistration(
            $input->getRegistrationToken(),
            $input->getName(),
            $input->getPassword()
        );

        if (!$result['success']) {
            return Result::failure($result['error_code'], $result['message']);
        }

        return Result::success(new CompleteRegistrationOutput($result['user']));
    }
}
