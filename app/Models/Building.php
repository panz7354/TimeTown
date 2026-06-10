<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Building extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'slot',        // ← 加這個
        'level',
        'name',
        'svg_file',
        'completed_count',
        'grid_x',
        'grid_y',
    ];
}
