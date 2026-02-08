<?php

namespace App\UseCases\Tasks;

use App\UseCases\Inputs\Input;

/**
 * 予定削除UseCaseの入力DTO
 */
class DeleteTaskInput implements Input
{
    private string $uuid;
    private int $user_id;

    public function __construct(string $uuid, int $user_id)
    {
        $this->uuid = $uuid;
        $this->user_id = $user_id;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }
}
