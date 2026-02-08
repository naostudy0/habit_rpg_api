<?php

namespace Tests\Unit\UseCases\TaskSuggestions;

use App\Services\TaskSuggestionService;
use App\UseCases\Inputs\Input;
use App\UseCases\TaskSuggestions\DeleteTaskSuggestionInput;
use App\UseCases\TaskSuggestions\DeleteTaskSuggestionOutput;
use App\UseCases\TaskSuggestions\DeleteTaskSuggestionUseCase;
use Tests\TestCase;

class DeleteTaskSuggestionUseCaseTest extends TestCase
{
    /**
     * 提案削除が成功する場合に成功結果が返ること
     */
    public function testHandleReturnsSuccessWhenSuggestionDeleted(): void
    {
        $task_suggestion_service = $this->createMock(TaskSuggestionService::class);
        $task_suggestion_service->expects($this->once())
            ->method('deleteSuggestion')
            ->with('suggestion-uuid', 1)
            ->willReturn(true);

        $use_case = new DeleteTaskSuggestionUseCase($task_suggestion_service);
        $result = $use_case->handle(new DeleteTaskSuggestionInput('suggestion-uuid', 1));

        $this->assertTrue($result->isSuccess());
        /** @var DeleteTaskSuggestionOutput $output */
        $output = $result->getOutput();
        $this->assertInstanceOf(DeleteTaskSuggestionOutput::class, $output);
        $this->assertSame('提案を削除しました', $output->getMessage());
    }

    /**
     * 提案が見つからない場合に失敗結果が返ること
     */
    public function testHandleReturnsFailureWhenSuggestionNotFound(): void
    {
        $task_suggestion_service = $this->createMock(TaskSuggestionService::class);
        $task_suggestion_service->expects($this->once())
            ->method('deleteSuggestion')
            ->with('suggestion-uuid', 1)
            ->willReturn(false);

        $use_case = new DeleteTaskSuggestionUseCase($task_suggestion_service);
        $result = $use_case->handle(new DeleteTaskSuggestionInput('suggestion-uuid', 1));

        $this->assertFalse($result->isSuccess());
        $this->assertNull($result->getOutput());
    }

    /**
     * 入力が不正な場合に失敗結果が返ること
     */
    public function testHandleReturnsFailureWhenInputIsInvalid(): void
    {
        $task_suggestion_service = $this->createMock(TaskSuggestionService::class);
        $use_case = new DeleteTaskSuggestionUseCase($task_suggestion_service);
        $invalid_input = new class () implements Input {
        };

        $result = $use_case->handle($invalid_input);

        $this->assertFalse($result->isSuccess());
        $this->assertNull($result->getOutput());
    }
}
