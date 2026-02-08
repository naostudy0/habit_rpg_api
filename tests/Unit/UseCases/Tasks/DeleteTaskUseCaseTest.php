<?php

namespace Tests\Unit\UseCases\Tasks;

use App\Services\TaskService;
use App\UseCases\Inputs\Input;
use App\UseCases\Tasks\DeleteTaskInput;
use App\UseCases\Tasks\DeleteTaskOutput;
use App\UseCases\Tasks\DeleteTaskUseCase;
use Tests\TestCase;

class DeleteTaskUseCaseTest extends TestCase
{
    /**
     * 予定削除が成功する場合に成功結果が返ること
     */
    public function testHandleReturnsSuccessWhenTaskDeleted(): void
    {
        $task_service = $this->createMock(TaskService::class);
        $task_service->expects($this->once())
            ->method('deleteTask')
            ->with('task-uuid', 1)
            ->willReturn(true);

        $use_case = new DeleteTaskUseCase($task_service);
        $result = $use_case->handle(new DeleteTaskInput('task-uuid', 1));

        $this->assertTrue($result->isSuccess());
        /** @var DeleteTaskOutput $output */
        $output = $result->getOutput();
        $this->assertInstanceOf(DeleteTaskOutput::class, $output);
        $this->assertSame('予定を削除しました', $output->getMessage());
    }

    /**
     * 入力が不正な場合に失敗結果が返ること
     */
    public function testHandleReturnsFailureWhenInputIsInvalid(): void
    {
        $task_service = $this->createMock(TaskService::class);
        $use_case = new DeleteTaskUseCase($task_service);
        $invalid_input = new class () implements Input {
        };

        $result = $use_case->handle($invalid_input);

        $this->assertFalse($result->isSuccess());
        $this->assertNull($result->getOutput());
    }
}
