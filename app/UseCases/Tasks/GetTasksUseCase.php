<?php

namespace App\UseCases\Tasks;

use App\Services\TaskService;
use App\UseCases\Inputs\Input;
use App\UseCases\Results\Result;
use App\UseCases\UseCaseInterface;

/**
 * 予定一覧取得のUseCase
 */
class GetTasksUseCase implements UseCaseInterface
{
    private TaskService $task_service;

    public function __construct(TaskService $task_service)
    {
        $this->task_service = $task_service;
    }

    public function handle(Input $input): Result
    {
        if (!$input instanceof GetTasksInput) {
            return Result::failure('INVALID_INPUT', '予定の取得に失敗しました');
        }

        $tasks = $this->task_service->getTasksForApi($input->getUserId());

        return Result::success(new GetTasksOutput($tasks));
    }
}
