<?php

namespace App\Presenters\Tasks;

use App\UseCases\Results\Result;
use App\UseCases\Tasks\UpdateTaskOutput;
use Illuminate\Http\JsonResponse;

/**
 * 予定更新UseCaseのResultをAPIレスポンスへ変換するPresenter
 */
class UpdateTaskPresenter
{
    public function present(Result $result): JsonResponse
    {
        if (!$result->isSuccess()) {
            return response()->json([
                'result' => false,
                'message' => $result->getErrorMessage() ?? '予定の更新に失敗しました',
            ], 400);
        }

        $output = $result->getOutput();
        if (!$output instanceof UpdateTaskOutput) {
            return response()->json([
                'result' => false,
                'message' => '予定の更新に失敗しました',
            ], 400);
        }

        return response()->json([
            'result' => true,
            'message' => '予定を更新しました',
            'data' => $output->getTask(),
        ], 200);
    }
}
