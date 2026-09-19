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
use DB;


class BirthdayController extends Controller

{
    public function happy_birthday(Request $request){
        $targetDate = $request->filled('date') ? $request->input('date') : date('Y-m-d');
        $dateObj = \Carbon\Carbon::parse($targetDate);
        $branchId = Session::get('branch_id');
        $sessionId = Session::get('session_id');

        $student = Admission::select('admissions.*', 'types.name as class_name')
            ->leftJoin('class_types as types', 'types.id', 'admissions.class_type_id')
            ->where('admissions.branch_id', $branchId)
            ->whereNull('admissions.deleted_at')
            ->whereMonth('admissions.dob', $dateObj->month)
            ->whereDay('admissions.dob', $dateObj->day)
            ->get();

        $user = User::whereNull('deleted_at')
            ->whereMonth('dob', $dateObj->month)
            ->whereDay('dob', $dateObj->day)
            ->get();

        $sentStudentIds = DB::table('birthday_wishes')
            ->where('session_id', $sessionId)
            ->where('branch_id', $branchId)
            ->where('role_id', 3)
            ->whereDate('created_at', $targetDate)
            ->whereNull('deleted_at')
            ->pluck('admission_id')
            ->all();

        $sentUserIds = DB::table('birthday_wishes')
            ->where('session_id', $sessionId)
            ->where('branch_id', $branchId)
            ->where('role_id', '!=', 3)
            ->whereDate('created_at', $targetDate)
            ->whereNull('deleted_at')
            ->pluck('user_id_sender')
            ->all();

        return view('birthday/view', [
            'data'           => $student,
            'data2'          => $user,
            'targetDate'     => $targetDate,
            'sentStudentIds' => $sentStudentIds,
            'sentUserIds'    => $sentUserIds
        ]);
    }
    
    public function send_wishes(Request $request){
        $template = MessageTemplate::Select('message_templates.*','message_types.slug','message_types.status as message_type_status')
            ->leftjoin('message_types','message_types.id','message_templates.message_type_id')
            ->where('message_types.slug','birthday-wishes')->first();
                
        $setting = Setting::where('branch_id',Session::get('branch_id'))->first();                 
                    
        if($request->isMethod('post')){
            $error = 0;
            if(!empty($request->checkbox_user))
            {
                foreach($request->checkbox_user as $key => $item)
                {
                    $arrey1 = array('{#name#}');
                    $arrey2 = array($request->first_name_user[$key] ?? '');
                    $whatsapp = str_replace($arrey1, $arrey2, $template->whatsapp_content ?? '');
                                        
                    if (!empty($setting->firebase_notification) && $setting->firebase_notification == 1) {
                        Helper::sendNotification(
                            $template->title ?? 'Happy Birthday',
                            $whatsapp,
                            'user',
                            $request->checkbox_user[$key]
                        ); 
                    }
                     
                    if (!empty($template->message_type_status) && $template->message_type_status == 1) {
                        $mobile = $request->mobile_user[$key] ?? '';
                        if (!empty($mobile)) {
                            Helper::MessageQueue($mobile, $whatsapp);
                        }
                    }

                    // Record wish log
                    DB::table('birthday_wishes')->insert([
                        'session_id'     => Session::get('session_id'),
                        'branch_id'      => Session::get('branch_id'),
                        'role_id'        => $request->role_id_user[$key] ?? 1,
                        'user_id_sender' => $request->checkbox_user[$key],
                        'status'         => 0,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ]);
                }
            }

            if(!empty($request->checkbox_student))
            {
                foreach($request->checkbox_student as $key => $item)
                {
                    $arrey1 = array('{#name#}', '{#school_name#}');
                    $arrey2 = array($request->first_name_student[$key] ?? '', $setting->name ?? '');
                    $whatsapp = str_replace($arrey1, $arrey2, $template->whatsapp_content ?? '');
                                        
                    if (!empty($setting->firebase_notification) && $setting->firebase_notification == 1) {
                        Helper::sendNotification(
                            $template->title ?? 'Happy Birthday',
                            $whatsapp,
                            'student',
                            $request->checkbox_student[$key]
                        ); 
                    }

                    // Record wish log
                    DB::table('birthday_wishes')->insert([
                        'session_id'   => Session::get('session_id'),
                        'branch_id'    => Session::get('branch_id'),
                        'role_id'      => 3,
                        'admission_id' => $request->checkbox_student[$key],
                        'status'       => 0,
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ]);
                }
            }       

            return redirect::to('happy_birthday')->with('message', 'Wishes Sent Successfully.'); 
        }
    }
    
 

 
 

    
}