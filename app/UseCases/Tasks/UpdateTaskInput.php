<?php

namespace App\UseCases\Tasks;

use App\UseCases\Inputs\Input;

/**
 * 予定更新UseCaseの入力DTO
 */
class UpdateTaskInput implements Input
{
    private string $uuid;
    private int $user_id;
    /**
     * @var array<string, mixed>
     */
    private array $data;

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(string $uuid, int $user_id, array $data)
    {
        $this->uuid = $uuid;
        $this->user_id = $user_id;
        $this->data = $data;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    /**
     * @return array<string, mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }
}
