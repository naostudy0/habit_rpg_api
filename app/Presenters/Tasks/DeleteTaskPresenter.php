<?php

namespace App\Presenters\Tasks;

use App\UseCases\Results\Result;
use App\UseCases\Tasks\DeleteTaskOutput;
use Illuminate\Http\JsonResponse;

/**
 * 予定削除UseCaseのResultをAPIレスポンスへ変換するPresenter
 */
class DeleteTaskPresenter
{
    public function present(Result $result): JsonResponse
    {
        if (!$result->isSuccess()) {
            return response()->json([
                'result' => false,
                'message' => $result->getErrorMessage() ?? '予定の削除に失敗しました',
            ], 400);
        }

        $output = $result->getOutput();
        if (!$output instanceof DeleteTaskOutput) {
            return response()->json([
                'result' => false,
                'message' => '予定の削除に失敗しました',
            ], 400);
        }

        return response()->json([
            'result' => true,
            'message' => $output->getMessage(),
        ], 200);
    }
}
