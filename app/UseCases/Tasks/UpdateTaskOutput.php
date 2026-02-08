<?php

namespace App\UseCases\Tasks;

use App\UseCases\Outputs\Output;

/**
 * 予定更新UseCaseの出力DTO
 */
class UpdateTaskOutput implements Output
{
    /**
     * @var array<string, mixed>|null
     */
    private ?array $task;

    /**
     * @param array<string, mixed>|null $task
     */
    public function __construct(?array $task)
    {
        $this->task = $task;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getTask(): ?array
    {
        return $this->task;
    }
}
