<?php

namespace App\Models\hostel;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Head extends Model
{
        use SoftDeletes;
	protected $table = "heads"; //table name

   public static function countHostelHead(){
        $data = Head::whereNull('deleted_at')->where('type','expance ')->count();
        if($data){
            return $data;
        }
        return 0;
    }  
   public static function countMessHead(){
        $data = Head::whereNull('deleted_at')->where('type','mess')->count();
        if($data){
            return $data;
        }
        return 0;
    }  
    
}