<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\BillCounter;
use App\Models\Enquiry;
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


class EnquiryController extends BaseController
{
    
    public function enquiryAdd(Request $request){
    
    $data_request = $request ->json()->all();
    
      try{
	 $BillCounter = BillCounter::where('session_id',3)->where('type', 'StudentRegistration')->get()->first();
        
        // if(Session::get('role_id') > 1){
        //   $BillCounter = $BillCounter->where('branch_id',Session::get('branch_id'));
        // }
        
        if (!empty($BillCounter)) {
            $counter = !empty($BillCounter->counter) ? $BillCounter->counter : 0;
            $BillCounterNo = $counter + 1;
        }
       
        if ($request->isMethod('post')) {

         
          
            $counter = !empty($BillCounter->counter) ? $BillCounter->counter : 0;
            $BillCounter->counter = $counter + 1;
            $BillCounter->save();

            $addstudents = new Enquiry; //model name
            $addstudents->user_id = 1;
            $addstudents->session_id = 3;
            $addstudents->branch_id = 1;
            $addstudents->registration_no = $BillCounter->counter;
            //$addstudents->userName = $request->mobile;
            $addstudents->first_name = $request->first_name;
            $addstudents->last_name = $request->last_name;
            $addstudents->aadhaar = $request->aadhaar;
            $addstudents->email  = $request->email;
            $addstudents->mobile  = $request->mobile;
            $addstudents->class_type_id = $request->class_type_id;
            $addstudents->father_name = $request->father_name;
            $addstudents->mother_name = $request->mother_name;
            $addstudents->father_mobile = $request->father_mobile;
            $addstudents->mother_mobile = $request->mother_mobile;
            $addstudents->id_proof_id = $request->id_proof_id;
            $addstudents->id_proof_no = $request->id_proof_no;
            $addstudents->dob = $request->dob;
            $addstudents->country_id = $request->country_id;
            $addstudents->gender_id = $request->gender_id;
            $addstudents->admission_type_id = $request->admission_type_id;
            $addstudents->registration_date = $request->registration_date;
            $addstudents->address = $request->address;
            $addstudents->village_city = $request->village_city;
            $addstudents->city_id = $request->city_id;
            $addstudents->state_id = $request->state_id;
            $addstudents->pincode = $request->pincode;
            $addstudents->remark_1 = $request->remark_1;
            //$addstudents->password  = Hash::make('12345678'); 
            //$addstudents->confirm_password  = '12345678';
            $addstudents->refer_name = $request->refer_name;
            $addstudents->refer_mobile = $request->refer_mobile;
            $addstudents->refer_address = $request->refer_address;
            $addstudents->save();
          
           
        return $this->sendResponseData($addstudents, 'success');
            }
	    
        } catch (Exception $e) {
            return $this->sendError('Validation Error.', 'Error');
        }
    
      
        }

