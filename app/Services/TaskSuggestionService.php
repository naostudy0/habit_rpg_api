<?php

namespace App\Services;

use App\Models\TaskSuggestion;
use App\Repositories\TaskSuggestionRepository;

class TaskSuggestionService
{
    private TaskSuggestionRepository $task_suggestion_repository;

    public function __construct(TaskSuggestionRepository $task_suggestion_repository)
    {
        $this->task_suggestion_repository = $task_suggestion_repository;
    }

    /**
     * 提案を作成
     *
     * @param int $user_id
     * @param array $data
     */
    public function createSuggestion(int $user_id, array $data): void
    {
        $this->task_suggestion_repository
            ->create([
                'user_id' => $user_id,
                'title' => $data['title'],
                'memo' => $data['memo'],
            ]);
    }

    /**
     * ユーザーIDで提案一覧を取得
     *
     * @param int $user_id
     * @return array
     */
    public function getSuggestionsForApi(int $user_id): array
    {
        $suggestions = $this->task_suggestion_repository->findByUserId($user_id);

        return $suggestions->map(function ($suggestion) {
            return $this->formatSuggestionForApi($suggestion);
        })->toArray();
    }

    /**
     * 提案を削除
     *
     * @param string $uuid
     * @param int $user_id
     * @return bool
     */
    public function deleteSuggestion(string $uuid, int $user_id): bool
    {
        // 提案を取得
        $suggestion = $this->task_suggestion_repository->findByUuidAndUserId($uuid, $user_id);
        if (!$suggestion) {
            return false;
        }

        // 提案を削除
        return $this->task_suggestion_repository->delete($suggestion);
    }

    /**
     * TaskSuggestionオブジェクトをAPIレスポンス形式の配列に変換
     *
     * @param TaskSuggestion $task_suggestion
     * @return array
     */
    private function formatSuggestionForApi(TaskSuggestion $task_suggestion): array
    {
        return [
            'uuid' => $task_suggestion->task_suggestion_uuid,
            'title' => $task_suggestion->title,
            'memo' => $task_suggestion->memo,
            'created_at' => $task_suggestion->created_at->toIso8601String(),
            'updated_at' => $task_suggestion->updated_at->toIso8601String(),
        ];
    }
}
