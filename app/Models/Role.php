<?php

namespace App\Models;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Role extends Model
{
        use SoftDeletes;
	protected $table = "role"; //table name
    
      public static function countRole(){
        $data = Role::where('deleted_at', null)->where('session_id',Session::get('session_id'))
		 ->where('branch_id',Session::get('branch_id'))->count();
        return $data;
    }
}