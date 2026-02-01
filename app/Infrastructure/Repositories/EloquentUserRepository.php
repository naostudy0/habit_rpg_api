<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\User as DomainUser;
use App\Domain\Repositories\UserRepositoryInterface;
use App\Models\User;

class EloquentUserRepository implements UserRepositoryInterface
{
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function findByEmail(string $email): ?DomainUser
    {
        $user = $this->user
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

        if (!$user) {
            return null;
        }

        return $this->toDomain($user);
    }

    public function findByUserId(int $user_id): ?DomainUser
    {
        $user = $this->user
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

        if (!$user) {
            return null;
        }

        return $this->toDomain($user);
    }

    public function update(DomainUser $user): DomainUser
    {
        $model = $this->user->newQuery()
            ->where('user_id', $user->getUserId())
            ->first();

        if (!$model) {
            throw new \RuntimeException('User not found.');
        }

        $update_data = [
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'is_dark_mode' => $user->isDarkMode(),
            'is_24_hour_format' => $user->is24HourFormat(),
        ];

        if ($user->getPasswordHash() !== null) {
            $update_data['password'] = $user->getPasswordHash();
        }

        $model->update($update_data);

        return $this->toDomain($model);
    }

    private function toDomain(User $user): DomainUser
    {
        return new DomainUser(
            $user->user_id,
            $user->user_uuid,
            $user->name,
            $user->email,
            $user->password,
            (bool) $user->is_dark_mode,
            (bool) $user->is_24_hour_format
        );
    }
}
