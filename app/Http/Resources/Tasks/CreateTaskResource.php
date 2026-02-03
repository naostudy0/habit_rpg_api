<?php

namespace App\Http\Resources\Tasks;

use App\Http\Resources\ApiResponseResource;
use App\UseCases\Results\Result;
use App\UseCases\Tasks\CreateTaskOutput;
use Illuminate\Http\JsonResponse;

/**
 * 予定作成ResultをAPIレスポンスへ変換するResource
 */
class CreateTaskResource
{
    public static function fromResult(Result $result): JsonResponse
    {
        if (!$result->isSuccess()) {
            return ApiResponseResource::error(
                $result->getErrorMessage() ?? '予定の作成に失敗しました',
                400
            );
        }

        $output = $result->getOutput();
        if (!$output instanceof CreateTaskOutput) {
            return ApiResponseResource::error('予定の作成に失敗しました', 400);
        }

        return ApiResponseResource::success(
            $output->getTask(),
            '予定を作成しました',
            201
        );
    }
}
