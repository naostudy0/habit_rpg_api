<?php

namespace App\UseCases\Users;

use App\Services\UserService;
use App\UseCases\Inputs\Input;
use App\UseCases\Results\Result;
use App\UseCases\UseCaseInterface;

/**
 * ユーザー更新のUseCase
 */
class UpdateUserUseCase implements UseCaseInterface
{
    private UserService $user_service;

    public function __construct(UserService $user_service)
    {
        $this->user_service = $user_service;
    }

    public function handle(Input $input): Result
    {
        if (!$input instanceof UpdateUserInput) {
            return Result::failure('INVALID_INPUT', 'ユーザー情報の更新に失敗しました');
        }

        $user = $this->user_service->updateUser($input->getUserId(), $input->getData());

        return Result::success(new UpdateUserOutput($user));
    }
}
