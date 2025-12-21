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

    /**
     * ユーザーIDでユーザーを取得
     *
     * @param int $user_id
     * @return User|null
     */
    public function findByUserId(int $user_id): ?User
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
            ->where('user_id', $user_id)
            ->first();
    }

    /**
     * ユーザーを更新
     *
     * @param User $user
     * @param array $data
     * @return User
     */
    public function update(User $user, array $data): User
    {
        $user->update($data);

        return $user;
    }
}
