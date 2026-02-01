<?php

namespace App\UseCases\Auth;

use App\Domain\Entities\User;
use App\UseCases\Outputs\Output;

/**
 * ログインUseCaseの出力DTO
 */
class LoginOutput implements Output
{
    private User $user;
    private string $token;

    public function __construct(User $user, string $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getToken(): string
    {
        return $this->token;
    }
}
