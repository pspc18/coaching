<?php

namespace App\Http\Controllers\student_login;
use App\Models\User;
use App\Models\State;
use App\Models\Admission;
use App\Models\Master\Branch;
use App\Models\Master\Uniform;
use App\Models\Master\Rule;
use App\Models\Master\GatePass;
use App\Models\Master\Prayer;
use App\Models\Master\Homework;
use App\Models\Master\TeacherSubject;
use Illuminate\Validation\Validator;
use Session;
use Hash;
use Str;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

           
        
            public function homeworkView(){
                $allhomework = Homework::with('Subject')->orderBy('id', 'DESC')->get();
                return view('dashboard.student.homework', ['data' => $allhomework]);
            }
        
        
          
        
           
        
            

}
