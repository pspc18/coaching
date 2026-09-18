<?php

namespace App\Models\fees;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class FeesAdvanceHistory extends Model
{
        use SoftDeletes;
	protected $table = "fees_advance_historys"; //table name
}