<?php

namespace App\UseCases\Auth;

/**
 * ログイントークンを発行するためのインターフェース
 */
interface TokenIssuerInterface
{
    public function issueToken(int $user_id): string;
}
