<?php

namespace App\Http\Resources\Auth;

use App\Http\Resources\ApiResponseResource;
use App\Services\RegistrationService;
use App\UseCases\Auth\SendRegistrationOtpOutput;
use App\UseCases\Results\Result;
use Illuminate\Http\JsonResponse;

class SendRegistrationOtpResource
{
    public static function fromResult(Result $result): JsonResponse
    {
        if (!$result->isSuccess()) {
            $status = match ($result->getErrorCode()) {
                RegistrationService::ERROR_EMAIL_ALREADY_REGISTERED => 409,
                RegistrationService::ERROR_RESEND_WAIT,
                RegistrationService::ERROR_RESEND_LIMIT_EXCEEDED => 429,
                default => 400,
            };

            return ApiResponseResource::error(
                $result->getErrorMessage() ?? 'ワンタイムパスワードの送信に失敗しました。',
                $status
            );
        }

        $output = $result->getOutput();
        if (!$output instanceof SendRegistrationOtpOutput) {
            return ApiResponseResource::error('ワンタイムパスワードの送信に失敗しました。', 400);
        }

        return ApiResponseResource::success([
            'email' => $output->getEmail(),
            'expires_at' => $output->getExpiresAt(),
            'resend_available_at' => $output->getResendAvailableAt(),
        ], 'ワンタイムパスワードを送信しました。', 200);
    }
}
