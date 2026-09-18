<?php

namespace App\Models\Master;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class SellInventory extends Model
{
        use SoftDeletes;
	protected $table = "sell_inventory"; //table name

    public function Admission(){
        return $this->belongsTo('App\Models\Admission','admission_id');
    }
    
}