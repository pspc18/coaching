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
    
    public static function countAssignments(){
        $data = DownloadCenter::where('session_id',Session::get('session_id'));
        if(Session::get('role_id') > 1){
            $data = $data->where('branch_id',Session::get('branch_id'));
        }
        $data = $data->where('content_type','Assignments')->count();
        return $data;
    }
    
    public static function countStudyMaterial(){
        $data = DownloadCenter::where('session_id',Session::get('session_id'));
        if(Session::get('role_id') > 1){
            $data = $data->where('branch_id',Session::get('branch_id'));
        }
        $data = $data->where('content_type','Study Material')->count();
        return $data;
    }
    
    
    public static function countSyllabus(){
        $data = DownloadCenter::where('session_id',Session::get('session_id'));
        if(Session::get('role_id') > 1){
            $data = $data->where('branch_id',Session::get('branch_id'));
        }
        $data = $data->where('content_type','Syllabus')->count();
        return $data;
    }
    
    public static function countOtherDownloads(){
        $data = DownloadCenter::where('session_id',Session::get('session_id'));
        if(Session::get('role_id') > 1){
            $data = $data->where('branch_id',Session::get('branch_id'));
        }
        $data = $data->where('content_type','Other Downloads')->count();
        return $data;
    }

}