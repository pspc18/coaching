<?php

namespace App\Models\Master;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Role extends Model
{
        use SoftDeletes;
	protected $table = "role"; //table name
	
	
	
	
  public static function countRole(){
        $data = Role::where('deleted_at', null)->count();
        return $data;
    }	
}