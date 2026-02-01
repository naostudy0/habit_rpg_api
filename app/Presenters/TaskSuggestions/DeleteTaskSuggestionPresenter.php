<?php

namespace App\Presenters\TaskSuggestions;

use App\UseCases\Results\Result;
use App\UseCases\TaskSuggestions\DeleteTaskSuggestionOutput;
use Illuminate\Http\JsonResponse;

/**
 * 提案削除UseCaseのResultをAPIレスポンスへ変換するPresenter
 */
class DeleteTaskSuggestionPresenter
{
    public function present(Result $result): JsonResponse
    {
        if (!$result->isSuccess()) {
            return response()->json([
                'result' => false,
                'message' => $result->getErrorMessage() ?? '提案が見つかりません',
            ], 404);
        }

        $output = $result->getOutput();
        if (!$output instanceof DeleteTaskSuggestionOutput) {
            return response()->json([
                'result' => false,
                'message' => '提案が見つかりません',
            ], 404);
        }

        return response()->json([
            'result' => true,
            'message' => $output->getMessage(),
        ], 200);
    }
}
