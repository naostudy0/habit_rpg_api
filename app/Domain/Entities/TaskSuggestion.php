<?php

namespace App\Domain\Entities;

/**
 * ドメイン層の予定提案を表すエンティティ。
 *
 * タイトルとメモを保持する。
 */
class TaskSuggestion
{
    private ?int $task_suggestion_id;
    private string $task_suggestion_uuid;
    private int $user_id;
    private string $title;
    private ?string $memo;
    private ?string $created_at;
    private ?string $updated_at;

    /**
     * @param string|null $created_at ISO8601
     * @param string|null $updated_at ISO8601
     */
    public function __construct(
        ?int $task_suggestion_id,
        string $task_suggestion_uuid,
        int $user_id,
        string $title,
        ?string $memo,
        ?string $created_at,
        ?string $updated_at
    ) {
        $this->task_suggestion_id = $task_suggestion_id;
        $this->task_suggestion_uuid = $task_suggestion_uuid;
        $this->user_id = $user_id;
        $this->title = $title;
        $this->memo = $memo;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    public function getTaskSuggestionId(): ?int
    {
        return $this->task_suggestion_id;
    }

    public function getTaskSuggestionUuid(): string
    {
        return $this->task_suggestion_uuid;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getMemo(): ?string
    {
        return $this->memo;
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
