<?php

namespace App\Repositories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;

class TaskRepository
{
    private Task $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    /**
     * ユーザーIDで予定一覧を取得
     *
     * @param int $user_id
     * @return Collection
     */
    public function findByUserId(int $user_id): Collection
    {
        return $this->task
            ->where('user_id', $user_id)
            ->orderBy('scheduled_date', 'asc')
            ->orderBy('scheduled_time', 'asc')
            ->get();
    }

    /**
     * UUIDで予定を取得
     *
     * @param string $uuid
     * @param int $user_id
     * @return Task|null
     */
    public function findByUuidAndUserId(string $uuid, int $user_id): ?Task
    {
        return $this->task
            ->where('task_uuid', $uuid)
            ->where('user_id', $user_id)
            ->first();
    }

    /**
     * 予定を作成
     *
     * @param array $data
     * @return Task
     */
    public function create(array $data): Task
    {
        return $this->task->create($data);
    }

    /**
     * 予定を更新
     *
     * @param Task $task
     * @param array $data
     * @return Task
     */
    public function update(Task $task, array $data): Task
    {
        $task->update($data);

        return $task;
    }

    /**
     * 予定を削除
     *
     * @param Task $task
     * @return bool
     */
    public function delete(Task $task): bool
    {
        return $task->delete();
    }

    /**
     * 予定の完了状態を更新
     *
     * @param Task $task
     * @param bool $is_completed
     * @return Task
     */
    public function updateCompletion(Task $task, bool $is_completed): Task
    {
        $task->is_completed = $is_completed;
        $task->save();

        return $task;
    }

    /**
     * ユーザーの最近の予定を取得（過去30日間と未来7日間）
     *
     * @param int $user_id
     * @param int $limit
     * @return Collection
     */
    public function findRecentTasksByUserId(int $user_id, int $limit = 20): Collection
    {
        return $this->task
            ->where('user_id', $user_id)
            ->whereBetween('scheduled_date', [
                now()->subDays(30)->format('Y-m-d'),
                now()->addDays(7)->format('Y-m-d')
            ])
            ->orderBy('scheduled_date', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * 予定があるユーザーIDの一覧を取得
     *
     * @return \Illuminate\Support\Collection
     */
    public function getDistinctUserIds(): \Illuminate\Support\Collection
    {
        return $this->task
            ->distinct()
            ->pluck('user_id');
    }

    /**
     * 指定されたユーザーに予定があるか確認
     *
     * @param int $user_id
     * @return bool
     */
    public function hasTasksByUserId(int $user_id): bool
    {
        return $this->task
            ->where('user_id', $user_id)
            ->exists();
    }
}
