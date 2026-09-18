<?php

namespace App\Models;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class CronJobs extends Model
{
        use SoftDeletes;
    protected $table = "cron_jobs"; //table name

    
    
}
