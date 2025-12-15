<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Task extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $primaryKey = 'task_id';

    /**
     * @var array<string>
     */
    protected $fillable = [
        'task_uuid',
        'user_id',
        'title',
        'scheduled_date',
        'scheduled_time',
        'memo',
        'is_completed',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'scheduled_date' => 'date',
            'scheduled_time' => 'string',
            'is_completed' => 'boolean',
        ];
    }

    /**
     * UUIDを自動生成する
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($task) {
            if (empty($task->task_uuid)) {
                $task->task_uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * ユーザーとのリレーション
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
