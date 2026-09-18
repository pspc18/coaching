<?php

namespace App\Models\Master;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class SellInvantoryItem extends Model
{
        use SoftDeletes;
	protected $table = "sell_invantory_items"; //table name
    
    public function Subject(){
        return $this->belongsTo('App\Models\Subject','subject_id');
    }  
  
    
}