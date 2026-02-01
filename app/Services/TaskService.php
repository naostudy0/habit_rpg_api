<?php

namespace App\Services;

use App\Domain\Entities\Task;
use App\Domain\Repositories\TaskRepositoryInterface;

class TaskService
{
    private TaskRepositoryInterface $task_repository;

    public function __construct(TaskRepositoryInterface $task_repository)
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

        // 予定をAPIレスポンス形式に変換して返す
        return array_map(function (Task $task): array {
            return $this->formatTaskForApi($task);
        }, $tasks);
    }

    /**
     * 予定を作成
     *
     * @param int $user_id
     * @param array $data
     * @return array
     */
    public function createTask(int $user_id, array $data): array
    {
        $task = new Task(
            null,
            '',
            $user_id,
            $data['title'],
            $data['scheduled_date'],
            $data['scheduled_time'],
            $data['memo'] ?? null,
            false,
            null,
            null
        );
        $task = $this->task_repository->create($task);

        // 予定をAPIレスポンス形式に変換して返す
        return $this->formatTaskForApi($task);
    }

    /**
     * 予定を更新
     *
     * @param string $uuid
     * @param int $user_id
     * @param array $data
     * @return array|null
     */
    public function updateTask(string $uuid, int $user_id, array $data): ?array
    {
        // 予定を取得
        $task = $this->task_repository->findByUuidAndUserId($uuid, $user_id);
        if (!$task) {
            return null;
        }

        // 予定を更新
        $updated_task = new Task(
            $task->getTaskId(),
            $task->getTaskUuid(),
            $task->getUserId(),
            $data['title'],
            $data['scheduled_date'],
            $data['scheduled_time'],
            $data['memo'] ?? null,
            $task->isCompleted(),
            $task->getCreatedAt(),
            $task->getUpdatedAt()
        );

        $task = $this->task_repository->update($updated_task);

        // 予定をAPIレスポンス形式に変換して返す
        return $this->formatTaskForApi($task);
    }

    /**
     * 予定を削除
     *
     * @param string $uuid
     * @param int $user_id
     * @return bool
     */
    public function deleteTask(string $uuid, int $user_id): bool
    {
        // 予定を取得
        $task = $this->task_repository->findByUuidAndUserId($uuid, $user_id);
        if (!$task) {
            return false;
        }

        // 予定を削除
        return $this->task_repository->delete($task);
    }

    /**
     * UUIDとユーザーIDで予定の存在確認
     *
     * @param string $uuid
     * @param int $user_id
     * @return bool
     */
    public function existsByUuidAndUserId(string $uuid, int $user_id): bool
    {
        return !is_null($this->task_repository->findByUuidAndUserId($uuid, $user_id));
    }

    /**
     * 予定の完了状態を切り替え
     *
     * @param string $uuid
     * @param int $user_id
     * @param bool $is_completed
     * @return array
     */
    public function updateCompletion(string $uuid, int $user_id, bool $is_completed): array
    {
        // 予定を取得
        $task = $this->task_repository->findByUuidAndUserId($uuid, $user_id);
        if (!$task) {
            return [];
        }

        $updated_task = new Task(
            $task->getTaskId(),
            $task->getTaskUuid(),
            $task->getUserId(),
            $task->getTitle(),
            $task->getScheduledDate(),
            $task->getScheduledTime(),
            $task->getMemo(),
            $is_completed,
            $task->getCreatedAt(),
            $task->getUpdatedAt()
        );

        // 予定の完了状態を更新
        $task = $this->task_repository->updateCompletion($updated_task, $is_completed);

        // 予定をAPIレスポンス形式に変換して返す
        return $this->formatTaskForApi($task);
    }

    /**
     * 予定があるユーザーIDの一覧を取得
     *
     * @return array
     */
    public function getUserIdsWithTasks(): array
    {
        return $this->task_repository->getDistinctUserIds();
    }

    /**
     * 指定されたユーザーに予定があるか確認
     *
     * @param int $user_id
     * @return bool
     */
    public function hasTasksByUserId(int $user_id): bool
    {
        return $this->task_repository->hasTasksByUserId($user_id);
    }

    /**
     * ユーザーの最近の予定をプロンプト用に整形
     *
     * @param int $user_id
     * @param int $limit
     * @return string
     */
    public function formatRecentTasksForPrompt(int $user_id, int $limit = 10): string
    {
        $tasks = $this->task_repository->findRecentTasksByUserId($user_id, $limit);

        if (empty($tasks)) {
            return '予定はありません。';
        }

        $formatted = [];
        foreach ($tasks as $task) {
            $formatted_line = $task->getTitle();
            if ($task->getMemo()) {
                $formatted_line .= "：{$task->getMemo()}";
            }
            $formatted[] = $formatted_line;
        }

        return implode("\n", $formatted);
    }

    /**
     * TaskオブジェクトをAPIレスポンス形式の配列に変換
     *
     * @param Task $task
     * @return array
     */
    private function formatTaskForApi(Task $task): array
    {
        return [
            'uuid' => $task->getTaskUuid(),
            'title' => $task->getTitle(),
            'scheduled_date' => $task->getScheduledDate(),
            'scheduled_time' => $task->getScheduledTime(),
            'memo' => $task->getMemo(),
            'is_completed' => $task->isCompleted(),
            'created_at' => $task->getCreatedAt(),
            'updated_at' => $task->getUpdatedAt(),
        ];
    }
}
