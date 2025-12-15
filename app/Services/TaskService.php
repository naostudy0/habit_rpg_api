<?php

namespace App\Services;

use App\Repositories\TaskRepository;

class TaskService
{
    private TaskRepository $task_repository;

    public function __construct(TaskRepository $task_repository)
    {
        $this->task_repository = $task_repository;
    }

    /**
     * 予定一覧を取得してAPIレスポンス形式に整形
     *
     * @param int $user_id
     * @return array
     */
    public function getTasksForApi(int $user_id): array
    {
        $tasks = $this->task_repository->findByUserId($user_id);

        return $tasks->map(function ($task) {
            return [
                'uuid' => $task->task_uuid,
                'title' => $task->title,
                'scheduled_date' => $task->scheduled_date->format('Y-m-d'),
                'scheduled_time' => $task->scheduled_time,
                'memo' => $task->memo,
                'is_completed' => $task->is_completed,
                'created_at' => $task->created_at->toIso8601String(),
                'updated_at' => $task->updated_at->toIso8601String(),
            ];
        })->toArray();
    }
}
