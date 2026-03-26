<?php

namespace App\UseCases\Auth;

use App\UseCases\Inputs\Input;

class SendRegistrationOtpInput implements Input
{
    private string $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
