<?php

namespace App\Domain\Entities;

/**
 * ドメイン層の予定を表すエンティティ
 *
 * タイトル、スケジュール日時、メモ、完了状態を保持する
 */
class Task
{
    private ?int $task_id;
    private string $task_uuid;
    private int $user_id;
    private string $title;
    private string $scheduled_date;
    private string $scheduled_time;
    private ?string $memo;
    private bool $is_completed;
    private ?string $created_at;
    private ?string $updated_at;

    /**
     * @param string $scheduled_date Y-m-d
     * @param string $scheduled_time HH:MM
     * @param string|null $created_at ISO8601
     * @param string|null $updated_at ISO8601
     */
    public function __construct(
        ?int $task_id,
        string $task_uuid,
        int $user_id,
        string $title,
        string $scheduled_date,
        string $scheduled_time,
        ?string $memo,
        bool $is_completed,
        ?string $created_at,
        ?string $updated_at
    ) {
        $this->task_id = $task_id;
        $this->task_uuid = $task_uuid;
        $this->user_id = $user_id;
        $this->title = $title;
        $this->scheduled_date = $scheduled_date;
        $this->scheduled_time = $scheduled_time;
        $this->memo = $memo;
        $this->is_completed = $is_completed;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    public function getTaskId(): ?int
    {
        return $this->task_id;
    }

    public function getTaskUuid(): string
    {
        return $this->task_uuid;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getScheduledDate(): string
    {
        return $this->scheduled_date;
    }

    public function getScheduledTime(): string
    {
        return $this->scheduled_time;
    }

    public function getMemo(): ?string
    {
        return $this->memo;
    }

    public function isCompleted(): bool
    {
        return $this->is_completed;
    }

    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updated_at;
    }
}
