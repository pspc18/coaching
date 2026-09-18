<?php

namespace App\Models\Master;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class MessageType extends Model
{
        use SoftDeletes;
	protected $table = "message_types"; //table name

    public static function countMessageType(){
        $data = MessageType::count();
        return $data;
    }	
}