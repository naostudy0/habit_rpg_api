<?php

namespace Tests\Unit\UseCases\Auth;

use App\Domain\Entities\User;
use App\Domain\Repositories\UserRepositoryInterface;
use App\Services\AuthService;
use App\UseCases\Auth\LoginInput;
use App\UseCases\Auth\LoginOutput;
use App\UseCases\Auth\LoginUseCase;
use App\UseCases\Auth\TokenIssuerInterface;
use App\UseCases\Inputs\Input;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginUseCaseTest extends TestCase
{
    /**
     * 認証が成功した場合に成功結果が返ること
     */
    public function testHandleReturnsSuccessWhenCredentialsAreValid(): void
    {
        $password = 'password123';
        $user = new User(
            1,
            'user-uuid',
            'テストユーザー',
            'test@example.com',
            Hash::make($password),
            false,
            false
        );

        $user_repository = $this->createMock(UserRepositoryInterface::class);
        $user_repository->expects($this->once())
            ->method('findByEmail')
            ->with('test@example.com')
            ->willReturn($user);

        $auth_service = new AuthService($user_repository);

        $token_issuer = $this->createMock(TokenIssuerInterface::class);
        $token_issuer->expects($this->once())
            ->method('issueToken')
            ->with(1)
            ->willReturn('token123');

        $use_case = new LoginUseCase($auth_service, $token_issuer);
        $result = $use_case->handle(new LoginInput('test@example.com', $password));

        $this->assertTrue($result->isSuccess());
        /** @var LoginOutput $output */
        $output = $result->getOutput();
        $this->assertInstanceOf(LoginOutput::class, $output);
        $this->assertSame('token123', $output->getToken());
        $this->assertSame($user, $output->getUser());
    }

    /**
     * ユーザーが存在しない場合に失敗結果が返ること
     */
    public function testHandleReturnsFailureWhenUserNotFound(): void
    {
        $user_repository = $this->createMock(UserRepositoryInterface::class);
        $user_repository->expects($this->once())
            ->method('findByEmail')
            ->with('test@example.com')
            ->willReturn(null);

        $auth_service = new AuthService($user_repository);

        $token_issuer = $this->createMock(TokenIssuerInterface::class);
        $token_issuer->expects($this->never())->method('issueToken');

        $use_case = new LoginUseCase($auth_service, $token_issuer);
        $result = $use_case->handle(new LoginInput('test@example.com', 'password123'));

        $this->assertFalse($result->isSuccess());
        $this->assertNull($result->getOutput());
    }

    /**
     * トークン発行に失敗した場合に失敗結果が返ること
     */
    public function testHandleReturnsFailureWhenTokenIssuanceFails(): void
    {
        $password = 'password123';
        $user = new User(
            1,
            'user-uuid',
            'テストユーザー',
            'test@example.com',
            Hash::make($password),
            false,
            false
        );

        $user_repository = $this->createMock(UserRepositoryInterface::class);
        $user_repository->expects($this->once())
            ->method('findByEmail')
            ->with('test@example.com')
            ->willReturn($user);

        $auth_service = new AuthService($user_repository);

        $token_issuer = $this->createMock(TokenIssuerInterface::class);
        $token_issuer->expects($this->once())
            ->method('issueToken')
            ->with(1)
            ->willThrowException(new \RuntimeException('token error'));

        $use_case = new LoginUseCase($auth_service, $token_issuer);
        $result = $use_case->handle(new LoginInput('test@example.com', $password));

        $this->assertFalse($result->isSuccess());
        $this->assertNull($result->getOutput());
    }

    /**
     * 入力が不正な場合に失敗結果が返ること
     */
    public function testHandleReturnsFailureWhenInputIsInvalid(): void
    {
        $user_repository = $this->createMock(UserRepositoryInterface::class);
        $auth_service = new AuthService($user_repository);
        $token_issuer = $this->createMock(TokenIssuerInterface::class);

        $use_case = new LoginUseCase($auth_service, $token_issuer);
        $invalid_input = new class () implements Input {
        };

        $result = $use_case->handle($invalid_input);

        $this->assertFalse($result->isSuccess());
        $this->assertNull($result->getOutput());
    }
}
