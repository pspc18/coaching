<?php

namespace App\Models\library;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class LibraryCabin extends Model
{
        use SoftDeletes;
	protected $table = "library_cabins"; //table name

	public function Library(){
        return $this->belongsTo('App\Models\library\Library','library_id');
    }

    public static function countTotelCabin(){
        $data = LibraryCabin::where('session_id',Session::get('session_id'));
        
        if(Session::get('role_id') > 1){
           $data = $data->where('branch_id',Session::get('branch_id'));
        }
		 $data = $data->count();
        return $data;
    }
    
}