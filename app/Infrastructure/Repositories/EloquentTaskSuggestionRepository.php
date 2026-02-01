<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\TaskSuggestion as DomainTaskSuggestion;
use App\Domain\Repositories\TaskSuggestionRepositoryInterface;
use App\Models\TaskSuggestion;

class EloquentTaskSuggestionRepository implements TaskSuggestionRepositoryInterface
{
    private TaskSuggestion $task_suggestion;

    public function __construct(TaskSuggestion $task_suggestion)
    {
        $this->task_suggestion = $task_suggestion;
    }

    /**
     * @param int $user_id
     * @return DomainTaskSuggestion[]
     */
    public function findByUserId(int $user_id): array
    {
        $suggestions = $this->task_suggestion
            ->where('user_id', $user_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return $suggestions->map(function (TaskSuggestion $suggestion): DomainTaskSuggestion {
            return $this->toDomain($suggestion);
        })->all();
    }

    public function create(DomainTaskSuggestion $task_suggestion): DomainTaskSuggestion
    {
        $created_suggestion = $this->task_suggestion->create($this->toCreateData($task_suggestion));

        return $this->toDomain($created_suggestion);
    }

    public function findByUuidAndUserId(string $uuid, int $user_id): ?DomainTaskSuggestion
    {
        $suggestion = $this->task_suggestion
            ->where('task_suggestion_uuid', $uuid)
            ->where('user_id', $user_id)
            ->first();

        if (!$suggestion) {
            return null;
        }

        return $this->toDomain($suggestion);
    }

    public function delete(DomainTaskSuggestion $task_suggestion): bool
    {
        $model = $this->findModelByDomainSuggestion($task_suggestion);

        return $model->delete();
    }

    private function toDomain(TaskSuggestion $task_suggestion): DomainTaskSuggestion
    {
        return new DomainTaskSuggestion(
            $task_suggestion->task_suggestion_id,
            $task_suggestion->task_suggestion_uuid,
            $task_suggestion->user_id,
            $task_suggestion->title,
            $task_suggestion->memo,
            $task_suggestion->created_at ? $task_suggestion->created_at->toIso8601String() : null,
            $task_suggestion->updated_at ? $task_suggestion->updated_at->toIso8601String() : null
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function toCreateData(DomainTaskSuggestion $task_suggestion): array
    {
        $data = [
            'user_id' => $task_suggestion->getUserId(),
            'title' => $task_suggestion->getTitle(),
            'memo' => $task_suggestion->getMemo(),
        ];

        if ($task_suggestion->getTaskSuggestionUuid() !== '') {
            $data['task_suggestion_uuid'] = $task_suggestion->getTaskSuggestionUuid();
        }

        return $data;
    }

    private function findModelByDomainSuggestion(DomainTaskSuggestion $task_suggestion): TaskSuggestion
    {
        $suggestion_id = $task_suggestion->getTaskSuggestionId();
        if ($suggestion_id !== null) {
            $model = $this->task_suggestion->newQuery()
                ->where('task_suggestion_id', $suggestion_id)
                ->first();
        } else {
            $model = $this->task_suggestion->newQuery()
                ->where('task_suggestion_uuid', $task_suggestion->getTaskSuggestionUuid())
                ->where('user_id', $task_suggestion->getUserId())
                ->first();
        }

        if (!$model) {
            throw new \RuntimeException('Task suggestion not found.');
        }

        return $model;
    }
}
