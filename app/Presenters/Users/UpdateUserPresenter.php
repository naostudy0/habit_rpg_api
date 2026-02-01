<?php

namespace App\Presenters\Users;

use App\UseCases\Results\Result;
use App\UseCases\Users\UpdateUserOutput;
use Illuminate\Http\JsonResponse;

/**
 * ユーザー更新UseCaseのResultをAPIレスポンスへ変換するPresenter
 */
class UpdateUserPresenter
{
    public function present(Result $result): JsonResponse
    {
        if (!$result->isSuccess()) {
            return response()->json([
                'result' => false,
                'message' => $result->getErrorMessage() ?? 'ユーザー情報の更新に失敗しました',
            ], 400);
        }

        $output = $result->getOutput();
        if (!$output instanceof UpdateUserOutput) {
            return response()->json([
                'result' => false,
                'message' => 'ユーザー情報の更新に失敗しました',
            ], 400);
        }

        return response()->json([
            'result' => true,
            'message' => 'ユーザー情報を更新しました',
            'data' => $output->getUser(),
        ], 200);
    }
}
