<?php

namespace App\Repositories;

use App\Models\TaskSuggestion;
use Illuminate\Database\Eloquent\Collection;

class TaskSuggestionRepository
{
    private TaskSuggestion $task_suggestion;

    public function __construct(TaskSuggestion $task_suggestion)
    {
        $this->task_suggestion = $task_suggestion;
    }

    /**
     * ユーザーIDで提案一覧を取得
     *
     * @param int $user_id
     * @return Collection
     */
    public function findByUserId(int $user_id): Collection
    {
        return $this->task_suggestion
            ->where('user_id', $user_id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * 提案を保存
     *
     * @param array $data
     * @return TaskSuggestion
     */
    public function create(array $data): TaskSuggestion
    {
        return $this->task_suggestion->create($data);
    }

    /**
     * UUIDで提案を取得
     *
     * @param string $uuid
     * @param int $user_id
     * @return TaskSuggestion|null
     */
    public function findByUuidAndUserId(string $uuid, int $user_id): ?TaskSuggestion
    {
        return $this->task_suggestion
            ->where('task_suggestion_uuid', $uuid)
            ->where('user_id', $user_id)
            ->first();
    }

    /**
     * 提案を削除
     *
     * @param TaskSuggestion $task_suggestion
     * @return bool
     */
    public function delete(TaskSuggestion $task_suggestion): bool
    {
        return $task_suggestion->delete();
    }
}
