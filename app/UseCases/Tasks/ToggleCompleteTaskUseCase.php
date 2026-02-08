<?php

namespace App\UseCases\Tasks;

use App\Services\TaskService;
use App\UseCases\Inputs\Input;
use App\UseCases\Results\Result;
use App\UseCases\UseCaseInterface;

/**
 * 予定完了切り替えのUseCase
 */
class ToggleCompleteTaskUseCase implements UseCaseInterface
{
    private TaskService $task_service;

    public function __construct(TaskService $task_service)
    {
        $this->task_service = $task_service;
    }

    public function handle(Input $input): Result
    {
        if (!$input instanceof ToggleCompleteTaskInput) {
            return Result::failure('INVALID_INPUT', '予定が見つかりません');
        }

        $task = $this->task_service->updateCompletion(
            $input->getUuid(),
            $input->getUserId(),
            $input->isCompleted()
        );

        if ($task === []) {
            return Result::failure('NOT_FOUND', '予定が見つかりません');
        }

        return Result::success(new ToggleCompleteTaskOutput($task, $input->isCompleted()));
    }
}
