<?php

namespace App\Models\library;
use Maatwebsite\Excel\Concerns\ToModel;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class LibraryBook extends Model
{
        use SoftDeletes;
	protected $table = "library_books"; //table name
   
   
   public static function countLibraryBook(){
        $data = LibraryBook:: where('session_id',Session::get('session_id'));
        
        if(Session::get('role_id') > 1){
            $data = $data->where('branch_id',Session::get('branch_id'));
        }
		 $data = $data->count();
        return $data;
    }
}