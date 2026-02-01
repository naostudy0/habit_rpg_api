<?php

namespace App\UseCases\Tasks;

use App\UseCases\Outputs\Output;

/**
 * 予定削除UseCaseの出力DTO
 */
class DeleteTaskOutput implements Output
{
    private string $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
