<?php

namespace App\Http\Controllers\master;
use Illuminate\Validation\Validator; 
use App\Models\User;
use App\Models\Master\Stork;
use Session;
use Hash;
use Str;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StorkController extends Controller

{
    

    public function view(Request $request){
                $enquiry =  Stork::where('session_id',Session::get('session_id'))
		 ->where('branch_id',Session::get('branch_id'));
                return view('master.stork.view',['data'=>$enquiry]);
    }    
    
    public function add(Request $request){
         if($request->isMethod('post')){
                 $request->validate([
            
            'books' => 'required',
            'uniform' => 'required',
            
           
         ]);

    
       
        $add = new Stork;//model name
        $add->user_id = Session::get('id');
        $add->session_id = Session::get('session_id');
        $add->branch_id = Session::get('branch_id');
		$add->books =$request->books;
		$add->uniform =$request->uniform;
		$add->diary =$request->diary;
		$add->furniture =$request->furniture;
		$add->duster =$request->duster;
	
			$add->save();

        return redirect::to('stork_view')->with('message', 'Stork added Successfully.');
        }

        return view('master.stork.add');
    } 
 public function delete(Request $request){
               $book = Stork::find($request->delete_id)->delete();
                return redirect::to('stork_view')->with('message', 'Stork Deleted Successfully.');
    }

 public function edit(Request $request, $id){
         $data = Stork::find($id);
                if($request->isMethod('post')){
                 $request->validate([
            
             'books' => 'required',
            'uniform' => 'required',
           
         ]);

        $data->session_id = Session::get('session_id');
        $data->branch_id = Session::get('branch_id');      
		$data->books =$request->books;
		$data->uniform =$request->uniform;
		$data->diary =$request->diary;
		$data->furniture =$request->diary;
		$data->duster =$request->duster;
	
		$data->save();

        return redirect::to('stork_view')->with('message', 'Stork Updated Successfully.');
        }

        return view('master.stork.edit',['data'=>$data]);
    } 

 



    
}
