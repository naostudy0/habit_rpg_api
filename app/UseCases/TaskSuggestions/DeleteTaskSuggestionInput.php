<?php

namespace App\UseCases\TaskSuggestions;

use App\UseCases\Inputs\Input;

/**
 * 提案削除UseCaseの入力DTO
 */
class DeleteTaskSuggestionInput implements Input
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
