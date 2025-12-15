<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * メールアドレスでユーザーを取得
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User
    {
        return $this->user
            ->select([
                'user_id',
                'user_uuid',
                'name',
                'email',
                'password',
                'is_dark_mode',
                'is_24_hour_format',
            ])
            ->where('email', $email)
            ->first();
    }
}
