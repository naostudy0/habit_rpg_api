<?php

namespace App\Http\Resources\Tasks;

use App\Http\Resources\ApiResponseResource;
use App\UseCases\Results\Result;
use App\UseCases\Tasks\DeleteTaskOutput;
use Illuminate\Http\JsonResponse;

/**
 * 予定削除ResultをAPIレスポンスへ変換するResource
 */
class DeleteTaskResource
{
    public static function fromResult(Result $result): JsonResponse
    {
        if (!$result->isSuccess()) {
            return ApiResponseResource::error(
                $result->getErrorMessage() ?? '予定の削除に失敗しました',
                400
            );
        }

        $output = $result->getOutput();
        if (!$output instanceof DeleteTaskOutput) {
            return ApiResponseResource::error('予定の削除に失敗しました', 400);
        }

        return ApiResponseResource::success(
            null,
            $output->getMessage(),
            200
        );
    }
}
