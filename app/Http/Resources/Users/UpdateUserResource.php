<?php

namespace App\Http\Resources\Users;

use App\Http\Resources\ApiResponseResource;
use App\UseCases\Results\Result;
use App\UseCases\Users\UpdateUserOutput;
use Illuminate\Http\JsonResponse;

/**
 * ユーザー更新ResultをAPIレスポンスへ変換するResource
 */
class UpdateUserResource
{
    public static function fromResult(Result $result): JsonResponse
    {
        if (!$result->isSuccess()) {
            return ApiResponseResource::error(
                $result->getErrorMessage() ?? 'ユーザー情報の更新に失敗しました',
                400
            );
        }

        $output = $result->getOutput();
        if (!$output instanceof UpdateUserOutput) {
            return ApiResponseResource::error('ユーザー情報の更新に失敗しました', 400);
        }

        return ApiResponseResource::success(
            $output->getUser(),
            'ユーザー情報を更新しました',
            200
        );
    }
}
