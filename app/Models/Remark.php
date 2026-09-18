<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Remark extends Model
{
    use SoftDeletes;

    protected $table = "registration_remarks";

    protected $fillable = ['student_id','date','remark'];
}
