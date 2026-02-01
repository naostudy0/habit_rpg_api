<?php

namespace App\Presenters\TaskSuggestions;

use App\UseCases\Results\Result;
use App\UseCases\TaskSuggestions\GetTaskSuggestionsOutput;
use Illuminate\Http\JsonResponse;

/**
 * 提案一覧取得UseCaseのResultをAPIレスポンスへ変換するPresenter
 */
class GetTaskSuggestionsPresenter
{
    public function present(Result $result): JsonResponse
    {
        if (!$result->isSuccess()) {
            return response()->json([
                'result' => false,
                'message' => $result->getErrorMessage() ?? '提案の取得に失敗しました',
            ], 400);
        }

        $output = $result->getOutput();
        if (!$output instanceof GetTaskSuggestionsOutput) {
            return response()->json([
                'result' => false,
                'message' => '提案の取得に失敗しました',
            ], 400);
        }

        return response()->json([
            'result' => true,
            'data' => $output->getSuggestions(),
        ], 200);
    }
}
