<?php

namespace Tests\Unit\UseCases\Users;

use App\Services\UserService;
use App\UseCases\Inputs\Input;
use App\UseCases\Users\UpdateUserInput;
use App\UseCases\Users\UpdateUserOutput;
use App\UseCases\Users\UpdateUserUseCase;
use Tests\TestCase;

class UpdateUserUseCaseTest extends TestCase
{
    /**
     * ユーザー更新が成功する場合に成功結果が返ること
     */
    public function testHandleReturnsSuccessWhenUserUpdated(): void
    {
        $update_data = ['name' => '更新後'];
        $updated_user = ['user_uuid' => 'user-uuid', 'name' => '更新後'];

        $user_service = $this->createMock(UserService::class);
        $user_service->expects($this->once())
            ->method('updateUser')
            ->with(1, $update_data)
            ->willReturn($updated_user);

        $use_case = new UpdateUserUseCase($user_service);
        $result = $use_case->handle(new UpdateUserInput(1, $update_data));

        $this->assertTrue($result->isSuccess());
        /** @var UpdateUserOutput $output */
        $output = $result->getOutput();
        $this->assertInstanceOf(UpdateUserOutput::class, $output);
        $this->assertSame($updated_user, $output->getUser());
    }

    /**
     * 更新対象が見つからない場合でも成功結果が返ること
     */
    public function testHandleReturnsSuccessWhenUserNotFound(): void
    {
        $update_data = ['name' => '更新後'];

        $user_service = $this->createMock(UserService::class);
        $user_service->expects($this->once())
            ->method('updateUser')
            ->with(1, $update_data)
            ->willReturn(null);

        $use_case = new UpdateUserUseCase($user_service);
        $result = $use_case->handle(new UpdateUserInput(1, $update_data));

        $this->assertTrue($result->isSuccess());
        /** @var UpdateUserOutput $output */
        $output = $result->getOutput();
        $this->assertInstanceOf(UpdateUserOutput::class, $output);
        $this->assertNull($output->getUser());
    }

    /**
     * 入力が不正な場合に失敗結果が返ること
     */
    public function testHandleReturnsFailureWhenInputIsInvalid(): void
    {
        $user_service = $this->createMock(UserService::class);
        $use_case = new UpdateUserUseCase($user_service);
        $invalid_input = new class () implements Input {
        };

        $result = $use_case->handle($invalid_input);

        $this->assertFalse($result->isSuccess());
        $this->assertNull($result->getOutput());
    }
}
