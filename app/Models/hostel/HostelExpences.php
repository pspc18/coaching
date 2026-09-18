<?php

namespace App\Models\hostel;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class HostelExpences extends Model
{
        use SoftDeletes;
	protected $table = "hostel_expences"; //table name

   public static function countHostelExpences(){
        $data = HostelExpences::whereNull('deleted_at')->count();
        if($data){
            return $data;
        }
        return 0;
    }    
}