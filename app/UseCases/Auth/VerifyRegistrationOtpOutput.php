<?php

namespace App\UseCases\Auth;

use App\UseCases\Outputs\Output;

class VerifyRegistrationOtpOutput implements Output
{
    private string $registration_token;
    private string $registration_token_expires_at;

    public function __construct(string $registration_token, string $registration_token_expires_at)
    {
        $this->registration_token = $registration_token;
        $this->registration_token_expires_at = $registration_token_expires_at;
    }

    public function getRegistrationToken(): string
    {
        return $this->registration_token;
    }

    public function getRegistrationTokenExpiresAt(): string
    {
        return $this->registration_token_expires_at;
    }
}
