<?php

namespace App\UseCases\TaskSuggestions;

use App\Services\TaskSuggestionService;
use App\UseCases\Inputs\Input;
use App\UseCases\Results\Result;
use App\UseCases\UseCaseInterface;

/**
 * 提案一覧取得のUseCase
 */
class GetTaskSuggestionsUseCase implements UseCaseInterface
{
    private TaskSuggestionService $task_suggestion_service;

    public function __construct(TaskSuggestionService $task_suggestion_service)
    {
        $this->task_suggestion_service = $task_suggestion_service;
    }

    public function handle(Input $input): Result
    {
        if (!$input instanceof GetTaskSuggestionsInput) {
            return Result::failure('INVALID_INPUT', '提案の取得に失敗しました');
        }

        $suggestions = $this->task_suggestion_service->getSuggestionsForApi($input->getUserId());

        return Result::success(new GetTaskSuggestionsOutput($suggestions));
    }
}
