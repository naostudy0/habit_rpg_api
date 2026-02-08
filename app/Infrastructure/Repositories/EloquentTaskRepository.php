<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\Task as DomainTask;
use App\Domain\Repositories\TaskRepositoryInterface;
use App\Models\Task;

class EloquentTaskRepository implements TaskRepositoryInterface
{
    private Task $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    /**
     * @param int $user_id
     * @return DomainTask[]
     */
    public function findByUserId(int $user_id): array
    {
        $tasks = $this->task
            ->where('user_id', $user_id)
            ->orderBy('scheduled_date', 'asc')
            ->orderBy('scheduled_time', 'asc')
            ->get();

        return $tasks->map(function (Task $task): DomainTask {
            return $this->toDomain($task);
        })->all();
    }

    public function findByUuidAndUserId(string $uuid, int $user_id): ?DomainTask
    {
        $task = $this->task
            ->where('task_uuid', $uuid)
            ->where('user_id', $user_id)
            ->first();

        if (!$task) {
            return null;
        }

        return $this->toDomain($task);
    }

    public function create(DomainTask $task): DomainTask
    {
        $created_task = $this->task->create($this->toCreateData($task));

        return $this->toDomain($created_task);
    }

    public function update(DomainTask $task): DomainTask
    {
        $model = $this->findModelByDomainTask($task);
        $model->update($this->toUpdateData($task));

        return $this->toDomain($model);
    }

    public function delete(DomainTask $task): bool
    {
        $model = $this->findModelByDomainTask($task);

        return $model->delete();
    }

    public function updateCompletion(DomainTask $task, bool $is_completed): DomainTask
    {
        $model = $this->findModelByDomainTask($task);
        $model->is_completed = $is_completed;
        $model->save();

        return $this->toDomain($model);
    }

    /**
     * @param int $user_id
     * @param int $limit
     * @return DomainTask[]
     */
    public function findRecentTasksByUserId(int $user_id, int $limit = 20): array
    {
        $tasks = $this->task
            ->where('user_id', $user_id)
            ->whereBetween('scheduled_date', [
                now()->subDays(30)->format('Y-m-d'),
                now()->addDays(7)->format('Y-m-d'),
            ])
            ->orderBy('scheduled_date', 'desc')
            ->limit($limit)
            ->get();

        return $tasks->map(function (Task $task): DomainTask {
            return $this->toDomain($task);
        })->all();
    }

    /**
     * @return int[]
     */
    public function getDistinctUserIds(): array
    {
        return $this->task
            ->distinct()
            ->pluck('user_id')
            ->values()
            ->all();
    }

    public function hasTasksByUserId(int $user_id): bool
    {
        return $this->task
            ->where('user_id', $user_id)
            ->exists();
    }

    private function toDomain(Task $task): DomainTask
    {
        return new DomainTask(
            $task->task_id,
            $task->task_uuid,
            $task->user_id,
            $task->title,
            $task->scheduled_date ? $task->scheduled_date->format('Y-m-d') : '',
            (string) $task->scheduled_time,
            $task->memo,
            (bool) $task->is_completed,
            $task->created_at ? $task->created_at->toIso8601String() : null,
            $task->updated_at ? $task->updated_at->toIso8601String() : null
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function toCreateData(DomainTask $task): array
    {
        $data = [
            'user_id' => $task->getUserId(),
            'title' => $task->getTitle(),
            'scheduled_date' => $task->getScheduledDate(),
            'scheduled_time' => $task->getScheduledTime(),
            'memo' => $task->getMemo(),
            'is_completed' => $task->isCompleted(),
        ];

        if ($task->getTaskUuid() !== '') {
            $data['task_uuid'] = $task->getTaskUuid();
        }

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    private function toUpdateData(DomainTask $task): array
    {
        return [
            'title' => $task->getTitle(),
            'scheduled_date' => $task->getScheduledDate(),
            'scheduled_time' => $task->getScheduledTime(),
            'memo' => $task->getMemo(),
            'is_completed' => $task->isCompleted(),
        ];
    }

    private function findModelByDomainTask(DomainTask $task): Task
    {
        $task_id = $task->getTaskId();
        if ($task_id !== null) {
            $model = $this->task->newQuery()
                ->where('task_id', $task_id)
                ->first();
        } else {
            $model = $this->task->newQuery()
                ->where('task_uuid', $task->getTaskUuid())
                ->where('user_id', $task->getUserId())
                ->first();
        }

        if (!$model) {
            throw new \RuntimeException('Task not found.');
        }

        return $model;
    }
}
