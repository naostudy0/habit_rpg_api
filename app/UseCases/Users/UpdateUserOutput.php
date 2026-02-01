<?php

namespace App\UseCases\Users;

use App\UseCases\Outputs\Output;

/**
 * ユーザー更新UseCaseの出力DTO
 */
class UpdateUserOutput implements Output
{
    /**
     * @var array<string, mixed>|null
     */
    private ?array $user;

    /**
     * @param array<string, mixed>|null $user
     */
    public function __construct(?array $user)
    {
        $this->user = $user;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getUser(): ?array
    {
        return $this->user;
    }
}
