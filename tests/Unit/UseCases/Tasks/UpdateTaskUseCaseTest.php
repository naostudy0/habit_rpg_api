<?php

namespace Tests\Unit\UseCases\Tasks;

use App\Services\TaskService;
use App\UseCases\Inputs\Input;
use App\UseCases\Tasks\UpdateTaskInput;
use App\UseCases\Tasks\UpdateTaskOutput;
use App\UseCases\Tasks\UpdateTaskUseCase;
use Tests\TestCase;

class UpdateTaskUseCaseTest extends TestCase
{
    /**
     * 予定更新が成功する場合に成功結果が返ること
     */
    public function testHandleReturnsSuccessWhenTaskUpdated(): void
    {
        $update_data = [
            'title' => '更新後',
            'scheduled_date' => '2026-02-02',
            'scheduled_time' => '11:00:00',
        ];
        $updated_task = ['uuid' => 'task-uuid', 'title' => '更新後'];

        $task_service = $this->createMock(TaskService::class);
        $task_service->expects($this->once())
            ->method('updateTask')
            ->with('task-uuid', 1, $update_data)
            ->willReturn($updated_task);

        $use_case = new UpdateTaskUseCase($task_service);
        $result = $use_case->handle(new UpdateTaskInput('task-uuid', 1, $update_data));

        $this->assertTrue($result->isSuccess());
        /** @var UpdateTaskOutput $output */
        $output = $result->getOutput();
        $this->assertInstanceOf(UpdateTaskOutput::class, $output);
        $this->assertSame($updated_task, $output->getTask());
    }

    /**
     * 更新対象が見つからない場合でも成功結果が返ること
     */
    public function testHandleReturnsSuccessWhenTaskNotFound(): void
    {
        $update_data = ['title' => '更新後'];

        $task_service = $this->createMock(TaskService::class);
        $task_service->expects($this->once())
            ->method('updateTask')
            ->with('task-uuid', 1, $update_data)
            ->willReturn(null);

        $use_case = new UpdateTaskUseCase($task_service);
        $result = $use_case->handle(new UpdateTaskInput('task-uuid', 1, $update_data));

        $this->assertTrue($result->isSuccess());
        /** @var UpdateTaskOutput $output */
        $output = $result->getOutput();
        $this->assertInstanceOf(UpdateTaskOutput::class, $output);
        $this->assertNull($output->getTask());
    }

    /**
     * 入力が不正な場合に失敗結果が返ること
     */
    public function testHandleReturnsFailureWhenInputIsInvalid(): void
    {
        $task_service = $this->createMock(TaskService::class);
        $use_case = new UpdateTaskUseCase($task_service);
        $invalid_input = new class () implements Input {
        };

        $result = $use_case->handle($invalid_input);

        $this->assertFalse($result->isSuccess());
        $this->assertNull($result->getOutput());
    }
}
