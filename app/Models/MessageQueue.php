<?php

namespace App\Models;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class MessageQueue extends Model
{
        use SoftDeletes;
		protected $fillable = [
    'receiver_number',
    'content',
    'message_status',
    'submitted_at',
];
	//protected $table = "message_queue"; //table name

}