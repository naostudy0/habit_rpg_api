<?php

namespace App\UseCases\Auth;

use App\UseCases\Outputs\Output;

class SendRegistrationOtpOutput implements Output
{
    private string $email;
    private string $expires_at;
    private string $resend_available_at;

    public function __construct(string $email, string $expires_at, string $resend_available_at)
    {
        $this->email = $email;
        $this->expires_at = $expires_at;
        $this->resend_available_at = $resend_available_at;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getExpiresAt(): string
    {
        return $this->expires_at;
    }

    public function getResendAvailableAt(): string
    {
        return $this->resend_available_at;
    }
}
