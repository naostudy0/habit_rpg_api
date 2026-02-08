<?php

namespace App\UseCases\Tasks;

use App\UseCases\Inputs\Input;

/**
 * 予定完了切り替えUseCaseの入力DTO
 */
class ToggleCompleteTaskInput implements Input
{
    private string $uuid;
    private int $user_id;
    private bool $is_completed;

    public function __construct(string $uuid, int $user_id, bool $is_completed)
    {
        $this->uuid = $uuid;
        $this->user_id = $user_id;
        $this->is_completed = $is_completed;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function isCompleted(): bool
    {
        return $this->is_completed;
    }
}
