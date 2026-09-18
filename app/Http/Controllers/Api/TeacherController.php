<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Admission;
use App\Models\StudentAttendance;
use App\Models\BillCounter;
use App\Models\Setting;
use App\Models\Teacher;
use App\Models\TeacherDocuments;
use App\Models\SalaryDocument;
use App\Models\PermissionManagement;
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


class TeacherController extends BaseController
{
    
        
    public function getAllTeacherCount(Request $request){
        if($request->isMethod('post')){
          try{
	     $teacher = Teacher::select('teachers.*','doc.photo','class.name as class_name')
                    ->leftJoin('teacher_documents as doc','doc.teacher_id','teachers.id')
                    ->leftJoin('class_types as class','class.id','teachers.class_type_id')
                   ->where('teachers.drop_status',0)->get();
	         	if ($teacher) {
	         	    
	         	      $data = array();
            foreach ($teacher as $key => $item) {
                $data[] = array(
                    'key' => $key+1,
                    'id' => $item->id,
                    'name' => $item->first_name.' '.$item->last_name,
                    'class_name' => $item->class_name,
                    'subject' => $item->teaching_subject,
                    'photo' => $item->photo != '' ? env('IMAGE_SHOW_PATH').'profile/'.$item->photo : '',
                );
            }
	   
	         	    
	return response()->json(['status' => true, 'message' => 'Data Found','data'=>$data], 200);
		} else {
			return response()->json(['status' => false, 'message' => 'No Data Found'], 200);
		}

	     } catch (Exception $e) {
            return $this->sendError('Validation Error.', 'Error');
        }
        
    }
    }
	public function teacherList(Request $request)
	{
	     try{
	    $teacher = User::where('role_id',2);
	    
	     $value = $request->searchValue;
		     if(!empty($value)){
        		$teacher = $teacher->where(function($query) use ($value){
        		        $query->where('first_name', 'LIKE', '%'.$value.'%');
        		        $query->orWhere('last_name', 'LIKE', '%'.$value.'%');
        		        $query->orWhere('father_name', 'LIKE', '%'.$value.'%');
        		        $query->orWhere('mobile', 'LIKE', '%'.$value.'%');
        		        $query->orWhere('email', 'LIKE', '%'.$value.'%');
        		});    
		    }
       
       $teacher = $teacher->get();
	         	if ($teacher) {
	         	    
	         	      $data = array();
            foreach ($teacher as $key => $item) {
                $data[] = array(
                    'key' => $key+1,
                    'id' => $item->id,
                    'name' => $item->first_name,
                    'fatherName' => $item->father_name,
                    'teacher_id' => $item->teacher_id,
                );
            }
	   
	         	    
	return response()->json(['status' => true, 'message' => 'Data Found','data'=>$data], 200);
		} else {
			return response()->json(['status' => false, 'message' => 'No Data Found'], 200);
		}

	     } catch (Exception $e) {
            return $this->sendError('Validation Error.', 'Error');
        }
	}

public function teacherDelete(Request $request)
	{
	    try{
	   
	   $delete_id = $request->delete_id;
	   
	   $delete_data = User::where('id',$delete_id)->delete();
           
	   return response()->json(['status' => true, 'message' => 'Teacher Deleted Successfully'], 200);
        } catch (Exception $e) {
            return $this->sendError('Validation Error.', 'Error');
        }
	    
	}
	
	
	
