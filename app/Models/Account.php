<?php

namespace App\Models;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Account extends Model
{
        use SoftDeletes;
	protected $table = "accounts"; //table name
	
	
	  public static function countAccount(){
        $data=Account::where('deleted_at', null)->where('branch_id',Session::get('branch_id'))->count();
        
            return $data;
        
    }
	
}