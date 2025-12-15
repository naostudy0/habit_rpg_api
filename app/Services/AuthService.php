<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    private UserRepository $user_repository;

    public function __construct(UserRepository $user_repository)
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
        if (!$user || !Hash::check($password, $user->password)) {
            return null;
        }

        // パスワードを削除して返す
        unset($user->password);
        return $user;
    }
}
