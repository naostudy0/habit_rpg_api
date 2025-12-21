<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UpdateUserRequest;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    private UserService $user_service;

    public function __construct(UserService $user_service)
    {
        $this->user_service = $user_service;
    }

    /**
     * 現在のユーザー情報を取得
     *
     * @return JsonResponse
     */
    public function show(): JsonResponse
    {
        $user = Auth::user();

        return response()->json([
            'result' => true,
            'data' => [
                'user_uuid' => $user->user_uuid,
                'name' => $user->name,
                'email' => $user->email,
                'is_dark_mode' => $user->is_dark_mode,
                'is_24_hour_format' => $user->is_24_hour_format,
            ],
        ], 200);
    }

    /**
     * ユーザー情報を更新
     *
     * @param UpdateUserRequest $request
     * @return JsonResponse
     */
    public function update(UpdateUserRequest $request): JsonResponse
    {
        $user = $this->user_service->updateUser(
            $request->user()->user_id,
            $request->validated()
        );

        return response()->json([
            'result' => true,
            'message' => 'ユーザー情報を更新しました',
            'data' => $user,
        ], 200);
    }
}
