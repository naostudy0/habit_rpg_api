<?php

namespace App\Http\Controllers;

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
     * 予定作成（仮の実装）
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        // バリデーション
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'scheduled_date' => ['required', 'date'],
            'scheduled_time' => ['required', 'string', 'regex:/^([0-1][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/'],
            'memo' => ['nullable', 'string', 'max:1000'],
        ], [
            'title.required' => 'タイトルは必須です。',
            'title.string' => 'タイトルは文字列で入力してください。',
            'title.max' => 'タイトルは255文字以内で入力してください。',
            'scheduled_date.required' => '予定日は必須です。',
            'scheduled_date.date' => '有効な日付を入力してください。',
            'scheduled_time.required' => '予定時刻は必須です。',
            'scheduled_time.regex' => '有効な時刻形式（HH:MM:SS）で入力してください。',
            'memo.string' => 'メモは文字列で入力してください。',
            'memo.max' => 'メモは1000文字以内で入力してください。',
        ]);

        // 仮の予定データを作成（実際の実装ではデータベースに保存）
        $task = [
            'uuid' => Str::uuid()->toString(),
            'title' => $validated['title'],
            'scheduled_date' => $validated['scheduled_date'],
            'scheduled_time' => $validated['scheduled_time'],
            'memo' => $validated['memo'] ?? null,
            'is_completed' => false,
            'created_at' => now()->toIso8601String(),
            'updated_at' => now()->toIso8601String(),
        ];

        return response()->json([
            'result' => true,
            'message' => '予定を作成しました',
            'data' => $task,
        ], 201);
    }

    /**
     * 予定更新（仮の実装）
     *
     * @param Request $request
     * @param string $uuid
     * @return JsonResponse
     */
    public function update(Request $request, string $uuid): JsonResponse
    {
        // バリデーション
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'scheduled_date' => ['required', 'date'],
            'scheduled_time' => ['required', 'string', 'regex:/^([0-1][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/'],
            'memo' => ['nullable', 'string', 'max:1000'],
        ], [
            'title.required' => 'タイトルは必須です。',
            'title.string' => 'タイトルは文字列で入力してください。',
            'title.max' => 'タイトルは255文字以内で入力してください。',
            'scheduled_date.required' => '予定日は必須です。',
            'scheduled_date.date' => '有効な日付を入力してください。',
            'scheduled_time.required' => '予定時刻は必須です。',
            'scheduled_time.regex' => '有効な時刻形式（HH:MM:SS）で入力してください。',
            'memo.string' => 'メモは文字列で入力してください。',
            'memo.max' => 'メモは1000文字以内で入力してください。',
        ]);

        // 仮の予定データを更新（実際の実装ではデータベースを更新）
        $task = [
            'uuid' => $uuid,
            'title' => $validated['title'],
            'scheduled_date' => $validated['scheduled_date'],
            'scheduled_time' => $validated['scheduled_time'],
            'memo' => $validated['memo'] ?? null,
            'is_completed' => false, // 既存の状態を保持する場合は、リクエストから取得
            'created_at' => now()->subDays(1)->toIso8601String(), // 既存の作成日時を保持
            'updated_at' => now()->toIso8601String(),
        ];

        return response()->json([
            'result' => true,
            'message' => '予定を更新しました',
            'data' => $task,
        ], 200);
    }

    /**
     * 予定削除（仮の実装）
     *
     * @param Request $request
     * @param string $uuid
     * @return JsonResponse
     */
    public function destroy(Request $request, string $uuid): JsonResponse
    {
        // 仮の予定削除処理（実際の実装ではデータベースから削除）
        // ここでは削除成功を返す

        return response()->json([
            'result' => true,
            'message' => '予定を削除しました',
        ], 200);
    }
}
