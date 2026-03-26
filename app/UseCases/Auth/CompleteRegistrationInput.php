<?php

namespace App\UseCases\Auth;

use App\UseCases\Inputs\Input;

class CompleteRegistrationInput implements Input
{
    private string $registration_token;
    private string $name;
    private string $password;

    public function __construct(string $registration_token, string $name, string $password)
    {
        $this->registration_token = $registration_token;
        $this->name = $name;
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
