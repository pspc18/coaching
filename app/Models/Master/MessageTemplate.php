<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class MessageTemplate extends Model
{
        use SoftDeletes;
	protected $table = "message_templates"; //table name

    public static function countMessageTemplate(){
        $data = MessageTemplate::count();
        return $data;
    }	
}