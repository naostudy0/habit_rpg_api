<?php

namespace App\UseCases\Users;

use App\Domain\Repositories\UserRepositoryInterface;
use App\UseCases\Inputs\Input;
use App\UseCases\Results\Result;
use App\UseCases\UseCaseInterface;

/**
 * ユーザー取得のUseCase
 */
class ShowUserUseCase implements UseCaseInterface
{
    private UserRepositoryInterface $user_repository;

    public function __construct(UserRepositoryInterface $user_repository)
    {
        $this->user_repository = $user_repository;
    }

    public function handle(Input $input): Result
    {
        if (!$input instanceof ShowUserInput) {
            return Result::failure('INVALID_INPUT', 'ユーザーが見つかりません');
        }

        $user = $this->user_repository->findByUserId($input->getUserId());
        if (!$user) {
            return Result::failure('NOT_FOUND', 'ユーザーが見つかりません');
        }

        return Result::success(new ShowUserOutput($user));
    }
}
