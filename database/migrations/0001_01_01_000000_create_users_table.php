<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('user_id');
            $table->uuid('user_uuid')->unique()->comment('ユーザーUUID');
            $table->string('name')->comment('ユーザー名');
            $table->string('email')->unique()->comment('メールアドレス');
            $table->timestamp('email_verified_at')
                ->nullable()
                ->comment('メール認証日時');
            $table->string('password')->comment('パスワード');
            $table->boolean('is_dark_mode')
                ->default(false)
                ->comment('ダークモード設定 1:ダーク, 0:ライト');
            $table->boolean('is_24_hour_format')
                ->default(true)
                ->comment('時刻表示形式 1:24時間, 0:12時間');
            $table->rememberToken()->comment('リメンバートークン');
            $table->timestamps();

            $table->index('user_uuid');
        });

        DB::statement("ALTER TABLE `users` COMMENT = 'ユーザー管理テーブル'");

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
