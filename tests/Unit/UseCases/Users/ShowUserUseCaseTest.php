<?php

namespace Tests\Unit\UseCases\Users;

use App\Domain\Entities\User;
use App\Domain\Repositories\UserRepositoryInterface;
use App\UseCases\Inputs\Input;
use App\UseCases\Users\ShowUserInput;
use App\UseCases\Users\ShowUserOutput;
use App\UseCases\Users\ShowUserUseCase;
use Tests\TestCase;

class ShowUserUseCaseTest extends TestCase
{
    /**
     * ユーザーが取得できる場合に成功結果が返ること
     */
    public function testHandleReturnsSuccessWhenUserExists(): void
    {
        $user = new User(
            1,
            'user-uuid',
            'テストユーザー',
            'test@example.com',
            null,
            false,
            false
        );

        $user_repository = $this->createMock(UserRepositoryInterface::class);
        $user_repository->expects($this->once())
            ->method('findByUserId')
            ->with(1)
            ->willReturn($user);

        $use_case = new ShowUserUseCase($user_repository);
        $result = $use_case->handle(new ShowUserInput(1));

        $this->assertTrue($result->isSuccess());
        /** @var ShowUserOutput $output */
        $output = $result->getOutput();
        $this->assertInstanceOf(ShowUserOutput::class, $output);
        $this->assertSame($user, $output->getUser());
    }

    /**
     * ユーザーが存在しない場合に失敗結果が返ること
     */
    public function testHandleReturnsFailureWhenUserNotFound(): void
    {
        $user_repository = $this->createMock(UserRepositoryInterface::class);
        $user_repository->expects($this->once())
            ->method('findByUserId')
            ->with(1)
            ->willReturn(null);

        $use_case = new ShowUserUseCase($user_repository);
        $result = $use_case->handle(new ShowUserInput(1));

        $this->assertFalse($result->isSuccess());
        $this->assertNull($result->getOutput());
    }

    /**
     * 入力が不正な場合に失敗結果が返ること
     */
    public function testHandleReturnsFailureWhenInputIsInvalid(): void
    {
        $user_repository = $this->createMock(UserRepositoryInterface::class);
        $use_case = new ShowUserUseCase($user_repository);
        $invalid_input = new class () implements Input {
        };

        $result = $use_case->handle($invalid_input);

        $this->assertFalse($result->isSuccess());
        $this->assertNull($result->getOutput());
    }
}
