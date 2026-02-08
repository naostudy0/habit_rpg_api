<?php

namespace App\Services;

use App\Domain\Entities\User;
use App\Domain\Repositories\UserRepositoryInterface;

class UserService
{
    private UserRepositoryInterface $user_repository;

    public function __construct(UserRepositoryInterface $user_repository)
    {
        $this->user_repository = $user_repository;
    }

    /**
     * ユーザー情報を更新
     *
     * @param int $user_id
     * @param array $data
     * @return array|null
     */
    public function updateUser(int $user_id, array $data): ?array
    {
        // ユーザーを取得
        $user = $this->user_repository->findByUserId($user_id);
        if (!$user) {
            return null;
        }

        $updated_user = new User(
            $user->getUserId(),
            $user->getUserUuid(),
            $data['name'] ?? $user->getName(),
            $user->getEmail(),
            $data['password'] ?? null,
            $data['is_dark_mode'] ?? $user->isDarkMode(),
            $data['is_24_hour_format'] ?? $user->is24HourFormat()
        );

        // ユーザーを更新
        $user = $this->user_repository->update($updated_user);

        // ユーザーをAPIレスポンス形式に変換して返す
        return $this->formatUserForApi($user);
    }

    /**
     * UserオブジェクトをAPIレスポンス形式の配列に変換
     *
     * @param User $user
     * @return array
     */
    private function formatUserForApi(User $user): array
    {
        return [
            'user_uuid' => $user->getUserUuid(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'is_dark_mode' => $user->isDarkMode(),
            'is_24_hour_format' => $user->is24HourFormat(),
        ];
    }
}
