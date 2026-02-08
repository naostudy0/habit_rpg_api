<?php

namespace App\UseCases\Tasks;

use App\UseCases\Outputs\Output;

/**
 * 予定一覧取得UseCaseの出力DTO
 */
class GetTasksOutput implements Output
{
    /**
     * @var array<int, array<string, mixed>>
     */
    private array $tasks;

    /**
     * @param array<int, array<string, mixed>> $tasks
     */
    public function __construct(array $tasks)
    {
        $this->tasks = $tasks;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getTasks(): array
    {
        return $this->tasks;
    }
}
