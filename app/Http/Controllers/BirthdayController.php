<?php

namespace App\Http\Controllers;
use Illuminate\Validation\Validator; 
use App\Models\Setting;
use App\Models\Admission;
use App\Models\User;
use App\Models\BirthdayWishes;
use App\Models\Master\MessageTemplate;
use Session;
use Hash;
use Str;
use Redirect;
use Helper;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class BirthdayController extends Controller

{
    public function happy_birthday(){
     $today=now();
    
        $student =  Admission::select('admissions.*','types.name as class_name')
                                ->leftjoin('class_types as types','types.id','admissions.class_type_id')->where('admissions.branch_id',Session::get('branch_id'))->whereMonth('admissions.dob',$today->month)
            ->whereDay('admissions.dob',$today->day)->get();
        $user =  User::whereMonth('dob',$today->month)->whereDay('dob',$today->day)->get();
        return view('birthday/view',['data'=>$student,'data2'=> $user]);
 
    }
    
     public function send_wishes(Request $request){
        
            $template = MessageTemplate::Select('message_templates.*','message_types.slug','message_types.status as message_type_status')
                    ->leftjoin('message_types','message_types.id','message_templates.message_type_id')
                    ->where('message_types.slug','birthday-wishes')->first();
                    
            $setting = Setting::where('branch_id',Session::get('branch_id'))->first();                 
                        
          
                if($request->isMethod('post')){
                    
            $error=0;
            if(!empty($request->checkbox_user))
            {
                foreach($request->checkbox_user as $key => $item)
                {
                     $arrey1 = array(
                                 '{#name#}',
                                 );
                       
                    $arrey2 = array(
                                    $request->first_name_user[$key],
                                    );
               
                                 $whatsapp = str_replace($arrey1, $arrey2, $template->whatsapp_content ?? '');
                                                    
                                if ($setting->firebase_notification == 1) {
                                    Helper::sendNotification(
                                        $template->title ?? 'Happy Birthday',
                                        $whatsapp,
                                        'user',
                                        $request->checkbox_user[$key]
                                    ); 
                                }
                                 
                                if ($template->message_type_status == 1) {
                                    if ($branch->whatsapp_srvc == 1) {
                                        $mobile = $request->mobile_user[$key]  ?? '';
                                        if (!empty($mobile)) {
                                            Helper::MessageQueue($mobile, $whatsapp);
                                        }
                                    }
                                }
            
             }
            }
            if(!empty($request->checkbox_student))
            {
                foreach($request->checkbox_student as $key => $item)
                {
                    $arrey1 = array(
                                '{#name#}',
                                '{#school_name#}');
                       
                    $arrey2 = array(
                                    $request->first_name_student[$key],
                                    $setting->name
                                    );
                                    
            $whatsapp = str_replace($arrey1, $arrey2, $template->whatsapp_content ?? '');
                                                    
                                if ($setting->firebase_notification == 1) {
                                    Helper::sendNotification(
                                        $template->title ?? 'Happy Birthday',
                                        $whatsapp,
                                        'student',
                                        $request->checkbox_student[$key]
                                    ); 
                                }
                                 
                                // if ($template->message_type_status == 1) {
                                //     if ($branch->whatsapp_srvc == 1) {
                                //         $mobile = $request->mobile_student[$key]  ?? '';
                                //         if (!empty($mobile)) {
                                //             Helper::MessageQueue($mobile, $whatsapp);
                                //         }
                                //     }
                                // }
                   return redirect::to('happy_birthday')->with('message', 'Wishes Sent Successfully.'); 
             }
        }       
        
        }
            }
    
 

 
 

    
}