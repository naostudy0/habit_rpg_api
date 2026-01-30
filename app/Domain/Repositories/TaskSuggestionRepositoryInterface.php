<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\TaskSuggestion;

/**
 * ドメイン層の予定提案を永続化するためのリポジトリ定義。
 */
interface TaskSuggestionRepositoryInterface
{
    /**
     * ユーザーIDで提案一覧を取得する。
     *
     * @param int $user_id
     * @return TaskSuggestion[]
     */
    public function findByUserId(int $user_id): array;

    /**
     * 提案を永続化して作成する。
     *
     * @param TaskSuggestion $task_suggestion
     * @return TaskSuggestion
     */
    public function create(TaskSuggestion $task_suggestion): TaskSuggestion;

    /**
     * UUIDとユーザーIDで提案を取得する。
     *
     * @param string $uuid
     * @param int $user_id
     * @return TaskSuggestion|null
     */
    public function findByUuidAndUserId(string $uuid, int $user_id): ?TaskSuggestion;

    /**
     * 提案を削除する。
     *
     * @param TaskSuggestion $task_suggestion
     * @return bool
     */
    public function delete(TaskSuggestion $task_suggestion): bool;
}
