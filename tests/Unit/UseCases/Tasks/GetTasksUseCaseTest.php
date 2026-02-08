<?php

namespace Tests\Unit\UseCases\Tasks;

use App\Services\TaskService;
use App\UseCases\Inputs\Input;
use App\UseCases\Tasks\GetTasksInput;
use App\UseCases\Tasks\GetTasksOutput;
use App\UseCases\Tasks\GetTasksUseCase;
use Tests\TestCase;

class GetTasksUseCaseTest extends TestCase
{
    /**
     * 予定一覧が取得できる場合に成功結果が返ること
     */
    public function testHandleReturnsSuccessWhenTasksExist(): void
    {
        $tasks = [
            ['uuid' => 'task-1', 'title' => '予定1'],
            ['uuid' => 'task-2', 'title' => '予定2'],
        ];

        $task_service = $this->createMock(TaskService::class);
        $task_service->expects($this->once())
            ->method('getTasksForApi')
            ->with(1)
            ->willReturn($tasks);

        $use_case = new GetTasksUseCase($task_service);
        $result = $use_case->handle(new GetTasksInput(1));

        $this->assertTrue($result->isSuccess());
        /** @var GetTasksOutput $output */
        $output = $result->getOutput();
        $this->assertInstanceOf(GetTasksOutput::class, $output);
        $this->assertSame($tasks, $output->getTasks());
    }

    /**
     * 入力が不正な場合に失敗結果が返ること
     */
    public function testHandleReturnsFailureWhenInputIsInvalid(): void
    {
        $task_service = $this->createMock(TaskService::class);
        $use_case = new GetTasksUseCase($task_service);
        $invalid_input = new class () implements Input {
        };

        $result = $use_case->handle($invalid_input);

        $this->assertFalse($result->isSuccess());
        $this->assertNull($result->getOutput());
    }
}
