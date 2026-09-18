<?php

namespace App\Models\hostel;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class MessFoodRoutine extends Model
{
        use SoftDeletes;
	protected $table = "mess_food_routine"; //table name

   public static function countMessFoodRoutine(){
        $data = MessFoodRoutine::whereNull('deleted_at')->count();
        if($data){
            return $data;
        }
        return 0;
    }  
    
}