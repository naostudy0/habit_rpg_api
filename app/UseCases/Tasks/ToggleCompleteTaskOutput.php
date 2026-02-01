<?php

namespace App\UseCases\Tasks;

use App\UseCases\Outputs\Output;

/**
 * 予定完了切り替えUseCaseの出力DTO
 */
class ToggleCompleteTaskOutput implements Output
{
    /**
     * @var array<string, mixed>
     */
    private array $task;
    private bool $is_completed;

    /**
     * @param array<string, mixed> $task
     */
    public function __construct(array $task, bool $is_completed)
    {
        $this->task = $task;
        $this->is_completed = $is_completed;
    }

    /**
     * @return array<string, mixed>
     */
    public function getTask(): array
    {
        return $this->task;
    }

    public function isCompleted(): bool
    {
        return $this->is_completed;
    }
}
