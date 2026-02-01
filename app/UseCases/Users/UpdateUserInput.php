<?php

namespace App\UseCases\Users;

use App\UseCases\Inputs\Input;

/**
 * ユーザー更新UseCaseの入力DTO
 */
class UpdateUserInput implements Input
{
    private int $user_id;
    /**
     * @var array<string, mixed>
     */
    private array $data;

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(int $user_id, array $data)
    {
        $this->user_id = $user_id;
        $this->data = $data;
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
