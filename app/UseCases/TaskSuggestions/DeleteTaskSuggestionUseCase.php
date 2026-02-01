<?php

namespace App\UseCases\TaskSuggestions;

use App\Services\TaskSuggestionService;
use App\UseCases\Inputs\Input;
use App\UseCases\Results\Result;
use App\UseCases\UseCaseInterface;

/**
 * 提案削除のUseCase
 */
class DeleteTaskSuggestionUseCase implements UseCaseInterface
{
    private TaskSuggestionService $task_suggestion_service;

    public function __construct(TaskSuggestionService $task_suggestion_service)
    {
        $this->task_suggestion_service = $task_suggestion_service;
    }

    public function handle(Input $input): Result
    {
        if (!$input instanceof DeleteTaskSuggestionInput) {
            return Result::failure('INVALID_INPUT', '提案が見つかりません');
        }

        $deleted = $this->task_suggestion_service->deleteSuggestion(
            $input->getUuid(),
            $input->getUserId()
        );

        if (!$deleted) {
            return Result::failure('NOT_FOUND', '提案が見つかりません');
        }

        return Result::success(new DeleteTaskSuggestionOutput('提案を削除しました'));
    }
}
