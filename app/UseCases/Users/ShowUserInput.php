<?php

namespace App\UseCases\Users;

use App\UseCases\Inputs\Input;

/**
 * ユーザー取得UseCaseの入力DTO
 */
class ShowUserInput implements Input
{
    private int $user_id;

    public function __construct(int $user_id)
    {
        $this->user_id = $user_id;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }
}
