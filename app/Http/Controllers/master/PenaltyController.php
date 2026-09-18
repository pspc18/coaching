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

class PenaltyController extends Controller

{
    

    public function add(Request $request){
        
            if($request->isMethod('post')){
                $request->validate([
             'class_type_id'  => 'required',
             'student_section'  => 'required',
             'pelanty_amount'  => 'required',
             
            
         ]);
         
        $addpenalty = new Penalty;//model name
        $addpenalty->user_id = Session::get('id');
        $addpenalty->session_id = Session::get('session_id');
        $addpenalty->branch_id = Session::get('branch_id');
		$addpenalty->class =$request->class;
		$addpenalty->student_section = $request->student_section;
		$addpenalty->name  = $request->name;
		$addpenalty->student_no =$request->student_no;
		$addpenalty->pelanty_amount = $request->pelanty_amount;
		$addpenalty->pelanty_reason  = $request->pelanty_reason;
		$addpenalty->pelanty_remark  = $request->pelanty_remark;
        $addpenalty->save(); 
         
         return redirect::to('penalty/index')->with('message', 'Penalty add Successfully.');
        } 
            
        return view('master.penalty.add');
    }    
    

    public function index(Request $request){
        
         $allpenalty =  Penalty::where('session_id',Session::get('session_id'))
                             ->where('branch_id',Session::get('branch_id'))->orderBy('id', 'DESC')->get();
         return view('master.penalty.index',['data'=>$allpenalty]);
    }
    
    public function edit(Request $request,$id){
        
             $data = Penalty::find($id);
        if($request->isMethod('post')){
             $request->validate([
            'class'  => 'required',
            'student_section'  => 'required',
            'pelanty_amount'  => 'required',
        ]);
          
          
        $data->session_id = Session::get('session_id');
        $data->branch_id = Session::get('branch_id');  
        $data->class =$request->class;
		$data->student_section = $request->student_section;
		$data->name  = $request->name;
		$data->student_no =$request->student_no;
		$data->pelanty_amount = $request->pelanty_amount;
		$data->pelanty_reason  = $request->pelanty_reason;
		$data->pelanty_remark  = $request->pelanty_remark;
        $data->save(); 
        
        
            
        return redirect::to('penalty/index')->with('message', 'Penalty Update Successfully.');
        } 
         return view('master.penalty.edit',['data'=>$data]);
    }
    
    
    public function delete(Request $request){
       
        $id = $request->delete_id;
        
        $penalty = Penalty::find($id)->delete();
       
         return redirect::to('penalty/index')->with('message', 'Penalty Delete Successfully.');
    }
    
}



