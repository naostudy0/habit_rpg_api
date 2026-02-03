<?php

namespace App\Http\Resources\Users;

use App\Http\Resources\ApiResponseResource;
use App\UseCases\Results\Result;
use App\UseCases\Users\ShowUserOutput;
use Illuminate\Http\JsonResponse;

/**
 * ユーザー取得ResultをAPIレスポンスへ変換するResource
 */
class ShowUserResource
{
    public static function fromResult(Result $result): JsonResponse
    {
        if (!$result->isSuccess()) {
            return ApiResponseResource::error(
                $result->getErrorMessage() ?? 'ユーザーが見つかりません',
                404
            );
        }

        $output = $result->getOutput();
        if (!$output instanceof ShowUserOutput) {
            return ApiResponseResource::error('ユーザーが見つかりません', 404);
        }

        $user = $output->getUser();

        return ApiResponseResource::success([
            'user_uuid' => $user->getUserUuid(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'is_dark_mode' => $user->isDarkMode(),
            'is_24_hour_format' => $user->is24HourFormat(),
        ]);
    }
}
