<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class EventManagement extends Model
{
        use SoftDeletes;
	protected $table = "event_management"; //table name
	
	public function ClassType(){
        return $this->belongsTo('App\Models\ClassType','class_type');
	}

    public function StudentName() {
        
        return $this->belongsTo('App\Models\StudentName','student_name');
    }
}