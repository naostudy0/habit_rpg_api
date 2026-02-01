<?php

namespace App\Domain\Entities;

/**
 * ドメイン層のユーザーを表すエンティティ。
 *
 * 認証用情報と表示設定を保持する。
 */
class User
{
    private ?int $user_id;
    private string $user_uuid;
    private string $name;
    private string $email;
    private ?string $password_hash;
    private bool $is_dark_mode;
    private bool $is_24_hour_format;

    /**
     * @param string|null $password_hash ハッシュ済みパスワード（更新時は生パスワード）
     */
    public function __construct(
        ?int $user_id,
        string $user_uuid,
        string $name,
        string $email,
        ?string $password_hash,
        bool $is_dark_mode,
        bool $is_24_hour_format
    ) {
        $this->user_id = $user_id;
        $this->user_uuid = $user_uuid;
        $this->name = $name;
        $this->email = $email;
        $this->password_hash = $password_hash;
        $this->is_dark_mode = $is_dark_mode;
        $this->is_24_hour_format = $is_24_hour_format;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function getUserUuid(): string
    {
        return $this->user_uuid;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPasswordHash(): ?string
    {
        return $this->password_hash;
    }

    public function isDarkMode(): bool
    {
        return $this->is_dark_mode;
    }

    public function is24HourFormat(): bool
    {
        return $this->is_24_hour_format;
    }
}
