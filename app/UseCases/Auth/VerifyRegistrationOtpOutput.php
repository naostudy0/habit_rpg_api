<?php

namespace App\UseCases\Auth;

use App\UseCases\Outputs\Output;
use DateTimeInterface;

class VerifyRegistrationOtpOutput implements Output
{
    private string $registration_token;
    private DateTimeInterface $registration_token_expires_at;

    public function __construct(string $registration_token, DateTimeInterface $registration_token_expires_at)
    {
        $this->registration_token = $registration_token;
        $this->registration_token_expires_at = $registration_token_expires_at;
    }

    public function getRegistrationToken(): string
    {
        return $this->registration_token;
    }

    public function getRegistrationTokenExpiresAt(): DateTimeInterface
    {
        return $this->registration_token_expires_at;
    }
}
