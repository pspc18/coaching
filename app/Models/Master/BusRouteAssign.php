<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class BusRouteAssign extends Model
{
        use SoftDeletes;
	protected $table = "bus_route_assign"; //table name

     public function busRoute()
    {
        return $this->belongsTo('App\Models\Master\BusRoute','route_id');
    }

     public function bus()
    {
        return $this->belongsTo('App\Models\Master\Bus','bus_id');
    }	
}