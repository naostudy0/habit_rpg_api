<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\User;

/**
 * ドメイン層のユーザーを永続化するためのリポジトリ定義
 */
interface UserRepositoryInterface
{
    /**
     * メールアドレスでユーザーを取得する
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User;

    /**
     * ユーザーIDでユーザーを取得する
     *
     * @param int $user_id
     * @return User|null
     */
    public function findByUserId(int $user_id): ?User;

    /**
     * ユーザーを更新して永続化する
     *
     * @param User $user
     * @return User
     */
    public function update(User $user): User;
}
