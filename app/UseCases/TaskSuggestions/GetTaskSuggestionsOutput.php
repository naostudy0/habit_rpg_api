<?php

namespace App\UseCases\TaskSuggestions;

use App\UseCases\Outputs\Output;

/**
 * 提案一覧取得UseCaseの出力DTO
 */
class GetTaskSuggestionsOutput implements Output
{
    /**
     * @var array<int, array<string, mixed>>
     */
    private array $suggestions;

    /**
     * @param array<int, array<string, mixed>> $suggestions
     */
    public function __construct(array $suggestions)
    {
        $this->suggestions = $suggestions;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getSuggestions(): array
    {
        return $this->suggestions;
    }
}
