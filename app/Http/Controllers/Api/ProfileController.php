<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\WebUser;
use App\Models\User;
use App\Models\Master\Rule;
use App\Models\Master\SchoolDesk;
use App\Models\Master\Gallery;
use App\Models\Master\Prayer;
use App\Models\Account;
use App\Models\Master\GatePass;
use App\Models\Master\NoticeBoard;
use App\Models\Admission;
use App\Models\StudentAttendance;
use App\Models\Teacher;
use App\Models\Expense;
use App\Models\FeesDetail;
use App\Models\Setting;
use App\Models\Master\Complaint;
use App\Models\Master\LeaveManagement;
use Illuminate\Support\Facades\Auth;
use Validator;
use Hash;
use File;
use App;
use URL;
use Image;
use Carbon;
use Str;
use App\Helpers\helpers;
use Mail;

class ProfileController extends BaseController

{
    
    
    public function getAllCount(Request $request){
        
     
        
          if($request->isMethod('post')){
              
                  $Admission = Admission::where('status',1)->where('school',1)->count();
                  $studentPresent=StudentAttendance::where('date',date('Y-m-d'))->where('attendance_status_id',1) ->where('session_id',1)
		          ->where('branch_id',1)->count();
		          
		           $todayCollection=FeesDetail::where('session_id',1)
		 ->where('branch_id',1)->where('fees_type',0)->where('date',date('Y-m-d'))->sum('total_amount');
		   $totalCollection=FeesDetail::where('session_id',1)->where('branch_id',1)->where('fees_type',0)->sum('total_amount');
  
		  $userCount = User::where('branch_id',1)->count();
		  
		     $teacherCount=Teacher::where('drop_status',0)->where('branch_id',1)->count();
		     
		      $accountCount=Account::where('deleted_at', null)->where('branch_id',1)->count();
		      
		       $complaintCount = Complaint::where('deleted_at', null)->where('branch_id',1)->count();
		           $monthlyExpenses=Expense::where('branch_id',1)->whereMonth('date',date('m'))->sum('amount');
		       
                  $data = array();
                  $data[] = array(
                  'admissionCount' => $Admission,
                  'presentCount' => $studentPresent,
                  'todayCollection' => $todayCollection,
                  'totalCollection' => $totalCollection,
                  'userCount' => $userCount,
                  'teacherCount' => $teacherCount,
                  'accountCount' => $accountCount,
                  'complaintCount' => $complaintCount,
                  'monthlyExpenses' => $monthlyExpenses,
                  );
            
          }
          return $this->sendResponseData($data, 'success');
        
    }
    public function getProfile(Request $request){
        
        
        
	    try{

       if($request->role_id ==3){
          $data = Admission::find($request->id);
      }else{
          $data = User::find($request->id);
      }
        
    //         if($request->isMethod('post')){
            
                
    //             if($request->role_id ==3){
    //                 if($request->file('photo')){
    //                 $image = $request->file('photo');
    //                 $path = $image->getRealPath();      
    //                 $student_image =  time().uniqid().$image->getClientOriginalName();
    //                 $destinationPath = env('IMAGE_UPLOAD_PATH').'profile';
    //                 $image->move($destinationPath, $student_image); 
    //                 $data->image = $student_image;
    //              }
                 
    //                 if($request->file('father_img')){
    //                  $image = $request->file('father_img');
    //                 $path = $image->getRealPath();      
    //                 $father_image =  time().uniqid().$image->getClientOriginalName();
    //                 $destinationPath = env('IMAGE_UPLOAD_PATH').'father_image';
    //                 $image->move($destinationPath, $father_image);    
    //                 $data->father_img = $father_image;
    //              }
                 
    //                 if($request->file('mother_img')){
    //                  $image = $request->file('mother_img');
    //                 $path = $image->getRealPath();      
    //                 $mother_image =  time().uniqid().$image->getClientOriginalName();
    //                 $destinationPath = env('IMAGE_UPLOAD_PATH').'mother_image';
    //                 $image->move($destinationPath, $mother_image);  
    //                 $data->mother_img = $mother_image;
    //              }                  
    //              }
                 
    //              else{
    //                 // if($request->file('photo')){
    //                 //     $image = $request->file('photo');
    //                 //     $path = $image->getRealPath();      
    //                 //     $photo =  time().uniqid().$image->getClientOriginalName();
    //                 //     $destinationPath = env('IMAGE_UPLOAD_PATH').'profile';
    //                 //     $image->move($destinationPath, $photo); 
    //                 //     $data->image = $photo;
    //                 // }
    //                     $data->image = $request->photo;
    //             }           

    //         $data->session_id = 1;
    //         $data->branch_id = 1;
    //         $data->first_name =$request->first_name;
    //         $data->last_name =$request->last_name;
    //         $data->dob= $request->dob;
    //         $data->email =$request->email;
    //         $data->mobile =$request->mobile;
    //         $data->father_name =$request->father_name;
    //         $data->father_mobile =$request->father_mobile;
    //         $data->mother_name =$request->mother_name;
    //         $data->mother_name =$request->mother_name;
    //         $data->father_mobile = $request->father_mobile;
    //         $data->city_id= $request->city_id;
    //     	$data->country_id= $request->country_id;
    //     	$data->state_id= $request->state_id;
    // 		$data->pincode= $request->pincode;
    // 		$data->address  = $request->address;
    //         $data->save();
            
    //           return $this->sendResponseData($data, 'success');
		
    //     }
       
     
	     return $this->sendResponseData($data, 'success');
        } catch (Exception $e) {
            return $this->sendError('Validation Error.', 'Error');
        }
        
        
    }
    
