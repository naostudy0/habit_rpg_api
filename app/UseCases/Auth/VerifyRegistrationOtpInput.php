<?php

namespace App\UseCases\Auth;

use App\UseCases\Inputs\Input;
use InvalidArgumentException;

class VerifyRegistrationOtpInput implements Input
{
    private string $email;
    private string $otp_code;

    public function __construct(string $email, string $otp_code)
    {
        $normalized_email = trim($email);
        if ($normalized_email === '' || filter_var($normalized_email, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidArgumentException('メールアドレスの形式が不正です。');
        }

        $normalized_otp_code = trim($otp_code);
        if ($normalized_otp_code === '' || preg_match('/^\d{6}$/', $normalized_otp_code) !== 1) {
            throw new InvalidArgumentException('ワンタイムパスワードの形式が不正です。');
        }

        $this->email = $normalized_email;
        $this->otp_code = $normalized_otp_code;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getOtpCode(): string
    {
        return $this->otp_code;
    }
}
