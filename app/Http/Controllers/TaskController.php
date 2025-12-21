<?php

namespace App\Http\Controllers;

use App\Http\Requests\Task\DeleteTaskRequest;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\ToggleCompleteTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    private TaskService $task_service;

    public function __construct(TaskService $task_service)
    {
        $this->task_service = $task_service;
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
        $tasks = $this->task_service->getTasksForApi($user->user_id);

        return response()->json([
            'result' => true,
            'data' => $tasks,
        ], 200);
    }

    /**
     * 予定作成
     *
     * @param StoreTaskRequest $request
     * @return JsonResponse
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = $this->task_service->createTask(
            $request->user()->user_id,
            $request->validated()
        );

        return response()->json([
            'result' => true,
            'message' => '予定を作成しました',
            'data' => $task,
        ], 201);
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
        $task = $this->task_service->updateTask(
            $uuid,
            $request->user()->user_id,
            $request->validated()
        );

        return response()->json([
            'result' => true,
            'message' => '予定を更新しました',
            'data' => $task,
        ], 200);
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
        $this->task_service->deleteTask(
            $uuid,
            $request->user()->user_id
        );

        return response()->json([
            'result' => true,
            'message' => '予定を削除しました',
        ], 200);
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

        // 予定の完了状態を切り替え
        $task = $this->task_service->toggleCompletion(
            $uuid,
            $request->user()->user_id,
            $is_completed
        );

        return response()->json([
            'result' => true,
            'message' => $is_completed ? '予定を完了にしました' : '予定を未完了にしました',
            'data' => $task,
        ], 200);
    }
}