     public function schoolDeskView(Request $request)
    {
            $data = SchoolDesk::where('id',1)->first();
            
            if(!empty($data))
            {
                 return $this->sendResponseData($data, 'success');
            }
            else
            {
                  return $this->sendError('Validation Error.', 'Error');
            }
          
        
    }
    
    
     public function schoolPrayer(Request $request){
        
        $data = Prayer::whereNull('deleted_at')->orderBy('id', 'DESC')->get();
		 
	
          if(!empty($data))
            {
                 return $this->sendResponseData($data, 'success');
            }
            else
            {
                  return $this->sendError('Validation Error.', 'Error');
            }
          
    } 
    
    
      public function schoolRules(Request $request){
    
      $data = Rule::select('rules.*','role.name as role_name')
        ->leftjoin('role','role.id','rules.role_id')->where('rules.role_id',3)->get();
           if(!empty($data))
            {
                  return $this->sendResponseData($data, 'success');
            }
            else
            {
                  return $this->sendError('Validation Error.', 'Error');
            }
}
      public function noticeBoard(Request $request){
    
    $data = NoticeBoard::where('role_id',3)->whereDate('to_date', '>=', date("Y-m-d"))->whereDate('from_date', '<=', date("Y-m-d"))->orderBy('id','DESC')->get();
    //   $data = NoticeBoard::where('role_id',3)->whereDate('from_date', '<=', date('Y-m-d'))->whereDate('to_date', '>=', date('Y-m-d'))->get();
           if(!empty($data))
            {
                  return $this->sendResponseData($data, 'success');
            }
            else
            {
                  return $this->sendError('Validation Error.', 'Error');
            }
}

public function schoolGallery(Request $request){
        $Category= Gallery::groupBy('img_category')->orderBy('id','DESC')->get();
        $Images= Gallery::orderBy('id','DESC')->get();
          if(!empty($Category))
            {
              return response()->json(['status' => true, 'message' => 'Success','images'=>$Images,'category'=>$Category], 200);
            }
            else
            {
                 return response()->json(['status' => false, 'message' => 'Error','images'=>[],'category'=>[]], 200);
            }
    }  
public function addComplain(Request $request){
    $admission_id  = $request->admission_id;
    $subject  = $request->subject;
    $class_type_id = $request->class_type_id;
    $message  = $request->message;
     if($request->isMethod('post')){
         
         $add = new Complaint();
         $add->description =$message; 
         $add->subject = $subject;
         $add->class_type_id= $class_type_id;
         $add->admission_id = $admission_id;
         $add->save();
          if(!empty($add))
            {
              return response()->json(['status' => true, 'message' => 'Success'], 200);
            }
            else
            {
                 return response()->json(['status' => false, 'message' => 'Error'], 200);
            }
         
         
     }
     
         
    }  
public function addLeave(Request $request){
    $admission_id  = $request->admission_id;
    $subject  = $request->subject;
    $class_type_id = $request->class_type_id;
    $message  = $request->message;
    $from_date = $request->from_date;
    $to_date  = $request->to_date;
     if($request->isMethod('post')){
         
         $add = new LeaveManagement();
         $add->reason =$message; 
         $add->subject = $subject;
        // $add->class_type_id= $class_type_id;
         $add->admission_id = $admission_id;
         $add->from_date = $from_date;
         $add->to_date = $to_date;
         $add->status = 2;
         $add->save();
          if(!empty($add))
            {
              return response()->json(['status' => true, 'message' => 'Success'], 200);
            }
            else
            {
                 return response()->json(['status' => false, 'message' => 'Error'], 200);
            }
         
         
     }
     
         
    }  
    
    
    public function profileEdit(Request $request){
         $decode = json_decode($request->data);
	    try{
      if($request->role_id ==3){
          $data = Admission::find($decode->id);
      }else{
          $data = User::find($decode->id);
      }
            if($request->isMethod('post')){
                if($request->role_id ==3){
                    $student_image = '';
                    if ($request->hasfile('photo')) {
                        $image = $request->file('photo');
                        $path = $image->getRealPath();
                        $student_image =  time() . uniqid() . $image->getClientOriginalName();
                        $destinationPath = env('IMAGE_UPLOAD_PATH') . 'profile';
                        $image->move($destinationPath, $student_image);
                         $data->image = $student_image;
                    }
                //     if($request->file('photo')){
                //     $image = $request->file('photo');
                //     $path = $image->getRealPath();      
                //     $student_image =  time().uniqid().$image->getClientOriginalName();
                //     $destinationPath = env('IMAGE_UPLOAD_PATH').'profile';
                //     $image->move($destinationPath, $student_image); 
                //     $data->image = $student_image;
                //  }
                 
                //     if($request->file('father_img')){
                //      $image = $request->file('father_img');
                //     $path = $image->getRealPath();      
                //     $father_image =  time().uniqid().$image->getClientOriginalName();
                //     $destinationPath = env('IMAGE_UPLOAD_PATH').'father_image';
                //     $image->move($destinationPath, $father_image);    
                //     $data->father_img = $father_image;
                //  }
                 
                //     if($request->file('mother_img')){
                //      $image = $request->file('mother_img');
                //     $path = $image->getRealPath();      
                //     $mother_image =  time().uniqid().$image->getClientOriginalName();
                //     $destinationPath = env('IMAGE_UPLOAD_PATH').'mother_image';
                //     $image->move($destinationPath, $mother_image);  
                //     $data->mother_img = $mother_image;
                //  }                  
                 }
                 else{
                    // if($request->file('photo')){
                    //     $image = $request->file('photo');
                    //     $path = $image->getRealPath();      
                    //     $photo =  time().uniqid().$image->getClientOriginalName();
                    //     $destinationPath = env('IMAGE_UPLOAD_PATH').'profile';
                    //     $image->move($destinationPath, $photo); 
                    //     $data->image = $photo;
                    // }
                        $data->image = $request->photo;
                }           
            $data->session_id = 1;
            $data->branch_id = 1;
            $data->first_name =$decode->first_name;
            $data->userName =$decode->userName;
            $data->last_name =$decode->last_name;
            $data->confirm_password =$decode->confirm_password;
            $data->password =Hash::make($decode->confirm_password);
            $data->dob= $decode->dob;
            $data->email =$decode->email;
            $data->mobile =$decode->mobile;
            $data->father_name =$decode->father_name;
            $data->father_mobile =$decode->father_mobile;
            $data->mother_name =$decode->mother_name;
            $data->father_mobile = $decode->father_mobile;
            $data->city_id= $request->city_id;
        	$data->country_id= $request->country_id;
        	$data->state_id= $request->state_id;
    		$data->pincode= $decode->pincode;
    		$data->address  = $decode->address;
            $data->save();
              return $this->sendResponseData($data, 'success');
        }
        } catch (Exception $e) {
            return $this->sendError('Validation Error.', 'Error');
        }
    }
     public function gatePassView(Request $request){
         $admission_id= $request->admission_id;
        $data = GatePass::where('admissionNo',$admission_id)->get();
          if(!empty($data))
            {
              return response()->json(['status' => true, 'message' => 'Success','data'=>$data], 200);
            }
            else
            {
                 return response()->json(['status' => false, 'message' => 'Error','data'=>[]], 200);
            } 
    }
     public function complainBox(Request $request){
         $admission_id= $request->admission_id;
        $data = Complaint::where('admission_id',$admission_id)->orderBy('id','DESC')->get();
          if(!empty($data))
            {
              return response()->json(['status' => true, 'message' => 'Success','data'=>$data], 200);
            }
            else
            {
                 return response()->json(['status' => false, 'message' => 'Error','data'=>[]], 200);
            } 
    }
     public function leaveBox(Request $request){
         $admission_id= $request->admission_id;
        $data = LeaveManagement::where('admission_id',$admission_id)->orderBy('id','DESC')->get();
          if(!empty($data))
            {
              return response()->json(['status' => true, 'message' => 'Success','data'=>$data], 200);
            }
            else
            {
                 return response()->json(['status' => false, 'message' => 'Error','data'=>[]], 200);
            } 
    }
}
