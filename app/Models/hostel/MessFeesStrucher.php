<?php

namespace App\Models\hostel;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class MessFeesStrucher extends Model
{
        use SoftDeletes;
	protected $table = "mess_fees_strucher"; //table name

   public static function countMessFeesStrucher(){
        $data = MessFeesStrucher::whereNull('deleted_at')->count();
        if($data){
            return $data;
        }
        return 0;
    }  
    
}