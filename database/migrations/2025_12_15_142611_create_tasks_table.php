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
        Schema::create('tasks', function (Blueprint $table) {
            $table->bigIncrements('task_id');
            $table->uuid('task_uuid')->unique()->comment('予定UUID');
            $table->unsignedBigInteger('user_id')->comment('ユーザーID');
            $table->string('title', 255)->comment('タイトル');
            $table->date('scheduled_date')->comment('予定日');
            $table->time('scheduled_time')->comment('予定時刻');
            $table->text('memo')->nullable()->comment('メモ');
            $table->boolean('is_completed')
                ->default(false)
                ->comment('完了フラグ 1:完了, 0:未完了');
            $table->timestamps();

            $table->foreign('user_id')
                ->references('user_id')
                ->on('users')
                ->onDelete('cascade');

            $table->index('task_uuid');
            $table->index('user_id');
            $table->index(['scheduled_date', 'scheduled_time']);
        });

        DB::statement("ALTER TABLE `tasks` COMMENT = '予定管理テーブル'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
