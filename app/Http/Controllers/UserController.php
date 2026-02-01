<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UpdateUserRequest;
use App\Presenters\Users\ShowUserPresenter;
use App\Presenters\Users\UpdateUserPresenter;
use App\UseCases\Users\ShowUserInput;
use App\UseCases\Users\ShowUserUseCase;
use App\UseCases\Users\UpdateUserInput;
use App\UseCases\Users\UpdateUserUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    private ShowUserUseCase $show_user_use_case;
    private ShowUserPresenter $show_user_presenter;
    private UpdateUserUseCase $update_user_use_case;
    private UpdateUserPresenter $update_user_presenter;

    public function __construct(
        ShowUserUseCase $show_user_use_case,
        ShowUserPresenter $show_user_presenter,
        UpdateUserUseCase $update_user_use_case,
        UpdateUserPresenter $update_user_presenter
    ) {
        $this->show_user_use_case = $show_user_use_case;
        $this->show_user_presenter = $show_user_presenter;
        $this->update_user_use_case = $update_user_use_case;
        $this->update_user_presenter = $update_user_presenter;
    }

    /**
     * 現在のユーザー情報を取得
     *
     * @return JsonResponse
     */
    public function show(): JsonResponse
    {
        $user = Auth::user();

        $input = new ShowUserInput($user->user_id);
        $result = $this->show_user_use_case->handle($input);

        return $this->show_user_presenter->present($result);
    }

    /**
     * ユーザー情報を更新
     *
     * @param UpdateUserRequest $request
     * @return JsonResponse
     */
    public function update(UpdateUserRequest $request): JsonResponse
    {
        $input = new UpdateUserInput(
            $request->user()->user_id,
            $request->validated()
        );
        $result = $this->update_user_use_case->handle($input);

        return $this->update_user_presenter->present($result);
    }
}
