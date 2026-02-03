<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Auth\LoginResource;
use App\UseCases\Auth\LoginInput;
use App\UseCases\Auth\LoginUseCase;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    private LoginUseCase $login_use_case;

    public function __construct(LoginUseCase $login_use_case)
    {
        $this->login_use_case = $login_use_case;
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

        $input = new LoginInput(
            $credentials['email'],
            $credentials['password']
        );
        $result = $this->login_use_case->handle($input);

        return LoginResource::fromResult($result);
    }
}
