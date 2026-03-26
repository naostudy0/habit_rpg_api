<?php

namespace App\UseCases\Auth;

use App\UseCases\Outputs\Output;

class CompleteRegistrationOutput implements Output
{
    /**
     * @var array<string, mixed>
     */
    private array $user;

    /**
     * @param array<string, mixed> $user
     */
    public function __construct(array $user)
    {
        $this->user = $user;
    }

    /**
     * @return array<string, mixed>
     */
    public function getUser(): array
    {
        return $this->user;
    }
}
