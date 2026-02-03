<?php

namespace App\Http\Resources\Auth;

use App\Http\Resources\ApiResponseResource;
use App\UseCases\Auth\LoginOutput;
use App\UseCases\Results\Result;
use Illuminate\Http\JsonResponse;

/**
 * ログインResultをAPIレスポンスへ変換するResource
 */
class LoginResource
{
    public static function fromResult(Result $result): JsonResponse
    {
        if (!$result->isSuccess()) {
            return ApiResponseResource::error(
                $result->getErrorMessage() ?? '認証に失敗しました',
                401,
                ['email' => ['メールアドレスまたはパスワードが正しくありません。']]
            );
        }

        $output = $result->getOutput();
        if (!$output instanceof LoginOutput) {
            return ApiResponseResource::error(
                '認証に失敗しました',
                401,
                ['email' => ['メールアドレスまたはパスワードが正しくありません。']]
            );
        }

        $user = $output->getUser();

        return ApiResponseResource::success(
            [
                'user_uuid' => $user->getUserUuid(),
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'is_dark_mode' => $user->isDarkMode(),
                'is_24_hour_format' => $user->is24HourFormat(),
            ],
            null,
            200,
            [
                'token' => $output->getToken(),
            ]
        );
    }
}
