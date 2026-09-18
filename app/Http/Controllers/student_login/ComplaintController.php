<?php

namespace App\Http\Controllers\student_login;
use Illuminate\Validation\Validator; 
use App\Models\Student_login;
use App\Models\Master\Complaint;
use App\Models\User;
use App\Models\Setting;
use App\Models\Admission;
use App\Models\ClassType;
use App\Models\Master\MessageTemplate;
use App\Models\Master\Branch;
use App\Helpers\helper;
use Session;
use Hash;  
use Str;
use Redirect;
use Response;
use Auth; 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ComplaintController extends Controller


{ 
            public function add(Request $request){
                $adminMail = User::where('id','1')->get()->first();
                $student = Admission::with('ClassTypes')->where('id',Session::get('id'))->get()->first();
                if($request->isMethod('post')){
                    $request->validate([
                        'subject' => 'required',
                        'description' => 'required',
                    ]);
                    $add = new Complaint;//model name
                    $add->user_id = Session::get('id');
                    $add->session_id = Session::get('session_id');
                    $add->branch_id = Session::get('branch_id');
            		$add->subject =$request->subject;
            		$add->description =$request->description;
            		$add->admission_id =Session::get('id');
            		$add->date =date('Y-m-d');
            	    $add->section_id = Session::get('section_id');
                    $add->class_type_id = Session::get('class_type_id');
            		$add->save();
			        $template =  MessageTemplate::Select('message_templates.*','message_types.slug','message_types.status as message_type_status')
                    ->leftjoin('message_types','message_types.id','message_templates.message_type_id')
                   ->where('message_types.slug','Complaint')->first();
                    $branch = Branch::find(Session::get('branch_id'));
                    $setting = Setting::where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))->first();
                    $class_name = ClassType::where('id',Session::get('class_type_id'))->first();
                    $arrey1 =   array(
                    '{#admin_name#}',
                    '{#name#}',
                    '{#class_name#}',
                    '{#subject#}',
                    '{#description#}');
                    $arrey2 = array(
                    $adminMail->first_name." ".$adminMail->last_name,
                    $student->first_name." ".$student->last_name,
                    $class_name->name,
                    $request->subject,
                    $request->description);
                    $whatsapp = str_replace($arrey1, $arrey2, $template->whatsapp_content ?? '');
                                
                                if ($setting->firebase_notification == 1) {
                                    Helper::sendNotification(
                                        $template->title ?? 'Complaint',
                                        $whatsapp,
                                        'student',
                                        $adminMail->id
                                    ); 
                                }
                                 
                                if ($template->message_type_status == 1) {
                                    if ($branch->whatsapp_srvc == 1) {
                                        $mobile = $adminMail->mobile  ?? '';
                                        if (!empty($mobile)) {
                                            Helper::MessageQueue($mobile, $whatsapp);
                                        }
                                    }
                                }
                    return redirect::to('complaintViewStudent')->with('message', 'Complaint added Successfully.');
                }
                
                 $Complaints =  Complaint::with('Admission')->with('ClassType')->where('session_id',Session::get('session_id'))
		        ->where('branch_id',Session::get('branch_id'));
		        if(Session::get('role_id') == 3){
		               $Complaints =$Complaints->where('teacher_id_to_complaint',null);  
		        }
		        $Complaints =$Complaints->get();
                return view('student_login.complaint.add',['data'=>$Complaints]);
            } 
            
            public function edit(Request $request, $id){
                $adminMail = User::where('id','1')->get()->first();
                $student = Admission::with('ClassTypes')->where('id',Session::get('id'))->get()->first();
                $data = Complaint::find($id);
                if($request->isMethod('post')){
                    $request->validate([
                        'subject' => 'required',
                        'description' => 'required',
                    ]);
                    $data->session_id = Session::get('session_id');
                    $data->branch_id = Session::get('branch_id');      
            		$data->subject =$request->subject;
            		$data->description =$request->description;
            		$data->admission_id =Session::get('id');
            		$data->date =date('Y-m-d');
		            $data->save();
                    return redirect::to('complaintAddStudent')->with('message', 'Complaint Updated Successfully.');
                }
                return view('student_login.complaint.edit',['data'=>$data]);
            } 

          public function delete(Request $request){
                $complaint = Complaint::find($request->delete_id)->delete();
                return redirect::to('complaintAddStudent')->with('message', 'complaint Deleted Successfully.');
            }
            


}
