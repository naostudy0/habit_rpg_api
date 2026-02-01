<?php

namespace App\Presenters\Auth;

use App\UseCases\Auth\LoginOutput;
use App\UseCases\Results\Result;
use Illuminate\Http\JsonResponse;

/**
 * ログインUseCaseのResultをAPIレスポンスへ変換するPresenter
 */
class LoginPresenter
{
    public function present(Result $result): JsonResponse
    {
        if (!$result->isSuccess()) {
            return response()->json([
                'message' => $result->getErrorMessage() ?? '認証に失敗しました。',
                'errors' => [
                    'email' => ['メールアドレスまたはパスワードが正しくありません。'],
                ],
            ], 401);
        }

        $output = $result->getOutput();
        if (!$output instanceof LoginOutput) {
            return response()->json([
                'message' => '認証に失敗しました。',
                'errors' => [
                    'email' => ['メールアドレスまたはパスワードが正しくありません。'],
                ],
            ], 401);
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
            'token' => $output->getToken(),
        ], 200);
    }
}
