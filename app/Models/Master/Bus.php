<?php

namespace App\Models\Master;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Bus extends Model
{
        use SoftDeletes;
	protected $table = "bus"; //table name

    public function countBus(){
        $data = Bus::where('session_id',Session::get('session_id'));
        
        if(Session::get('role_id') > 1){
            $data = $data->where('branch_id',Session::get('branch_id'));
        }
        $bus = $data->count();
        return $bus;
    }
	
}