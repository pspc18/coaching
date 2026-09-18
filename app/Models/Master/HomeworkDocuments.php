<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class HomeworkDocuments extends Model
{
        use SoftDeletes;
	protected $table = "homework_documents"; //table name

    public function Admission(){
       return $this->belongsTo('App\Models\Admission','admission_id');
    }	

    public function UploadHomework(){
       return $this->belongsTo('App\Models\Master\UploadHomework','upload_hw_id');
    }	
    
}