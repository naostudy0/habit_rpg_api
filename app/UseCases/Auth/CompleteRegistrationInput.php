<?php

namespace App\UseCases\Auth;

use App\UseCases\Inputs\Input;
use InvalidArgumentException;

class CompleteRegistrationInput implements Input
{
    private string $registration_token;
    private string $name;
    private string $password;

    public function __construct(string $registration_token, string $name, string $password)
    {
        $normalized_registration_token = trim($registration_token);
        if ($normalized_registration_token === '') {
            throw new InvalidArgumentException('本登録トークンは必須です。');
        }

        $normalized_name = trim($name);
        if ($normalized_name === '') {
            throw new InvalidArgumentException('名前は必須です。');
        }

        if ($password === '' || mb_strlen($password) < 8) {
            throw new InvalidArgumentException('パスワードは8文字以上で入力してください。');
        }

        $this->registration_token = $normalized_registration_token;
        $this->name = $normalized_name;
        $this->password = $password;
    }

    public function getRegistrationToken(): string
    {
        return $this->registration_token;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
