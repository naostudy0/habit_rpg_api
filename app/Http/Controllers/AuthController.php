<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

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
     * @throws ValidationException
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
            throw ValidationException::withMessages([
                'email' => ['メールアドレスまたはパスワードが正しくありません。'],
            ])->status(401);
        }

        // トークンを生成
        $token = $user->createToken('auth-token')->plainTextToken;

        // 認証成功時
        return response()->json([
            'result' => true,
            'data' => $user,
            'token' => $token,
        ], 200);
    }
}
