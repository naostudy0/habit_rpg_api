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
        Schema::create('registration_otps', function (Blueprint $table) {
            $table->bigIncrements('registration_otp_id');
            $table->uuid('registration_otp_uuid')->unique()->comment('登録OTP UUID');
            $table->string('email')->unique()->comment('メールアドレス');
            $table->string('otp_hash')->comment('OTPハッシュ');
            $table->unsignedInteger('attempt_count')->default(0)->comment('OTP試行回数');
            $table->unsignedInteger('resend_count')->default(0)->comment('OTP再送回数');
            $table->timestamp('last_sent_at')->nullable()->comment('最終送信日時');
            $table->timestamp('expires_at')->comment('OTP有効期限');
            $table->timestamp('verified_at')->nullable()->comment('OTP検証日時');
            $table->string('registration_token_hash')->nullable()->index()->comment('本登録トークンハッシュ');
            $table->timestamp('registration_token_expires_at')->nullable()->comment('本登録トークン有効期限');
            $table->timestamps();

            $table->index('expires_at');
        });

        DB::statement("ALTER TABLE `registration_otps` COMMENT = '新規登録OTP管理テーブル'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('registration_otps');
    }
};
