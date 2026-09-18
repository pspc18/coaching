<?php

namespace App\Http\Controllers\student_login;
use Illuminate\Validation\Validator; 
use App\Models\User;
use App\Models\Master\NoticeBoard;
use App\Models\Master\Role;
use App\Models\Master\Library;
use App\Models\Master\Branch;
use App\Models\Master\MessageTemplate;
use App\Models\Master\MessageType;
use App\Models\Setting;
use App\Models\NoticeText;
use App\Models\Admission;
use App\Models\Teacher;
use Session;
use Hash;
use Helper;
use Str;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Detection\MobileDetect;


class NoticeBoardController extends Controller

{
            public function view(Request $request,$id){
                $data =  NoticeBoard:: where('session_id',Session::get('session_id'))
		        ->where('branch_id',Session::get('branch_id'))->orderBy('id', 'DESC')->get();
                return view('student_login.notice_board',['data'=>$data,'data_id'=>$id]);
             }    
    
            

} 
