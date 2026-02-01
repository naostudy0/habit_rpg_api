<?php

namespace App\Presenters\Tasks;

use App\UseCases\Results\Result;
use App\UseCases\Tasks\ToggleCompleteTaskOutput;
use Illuminate\Http\JsonResponse;

/**
 * 予定完了切り替えUseCaseのResultをAPIレスポンスへ変換するPresenter
 */
class ToggleCompleteTaskPresenter
{
    public function present(Result $result): JsonResponse
    {
        if (!$result->isSuccess()) {
            return response()->json([
                'result' => false,
                'message' => $result->getErrorMessage() ?? '予定が見つかりません',
            ], 404);
        }

        $output = $result->getOutput();
        if (!$output instanceof ToggleCompleteTaskOutput) {
            return response()->json([
                'result' => false,
                'message' => '予定が見つかりません',
            ], 404);
        }

        return response()->json([
            'result' => true,
            'message' => $output->isCompleted() ? '予定を完了にしました' : '予定を未完了にしました',
            'data' => $output->getTask(),
        ], 200);
    }
}
