<?php

namespace App\Http\Controllers\master;
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

class ComplaintsController extends Controller


{ 
            public function parent_teacherConversation(Request $request){
                $Complaints = Complaint::Select('complaint.*','admissions.first_name','admissions.last_name','admissions.image','class_types.name as class_name')
                ->leftjoin('admissions','admissions.id','complaint.admission_id')
                ->leftjoin('class_types','class_types.id','complaint.class_type_id')
                ->where('complaint.session_id',Session::get('session_id'))
                ->where('complaint.branch_id',Session::get('branch_id'))->where('complaint.teacher_id_to_complaint',Session::get('teacher_id'))->get();
                return view('staff.parentTeacherConversation.index',['data'=>$Complaints]);
            }    
    
            public function view(Request $request){
                $Complaints =  Complaint::with('Admission')->with('ClassType')->where('session_id',Session::get('session_id'))
		        ->where('branch_id',Session::get('branch_id'));
		        if(Session::get('role_id') == 3){
		               $Complaints =$Complaints->where('teacher_id_to_complaint',null);  
		        }
		        $Complaints =$Complaints->get();
                return view('dashboard.student.complaint.view',['data'=>$Complaints]);
            }    
    
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
                    return redirect::to('complaint_view')->with('message', 'Complaint added Successfully.');
                }
                return view('dashboard.student.complaint.add');
            } 
            
            public function delete(Request $request){
                $complaint = Complaint::find($request->delete_id)->delete();
                return redirect::to('complaint_view')->with('message', 'complaint Deleted Successfully.');
            }
            
            public function sendConversation(Request $request){
                $oldData = Complaint::find($request->complaint_id);
                if(!empty($oldData)){
                    $oldChat = $oldData->chat;
                    $phpArray = json_decode($oldChat, true);
                    $newData = [$request->user_id.'' => $request->message];
                    $phpArray[] = $newData;
                    $viewStatus = [  $request->admin_id => $request->admin_status, 
                    $request->teacher_id => $request->teacher_status, 
                    $request->student_id => $request->student_status ];
                    $updatedJsonData = json_encode($phpArray);
                    $oldData->chat = $updatedJsonData;
                    $oldData->view_status = json_encode($viewStatus);
                    $oldData->save();
                    return Response::json(array('data' => $updatedJsonData)); 
                }
                else{
                $newData = [$request->user_id.'' => $request->message];
                    $phpArray[] = $newData;
                    $updatedJsonData = json_encode($phpArray);
                    $viewStatus = [  1 => 0, 
                    $request->teacher_id => 0, 
                    Session::get('id') => 1 ];
                    $add = new Complaint;
                    $add->user_id = Session::get('id');
                    $add->session_id = Session::get('session_id');
                    $add->branch_id = Session::get('branch_id');
            		//$add->subject =$request->subject;
            		//$add->description =$request->description;
            		$add->admission_id =Session::get('id');
            		$add->date =date('Y-m-d');
                    $add->class_type_id = Session::get('class_type_id');
                    $add->chat = $updatedJsonData;
                    $add->view_status = json_encode($viewStatus);
                    $add->teacher_id_to_complaint = $request->teacher_id ?? '';
            		$add->save();
		            return Response::json(array('data' => $updatedJsonData)); 
                }
            }
            
            public function complaintAction(Request $request){
                if($request->isMethod('post')){
                    $request->validate([]);
                    $data = Complaint::where('id',$request->complaint_id)->update(['admin_action'=>$request->admin_action]);
                    return redirect::to('complaint_view')->with('message', 'Action Submitted Successfully.');
                }  
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
                    /* if(!empty($adminMail['email'])){
                        $emailData = ['email'=>$adminMail['email'],'subject1'=>$request->subject, 
                        'description'=>$request->description,'first_name'=>$student['first_name'],'class'=>$student['ClassTypes']['name'],'section'=>$student['Section']['name'], 'date'=>date('d-m-Y', strtotime($data->date)), 'father_mobile'=>$student['father_mobile'],
                        'father_name'=>$student['father_name'],'last_name'=>$student['last_name'],'subject' => 'Complaint Sent Seccessfully'];
                        Helper::sendMail('email_print.complaint',$emailData);
                    }	*/
                    return redirect::to('complaint_view')->with('message', 'Complaint Updated Successfully.');
                }
                return view('dashboard.student.complaint.edit',['data'=>$data]);
            } 

}
