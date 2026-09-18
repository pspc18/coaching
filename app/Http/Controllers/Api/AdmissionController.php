<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\WebUser;
use App\Models\User;
use App\Models\Admission;
use App\Models\ClassType;
use App\Models\BillCounter;
use App\Models\UserDocument;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use App\Models\Wallet;
use App\Models\WalletDetail;
use App\Models\ForgotOtps;
use App\Models\NewsLetter;
use App\Models\EmailTamplate;
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


class AdmissionController extends BaseController
{

	public function studentAdd(Request $request)
	{
	    
	     
	
	    try{
	    $BillCounter = BillCounter::where('session_id',1)->where('branch_id',1)->where('type', 'StudentAdmission')->get()->first();
        if (!empty($BillCounter)) {
            $counter = !empty($BillCounter->counter) ? $BillCounter->counter : 0;
            $BillCounterNo = $counter + 1;
        }
        
      
	   //  $student_image = '';
    //         if ($request->hasFile('student_img')) {
    //             $image = $request->file('student_img');
    //             $path = $image->getRealPath();
    //             $student_image = time() . uniqid() . $image->getClientOriginalName();
    //             $destinationPath = env('IMAGE_UPLOAD_PATH') . 'profile';
    //             $image->move($destinationPath, $student_image);
    //         }
            
                 $counter = !empty($BillCounter->counter) ? $BillCounter->counter : 0;
            $BillCounter->counter = $counter + 1;
            $BillCounter->save();
            
      $addadmission = new Admission(); //model name
            $addadmission->user_id = 1;
            $addadmission->session_id = 1;
            $addadmission->branch_id = 1;
            $addadmission->admissionNo = $BillCounter->counter;
            $addadmission->school = '1';
            $addadmission->library = '0';
            $addadmission->hostel = '0';
            $addadmission->roll_no = $request->roll_no;
            $addadmission->admission_date = $request->admission_date;
            $addadmission->section_id = $request->section_id;
            $addadmission->admission_type_id = $request->admission_type_id;
            $addadmission->class_type_id = $request->classType;
            $addadmission->first_name = $request->firstName;
            $addadmission->last_name = $request->lastName;
            $addadmission->aadhaar = $request->adharNo;
            $addadmission->email = $request->email;
            $addadmission->mobile = $request->mobile;
            $addadmission->father_name = $request->fatherName;
            $addadmission->mother_name = $request->motherName;
            $addadmission->father_mobile = $request->fatherMobile;
            $addadmission->dob = $request->dob;
            $addadmission->gender_id = $request->gender;
            $addadmission->admission_type_id = $request->admission_type_id;
            $addadmission->address = $request->address;
            $addadmission->country_id = $request->country;
            $addadmission->village_city = $request->village_city;
            $addadmission->city_id = $request->city;
            $addadmission->state_id = $request->state;
            $addadmission->pincode = $request->pincode;
            $addadmission->image = $request->image;
            // $addadmission->image = $student_image;
        
        
            //$addadmission->father_img = $father_image;
           // $addadmission->mother_img = $mother_image;
        //$addadmission->stream_subject = implode(',', $request->streamSubjects);
            $addadmission->remark_1 = $request->remark_1;
            $addadmission->userName = $request->mobile;
            $addadmission->password = Hash::make('12345678');
            $addadmission->confirm_password = '12345678';
            $addadmission->save();
           
	     return $this->sendResponseData($addadmission, 'success');
        } catch (Exception $e) {
            return $this->sendError('Validation Error.', 'Error');
        }
	}

public function studentDelete(Request $request)
	{
	    try{
	   
	   $delete_id = $request->delete_id;
	   
	   $delete_data = Admission::where('id',$delete_id)->delete();
           
	   return response()->json(['status' => true, 'message' => 'Admission Deleted Successfully'], 200);
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
	    

	    
	$list=    Admission::Select('admissions.*','class_types.name as class_name')
                            ->leftjoin('class_types','class_types.id','admissions.class_type_id');
                   
	    
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
                    'admissionNo' => $item->admissionNo,
                    'id' => $item->id,
                    'name' => $item->first_name,
                    'last_name' => $item->last_name,
                    'fatherName' => $item->father_name,
                    'mobile' => $item->mobile,
                    'class_name' => $item->class_name,
                    'address' => $item->address,
                );
            }
	   
           
	     return $this->sendResponseData($data, 'success');
        } catch (Exception $e) {
            return $this->sendError('Validation Error.', 'Error');
        }
	    
	}


  public function studentPromoteAdd(Request $request)
    {

        if ($request->isMethod('post')) {
            $request->validate([
              
            ]);

                   
        
            if(!empty($request->admission_ids))
            {
                foreach($request->admission_ids as $key => $item)
                {
                    
               $classType = Admission::where('id', $item)->first();
                 $class =ClassType::where('id',$classType->class_type_id+1)->get();

                    if(count($class) > 0)
                    {
                        
                     
                        $BillCounter = BillCounter::where('type', 'StudentAdmission')
                        ->where('session_id',4)->get()->first();
                        $counter = !empty($BillCounter->counter) ? $BillCounter->counter : 0;
                        $BillCounter->counter = $counter + 1;
                        $BillCounter->save();
                        $oldRow = Admission::find($item);
                        $newRow = $oldRow->replicate();
                        $newRow->admissionNo = $BillCounter->counter;
                        $newRow->session_id = 4;
                        $newRow->class_type_id = $classType->class_type_id+1;
                        $newRow->save();
                    }
                    else{
                        
                       return response()->json(['status' => false, 'message' => 'Something Went Wrong'], 200);
                        break;
                       
                    }
                  
                }
               return response()->json(['status' => true, 'message' => 'Student Promoted Successfully'], 200);
            }

        }


        
              
    }

}