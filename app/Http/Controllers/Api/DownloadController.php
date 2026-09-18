<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\DownloadCenter;
use Illuminate\Support\Facades\Auth;
use Validator;
use Hash;
use Session;
use File;
use App;
use URL;
use Image;
use Carbon;
use Str;
use App\Helpers\helpers;
use Mail;


class DownloadController extends BaseController
{
    
     public function getVersion()
    { 
        
        return response()->json([
            'version' => '0.0.1',       
            'forceUpdate' => true     
        ]);
    }
        
    public function getDownloadCenter(Request $request){
       $class_type_id = $request->class_type_id ;
       $data = DownloadCenter::whereNotIn('content_type',['Study Material'])->where('class_type_id',$class_type_id)->get();
       $count['syllabus'] = DownloadCenter::where('content_type',['Syllabus'])->where('class_type_id',$class_type_id)->count();
       $count['other'] = DownloadCenter::where('content_type',['Other Downloads'])->where('class_type_id',$class_type_id)->count();
       $count['assignment'] = DownloadCenter::where('content_type',['Assignments'])->where('class_type_id',$class_type_id)->count();
       	if (!empty($data)) {
      	return response()->json(['status' => true, 'message' => 'Download Center Fetched','data'=>$data,'count'=>$count], 200);
		} else {
			return response()->json(['status' => false, 'message' => 'No Assignment Fount'], 200);
		}
    }
    public function getStudyMaterial(Request $request){
       $class_type_id = $request->class_type_id ;
       $data = DownloadCenter::where('content_type','Study Material')->where('class_type_id',$class_type_id)->get();
       	if (!empty($data)) {
      	return response()->json(['status' => true, 'message' => 'Study Material Fetched','data'=>$data], 200);
		} else {
			return response()->json(['status' => false, 'message' => 'No Assignment Fount'], 200);
		}
    }
}