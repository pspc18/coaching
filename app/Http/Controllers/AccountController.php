<?php

namespace App\Http\Controllers;
use Illuminate\Validation\Validator; 
use App\Models\User;
use App\Models\Account;
use App\Models\Master\MessageTemplate;
use App\Models\Master\MessageType;
use App\Models\Master\Branch;
use App\Models\Setting;
use Session;
use Hash;
use Str;
use Redirect;
use File;
use Helper;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
class AccountController extends Controller

{
    
    
    public function account_dashboard(){
    
        return view('account/account_dashboard');
 
    }
    
    public function add(Request $request){
        
        if($request->isMethod('post')){
                $request->validate([
            'name' => 'required',
            'account_number' => 'required',
            'bank_name' => 'required',
            'branch_name' => 'required',
            'ifsc_code' => 'required',     
            'uplode_qr' => 'required',        
         ]);
        
        $uplode_qr ='';
                if($request->file('uplode_qr')){
                 $image = $request->file('uplode_qr');
                $path = $image->getRealPath();      
                $uplode_qr =  time().uniqid().$image->getClientOriginalName();
                $destinationPath = env('IMAGE_UPLOAD_PATH').'uplode_qr';
                $image->move($destinationPath, $uplode_qr);     
             } 
           
        $addaccount = new Account;//model name
        $addaccount->user_id = Session::get('id');
        $addaccount->session_id = Session::get('session_id');
        $addaccount->branch_id = Session::get('branch_id');
        $addaccount->role_id = Session::get('role_id');
		$addaccount->name =$request->name;
		$addaccount->account_number= $request->account_number;
		$addaccount->bank_name= $request->bank_name;
		$addaccount->branch_name= $request->branch_name;
		$addaccount->ifsc_code= $request->ifsc_code;
		$addaccount->uplode_qr= $uplode_qr;
	    $addaccount->save();
	    
	    $template = MessageTemplate::Select('message_templates.*','message_types.slug')
                    ->leftjoin('message_types','message_types.id','message_templates.message_type_id')
                    ->where('message_types.status',1)->where('message_types.slug','Account')->first();
            
        $branch = Branch::find(Session::get('branch_id'));
        $setting = Setting::where('branch_id',Session::get('branch_id'))->first();

        $arrey1 =   array(
                        '{#name#}',
                        '{#account_number#}',
                        '{#bank_name#}',
                        '{#branch_name#}',
                        '{#ifsc_code#}',
                        '{#school_name#}');
                       
        $arrey2 = array(
                        $request->name,
                        $request->account_number,
                        $request->bank_name,
                        $request->branch_name,
                        $request->ifsc_code,
                        $setting->name);
                    
                    if($template->status != 1){
                            if($setting->gmail != ""){
                                if($branch->email_srvc != 0){
                                    if($template->email_status != 0){
                                        $message = str_replace($arrey1,$arrey2,$template->email_content);
                                        $emailData = ['email' => $setting->gmail, 'data' => $message, 'subject' => $template->title];
                                        Helper::sendMail('email_print.template_print', $emailData);
                                    } 
                                } 
                            }
                        
                            if($branch->whatsapp_srvc != 0){
                                if ($setting->mobile != ""){
                                    if($template->whatsapp_status != 0){
                                        $whatsapp = str_replace($arrey1,$arrey2,$template->whatsapp_content);
                                        Helper::sendWhatsappMessage($setting->mobile,$whatsapp);
                                    }
                                }
                            }
                            
                            if($branch->sms_srvc != 0){
                                if($setting->mobile != ""){
                                    if($template->sms_status != 0){
                                        $sms = str_replace($arrey1,$arrey2,$template->sms_content);
                                        Helper::SendMessage($setting->mobile, $sms);
                                    }
                                }
                            }    
                    }


        return redirect::to('bank/account/index')->with('message', 'Account add Successfully.');
        }
        
        return view('account.add_bank.add');
 
     }
     
      public function accountList(Request $request){
       $data =  Account::where('session_id',Session::get('session_id'));
       
       if(Session::get('role_id') > 1){
           $data = $data->where('branch_id',Session::get('branch_id'));
       }
        $allaccount = $data->orderBy('id', 'DESC')->get();
        return view('account.add_bank.index',['data'=>$allaccount]);
    }
    
      public function editBank(Request $request,$id){
            // dd($request);
            $data = Account::find($id);
        if($request->isMethod('post')){
             $request->validate([
            'name' => 'required',
            'account_number' => 'required',
            'bank_name' => 'required',
            'branch_name' => 'required',
            'ifsc_code' => 'required', 
            
        ]);
            
          
                if($request->file('uplode_qr')){
                 $image = $request->file('uplode_qr');
                $path = $image->getRealPath();      
                $uplode_qr =  time().uniqid().$image->getClientOriginalName();
                $destinationPath = env('IMAGE_UPLOAD_PATH').'uplode_qr';
                $image->move($destinationPath, $uplode_qr);
                 if (File::exists(env('IMAGE_UPLOAD_PATH') . 'uplode_qr/' . $data->uplode_qr)) {
                    File::delete(env('IMAGE_UPLOAD_PATH') . 'uplode_qr/' . $data->uplode_qr);
                    }
                $data->uplode_qr= $uplode_qr;
             } 
             
         $data->user_id = Session::get('id');
        $data->session_id = Session::get('session_id');
        $data->branch_id = Session::get('branch_id');     
    	$data->name =$request->name;
		$data->account_number= $request->account_number;
		$data->bank_name= $request->bank_name;
		$data->branch_name= $request->branch_name;
		$data->ifsc_code= $request->ifsc_code;
		
    	$data->save();
    		
            return redirect::to('bank/account/index')->with('message', 'Account Updated Successfully.');
        }
         return view('account.add_bank.edit',['data'=>$data]);
    }
    
    public function delete(Request $request){
       
        $id = $request->delete_id;
        
        $students = Account::find($id);
        
          if (File::exists(env('IMAGE_UPLOAD_PATH') . 'uplode_qr/' . $students->uplode_qr)) {
        File::delete(env('IMAGE_UPLOAD_PATH') . 'uplode_qr/' . $students->uplode_qr);
        }
         $students->delete();
       
         return redirect::to('bank/account/index')->with('message', 'Account Deleted Successfully.');
    }
    
    
    
    public function dashboard (Request $request){
        
     return view('account.teachers_account.dashboard');  
    }
  
    
} 




