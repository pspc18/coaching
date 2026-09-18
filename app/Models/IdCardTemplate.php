<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IdCardTemplate extends Model
{
    protected $fillable = ['user_id','branch_id','session_id','name', 'design_content', 'bg_image','type'];

    protected $casts = [
        'design_content' => 'array',
    ];
}