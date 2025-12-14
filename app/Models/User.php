<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    /**
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * @var array<string>
     */
    protected $fillable = [
        'user_uuid',
        'name',
        'email',
        'password',
        'is_dark_mode',
        'is_24_hour_format',
    ];

    /**
     * @var array<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_dark_mode' => 'boolean',
            'is_24_hour_format' => 'boolean',
        ];
    }

    /**
     * UUIDを自動生成する
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->user_uuid)) {
                $user->user_uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * パスワードをハッシュ化するミューテタ
     *
     * @return Attribute
     */
    protected function password(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => $value,
            set: fn (string $value) => ($value !== null && $value !== '')
                ? Hash::make($value)
                : $value,
        );
    }
}
