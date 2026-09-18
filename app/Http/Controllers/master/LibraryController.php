<?php

namespace App\Http\Controllers\master;
use Illuminate\Validation\Validator; 
use App\Models\User;
use App\Models\Master\Library;
use Session;
use Hash;
use Str;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LibraryController extends Controller

{
    

    public function view(Request $request){
                $books =  Library::where('session_id',Session::get('session_id'))
		 ->where('branch_id',Session::get('branch_id'));
		 
                return view('master.library.view',['data'=>$books]);
    }    
    
    public function add(Request $request){
         if($request->isMethod('post')){
                 $request->validate([
            
            'name' => 'required',
           
         ]);

       
       
        $add = new Library;//model name
        $add->user_id = Session::get('id');
        $add->session_id = Session::get('session_id');
        $add->branch_id = Session::get('branch_id');
		$add->name =$request->name;
			$add->save();

        return redirect::to('library_view')->with('message', 'Book added Successfully.');
        }

        return view('master.library.add');
    } 



    public function delete(Request $request){
               $book = Library::find($request->delete_id)->delete();
                return redirect::to('library_view')->with('message', 'Book Deleted Successfully.');
    }


    public function edit(Request $request, $id){
         $data = Library::find($id);
                if($request->isMethod('post')){
                 $request->validate([
            
            'name' => 'required',
           
         ]);

          
        $data->session_id = Session::get('session_id');
        $data->branch_id = Session::get('branch_id');     
		$data->name =$request->name;
	
		$data->save();

        return redirect::to('library_view')->with('message', 'Book Updated Successfully.');
        }

        return view('master.library.edit',['data'=>$data]);
    } 







































    
    
}
