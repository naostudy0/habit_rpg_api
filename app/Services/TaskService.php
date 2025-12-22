<?php

namespace App\Services;

use App\Models\Task;
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

        // 予定をAPIレスポンス形式に変換して返す
        return $tasks->map(function ($task) {
            return $this->formatTaskForApi($task);
        })->toArray();
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
        $task = $this->task_repository->create([
            'user_id' => $user_id,
            'title' => $data['title'],
            'scheduled_date' => $data['scheduled_date'],
            'scheduled_time' => $data['scheduled_time'],
            'memo' => $data['memo'] ?? null,
            'is_completed' => false,
        ]);

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
        $update_data = [
            'title' => $data['title'],
            'scheduled_date' => $data['scheduled_date'],
            'scheduled_time' => $data['scheduled_time'],
            'memo' => $data['memo'] ?? null,
        ];

        $task = $this->task_repository->update($task, $update_data);

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

        // 予定の完了状態を更新
        $task = $this->task_repository->updateCompletion($task, $is_completed);

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
        return $this->task_repository->getDistinctUserIds()->toArray();
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

        if ($tasks->isEmpty()) {
            return '予定はありません。';
        }

        $formatted = [];
        foreach ($tasks as $task) {
            $formatted_line = $task->title;
            if ($task->memo) {
                $formatted_line .= "：{$task->memo}";
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
            'uuid' => $task->task_uuid,
            'title' => $task->title,
            'scheduled_date' => $task->scheduled_date->format('Y-m-d'),
            'scheduled_time' => $task->scheduled_time,
            'memo' => $task->memo,
            'is_completed' => $task->is_completed,
            'created_at' => $task->created_at->toIso8601String(),
            'updated_at' => $task->updated_at->toIso8601String(),
        ];
    }
}
