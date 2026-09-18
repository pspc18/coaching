<?php

namespace App\Http\Controllers\master;
use Illuminate\Validation\Validator; 
use App\Models\User;
use App\Models\Master\Time_Table;
use App\Models\ClassType;
use App\Models\Master\Section;
use Session;
use Hash;
use Str;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Time_TableController extends Controller

{
    

    public function dashboard(){
        
        
        return view('master.time_table.dashboard');
    }    
    
     public function view(){
        
        $data =  Time_Table::with('ClassType')->with('Section')->where('branch_id',Session::get('branch_id'))->where('session_id',Session::get('session_id'))->orderBy('id', 'DESC')->get();
        return view('master.time_table.class_preiod.view',['data'=>$data]);
    }    

    public function add(Request $request){
        if($request->isMethod('post')){
                $request->validate([
             'class_type_id'  => 'required',
             'section_id'  => 'required',
             'preiod_name'  => 'required',
             'start_time'  => 'required',
             'end_time'  => 'required',
             
            
         ]);
         
        $addtime_table = new Time_Table;//model name
        $addtime_table->user_id = Session::get('id');
        $addtime_table->session_id = Session::get('session_id');
        $addtime_table->branch_id = Session::get('branch_id');
		$addtime_table->class_type_id =$request->class_type_id;
		$addtime_table->section_id =$request->section_id;
		$addtime_table->preiod_name =$request->preiod_name;
		$addtime_table->start_time = $request->start_time;
		$addtime_table->end_time  = $request->end_time;
        $addtime_table->save(); 
         
         return redirect::to('class/preiod/add')->with('message', 'Time Table add Successfully.');
        } 
         $alltime_table =  Time_Table::with('ClassType')->with('Section')->get();
        return view('master.time_table.class_preiod.add',['data'=>$alltime_table]);
    }   
    
    public function edit(Request $request,$id){
         
           $data = Time_Table::find($id);
        if($request->isMethod('post')){
             $request->validate([
            'class_type_id'  => 'required',
             'section_id'  => 'required',
             'preiod_name'  => 'required',
             'start_name'  => 'required',
             'end_name'  => 'required',
            
         ]);
         
        $data->session_id = Session::get('session_id');
        $data->branch_id = Session::get('branch_id');
        $data->class_type_id =$request->class_type_id;
        $data->section_id =$request->section_id;
        $data->preiod_name =$request->preiod_name;
		$data->start_time = $request->start_time;
		$data->end_time  = $request->end_time;
        $data->save(); 
         
         return redirect::to('class/preiod/add')->with('message', 'Time Table Update Successfully.');
        
        }
        
         return view('master.time_table.class_preiod.edit',['data'=>$data]);  
    }
    
    
    public function timeTableAadd(){
        
        
        return view('master.time_table.time_table.add');
    }
    
    public function delete(Request $request){
       
        $id = $request->delete_id;
       
        $addtime_table = Time_Table::find($id)->delete();
       
         return redirect::to('class/preiod/view')->with('message', 'Time Table Deleted Successfully.');
    }  
}





