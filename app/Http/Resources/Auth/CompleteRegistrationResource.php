<?php

namespace App\Http\Resources\Auth;

use App\Http\Resources\ApiResponseResource;
use App\Services\RegistrationService;
use App\UseCases\Auth\CompleteRegistrationOutput;
use App\UseCases\Results\Result;
use Illuminate\Http\JsonResponse;

class CompleteRegistrationResource
{
    public static function fromResult(Result $result): JsonResponse
    {
        if (!$result->isSuccess()) {
            $status = match ($result->getErrorCode()) {
                RegistrationService::ERROR_EMAIL_ALREADY_REGISTERED => 409,
                RegistrationService::ERROR_REGISTRATION_TOKEN_INVALID,
                RegistrationService::ERROR_REGISTRATION_TOKEN_EXPIRED => 422,
                default => 400,
            };

            return ApiResponseResource::error(
                $result->getErrorMessage() ?? '会員登録に失敗しました。',
                $status
            );
        }

        $output = $result->getOutput();
        if (!$output instanceof CompleteRegistrationOutput) {
            return ApiResponseResource::error('会員登録に失敗しました。', 400);
        }

        return ApiResponseResource::success(
            $output->getUser(),
            '会員登録が完了しました。',
            201
        );
    }
}
