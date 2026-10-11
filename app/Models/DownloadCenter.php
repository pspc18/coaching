<?php

namespace App\Models;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class DownloadCenter extends Model
{
       use SoftDeletes;
	protected $table = "download_center"; //table name
	
	public static function countContent(){
        $data = DownloadCenter::where('session_id',Session::get('session_id'));
        if(Session::get('role_id') > 1){
            $data = $data->where('branch_id',Session::get('branch_id'));
        }
        $data = $data->count();
        return $data;
    }

    public function ClassType()
    {
        return $this->belongsTo('App\Models\ClassType', 'class_type_id')->withDefault(['name' => 'All Classes']);
    }

    public function User()
    {
        return $this->belongsTo('App\Models\User', 'user_id')->withDefault(['first_name' => 'Faculty']);
    }

}