	public function studentEdit(Request $request ,$id)
	{
	     try{
	
           $data = Admission::where('id',$id)->first();
           
            if ($request->isMethod('post')) {
                $data = Admission::find($id);
                
               
                
                     $data->roll_no = $request->roll_no;
            $data->admission_date = $request->admission_date;
            $data->section_id = $request->section_id;
            $data->admission_type_id = $request->admission_type_id;
            $data->class_type_id = $request->classType;
            $data->first_name = $request->firstName;
            $data->last_name = $request->lastName;
            $data->aadhaar = $request->adharNo;
            $data->email = $request->email;
            $data->mobile = $request->mobile;
            $data->father_name = $request->fatherName;
            $data->mother_name = $request->motherName;
            $data->father_mobile = $request->fatherMobile;
            $data->dob = $request->dob;
            $data->gender_id = $request->gender;
            $data->admission_type_id = $request->admission_type_id;
            $data->address = $request->address;
            $data->country_id = $request->country;
            $data->village_city = $request->village_city;
            $data->city_id = $request->city;
            $data->state_id = $request->state;
            $data->pincode = $request->pincode;
            $data->image = $request->image;
            // $addadmission->image = $student_image;
        
        
            //$addadmission->father_img = $father_image;
           // $addadmission->mother_img = $mother_image;
       // $addadmission->stream_subject = $request->streamSubjects== '' ? null : implode(',', $request->streamSubjects);
            $data->remark_1 = $request->remark_1;
            $data->userName = $request->mobile;
           
            $data->save();
                
            }
	     return $this->sendResponseData($data, 'success');
        } catch (Exception $e) {
            return $this->sendError('Validation Error.', 'Error');
        }
	    
	}
public function studentList(Request $request)
	{
	     try{
	    $admissionNo = $request->admissionNo;
	    $section_id = $request->section_id;
	    $class_type_id = $request->class_type;
	    
	    $list = Admission::where('session_id',1);
	    
	    if(!empty($admissionNo))
	    {
	        $list = $list->where('admissionNo',$admissionNo);
	    }
	    
	    if(!empty($section_id))
	    {
	         $list = $list->where('section_id',$section_id);
	    }
	    
	    if(!empty($class_type_id))
	    {
	         $list = $list->where('class_type_id',$class_type_id);
	    }
	    
	    $list= $list->get();
	    
	    $data = array();
            foreach ($list as $key => $item) {
                $data[] = array(
                    'key' => $key+1,
                    'id' => $item->id,
                    'name' => $item->first_name,
                    'fatherName' => $item->father_name,
                );
            }
	   
           
	     return $this->sendResponseData($data, 'success');
        } catch (Exception $e) {
            return $this->sendError('Validation Error.', 'Error');
        }
	    
	}


	public function teacherEdit(Request $request ,$id)
	{
	     try{
	
        //   $data = User::where('id',$id)->where('role_id',2)->first();
              $data =  User::Select('users.*','teacher_documents.Id_proof_img','teacher_documents.qualification_proof_img','teacher_documents.experience_letter_img')
                        ->leftjoin('teacher_documents','users.teacher_id','teacher_documents.teacher_id')->where('users.teacher_id',$id)->where('users.role_id',2)->first();
           
            if ($request->isMethod('post')) {    
                $teacher =Teacher::find($id);
     $data_user = User::where('teacher_id',$id)->get()->first();
     $data_user_doc = TeacherDocuments::where('teacher_id',$id)->get()->first();

    
        if($request->isMethod('post')){
    //           $request->validate([

    //           'UniqueId' => 'required',
    //           'first_name' => 'required',
    //           'last_name' => 'required',
    //           'gender_id' => 'required',
    //           'email' => 'required',
    //           'aadhaar' => 'required',
    //           'dob' => 'required',
    //           'mobile' => 'required',
    //           'father_name' => 'required',
    //           'qualification'  => 'required',
    //           'userName'  => 'required',
    //           'password' => 'required|min:4',
    //          ]);
    //   $photo =null;
    //             if($request->file('photo')){
    //              $image = $request->file('photo');
    //             $path = $image->getRealPath();      
    //             $photo =  time().uniqid().$image->getClientOriginalName();
    //             $destinationPath = env('IMAGE_UPLOAD_PATH').'profile';
    //             $image->move($destinationPath, $photo);
    //              if(File::exists(env('IMAGE_UPLOAD_PATH').'profile/'.$data_user->image)){
    //                     File::delete(env('IMAGE_UPLOAD_PATH').'profile/'.$data_user->image);
    //                 }      
                  $data_user->image = $request->image;

    //          }
           
    //     $experience_letter =null;
    //             if($request->file('experience_letter')){
    //              $image = $request->file('experience_letter');
    //             $path = $image->getRealPath();      
    //             $experience_letter =  time().uniqid().$image->getClientOriginalName();
    //             $destinationPath = env('IMAGE_UPLOAD_PATH').'teacher/teacher_'.$request->UniqueId.'/experience_letter';
    //             $image->move($destinationPath, $experience_letter);
               
                $data_user_doc->experience_letter_img =$request->experience;
    //          }
    //     $qualification_proof =null;
    //             if($request->file('qualification_proof')){
    //              $image = $request->file('qualification_proof');
    //             $path = $image->getRealPath();      
    //             $qualification_proof =  time().uniqid().$image->getClientOriginalName();
    //             $destinationPath = env('IMAGE_UPLOAD_PATH').'teacher/teacher_'.$request->UniqueId.'/qualification_proof';
    //             $image->move($destinationPath, $qualification_proof);
               $data_user_doc->qualification_proof_img =$request->qualificationPhoto;
    //          }
    //     $id_proof =null;
    //             if($request->file('id_proof')){
    //              $image = $request->file('id_proof');
    //             $path = $image->getRealPath();      
    //             $id_proof =  time().uniqid().$image->getClientOriginalName();
    //             $destinationPath = env('IMAGE_UPLOAD_PATH').'teacher/teacher_'.$request->UniqueId.'/id_proof';
    //             $image->move($destinationPath, $id_proof);
               $data_user_doc->Id_proof_img =$request->idProof;
    //          }
    
        
        $teacher->session_id = 1;
        $teacher->branch_id = 1;
     //   $teacher->UniqueId = $request->UniqueId;
       // $teacher->userName = $request->userName;
		$teacher->first_name =$request->first_name;
		$teacher->last_name = $request->last_name;
		$teacher->gender_id = $request->gender_id;
		$teacher->dob =$request->dob;
		$teacher->father_name =$request->father_name;
		$teacher->class_type_id=$request->class_type_id;
		$teacher->email =$request->email;
		$teacher->mobile =$request->mobile;
		$teacher->address =$request->address;
		$teacher->qualification =$request->qualification;
		$teacher->blood_group =$request->blood_group;
		$teacher->teacher_categorie_id =$request->category_id;
	    $teacher->casual_leave =$request->casual_leave;
		$teacher->medical_leave =$request->medical_leave;
		$teacher->other_leave =$request->other_leave;
		$teacher->refer_name =$request->refer_name;
        $teacher->refer_address =$request->refer_address;
        $teacher->refer_mobile =$request->refer_mobile;
        $teacher->pan_no =$request->pan_card;
		$teacher->aadhaar =$request->aadhaar;
		$teacher->bank_name =$request->bank;
		$teacher->account_no =$request->account_no;
		$teacher->ifsc_code =$request->ifsc_code;
		$teacher->salary =$request->salary;
        $teacher->save();
        
        $data_user->session_id = 1;
        $data_user->branch_id = 1;
        $data_user->teacher_id = $id;
        $data_user->userName = $request->userName;
		$data_user->email  = $request->email;
		$data_user->mobile  = $request->mobile;
		$data_user->password  =  Hash::make($request->mobile);
		$data_user->role_id  = 2;
        $data_user->dob = $request->dob;
        $data_user->save();
        
        $data_user_doc->teacher_id = $id;
        $data_user_doc->session_id = 1;
        $data_user_doc->branch_id = 1;
        $data_user_doc->joining_date =$request->joining_date;
        $data_user_doc->referral_name =$request->refer_name;
        $data_user_doc->save();

   	   return response()->json(['status' => true, 'message' => 'Teacher Updated Successfully'], 200);
        }         
    
     
                
            }
	     return $this->sendResponseData($data, 'success');
        } catch (Exception $e) {
            return $this->sendError('Validation Error.', 'Error');
        }
	    
	}
	
