<?php

namespace App\UseCases\Tasks;

use App\Services\TaskService;
use App\UseCases\Inputs\Input;
use App\UseCases\Results\Result;
use App\UseCases\UseCaseInterface;

/**
 * 予定作成のUseCase
 */
class CreateTaskUseCase implements UseCaseInterface
{
    private TaskService $task_service;

    public function __construct(TaskService $task_service)
    {
        $this->task_service = $task_service;
    }

    public function handle(Input $input): Result
    {
        if (!$input instanceof CreateTaskInput) {
            return Result::failure('INVALID_INPUT', '予定の作成に失敗しました');
        }

        $task = $this->task_service->createTask($input->getUserId(), $input->getData());

        return Result::success(new CreateTaskOutput($task));
    }
}
