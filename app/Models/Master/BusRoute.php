<?php

namespace App\Models\Master;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class BusRoute extends Model
{
        use SoftDeletes;
	protected $table = "bus_route"; //table name
	
	
	public static function countBusRoute(){
        $data = BusRoute::where('session_id',Session::get('session_id'))
		 ->where('branch_id',Session::get('branch_id'))->count();
        
        return $data;
    }
	
}