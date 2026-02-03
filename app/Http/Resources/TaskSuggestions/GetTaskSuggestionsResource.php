<?php

namespace App\Http\Resources\TaskSuggestions;

use App\Http\Resources\ApiResponseResource;
use App\UseCases\Results\Result;
use App\UseCases\TaskSuggestions\GetTaskSuggestionsOutput;
use Illuminate\Http\JsonResponse;

/**
 * 提案一覧取得ResultをAPIレスポンスへ変換するResource
 */
class GetTaskSuggestionsResource
{
    public static function fromResult(Result $result): JsonResponse
    {
        if (!$result->isSuccess()) {
            return ApiResponseResource::error(
                $result->getErrorMessage() ?? '提案の取得に失敗しました',
                400
            );
        }

        $output = $result->getOutput();
        if (!$output instanceof GetTaskSuggestionsOutput) {
            return ApiResponseResource::error('提案の取得に失敗しました', 400);
        }

        return ApiResponseResource::success($output->getSuggestions());
    }
}
