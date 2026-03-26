<?php

namespace App\UseCases\Auth;

use App\UseCases\Outputs\Output;
use InvalidArgumentException;

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
        $required_keys = ['user_uuid', 'email'];
        foreach ($required_keys as $required_key) {
            if (!array_key_exists($required_key, $user)) {
                throw new InvalidArgumentException("ユーザー情報に必須キー {$required_key} がありません。");
            }
        }

        $this->user = array_intersect_key($user, array_flip([
            'user_uuid',
            'name',
            'email',
            'is_dark_mode',
            'is_24_hour_format',
        ]));
    }

    /**
     * @return array<string, mixed>
     */
    public function getUser(): array
    {
        return $this->user;
    }
}
