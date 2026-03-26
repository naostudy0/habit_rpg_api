<?php

namespace App\UseCases\Auth;

use App\UseCases\Inputs\Input;

class VerifyRegistrationOtpInput implements Input
{
    private string $email;
    private string $otp_code;

    public function __construct(string $email, string $otp_code)
    {
        $this->email = $email;
        $this->otp_code = $otp_code;
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
