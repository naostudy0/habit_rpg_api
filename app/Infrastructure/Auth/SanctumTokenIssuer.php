<?php

namespace App\Infrastructure\Auth;

use App\Models\User;
use App\UseCases\Auth\TokenIssuerInterface;

/**
 * Sanctumによるトークン発行実装
 */
class SanctumTokenIssuer implements TokenIssuerInterface
{
    public function issueToken(int $user_id): string
    {
        $user = User::where('user_id', $user_id)->first();
        if (!$user) {
            throw new \RuntimeException('User not found.');
        }

        return $user->createToken('auth-token')->plainTextToken;
    }
}
