<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Setting;
use App\Models\Admission;
use App\Models\Master\Branch;
use App\Models\Master\MessageTemplate;
use App\Models\Master\MessageType;
use Validator;
use Hash;
use File;
use App;
use URL;
use Image;
use Carbon;
use Str;
use App\Helpers\helper;
use Mail;


class LoginController extends BaseController
{

	public function appDataApi(Request $request)
	{
	    $setting = Setting::first();
	    try{
	     $data = array(
                'logo' => URL::asset('schoolimage/setting/left_logo/'.$setting->left_logo),
                'name' => $setting->name
            );
	    
	     return $this->sendResponseData($data, 'success');
        } catch (Exception $e) {
            return $this->sendError('Validation Error.', 'Error');
        }
	}

	
	public function login(Request $request)
	{
		$request_data = $request->json()->all();
	$user = User::where("userName", $request->username)->where('confirm_password', $request->password)->where('role_id',1)->first();



		if ($user) {
	return response()->json(['status' => true, 'message' => 'Login Successfull','data'=>$user], 200);
		} else {
			return response()->json(['status' => false, 'message' => 'No User Found'], 200);
		}

	}
	public function teacherLogin(Request $request)
	{
		$request_data = $request->json()->all();
	$user = User::where("userName", $request->username)->where('confirm_password', $request->password)->where('role_id',2)->first();





		if ($user) {
	return response()->json(['status' => true, 'message' => 'Login Successfull','data'=>$user], 200);
		} else {
			return response()->json(['status' => false, 'message' => 'No User Found'], 200);
		}

	}
	public function studentLogin(Request $request)
	{
		$request_data = $request->json()->all();
// 	$user = Admission::where("userName", $request->username)->where('confirm_password', $request->password)->first();

              $user =   Admission::Select('admissions.*','class_types.name as class_name','gender.name as gender_name')
                         ->leftjoin('class_types','class_types.id','admissions.class_type_id')
                         ->leftjoin('gender','gender.id','admissions.gender_id')
                        
               ->where("admissions.userName", $request->username)->where('admissions.confirm_password', $request->password)->first();

		if ($user) {
	return response()->json(['status' => true, 'message' => 'Login Successfull','data'=>$user,'show_path'=>env('IMAGE_SHOW_PATH')], 200);
		} else {
			return response()->json(['status' => false, 'message' => 'No User Found'], 200);
		}

	}
	
public function forgotPassword(Request $request)
{
    try {
        if ($request->isMethod('post')) {
    		$validator = Validator::make(
    			$request->all(),[
    				'mobile' => 'required',
    			]
	        );

    		if ($validator->fails()) {
    			return $this->sendError('Validation Error.', $validator->messages()->first());
    		}
    		
    		$old_data = User::where('mobile',$request->mobile)->first();
    		
    		$template = MessageTemplate::Select('message_templates.*','message_types.slug')
                        ->leftjoin('message_types','message_types.id','message_templates.message_type_id')
                       ->where('message_types.status',1)->where('message_types.slug','send-otp')->first();
                       
            $arrey1 = array(
                        '{#name#}',
                        '{#otp#}',
                        '{#school_name#}'
                    );           
                       
		    $otp = mt_rand(1000, 9999);
        
    		if(!empty($old_data)){
    		    $data = $old_data;
    		}else{
    		    $data = Admission::where('mobile',$request->mobile)->first();
    		}
    		
    		$branch = Branch::find($data->branch_id);
            $setting = Setting::where('branch_id',$data->branch_id)->first();
            
            $arrey2 = array(
                        $data->first_name." ".$data->last_name,
                        $otp,
                        $setting->name
                    );
    		
            if($template->status != 1){
                if($data->email != ""){
                    if($branch->email_srvc != 0){
                        if($template->email_status != 0){
                            $message = str_replace($arrey1,$arrey2,$template->email_content);
                            $emailData = ['email' => $data->email, 'data' => $message, 'subject' => $template->title];
                            Helper::sendMail('email_print.template_print', $emailData);
                        } 
                    } 
                }
            
                if($branch->whatsapp_srvc != 0){
                    if ($data->mobile != ""){
                        if($template->whatsapp_status != 0){
                            $whatsapp = str_replace($arrey1,$arrey2,$template->whatsapp_content);
                            Helper::sendWhatsappMessage($data->mobile, $whatsapp);
                        }
                    }
                }
                
                if($branch->sms_srvc != 0){
                    if($data->mobile != ""){
                        if($template->sms_status != 0){
                            $sms = str_replace($arrey1,$arrey2,$template->sms_content);
                            Helper::SendMessage($data->mobile, $sms);
                        }
                    }
                }    
            }
    		
    		return response()->json(['status' => true, 'otp' => $otp, 'message' => 'OTP Send Successfully'], 200);
        }
    } catch (Exception $e) {
        return $this->sendError('Something Went Wrong', 'Error');
    }
}

        public function libraryHostelDetails(Request $request){
            try{
                if($request->isMethod('post')){
                    $details['library'] = null;
                    $details['hostel'] = null;

                    return response()->json(['status' => true, 'data' => $details, 'message' => 'Hostel And Library Details Fatched Successfully'], 200);
                }
            }catch (Exception $e) {
                return $this->sendError('Something Went Wrong', 'Error');
            }
        }

}