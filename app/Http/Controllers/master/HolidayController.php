<?php

namespace App\Http\Controllers\master;
use Illuminate\Validation\Validator; 
use App\Models\Master\Holidays;
use Session;
use Hash;
use Str;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HolidayController extends Controller

{
    
    public function add(Request $request){
         if($request->isMethod('post')){
                $request->validate([
             'name'  => 'required',

         ]);
       
        $holiday = new Holidays;//model name
        $holiday->user_id = Session::get('id');
        $holiday->session_id = Session::get('session_id');
        $holiday->branch_id = Session::get('branch_id');
        $holiday->name =$request->name;
        $holiday->save();
           
        return redirect::to('holiday/view')->with('message', 'Holiday add Successfully.');
        }  
         
         return view('master.holiday.add');
    } 

    public function view(Request $request){
        
        $data = Holidays::where('session_id',Session::get('session_id'))
                             ->where('branch_id',Session::get('branch_id'))->orderBy('id', 'DESC')->get();
                             
         return view('master.holiday.view',['data'=>$data]);
    } 

}
