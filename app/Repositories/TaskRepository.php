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
}
