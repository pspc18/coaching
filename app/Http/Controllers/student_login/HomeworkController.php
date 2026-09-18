<?php

namespace App\Http\Controllers\student_login;
use Illuminate\Validation\Validator; 
use App\Models\User;
use App\Models\Master\Homework;
use App\Models\Master\HourlyHomework;
use App\Models\Master\UploadHomework;
use App\Models\Master\HomeworkDocuments;
use App\Models\SmsSetting;
use App\Models\Master\MessageTemplate;
use App\Models\Master\MessageType;
use App\Models\Master\TeacherSubject;
use App\Models\Master\Branch;
use App\Models\Setting;
use App\Models\Subject;
use App\Models\Student;
use App\Models\Admission;
use App\Models\Teacher;
use App\Helpers\helper;
use Session;
use Hash;
use Str;
use File;
use Redirect;
use Response;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Image;
class HomeworkController extends Controller

{

          public function view(Request $request){
                $homework = Homework::select('homeworks.*')->with('Subject')->with('ClassType')->with('Teacher')
    		    ->leftjoin('admissions as admission','admission.class_type_id','homeworks.class_type_id')
    		    ->where('homeworks.session_id',Session::get('session_id'))
    		    ->where('homeworks.branch_id',Session::get('branch_id'))->where('homeworks.class_type_id',Session::get('class_type_id'))
                ->whereDate('homeworks.homework_issue_date','<=', date('Y-m-d'));
        		$allhomework = $homework->groupBy('homeworks.id')->orderBy('id','DESC')->get();
                return view('student_login.homework.index',['data'=>$allhomework]);
            
            }

            public function uploadHomework(Request $request){
                $adminMail = User::where('role_id','1')->get()->first();
                $teacherMail = Teacher::where('class_type_id',Session::get('class_type_id'))->get()->first();
                $stu = Admission::where('id',Session::get('id'))->get()->first();
                if($request->isMethod('post')){
                    $request->validate([]);
                    $uploadHW = new UploadHomework;//model name
                    $uploadHW->user_id = Session::get('id');
                    $uploadHW->session_id = Session::get('session_id');
                    $uploadHW->admission_id = Session::get('id');
                    $uploadHW->branch_id = Session::get('branch_id');
            		$uploadHW->class_type_id = Session::get('class_type_id');
            		$uploadHW->submission_date  = date('Y-m-d');
            		$uploadHW->message  = $request->message;
            		$uploadHW->homework_id  = $request->homework_id;
                    $uploadHW->save();
                    $upload_hw_id = $uploadHW->id;
                    for ($count = 0; $count <= count($request->content_file); $count++) {
                        if (isset($request->content_file[$count])) {   
                            $uploadDocument = new HomeworkDocuments;//model name
                            if ( $request->file('content_file')[$count]) {
                                $image =  $request->file('content_file')[$count];
                                $document =  uniqid() . '.' . $image->getClientOriginalName();
                                $destinationPath = env('IMAGE_UPLOAD_PATH') . 'uploadHomework/';
                                if (!file_exists($destinationPath)) {
                                    mkdir($destinationPath, 0755, true);
                                }
                                $compressedImage = Image::make($image)
                                    ->resize(300, null, function ($constraint) {
                                    $constraint->aspectRatio();
                                    $constraint->upsize();
                                })
                                ->encode('jpg', 10); // Adjust quality as needed
                                $compressedImage->save($destinationPath . $document);
                                $image->move($destinationPath, $document);
                            }
                            $uploadDocument->user_id = Session::get('id');
                            $uploadDocument->session_id = Session::get('session_id');
                            $uploadDocument->branch_id = Session::get('branch_id');   
                            $uploadDocument->admission_id = Session::get('id');
                            $uploadDocument->upload_hw_id  = $upload_hw_id;
                            $uploadDocument->content_file  = $document;
                            $uploadDocument->save(); 
                        }
                    }  
                    if(Session::get('role_id') == 3){
                        return redirect::to('student_homework')->with('message', ' Assignment Upload Successfully.');
                    }else{
                        return redirect::to('homework/index')->with('message', ' Assignment Upload Successfully.');
                    }
                }
            }    

            public function homeworkDetails(Request $request, $id){
                $search['name'] = $request->name;
                $students = UploadHomework::select('upload_homeworks.*')->with('Admission')->with('ClassType')
                ->where('upload_homeworks.session_id',Session::get('session_id'))->where('upload_homeworks.branch_id',Session::get('branch_id'))->where('homework_id',$id)
                ->leftjoin('admissions as Admission','Admission.id','upload_homeworks.admission_id');
    		       if($request->isMethod('post')){
    		           if(!empty($request->name)){
            		        $students = $students->where('Admission.first_name', 'LIKE', '%'.$request->name.'%')
		                    ->orWhere('Admission.last_name', 'LIKE', '%'.$request->name.'%')
		                    ->orWhere('Admission.mobile', 'LIKE', '%'.$request->name.'%')
		                    ->orWhere('Admission.email', 'LIKE', '%'.$request->name.'%')
		                    ->orWhere('Admission.aadhaar', 'LIKE', '%'.$request->name.'%')
		                    ->orWhere('Admission.father_name', 'LIKE', '%'.$request->name.'%')
		                    ->orWhere('Admission.mother_name', 'LIKE', '%'.$request->name.'%')
		                    ->orWhere('Admission.address', 'LIKE', '%'.$request->name.'%');
    		           }
    	        }
		       
		    	    $students = $students->where('upload_homeworks.class_type_id',Session::get('class_type_id'))->where('admission_id',Session::get('id'))->orderBy('id','DESC')
		       ->groupBy('admission_id')->groupBy('homework_id')->get();
	                return view('student_login.homework.details',['students'=>$students, 'id'=>$id, 'search'=>$search]);
	           
            }

            public function particularHomeworkDetails(Request $request){
                $data = UploadHomework::with('Admission')->where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))->where('admission_id',$request->admission_id)->where('homework_id',$request->homework_id)->orderBy('id','DESC')->get();
                return view('student_login.homework.data_homework',['data'=>$data]);
            }

          
    
           
    
}
