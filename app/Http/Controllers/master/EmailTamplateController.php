<?php

namespace App\Http\Controllers\master;
use Illuminate\Validation\Validator; 
use App\Models\User;
use App\Models\Master\EmailTamplate;
use Session;
use Hash;
use Str;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmailTamplateController extends Controller

{
    

    public function EmailTamplate(Request $request){
        $data = EmailTamplate::where('session_id',Session::get('session_id'))
                             ->where('branch_id',Session::get('branch_id'))->orderBy('id', 'DESC')->get();
        
        return view('master.EmailTamplate.fille',['data'=>$data]);
    }    
    
    
    public function EmailTamplateEdit(Request $request,$id){
        $data = EmailTamplate::find($id);
        
        return view('master.EmailTamplate.edit',['data'=>$data]);
    }   

    
    
}
