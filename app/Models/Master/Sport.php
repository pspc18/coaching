<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Sport extends Model
{
        use SoftDeletes;
	protected $table = "sports"; //table name

    public function ClassTypes(){
        return $this->belongsTo('App\Models\ClassType','for_class');
    }
	
	
		
}