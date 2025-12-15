<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // テストユーザーを取得
        $test_user = User::where('email', 'test@example.com')->first();
        if (!$test_user) {
            return;
        }

        // 過去の予定（完了済み）
        Task::factory()->create([
            'user_id' => $test_user->user_id,
            'title' => '朝のジョギング',
            'scheduled_date' => now()->subDays(3)->format('Y-m-d'),
            'scheduled_time' => '07:00:00',
            'memo' => '30分程度の軽いジョギング',
            'is_completed' => true,
        ]);
        Task::factory()->create([
            'user_id' => $test_user->user_id,
            'title' => '読書',
            'scheduled_date' => now()->subDays(2)->format('Y-m-d'),
            'scheduled_time' => '20:00:00',
            'memo' => '技術書を30分読む',
            'is_completed' => true,
        ]);

        Task::factory()->create([
            'user_id' => $test_user->user_id,
            'title' => '筋トレ',
            'scheduled_date' => now()->subDays(1)->format('Y-m-d'),
            'scheduled_time' => '19:30:00',
            'memo' => '腕立て伏せ、腹筋、スクワット',
            'is_completed' => false,
        ]);

        // 今日の予定
        Task::factory()->create([
            'user_id' => $test_user->user_id,
            'title' => 'プログラミング学習',
            'scheduled_date' => now()->format('Y-m-d'),
            'scheduled_time' => '21:00:00',
            'memo' => 'Flutterの学習を1時間',
            'is_completed' => false,
        ]);

        Task::factory()->create([
            'user_id' => $test_user->user_id,
            'title' => '買い物',
            'scheduled_date' => now()->format('Y-m-d'),
            'scheduled_time' => '15:00:00',
            'memo' => 'スーパーで食材を購入',
            'is_completed' => false,
        ]);

        // 明日の予定
        Task::factory()->create([
            'user_id' => $test_user->user_id,
            'title' => '朝のジョギング',
            'scheduled_date' => now()->addDay()->format('Y-m-d'),
            'scheduled_time' => '07:00:00',
            'memo' => '30分程度の軽いジョギング',
            'is_completed' => false,
        ]);

        // 未来の予定
        Task::factory()->create([
            'user_id' => $test_user->user_id,
            'title' => '読書',
            'scheduled_date' => now()->addDays(2)->format('Y-m-d'),
            'scheduled_time' => '20:00:00',
            'memo' => '技術書を30分読む',
            'is_completed' => false,
        ]);
    }
}
