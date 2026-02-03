<?php

namespace Tests\Unit\UseCases\TaskSuggestions;

use App\Services\TaskSuggestionService;
use App\UseCases\Inputs\Input;
use App\UseCases\TaskSuggestions\GetTaskSuggestionsInput;
use App\UseCases\TaskSuggestions\GetTaskSuggestionsOutput;
use App\UseCases\TaskSuggestions\GetTaskSuggestionsUseCase;
use Tests\TestCase;

class GetTaskSuggestionsUseCaseTest extends TestCase
{
    /**
     * 提案一覧が取得できる場合に成功結果が返ること
     */
    public function testHandleReturnsSuccessWhenSuggestionsExist(): void
    {
        $suggestions = [
            ['uuid' => 'suggestion-1', 'title' => '提案1'],
            ['uuid' => 'suggestion-2', 'title' => '提案2'],
        ];

        $task_suggestion_service = $this->createMock(TaskSuggestionService::class);
        $task_suggestion_service->expects($this->once())
            ->method('getSuggestionsForApi')
            ->with(1)
            ->willReturn($suggestions);

        $use_case = new GetTaskSuggestionsUseCase($task_suggestion_service);
        $result = $use_case->handle(new GetTaskSuggestionsInput(1));

        $this->assertTrue($result->isSuccess());
        /** @var GetTaskSuggestionsOutput $output */
        $output = $result->getOutput();
        $this->assertInstanceOf(GetTaskSuggestionsOutput::class, $output);
        $this->assertSame($suggestions, $output->getSuggestions());
    }

    /**
     * 入力が不正な場合に失敗結果が返ること
     */
    public function testHandleReturnsFailureWhenInputIsInvalid(): void
    {
        $task_suggestion_service = $this->createMock(TaskSuggestionService::class);
        $use_case = new GetTaskSuggestionsUseCase($task_suggestion_service);
        $invalid_input = new class () implements Input {
        };

        $result = $use_case->handle($invalid_input);

        $this->assertFalse($result->isSuccess());
        $this->assertNull($result->getOutput());
    }
}
