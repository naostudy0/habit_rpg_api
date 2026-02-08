<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Task;

/**
 * ドメイン層の予定を永続化するためのリポジトリ定義
 */
interface TaskRepositoryInterface
{
    /**
     * ユーザーIDで予定一覧を取得する
     *
     * @param int $user_id
     * @return Task[]
     */
    public function findByUserId(int $user_id): array;

    /**
     * UUIDとユーザーIDで予定を取得する
     *
     * @param string $uuid
     * @param int $user_id
     * @return Task|null
     */
    public function findByUuidAndUserId(string $uuid, int $user_id): ?Task;

    /**
     * 予定を永続化して作成する
     *
     * @param Task $task
     * @return Task
     */
    public function create(Task $task): Task;

    /**
     * 予定を更新して永続化する
     *
     * @param Task $task
     * @return Task
     */
    public function update(Task $task): Task;

    /**
     * 予定を削除する
     *
     * @param Task $task
     * @return bool
     */
    public function delete(Task $task): bool;

    /**
     * 予定の完了状態を更新して永続化する
     *
     * @param Task $task
     * @param bool $is_completed
     * @return Task
     */
    public function updateCompletion(Task $task, bool $is_completed): Task;

    /**
     * 直近の予定一覧を取得する
     *
     * @param int $user_id
     * @param int $limit
     * @return Task[]
     */
    public function findRecentTasksByUserId(int $user_id, int $limit = 20): array;

    /**
     * 予定があるユーザーIDの一覧を取得する
     *
     * @return int[]
     */
    public function getDistinctUserIds(): array;

    /**
     * 指定されたユーザーに予定があるか確認する
     *
     * @param int $user_id
     * @return bool
     */
    public function hasTasksByUserId(int $user_id): bool;
}