	 public function teacherAdd(Request $request){
        $BillCounter = BillCounter::where('type','Teacher')->get()->first();
        
        if(Session::get('role_id') > 1){
            $BillCounter = $BillCounter->where('branch_id',Session::get('branch_id'));
        }
      
        if($request->isMethod('post')){

                // $request->validate([

                // 'UniqueId' => 'required',
                // 'first_name' => 'required',
                // 'last_name' => 'required',
                // 'gender_id' => 'required',
                // 'email' => 'required',
                // 'aadhaar' => 'required|unique:teachers,aadhaar',
                // 'dob' => 'required',
                // 'mobile' => 'required|unique:teachers,mobile',
                // 'father_name' => 'required',
                // 'qualification'  => 'required',
                // 'userName'  => 'required|unique:users,userName',
                // 'password' => 'required|min:4'
                
                // ]);
             
        // $photo = null;
        //         if($request->file('photo')){
        //          $image = $request->file('photo');
        //         $path = $image->getRealPath();      
        //         $photo =  time().uniqid().$image->getClientOriginalName();
        //         $destinationPath = env('IMAGE_UPLOAD_PATH').'profile';
        //         $image->move($destinationPath, $photo);     
        //      }
           
        // $experience_letter = null;
        //         if($request->file('experience_letter')){
        //          $image = $request->file('experience_letter');
        //         $path = $image->getRealPath();      
        //         $experience_letter =  time().uniqid().$image->getClientOriginalName();
        //         $destinationPath = env('IMAGE_UPLOAD_PATH').'teacher/teacher_'.$request->UniqueId.'/experience_letter';
        //         $image->move($destinationPath, $experience_letter); 
        //      }
        // $qualification_proof = null;
        //         if($request->file('qualification_proof')){
        //          $image = $request->file('qualification_proof');
        //         $path = $image->getRealPath();      
        //         $qualification_proof =  time().uniqid().$image->getClientOriginalName();
        //         $destinationPath = env('IMAGE_UPLOAD_PATH').'teacher/teacher_'.$request->UniqueId.'/qualification_proof';
        //         $image->move($destinationPath, $qualification_proof);     
        //      }
        // $id_proof = null;
        //         if($request->file('id_proof')){
        //          $image = $request->file('id_proof');
        //         $path = $image->getRealPath();      
        //         $id_proof =  time().uniqid().$image->getClientOriginalName();
        //         $destinationPath = env('IMAGE_UPLOAD_PATH').'teacher/teacher_'.$request->UniqueId.'/id_proof';
        //         $image->move($destinationPath, $id_proof);     
        //      }
            
        $counter = !empty($BillCounter->counter) ? $BillCounter->counter : 0 ;
        $BillCounter->counter = $counter + 1 ;
        $BillCounter->save();
             
        $teacher = new Teacher;//model name
        $teacher->session_id = 1;
        $teacher->branch_id = 1;
        $teacher->UniqueId = $counter + 1;
        $teacher->userName = $request->userName;
        $teacher->first_name = $request->first_name;
        $teacher->last_name = $request->last_name;
        $teacher->password = $request->password;
		$teacher->gender_id =$request->gender_id;
		$teacher->dob =$request->dob;
		$teacher->joining_date =$request->joining_date;
		$teacher->refer_name =$request->refer_name;
		$teacher->refer_address =$request->refer_address;
		$teacher->refer_mobile =$request->refer_mobile;
		$teacher->father_name =$request->father_name;
		$teacher->class_type_id=$request->class_type_id;
		$teacher->section_id=$request->section_id;
		$teacher->email =$request->email;
		$teacher->mobile =$request->mobile;
		$teacher->address =$request->address;
		$teacher->qualification =$request->qualification;
		$teacher->medical_leave =$request->medical_leave;
		$teacher->casual_leave =$request->casual_leave;
		$teacher->other_leave =$request->other_leave;
		$teacher->aadhaar =$request->aadhaar;
		$teacher->pan_no =$request->pan_card;
		$teacher->bank_name =$request->bank;
		$teacher->account_no =$request->account_no;
		$teacher->ifsc_code =$request->ifsc_code;
		$teacher->salary =$request->salary;
        $teacher->save();
        
        
        $teacher_id =$teacher->id;
        $teacher_us = new User;//model name
        $teacher_us->session_id = 1;
        $teacher_us->branch_id = 1;
        $teacher_us->teacher_id = $teacher_id;
		$teacher_us->first_name = $request->first_name;
		$teacher_us->last_name = $request->last_name;
		$teacher_us->email  = $request->email;
		$teacher_us->mobile  = $request->mobile;
		$teacher_us->role_id  = 2;
        $teacher_us->dob = $request->dob;
        $teacher_us->image = $request->image;
        $teacher_us->father_name = $request->father_name;
        $teacher_us->address = $request->address;
        $teacher_us->gender =$request->gender_id;
        $teacher_us->status  = 1;
	    $teacher_us->userName = $request->userName;
		$teacher_us->password = Hash::make($request->password);
		$teacher_us->confirm_password  = $request->password;
        $teacher_us->save();
        $user_id = $teacher_us->id;
        



        $document_upl  = new  TeacherDocuments;//model name
        $document_upl->session_id =1;
        $document_upl->branch_id = 1;
        $document_upl->teacher_id = $teacher_id;
        $document_upl->user_id = $user_id;
        $document_upl->joining_date =$request->joining_date;
        $document_upl->referral_name =$request->referral_name;
        $document_upl->photo = $request->image;
        $document_upl->Id_proof_img = $request->idProof;
        $document_upl->qualification_proof_img =$request->qualificationPhoto;
        $document_upl->experience_letter_img =$request->experience;
        $document_upl->save();

	    $PermissionManagement = new PermissionManagement; //model name
        $PermissionManagement->session_id = 1;
        $PermissionManagement->branch_id = 1;
        $PermissionManagement->reg_user_id = $user_id;
        $PermissionManagement->sidebar_id = 1;
        $PermissionManagement->save();

            
       
    	   return response()->json(['status' => true, 'message' => 'Teacher Added Successfully'], 200);
        }
        
    }

}