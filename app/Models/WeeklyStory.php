<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeeklyStory extends Model
{
    protected $fillable = [
        'user_id', 'week', 'year',
        'task_summary', 'prev_story_tail',
        'story_text', 'generated_at',
    ];

    protected $casts = [
        'task_summary' => 'array',
        'generated_at' => 'datetime',
    ];
}
