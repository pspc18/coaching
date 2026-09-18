<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Subject;
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


class SubjectController extends BaseController
{
    
        
    public function studentSubjectList(Request $request){
       
       $class_type_id = $request->class_type_id;
        if($request->isMethod('post')){
          try{
	        
	        
         $data = Subject::where('class_type_id',$class_type_id)->get();
         
	         	if ($data) {
	         	    
	         	      $subjects = array();
            foreach ($data as $key => $item) {
                $subjects[] = array(
                    'key' => $key+1,
                    'name' => $item->name
                );
            }
	   
	         	    
	return response()->json(['status' => true, 'message' => 'Data Found','data'=>$subjects], 200);
		} else {
			return response()->json(['status' => false, 'message' => 'No Data Found'], 200);
		}

	     } catch (Exception $e) {
            return $this->sendError('Validation Error.', 'Error');
        }
        
    }
    }


}