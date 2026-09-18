<?php

namespace App\Http\Controllers\master;
use Illuminate\Validation\Validator; 
use App\Models\User;
use App\Models\Master\Sport;
use Session;
use Hash;
use Str;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SportsController extends Controller

{
    

    public function view(Request $request){
        
        $data = Sport::with('ClassTypes') ->where('session_id',Session::get('session_id'))
		 ->where('branch_id',Session::get('branch_id'))->orderBy('id', 'DESC')->get();
        
        return view('master.sports.view',['data'=>$data]);
    }    
    
    public function add(Request $request){
 
        if($request->isMethod('post')){
                 $request->validate([
            
          //  'game' => 'required',
            'for_class' => 'required',
            'photo' => 'required',
            
         ]);

        $photo ='';
                if($request->file('photo')){
                 $image = $request->file('photo');
                $path = $image->getRealPath();      
                $photo =  time().uniqid().$image->getClientOriginalName();
                $destinationPath = env('IMAGE_UPLOAD_PATH').'sports';
                $image->move($destinationPath, $photo);     
             }  
             
        $add = new Sport;//model name
	//	$add->game =$request->game;
	    $add->user_id = Session::get('id');
        $add->session_id = Session::get('session_id');
        $add->branch_id = Session::get('branch_id');
		$add->for_class =$request->for_class;
		$add->photo = $photo;
		$add->save();

        return redirect::to('sports_view')->with('message', 'Game added Successfully.');
        }

        return view('master.sports.add');
    } 

    public function delete(Request $request){
       
        $book = Sport::find($request->delete_id)->delete();
        
        return redirect::to('sports_view')->with('message', 'Book Deleted Successfully.');
    }

    public function edit(Request $request, $id){
 
        $data = Sport::find($id);
        
        if($request->isMethod('post')){
                 $request->validate([
            
            'game' => 'required',
            
            'for_class' => 'required',
            'photo' => 'required',
            
            
         ]);

                if($request->file('photo')){
                $image = $request->file('photo');
                $path = $image->getRealPath();      
                $photo =  time().uniqid().$image->getClientOriginalName();
                $destinationPath = env('IMAGE_UPLOAD_PATH').'sports';
                $image->move($destinationPath, $photo);  
                $data->photo = $photo;
                }
		$data->game =$request->game;
	
		$data->for_class =$request->for_class;
	
		$data->photo =$request->photo;
	
		$data->save();

        return redirect::to('sports_view')->with('message', 'Book Updated Successfully.');
        }

        return view('master.sports.edit',['data'=>$data]);
    } 







































    
    
}
