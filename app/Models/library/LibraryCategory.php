<?php

namespace App\Models\library;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class LibraryCategory extends Model
{
        use SoftDeletes;
	protected $table = "library_categarys"; //table name
   
   
   
       public static function countLibraryCategory(){
        $data = LibraryCategory:: where('session_id',Session::get('session_id'));
        
        if(Session::get('role_id') > 1){
            $data = $data->where('branch_id',Session::get('branch_id'));
        }
		 $data = $data->count();
        return $data;
    }
}