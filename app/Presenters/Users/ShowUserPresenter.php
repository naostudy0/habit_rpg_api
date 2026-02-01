<?php

namespace App\Presenters\Users;

use App\UseCases\Results\Result;
use App\UseCases\Users\ShowUserOutput;
use Illuminate\Http\JsonResponse;

/**
 * ユーザー取得UseCaseのResultをAPIレスポンスへ変換するPresenter
 */
class ShowUserPresenter
{
    public function present(Result $result): JsonResponse
    {
        if (!$result->isSuccess()) {
            return response()->json([
                'result' => false,
                'message' => $result->getErrorMessage() ?? 'ユーザーが見つかりません',
            ], 404);
        }

        $output = $result->getOutput();
        if (!$output instanceof ShowUserOutput) {
            return response()->json([
                'result' => false,
                'message' => 'ユーザーが見つかりません',
            ], 404);
        }

        $user = $output->getUser();

        return response()->json([
            'result' => true,
            'data' => [
                'user_uuid' => $user->getUserUuid(),
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'is_dark_mode' => $user->isDarkMode(),
                'is_24_hour_format' => $user->is24HourFormat(),
            ],
        ], 200);
    }
}
