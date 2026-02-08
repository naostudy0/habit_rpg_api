<?php

namespace Tests\Unit\UseCases\Tasks;

use App\Services\TaskService;
use App\UseCases\Inputs\Input;
use App\UseCases\Tasks\ToggleCompleteTaskInput;
use App\UseCases\Tasks\ToggleCompleteTaskOutput;
use App\UseCases\Tasks\ToggleCompleteTaskUseCase;
use Tests\TestCase;

class ToggleCompleteTaskUseCaseTest extends TestCase
{
    /**
     * 予定の完了状態切り替えが成功する場合に成功結果が返ること
     */
    public function testHandleReturnsSuccessWhenTaskUpdated(): void
    {
        $task = ['uuid' => 'task-uuid', 'is_completed' => true];

        $task_service = $this->createMock(TaskService::class);
        $task_service->expects($this->once())
            ->method('updateCompletion')
            ->with('task-uuid', 1, true)
            ->willReturn($task);

        $use_case = new ToggleCompleteTaskUseCase($task_service);
        $result = $use_case->handle(new ToggleCompleteTaskInput('task-uuid', 1, true));

        $this->assertTrue($result->isSuccess());
        /** @var ToggleCompleteTaskOutput $output */
        $output = $result->getOutput();
        $this->assertInstanceOf(ToggleCompleteTaskOutput::class, $output);
        $this->assertSame($task, $output->getTask());
        $this->assertTrue($output->isCompleted());
    }

    /**
     * 予定が見つからない場合に失敗結果が返ること
     */
    public function testHandleReturnsFailureWhenTaskNotFound(): void
    {
        $task_service = $this->createMock(TaskService::class);
        $task_service->expects($this->once())
            ->method('updateCompletion')
            ->with('task-uuid', 1, false)
            ->willReturn([]);

        $use_case = new ToggleCompleteTaskUseCase($task_service);
        $result = $use_case->handle(new ToggleCompleteTaskInput('task-uuid', 1, false));

        $this->assertFalse($result->isSuccess());
        $this->assertNull($result->getOutput());
    }

    /**
     * 入力が不正な場合に失敗結果が返ること
     */
    public function testHandleReturnsFailureWhenInputIsInvalid(): void
    {
        $task_service = $this->createMock(TaskService::class);
        $use_case = new ToggleCompleteTaskUseCase($task_service);
        $invalid_input = new class () implements Input {
        };

        $result = $use_case->handle($invalid_input);

        $this->assertFalse($result->isSuccess());
        $this->assertNull($result->getOutput());
    }
}
