<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TaskSuggestion extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $primaryKey = 'task_suggestion_id';

    /**
     * @var array<string>
     */
    protected $fillable = [
        'task_suggestion_uuid',
        'user_id',
        'title',
        'memo',
    ];

    /**
     * UUIDを自動生成する
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($task_suggestion) {
            if (empty($task_suggestion->task_suggestion_uuid)) {
                $task_suggestion->task_suggestion_uuid = (string) Str::uuid();
            }
        });
    }
}
