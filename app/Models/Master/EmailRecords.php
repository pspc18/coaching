<?php

namespace App\Models\Master;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class EmailRecords extends Model
{
        use SoftDeletes;
	protected $table = "email_records"; //table name
	
}