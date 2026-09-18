<?php

namespace App\Models\hostel;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class FoodMenuList extends Model
{
        use SoftDeletes;
	protected $table = "food_menu_lists"; //table name

   public static function FoodMenuList(){
        $data = FoodMenuList::whereNull('deleted_at')->count();
        if($data){
            return $data;
        }
        return 0;
    }  
    
}