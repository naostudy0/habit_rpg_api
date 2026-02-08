<?php

namespace App\Services;

use App\Domain\Entities\User;
use App\Domain\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    private UserRepositoryInterface $user_repository;

    public function __construct(UserRepositoryInterface $user_repository)
    {
        $this->user_repository = $user_repository;
    }

    /**
     * 認証処理
     *
     * @param string $email
     * @param string $password
     * @return User|null
     */
    public function authenticate(string $email, string $password): ?User
    {
        // ユーザーを取得
        $user = $this->user_repository->findByEmail($email);

        // ユーザーが存在しない、またはパスワードが一致しない場合
        if (!$user || !$user->getPasswordHash() || !Hash::check($password, $user->getPasswordHash())) {
            return null;
        }

        return $user;
    }
}
