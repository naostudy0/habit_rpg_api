<?php

namespace App\Http\Resources\Auth;

use App\Http\Resources\ApiResponseResource;
use App\Services\RegistrationService;
use App\UseCases\Auth\VerifyRegistrationOtpOutput;
use App\UseCases\Results\Result;
use Illuminate\Http\JsonResponse;

class VerifyRegistrationOtpResource
{
    public static function fromResult(Result $result): JsonResponse
    {
        if (!$result->isSuccess()) {
            $status = match ($result->getErrorCode()) {
                RegistrationService::ERROR_EMAIL_ALREADY_REGISTERED => 409,
                RegistrationService::ERROR_OTP_ATTEMPTS_EXCEEDED => 429,
                RegistrationService::ERROR_OTP_NOT_FOUND,
                RegistrationService::ERROR_OTP_EXPIRED,
                RegistrationService::ERROR_OTP_INVALID => 422,
                default => 400,
            };

            return ApiResponseResource::error(
                $result->getErrorMessage() ?? 'ワンタイムパスワードの検証に失敗しました。',
                $status
            );
        }

        $output = $result->getOutput();
        if (!$output instanceof VerifyRegistrationOtpOutput) {
            return ApiResponseResource::error('ワンタイムパスワードの検証に失敗しました。', 400);
        }

        return ApiResponseResource::success([
            'registration_token' => $output->getRegistrationToken(),
            'registration_token_expires_at' => $output->getRegistrationTokenExpiresAt(),
        ], 'ワンタイムパスワードを検証しました。', 200);
    }
}
