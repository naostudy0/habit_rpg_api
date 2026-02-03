<?php

namespace App\Http\Resources\Tasks;

use App\Http\Resources\ApiResponseResource;
use App\UseCases\Results\Result;
use App\UseCases\Tasks\UpdateTaskOutput;
use Illuminate\Http\JsonResponse;

/**
 * 予定更新ResultをAPIレスポンスへ変換するResource
 */
class UpdateTaskResource
{
    public static function fromResult(Result $result): JsonResponse
    {
        if (!$result->isSuccess()) {
            return ApiResponseResource::error(
                $result->getErrorMessage() ?? '予定の更新に失敗しました',
                400
            );
        }

        $output = $result->getOutput();
        if (!$output instanceof UpdateTaskOutput) {
            return ApiResponseResource::error('予定の更新に失敗しました', 400);
        }

        $task = $output->getTask();
        if ($task === null) {
            return ApiResponseResource::error('予定の更新に失敗しました', 400);
        }

        return ApiResponseResource::success(
            $task,
            '予定を更新しました',
            200
        );
    }
}
