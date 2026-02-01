<?php

namespace App\UseCases\Tasks;

use App\UseCases\Inputs\Input;

/**
 * 予定一覧取得UseCaseの入力DTO
 */
class GetTasksInput implements Input
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
