<?php

namespace App\Presenters\Tasks;

use App\UseCases\Results\Result;
use App\UseCases\Tasks\GetTasksOutput;
use Illuminate\Http\JsonResponse;

/**
 * 予定一覧取得UseCaseのResultをAPIレスポンスへ変換するPresenter
 */
class GetTasksPresenter
{
    public function present(Result $result): JsonResponse
    {
        if (!$result->isSuccess()) {
            return response()->json([
                'result' => false,
                'message' => $result->getErrorMessage() ?? '予定の取得に失敗しました',
            ], 400);
        }

        $output = $result->getOutput();
        if (!$output instanceof GetTasksOutput) {
            return response()->json([
                'result' => false,
                'message' => '予定の取得に失敗しました',
            ], 400);
        }

        return response()->json([
            'result' => true,
            'data' => $output->getTasks(),
        ], 200);
    }
}
