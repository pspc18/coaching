<?php

namespace App\Http\Controllers\student_login;
use Illuminate\Validation\Validator; 
use App\Models\User;
use App\Models\Master\Penalty;
use Session;
use Hash;
use Str;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CalendarController extends Controller

{
    

    public function calendar(Request $request){
        
        
        return view('master.school_calendar.calendar');
    }    
    

    
    
}
