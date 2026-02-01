<?php

namespace App\UseCases\Tasks;

use App\Services\TaskService;
use App\UseCases\Inputs\Input;
use App\UseCases\Results\Result;
use App\UseCases\UseCaseInterface;

/**
 * 予定削除のUseCase
 */
class DeleteTaskUseCase implements UseCaseInterface
{
    private TaskService $task_service;

    public function __construct(TaskService $task_service)
    {
        $this->task_service = $task_service;
    }

    public function handle(Input $input): Result
    {
        if (!$input instanceof DeleteTaskInput) {
            return Result::failure('INVALID_INPUT', '予定の削除に失敗しました');
        }

        $this->task_service->deleteTask($input->getUuid(), $input->getUserId());

        return Result::success(new DeleteTaskOutput('予定を削除しました'));
    }
}
