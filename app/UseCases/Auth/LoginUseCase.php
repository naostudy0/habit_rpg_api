<?php

namespace App\UseCases\Auth;

use App\Services\AuthService;
use App\UseCases\Inputs\Input;
use App\UseCases\Results\Result;
use App\UseCases\UseCaseInterface;

/**
 * ログインのUseCase
 */
class LoginUseCase implements UseCaseInterface
{
    private AuthService $auth_service;
    private TokenIssuerInterface $token_issuer;

    public function __construct(AuthService $auth_service, TokenIssuerInterface $token_issuer)
    {
        $this->auth_service = $auth_service;
        $this->token_issuer = $token_issuer;
    }

    public function handle(Input $input): Result
    {
        if (!$input instanceof LoginInput) {
            return Result::failure('INVALID_INPUT', '認証に失敗しました。');
        }

        $user = $this->auth_service->authenticate($input->getEmail(), $input->getPassword());
        if (!$user) {
            return Result::failure('AUTH_FAILED', '認証に失敗しました。');
        }

        try {
            $token = $this->token_issuer->issueToken($user->getUserId() ?? 0);
        } catch (\Throwable $e) {
            return Result::failure('TOKEN_FAILED', '認証に失敗しました。');
        }

        return Result::success(new LoginOutput($user, $token));
    }
}
