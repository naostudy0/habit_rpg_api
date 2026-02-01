<?php

namespace App\Http\Controllers;

use App\Http\Requests\Task\DeleteTaskRequest;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\ToggleCompleteTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Presenters\Tasks\CreateTaskPresenter;
use App\Presenters\Tasks\DeleteTaskPresenter;
use App\Presenters\Tasks\GetTasksPresenter;
use App\Presenters\Tasks\ToggleCompleteTaskPresenter;
use App\Presenters\Tasks\UpdateTaskPresenter;
use App\UseCases\Tasks\CreateTaskInput;
use App\UseCases\Tasks\CreateTaskUseCase;
use App\UseCases\Tasks\DeleteTaskInput;
use App\UseCases\Tasks\DeleteTaskUseCase;
use App\UseCases\Tasks\GetTasksInput;
use App\UseCases\Tasks\GetTasksUseCase;
use App\UseCases\Tasks\ToggleCompleteTaskInput;
use App\UseCases\Tasks\ToggleCompleteTaskUseCase;
use App\UseCases\Tasks\UpdateTaskInput;
use App\UseCases\Tasks\UpdateTaskUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    private GetTasksUseCase $get_tasks_use_case;
    private GetTasksPresenter $get_tasks_presenter;
    private CreateTaskUseCase $create_task_use_case;
    private CreateTaskPresenter $create_task_presenter;
    private UpdateTaskUseCase $update_task_use_case;
    private UpdateTaskPresenter $update_task_presenter;
    private DeleteTaskUseCase $delete_task_use_case;
    private DeleteTaskPresenter $delete_task_presenter;
    private ToggleCompleteTaskUseCase $toggle_complete_task_use_case;
    private ToggleCompleteTaskPresenter $toggle_complete_task_presenter;

    public function __construct(
        GetTasksUseCase $get_tasks_use_case,
        GetTasksPresenter $get_tasks_presenter,
        CreateTaskUseCase $create_task_use_case,
        CreateTaskPresenter $create_task_presenter,
        UpdateTaskUseCase $update_task_use_case,
        UpdateTaskPresenter $update_task_presenter,
        DeleteTaskUseCase $delete_task_use_case,
        DeleteTaskPresenter $delete_task_presenter,
        ToggleCompleteTaskUseCase $toggle_complete_task_use_case,
        ToggleCompleteTaskPresenter $toggle_complete_task_presenter
    ) {
        $this->get_tasks_use_case = $get_tasks_use_case;
        $this->get_tasks_presenter = $get_tasks_presenter;
        $this->create_task_use_case = $create_task_use_case;
        $this->create_task_presenter = $create_task_presenter;
        $this->update_task_use_case = $update_task_use_case;
        $this->update_task_presenter = $update_task_presenter;
        $this->delete_task_use_case = $delete_task_use_case;
        $this->delete_task_presenter = $delete_task_presenter;
        $this->toggle_complete_task_use_case = $toggle_complete_task_use_case;
        $this->toggle_complete_task_presenter = $toggle_complete_task_presenter;
    }

    /**
     * 予定一覧取得
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // 認証済みユーザーの予定を取得
        $user = $request->user();
        $input = new GetTasksInput($user->user_id);
        $result = $this->get_tasks_use_case->handle($input);

        return $this->get_tasks_presenter->present($result);
    }

    /**
     * 予定作成
     *
     * @param StoreTaskRequest $request
     * @return JsonResponse
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        $input = new CreateTaskInput(
            $request->user()->user_id,
            $request->validated()
        );
        $result = $this->create_task_use_case->handle($input);

        return $this->create_task_presenter->present($result);
    }

    /**
     * 予定更新
     *
     * @param UpdateTaskRequest $request
     * @param string $uuid
     * @return JsonResponse
     */
    public function update(UpdateTaskRequest $request, string $uuid): JsonResponse
    {
        $input = new UpdateTaskInput(
            $uuid,
            $request->user()->user_id,
            $request->validated()
        );
        $result = $this->update_task_use_case->handle($input);

        return $this->update_task_presenter->present($result);
    }

    /**
     * 予定削除
     *
     * @param DeleteTaskRequest $request
     * @param string $uuid
     * @return JsonResponse
     */
    public function destroy(DeleteTaskRequest $request, string $uuid): JsonResponse
    {
        $input = new DeleteTaskInput(
            $uuid,
            $request->user()->user_id
        );
        $result = $this->delete_task_use_case->handle($input);

        return $this->delete_task_presenter->present($result);
    }

    /**
     * 予定の完了状態を切り替え
     *
     * @param ToggleCompleteTaskRequest $request
     * @param string $uuid
     * @return JsonResponse
     */
    public function toggleComplete(ToggleCompleteTaskRequest $request, string $uuid): JsonResponse
    {
        $is_completed = $request->validated()['is_completed'];

        $input = new ToggleCompleteTaskInput(
            $uuid,
            $request->user()->user_id,
            $is_completed
        );
        $result = $this->toggle_complete_task_use_case->handle($input);

        return $this->toggle_complete_task_presenter->present($result);
    }
}
