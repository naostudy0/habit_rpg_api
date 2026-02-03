<?php

namespace Tests\Unit\UseCases\Tasks;

use App\Services\TaskService;
use App\UseCases\Inputs\Input;
use App\UseCases\Tasks\CreateTaskInput;
use App\UseCases\Tasks\CreateTaskOutput;
use App\UseCases\Tasks\CreateTaskUseCase;
use Tests\TestCase;

class CreateTaskUseCaseTest extends TestCase
{
    /**
     * 予定作成が成功する場合に成功結果が返ること
     */
    public function testHandleReturnsSuccessWhenTaskCreated(): void
    {
        $task_data = [
            'title' => '予定1',
            'scheduled_date' => '2026-02-01',
            'scheduled_time' => '10:00:00',
        ];
        $created_task = ['uuid' => 'task-uuid', 'title' => '予定1'];

        $task_service = $this->createMock(TaskService::class);
        $task_service->expects($this->once())
            ->method('createTask')
            ->with(1, $task_data)
            ->willReturn($created_task);

        $use_case = new CreateTaskUseCase($task_service);
        $result = $use_case->handle(new CreateTaskInput(1, $task_data));

        $this->assertTrue($result->isSuccess());
        /** @var CreateTaskOutput $output */
        $output = $result->getOutput();
        $this->assertInstanceOf(CreateTaskOutput::class, $output);
        $this->assertSame($created_task, $output->getTask());
    }

    /**
     * 入力が不正な場合に失敗結果が返ること
     */
    public function testHandleReturnsFailureWhenInputIsInvalid(): void
    {
        $task_service = $this->createMock(TaskService::class);
        $use_case = new CreateTaskUseCase($task_service);
        $invalid_input = new class () implements Input {
        };

        $result = $use_case->handle($invalid_input);

        $this->assertFalse($result->isSuccess());
        $this->assertNull($result->getOutput());
    }
}
