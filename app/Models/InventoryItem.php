<?php

namespace App\Models;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class InventoryItem extends Model
{
        use SoftDeletes;
	protected $table = "inventory_items"; //table name
    
    
    public static function countInvantoryItem(){
        $data=InventoryItem::where('session_id',Session::get('session_id'));
        
        if(Session::get('role_id') > 1){
            $invantoryItem = $data->where('branch_id',Session::get('branch_id'));
        }
        else{
            $invantoryItem = $data->count();
        }
        return $invantoryItem;
    }	
    
    
    public function Invantory(){
        
      return $this->belongsTo('App\Models\Inventory','name');   
        
    }
    
}