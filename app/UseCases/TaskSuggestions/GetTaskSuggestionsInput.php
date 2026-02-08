<?php

namespace App\UseCases\TaskSuggestions;

use App\UseCases\Inputs\Input;

/**
 * 提案一覧取得UseCaseの入力DTO
 */
class GetTaskSuggestionsInput implements Input
{
    private int $user_id;

    public function __construct(int $user_id)
    {
        $this->user_id = $user_id;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }
}
