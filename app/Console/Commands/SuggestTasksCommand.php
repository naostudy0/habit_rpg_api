<?php

namespace App\Console\Commands;

use App\Services\AISuggestionService;
use App\Services\TaskService;
use App\Services\TaskSuggestionService;
use Illuminate\Console\Command;

class SuggestTasksCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:suggest
                            {--user-id= : 特定のユーザーIDを指定（指定しない場合は予定がある全ユーザー）}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'AIがユーザーの過去の予定を元に、新たな予定を提案する';

    private AISuggestionService $ai_suggestion_service;
    private TaskService $task_service;
    private TaskSuggestionService $task_suggestion_service;

    /**
     * Create a new command instance.
     */
    public function __construct(
        AISuggestionService $ai_suggestion_service,
        TaskService $task_service,
        TaskSuggestionService $task_suggestion_service
    ) {
        parent::__construct();
        $this->ai_suggestion_service = $ai_suggestion_service;
        $this->task_service = $task_service;
        $this->task_suggestion_service = $task_suggestion_service;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $user_id_option = $this->option('user-id');

        // 特定のユーザーIDが指定された場合
        if ($user_id_option) {
            $user_id = (int) $user_id_option;

            // 指定されたユーザーに予定があるか確認
            if (!$this->task_service->hasTasksByUserId($user_id)) {
                $this->error("ユーザーID {$user_id} は予定がありません。");
                return Command::SUCCESS;
            }

            $user_ids = [$user_id];
        } else {
            // 予定があるユーザーIDの一覧を取得
            $user_ids = $this->task_service->getUserIdsWithTasks();
        }

        if (empty($user_ids)) {
            $this->error('ユーザーが見つかりませんでした。');
            return Command::SUCCESS;
        }

        $success_count = 0;
        $failure_count = 0;

        foreach ($user_ids as $user_id) {
            $this->info("ユーザーID: {$user_id} の予定提案を生成中...");

            // 予定データをプロンプト用に整形
            $tasks_summary = $this->task_service->formatRecentTasksForPrompt($user_id, 10);

            // AIによる予定提案を取得
            $suggestion = $this->ai_suggestion_service->generateSuggestion($tasks_summary);

            if (!$suggestion || empty($suggestion['title']) || empty($suggestion['memo'])) {
                $failure_count++;
                continue;
            }

            // 提案を保存
            $this->task_suggestion_service->createSuggestion($user_id, [
                'title' => $suggestion['title'],
                'memo' => $suggestion['memo'],
            ]);
            $success_count++;
        }

        $this->info("\n処理完了: 成功 {$success_count}件、失敗 {$failure_count}件");

        return Command::SUCCESS;
    }
}
