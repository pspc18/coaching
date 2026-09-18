<?php

namespace App\Http\Controllers\master;
use Illuminate\Validation\Validator; 
use App\Models\User;
use App\Models\Master\RecycleBin;
use App\Models\Master\Penalty;
use Session;
use Hash;
use Str;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RecycleBinController extends Controller

{
    

    public function add(Request $request){
     
     if($request->isMethod('post')){
                $request->validate([
             'resent'  => 'required',

         ]);
       
        $holiday = new RecycleBin;//model name
        $holiday->user_id = Session::get('id');
        $holiday->session_id = Session::get('session_id');
        $holiday->branch_id = Session::get('branch_id');
        $holiday->resent =$request->resent;
        $holiday->save();
           
        return redirect::to('recycle_bin/view')->with('message', 'RecycleBin add Successfully.');
        }  
         
         return view('master.recycle_bin.add');
    } 

    public function view(Request $request){
        
        $data = RecycleBin::where('session_id',Session::get('session_id'))
		 ->where('branch_id',Session::get('branch_id'))->orderBy('id', 'DESC')->get();
		 
         return view('master.recycle_bin.view',['data'=>$data]);
    } 
    public function delete(Request $request){
    
        $id=$request->delete_id;
        $data = RecycleBin::find($id)->delete();
		 
		 
        return redirect::to('recycle_bin/view')->with('message', 'RecycleBin Deleted Successfully.');
    } 


   public function editRecycleBin(Request $request,$id){
     
     $data=RecycleBin::find($id);
     if($request->isMethod('post')){
                $request->validate([
             'resent'  => 'required',

         ]);
       
        $data->user_id = Session::get('id');
        $data->session_id = Session::get('session_id');
        $data->branch_id = Session::get('branch_id');
        $data->resent =$request->resent;
        $data->save();
           
        return redirect::to('recycle_bin/view')->with('message', 'RecycleBin Updated Successfully.');
        }  
         
         return view('master.recycle_bin.edit',['data'=>$data]);
    } 
      
    
}
