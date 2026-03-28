<?php

namespace App\UseCases\Auth;

use App\UseCases\Inputs\Input;
use InvalidArgumentException;

class SendRegistrationOtpInput implements Input
{
    private string $email;

    public function __construct(string $email)
    {
        $normalized_email = trim($email);
        if ($normalized_email === '' || filter_var($normalized_email, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidArgumentException('メールアドレスの形式が不正です。');
        }

        $this->email = $normalized_email;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
