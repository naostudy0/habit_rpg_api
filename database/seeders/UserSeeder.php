<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // メールアドレスがuniqueのため、既に存在する場合は作成しない
        if (!User::where('email', 'test@example.com')->exists()) {
            User::factory()->create([
                'name' => 'テストユーザー',
                'email' => 'test@example.com',
                'password' => 'password',
            ]);
        }
    }
}
