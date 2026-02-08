<?php

namespace App\UseCases\TaskSuggestions;

use App\UseCases\Outputs\Output;

/**
 * 提案削除UseCaseの出力DTO
 */
class DeleteTaskSuggestionOutput implements Output
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
