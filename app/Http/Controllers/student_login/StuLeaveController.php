<?php

namespace App\Http\Controllers\student_login;
use Illuminate\Validation\Validator; 
use App\Models\Master\LeaveManagement;
use App\Models\Master\MessageTemplate;
use App\Models\Master\Branch;
use App\Models\Master\Section;
use App\Models\Setting;
use App\Models\User;
use App\Models\ClassType;
use App\Models\Admission;
use Session;
use Hash;
use Str;
use Redirect;
use Helper;
use Response;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StuLeaveController extends Controller

{
    
   
   
    public function leaveAdd(Request $request){
    
        $dataview = LeaveManagement::where('session_id',Session::get('session_id'))
                             ->where('branch_id',Session::get('branch_id'))->orderBy('id', 'DESC')->get();
         
         if($request->isMethod('post')){
                $request->validate([
        'subject' => 'required',
        'from_date' => 'required',
            'to_date' => 'required',
            'reason' => 'required',
         ]);
         
              $add_leave = new LeaveManagement;//model name
                $add_leave->session_id = Session::get('session_id');
                $add_leave->branch_id = Session::get('branch_id');
                $add_leave->admission_id = Session::get('id');
                $add_leave->class_type_id = Session::get('class_type_id');
    			$add_leave->subject  = $request->subject;
    			$add_leave->from_date  = $request->from_date;
                $add_leave->to_date = $request->to_date;
                $add_leave->reason = $request->reason;
                $add_leave->status = '2';
              
                $add_leave->save();
                
            	$template =  MessageTemplate::Select('message_templates.*','message_types.slug')
                            ->leftjoin('message_types','message_types.id','message_templates.message_type_id')
                            ->where('message_types.status',1)->where('message_types.slug','Leaves')->first();
                            
                $adminMail = User::where('id','1')->get()->first();
                $branch = Branch::find(Session::get('branch_id'));
                $setting = Setting::where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))->first();
                $class_name = ClassType::where('id',Session::get('class_type_id'))->first();
                $student = Admission::find(Session::get('id'));
                
                $arrey1 =   array(
                                '{#admin_name#}',
                                '{#from_date#}',
                                '{#to_date#}',
                                '{#reason#}',
                                '{#name#}',
                                '{#class_name#}');
   
                $arrey2 = array(
                            $adminMail->first_name." ".$adminMail->last_name,
                            date('d-m-Y',strtotime($request->from_date)),
                            date('d-m-Y',strtotime($request->to_date)),
                            $request->reason,
                            $student->first_name." ".$student->last_name,
                            $class_name->name);
                                
                        if($template->status != 1){
                                    if($branch->whatsapp_srvc != 0){
                                        if ($adminMail->mobile != ""){
                                            if($template->whatsapp_status != 0){
                                                $whatsapp = str_replace($arrey1,$arrey2,$template->whatsapp_content);
                                                Helper::sendWhatsappMessage($adminMail->mobile,$whatsapp);
                                            }
                                        }
                                    }
                                       
                            }

                
                     { $emailData = ['email'=>'2003harshp@gmail.com','subject'=>'school'];
               Helper::sendMail('email_print.loginDetails',$emailData);
                }	
    
            return redirect::to('applyLeaveStudent')->with('message', 'Leave application applied Successfully.');
     }
    return view('student_login.leave.student_leave',['dataview'=>$dataview]);    
        }

    
    public function leaveEdit(Request $request,$id){
         
                $data = LeaveManagement::find($id);
               
        if($request->isMethod('post')){
            
             $request->validate([
                 'subject' => 'required',
        'from_date' => 'required',
            'to_date' => 'required',
            'reason' => 'required',
        ]);
            
                 $data->session_id = Session::get('session_id');
                $data->branch_id = Session::get('branch_id');
                $data->subject  = $request->subject;
    			$data->from_date  = $request->from_date;
                $data->to_date = $request->to_date;
                $data->reason = $request->reason;
            $data->save();
                
            return redirect::to('applyLeaveStudent')->with('message', 'Application Edited Successfully.');
        }
          $dataview = LeaveManagement::where('session_id',Session::get('session_id'))
                             ->where('branch_id',Session::get('branch_id'))->orderBy('id', 'DESC')->get();
          
          return view('student_login.leave.edit_student_leave',['data'=>$data,'dataview'=>$dataview]);
          
     }
    
    public function leaveDelete(Request $request){
       
        $id = $request->delete_id;
       
        $leave = LeaveManagement::find($id)->delete();
        
         return redirect::to('applyLeaveStudent')->with('message', 'Leave Deleted Successfully !');
    }    

}