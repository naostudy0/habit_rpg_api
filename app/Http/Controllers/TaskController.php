<?php

namespace App\Http\Controllers;

use App\Http\Requests\Task\DeleteTaskRequest;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\ToggleCompleteTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Resources\Tasks\CreateTaskResource;
use App\Http\Resources\Tasks\DeleteTaskResource;
use App\Http\Resources\Tasks\GetTasksResource;
use App\Http\Resources\Tasks\ToggleCompleteTaskResource;
use App\Http\Resources\Tasks\UpdateTaskResource;
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
    private CreateTaskUseCase $create_task_use_case;
    private UpdateTaskUseCase $update_task_use_case;
    private DeleteTaskUseCase $delete_task_use_case;
    private ToggleCompleteTaskUseCase $toggle_complete_task_use_case;

    public function __construct(
        GetTasksUseCase $get_tasks_use_case,
        CreateTaskUseCase $create_task_use_case,
        UpdateTaskUseCase $update_task_use_case,
        DeleteTaskUseCase $delete_task_use_case,
        ToggleCompleteTaskUseCase $toggle_complete_task_use_case
    ) {
        $this->get_tasks_use_case = $get_tasks_use_case;
        $this->create_task_use_case = $create_task_use_case;
        $this->update_task_use_case = $update_task_use_case;
        $this->delete_task_use_case = $delete_task_use_case;
        $this->toggle_complete_task_use_case = $toggle_complete_task_use_case;
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

        return GetTasksResource::fromResult($result);
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

        return CreateTaskResource::fromResult($result);
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

        return UpdateTaskResource::fromResult($result);
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

        return DeleteTaskResource::fromResult($result);
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

        return ToggleCompleteTaskResource::fromResult($result);
    }
}
