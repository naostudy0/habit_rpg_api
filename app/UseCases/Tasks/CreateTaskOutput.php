<?php

namespace App\UseCases\Tasks;

use App\UseCases\Outputs\Output;

/**
 * 予定作成UseCaseの出力DTO
 */
class CreateTaskOutput implements Output
{
    /**
     * @var array<string, mixed>
     */
    private array $task;

    /**
     * @param array<string, mixed> $task
     */
    public function __construct(array $task)
    {
        $this->task = $task;
    }

    /**
     * @return array<string, mixed>
     */
    public function getTask(): array
    {
        return $this->task;
    }
}
