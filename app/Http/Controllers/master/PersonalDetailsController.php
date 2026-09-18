<?php

namespace App\Http\Controllers\master;
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

class PersonalDetailsController extends Controller

{
    

    public function dashboard(Request $request){
        
        
        return view('master.penalty.dashboard');
    }    
    

    
    
}