	    public function enquiryView(Request $request)
    {

        $search['enquiry_status'] = $request->enquiry_status;
        $search['state_id'] = $request->state_id;
        $search['city_id'] = $request->city_id;
        $search['class_type_id'] = $request->class_type_id;
        $search['name'] = $request->name;
        $search['reminder_date'] = $request->reminder_date;

        $data =  Enquiry::Select('enquirys.*', 'class_types.name as class_name')
            ->leftjoin('class_types', 'class_types.id', 'enquirys.class_type_id')
            ->where('enquirys.session_id', Session::get('session_id'));
            
        if (Session::get('role_id') > 1) {
            $data = $data->where('branch_id', Session::get('branch_id'));
        }
     
        if ($request->isMethod('post')) {
            $request->validate([]);
            if (!empty($request->name)) {
                $value = $request->name;
                $allstudents = $data = $data->where(function ($query) use ($value) {
                    $query->where('enquirys.first_name', 'like', '%' . $value . '%');
                    $query->orWhere('enquirys.last_name', 'like', '%' . $value . '%');
                    $query->orWhere('enquirys.registration_no', 'like', '%' . $value . '%');
                    $query->orWhere('enquirys.mobile', 'like', '%' . $value . '%');
                    $query->orWhere('enquirys.aadhaar', 'like', '%' . $value . '%');
                    $query->orWhere('enquirys.email', 'like', '%' . $value . '%');
                    $query->orWhere('enquirys.father_name', 'like', '%' . $value . '%');
                    $query->orWhere('enquirys.mother_name', 'like', '%' . $value . '%');
                    $query->orWhere('enquirys.address', 'like', '%' . $value . '%');
                });
            }
            if (!empty($request->state_id)) {
                $data = $data->where("enquirys.state_id", $request->state_id);
            }
            if (!empty($request->city_id)) {
                $data = $data->where("enquirys.city_id", $request->city_id);
            }
            if (!empty($request->enquiry_status)) {
                $data = $data->where("enquirys.enquiry_status", $request->enquiry_status);
            }
            if (!empty($request->reminder_date)) {
                $data = $data->whereDate("enquirys.reminder_date", $request->reminder_date);
            }
            if (!empty($request->class_type_id)) {
                $data = $data->where("enquirys.class_type_id", $request->class_type_id);
            }
        }

        $allstudents = $data->orderBy('id', 'DESC')->get();
        if($request->pdf == "pdf"){
            $allstudents = $data->orderBy('id', 'DESC')->get();
              $pdf = PDF::loadView('print_file.pdf.registration_list',['data'=>$allstudents]);
              return $pdf->download('student_registration.pdf');
        }
        return view('students.enquiry.view', ['data' => $allstudents, 'search' => $search]);
    }
public function enquiryEdit(Request $request,$id)
	{
	    	
	     try{
	
           $data = Enquiry::where('id',$id)->first();
           
            if ($request->isMethod('post')) {
                $data = Enquiry::find($id);
                
              
                    $data->user_id = 1;
            $data->session_id = 1;
            $data->branch_id = 1;
            $data->first_name = $request->first_name;
            $data->last_name = $request->last_name;
            $data->aadhaar = $request->aadhaar;
            $data->email  = $request->email;
            $data->mobile  = $request->mobile;
            $data->class_type_id = $request->class_type_id;
            $data->father_name = $request->father_name;
            $data->mother_name = $request->mother_name;
            $data->father_mobile = $request->father_mobile;
            $data->mother_mobile = $request->mother_mobile;
            $data->id_proof_id = $request->id_proof_id;
            $data->id_proof_no = $request->id_proof_no;
            $data->dob = $request->dob;
            $data->country_id = $request->country_id;
            $data->gender_id = $request->gender_id;
            $data->admission_type_id = $request->admission_type_id;
            $data->registration_date = $request->registration_date;
            $data->address = $request->address;
            $data->village_city = $request->village_city;
            $data->city_id = $request->city_id;
            $data->state_id = $request->state_id;
            $data->pincode = $request->pincode;
            $data->remark_1 = $request->remark_1;
            $data->refer_name = $request->refer_name;
            $data->refer_mobile = $request->refer_mobile;
            $data->refer_address = $request->refer_address;
            $data->save();
                
            }
	     return $this->sendResponseData($data, 'success');
        } catch (Exception $e) {
            return $this->sendError('Validation Error.', 'Error');
        }
	    
	
	}

	

public function enquiryList(Request $request)
	{
	     try{
	    $remainder_date = $request->remainder_date;
	    $status = $request->status;
	    $class_type_id = $request->class_type;
	    

	    
	$list=    Enquiry::Select('enquirys.*','class_types.name as class_name')
                            ->leftjoin('class_types','class_types.id','enquirys.class_type_id');
                   
	    
	    if(!empty($remainder_date))
	    {
	        $list = $list->where('enquirys.reminder_date',$remainder_date);
	    }
	    
	    if(!empty($status))
	    {
	         $list = $list->where('enquirys.enquiry_status',$status);
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



public function enquiryDelete(Request $request)
	{
	    try{
	   
	   $delete_id = $request->delete_id;
	   
	   $delete_data = Enquiry::where('id',$delete_id)->delete();
           
	   return response()->json(['status' => true, 'message' => 'Enquiry Deleted Successfully'], 200);
        } catch (Exception $e) {
            return $this->sendError('Validation Error.', 'Error');
        }
	    
	}
}