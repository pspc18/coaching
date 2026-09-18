<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Visitor extends Model
{
    use SoftDeletes;
    protected $table = "visitors";
    protected $guarded = [];

    public function classType()
    {
        return $this->belongsTo(ClassType::class, 'class_type_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}