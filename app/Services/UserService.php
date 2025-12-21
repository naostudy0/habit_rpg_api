<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;

class UserService
{
    private UserRepository $user_repository;

    public function __construct(UserRepository $user_repository)
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

        // 更新データを準備
        $update_data = [];

        if (isset($data['name'])) {
            $update_data['name'] = $data['name'];
        }

        if (isset($data['password'])) {
            // ハッシュ化はミューテタで行われる
            $update_data['password'] = $data['password'];
        }

        if (isset($data['is_dark_mode'])) {
            $update_data['is_dark_mode'] = $data['is_dark_mode'];
        }

        if (isset($data['is_24_hour_format'])) {
            $update_data['is_24_hour_format'] = $data['is_24_hour_format'];
        }

        // ユーザーを更新
        $user = $this->user_repository->update($user, $update_data);

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
            'user_uuid' => $user->user_uuid,
            'name' => $user->name,
            'email' => $user->email,
            'is_dark_mode' => $user->is_dark_mode,
            'is_24_hour_format' => $user->is_24_hour_format,
        ];
    }
}
