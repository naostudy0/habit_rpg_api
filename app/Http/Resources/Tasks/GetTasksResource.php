<?php

namespace App\Http\Resources\Tasks;

use App\Http\Resources\ApiResponseResource;
use App\UseCases\Results\Result;
use App\UseCases\Tasks\GetTasksOutput;
use Illuminate\Http\JsonResponse;

/**
 * 予定一覧取得ResultをAPIレスポンスへ変換するResource
 */
class GetTasksResource
{
    public static function fromResult(Result $result): JsonResponse
    {
        if (!$result->isSuccess()) {
            return ApiResponseResource::error(
                $result->getErrorMessage() ?? '予定の取得に失敗しました',
                400
            );
        }

        $output = $result->getOutput();
        if (!$output instanceof GetTasksOutput) {
            return ApiResponseResource::error('予定の取得に失敗しました', 400);
        }

        return ApiResponseResource::success($output->getTasks());
    }
}
