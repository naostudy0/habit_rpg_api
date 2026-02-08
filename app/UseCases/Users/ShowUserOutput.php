<?php

namespace App\UseCases\Users;

use App\Domain\Entities\User;
use App\UseCases\Outputs\Output;

/**
 * ユーザー取得UseCaseの出力DTO
 */
class ShowUserOutput implements Output
{
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
