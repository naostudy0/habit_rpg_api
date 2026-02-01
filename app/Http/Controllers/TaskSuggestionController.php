<?php

namespace App\Http\Controllers;

use App\Presenters\TaskSuggestions\DeleteTaskSuggestionPresenter;
use App\Presenters\TaskSuggestions\GetTaskSuggestionsPresenter;
use App\UseCases\TaskSuggestions\DeleteTaskSuggestionInput;
use App\UseCases\TaskSuggestions\DeleteTaskSuggestionUseCase;
use App\UseCases\TaskSuggestions\GetTaskSuggestionsInput;
use App\UseCases\TaskSuggestions\GetTaskSuggestionsUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskSuggestionController extends Controller
{
    private GetTaskSuggestionsUseCase $get_task_suggestions_use_case;
    private GetTaskSuggestionsPresenter $get_task_suggestions_presenter;
    private DeleteTaskSuggestionUseCase $delete_task_suggestion_use_case;
    private DeleteTaskSuggestionPresenter $delete_task_suggestion_presenter;

    public function __construct(
        GetTaskSuggestionsUseCase $get_task_suggestions_use_case,
        GetTaskSuggestionsPresenter $get_task_suggestions_presenter,
        DeleteTaskSuggestionUseCase $delete_task_suggestion_use_case,
        DeleteTaskSuggestionPresenter $delete_task_suggestion_presenter
    ) {
        $this->get_task_suggestions_use_case = $get_task_suggestions_use_case;
        $this->get_task_suggestions_presenter = $get_task_suggestions_presenter;
        $this->delete_task_suggestion_use_case = $delete_task_suggestion_use_case;
        $this->delete_task_suggestion_presenter = $delete_task_suggestion_presenter;
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
        $input = new GetTaskSuggestionsInput($user->user_id);
        $result = $this->get_task_suggestions_use_case->handle($input);

        return $this->get_task_suggestions_presenter->present($result);
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
        $input = new DeleteTaskSuggestionInput(
            $uuid,
            $request->user()->user_id
        );
        $result = $this->delete_task_suggestion_use_case->handle($input);

        return $this->delete_task_suggestion_presenter->present($result);
    }
}
