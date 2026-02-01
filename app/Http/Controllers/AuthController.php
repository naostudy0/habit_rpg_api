<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    private AuthService $auth_service;

    public function __construct(AuthService $auth_service)
    {
        $this->auth_service = $auth_service;
    }

    /**
     * ログイン処理
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        // 認証処理
        $user = $this->auth_service->authenticate(
            $credentials['email'],
            $credentials['password']
        );

        // 認証失敗時
        if (!$user) {
            return response()->json([
                'message' => '認証に失敗しました。',
                'errors' => [
                    'email' => ['メールアドレスまたはパスワードが正しくありません。'],
                ],
            ], 401);
        }

        // トークンを生成
        $auth_user = User::where('user_id', $user->getUserId())->first();
        if (!$auth_user) {
            return response()->json([
                'message' => '認証に失敗しました。',
                'errors' => [
                    'email' => ['メールアドレスまたはパスワードが正しくありません。'],
                ],
            ], 401);
        }
        $token = $auth_user->createToken('auth-token')->plainTextToken;

        // 認証成功時
        return response()->json([
            'result' => true,
            'data' => $auth_user,
            'token' => $token,
        ], 200);
    }
}
