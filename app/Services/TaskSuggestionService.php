<?php

namespace App\Services;

use App\Domain\Entities\TaskSuggestion;
use App\Domain\Repositories\TaskSuggestionRepositoryInterface;

class TaskSuggestionService
{
    private TaskSuggestionRepositoryInterface $task_suggestion_repository;

    public function __construct(TaskSuggestionRepositoryInterface $task_suggestion_repository)
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
        $task_suggestion = new TaskSuggestion(
            null,
            '',
            $user_id,
            $data['title'],
            $data['memo'],
            null,
            null
        );
        $this->task_suggestion_repository->create($task_suggestion);
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

        return array_map(function (TaskSuggestion $suggestion): array {
            return $this->formatSuggestionForApi($suggestion);
        }, $suggestions);
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
            'uuid' => $task_suggestion->getTaskSuggestionUuid(),
            'title' => $task_suggestion->getTitle(),
            'memo' => $task_suggestion->getMemo(),
            'created_at' => $task_suggestion->getCreatedAt(),
            'updated_at' => $task_suggestion->getUpdatedAt(),
        ];
    }
}
