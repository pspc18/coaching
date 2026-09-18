<?php

namespace App\Models\library;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Library extends Model
{
        use SoftDeletes;
	protected $table = "librarys"; //table name




    public static function countTotelLibrary(){
        $data = Library:: where('session_id',Session::get('session_id'));
        
        if(Session::get('role_id') > 1){
          $data = $data->where('branch_id',Session::get('branch_id'));
        }
		 $data = $data->count();
        return $data;
    }
    
}