<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Presenters\Auth\LoginPresenter;
use App\UseCases\Auth\LoginInput;
use App\UseCases\Auth\LoginUseCase;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    private LoginUseCase $login_use_case;
    private LoginPresenter $login_presenter;

    public function __construct(LoginUseCase $login_use_case, LoginPresenter $login_presenter)
    {
        $this->login_use_case = $login_use_case;
        $this->login_presenter = $login_presenter;
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

        return $this->login_presenter->present($result);
    }
}
