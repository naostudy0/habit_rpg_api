<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RegistrationOtp extends Model
{
    /**
     * @var string
     */
    protected $primaryKey = 'registration_otp_id';

    /**
     * @var array<string>
     */
    protected $fillable = [
        'registration_otp_uuid',
        'email',
        'otp_hash',
        'attempt_count',
        'resend_count',
        'last_sent_at',
        'expires_at',
        'verified_at',
        'registration_token_hash',
        'registration_token_expires_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'last_sent_at' => 'datetime',
            'expires_at' => 'datetime',
            'verified_at' => 'datetime',
            'registration_token_expires_at' => 'datetime',
        ];
    }

    /**
     * UUIDを自動生成する
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($registration_otp) {
            if (empty($registration_otp->registration_otp_uuid)) {
                $registration_otp->registration_otp_uuid = (string) Str::uuid();
            }
        });
    }
}
