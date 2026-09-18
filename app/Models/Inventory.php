<?php

namespace App\Models;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Inventory extends Model
{
        use SoftDeletes;
	protected $table = "inventorys"; //table name
    
    
    public static function countInvantory(){
        $data=Inventory::whereNull('deleted_at');
        
        if(Session::get('role_id') > 1){
            $data = $data->where('branch_id',Session::get('branch_id'));
        }
       
        $invantory = $data->count();
        return $invantory;
    }	
    
}