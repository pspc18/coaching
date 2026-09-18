<?php

namespace App\Models\hostel;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class MessFoodCategory extends Model
{
        use SoftDeletes;
	protected $table = "mess_food_categorys"; //table name

   public static function countHead(){
        $data = MessFoodCategory::whereNull('deleted_at')->count();
        if($data){
            return $data;
        }
        return 0;
    }  
    
}