<?php

namespace App\Http\Resources\Tasks;

use App\Http\Resources\ApiResponseResource;
use App\UseCases\Results\Result;
use App\UseCases\Tasks\ToggleCompleteTaskOutput;
use Illuminate\Http\JsonResponse;

/**
 * 予定完了切り替えResultをAPIレスポンスへ変換するResource
 */
class ToggleCompleteTaskResource
{
    public static function fromResult(Result $result): JsonResponse
    {
        if (!$result->isSuccess()) {
            return ApiResponseResource::error(
                $result->getErrorMessage() ?? '予定が見つかりません',
                404
            );
        }

        $output = $result->getOutput();
        if (!$output instanceof ToggleCompleteTaskOutput) {
            return ApiResponseResource::error('予定が見つかりません', 404);
        }

        return ApiResponseResource::success(
            $output->getTask(),
            $output->isCompleted() ? '予定を完了にしました' : '予定を未完了にしました',
            200
        );
    }
}
