<?php

namespace App\Http\Resources\TaskSuggestions;

use App\Http\Resources\ApiResponseResource;
use App\UseCases\Results\Result;
use App\UseCases\TaskSuggestions\DeleteTaskSuggestionOutput;
use Illuminate\Http\JsonResponse;

/**
 * 提案削除ResultをAPIレスポンスへ変換するResource
 */
class DeleteTaskSuggestionResource
{
    public static function fromResult(Result $result): JsonResponse
    {
        if (!$result->isSuccess()) {
            return ApiResponseResource::error(
                $result->getErrorMessage() ?? '提案が見つかりません',
                404
            );
        }

        $output = $result->getOutput();
        if (!$output instanceof DeleteTaskSuggestionOutput) {
            return ApiResponseResource::error('提案が見つかりません', 404);
        }

        return ApiResponseResource::success(
            null,
            $output->getMessage(),
            200
        );
    }
}
