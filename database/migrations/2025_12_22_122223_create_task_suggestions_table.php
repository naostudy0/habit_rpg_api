<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('task_suggestions', function (Blueprint $table) {
            $table->bigIncrements('task_suggestion_id');
            $table->uuid('task_suggestion_uuid')->unique()->comment('提案UUID');
            $table->unsignedBigInteger('user_id')->comment('ユーザーID');
            $table->string('title', 255)->comment('タイトル');
            $table->text('memo')->comment('メモ');
            $table->timestamps();

            $table->foreign('user_id')
                ->references('user_id')
                ->on('users')
                ->onDelete('cascade');
        });

        DB::statement("ALTER TABLE `task_suggestions` COMMENT = '予定提案テーブル'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_suggestions');
    }
};
