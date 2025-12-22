<?php

namespace App\Http\Controllers;

use App\Services\TaskSuggestionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskSuggestionController extends Controller
{
    private TaskSuggestionService $task_suggestion_service;

    public function __construct(TaskSuggestionService $task_suggestion_service)
    {
        $this->task_suggestion_service = $task_suggestion_service;
    }

    /**
     * 提案一覧取得
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // 認証済みユーザーの提案を取得
        $user = $request->user();
        $suggestions = $this->task_suggestion_service->getSuggestionsForApi($user->user_id);

        return response()->json([
            'result' => true,
            'data' => $suggestions,
        ], 200);
    }

    /**
     * 提案削除
     *
     * @param Request $request
     * @param string $uuid
     * @return JsonResponse
     */
    public function destroy(Request $request, string $uuid): JsonResponse
    {
        $deleted = $this->task_suggestion_service->deleteSuggestion(
            $uuid,
            $request->user()->user_id
        );

        if (!$deleted) {
            return response()->json([
                'result' => false,
                'message' => '提案が見つかりません',
            ], 404);
        }

        return response()->json([
            'result' => true,
            'message' => '提案を削除しました',
        ], 200);
    }
}
