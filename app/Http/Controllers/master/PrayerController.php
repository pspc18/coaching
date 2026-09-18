<?php

namespace App\Http\Controllers\master;
use Illuminate\Validation\Validator; 
use App\Models\User;
use App\Models\Master\Prayer;
use Session;
use Hash;
use Str;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PrayerController extends Controller

{
    
            public function add(Request $request){
                if($request->isMethod('post')){
                    $request->validate([
                        'name'  => 'required',
                        'prayer'  => 'required',
                    ]);
                    $holiday = new Prayer;//model name
                    $holiday->branch_id =Session::get('branch_id');
                    $holiday->user_id =Session::get('id');
                    $holiday->session_id =Session::get('session_id');
                    $holiday->name =$request->name;
                    $holiday->prayer =$request->prayer;
                    $holiday->save();
                    return response()->json(['status' => 'success','message' => 'Prayer add Successfully.',]);
                   
                }  
                return view('master.Prayer.add');
            } 

            public function view(Request $request){
                $data = Prayer::whereNull('deleted_at')->where('branch_id',Session::get('branch_id'))->orderBy('id', 'DESC')->get();
                return view('master.Prayer.view',['data'=>$data]);
            } 

            public function edit(Request $request,$id){
                $data = Prayer::find($id);
                if($request->isMethod('post')){
                    $request->validate([
                        'name'  => 'required',
                        'prayer'  => 'required',
                    ]);
                    $data->branch_id =Session::get('branch_id');
                    $data->session_id =Session::get('session_id');
                    $data->name =$request->name;
                    $data->prayer =$request->prayer;
                    $data->save();
                     return response()->json(['status' => 'success', 'message' => 'Prayer Updated Successfully.','redirect' => url('prayer_view')]);
                }
                return view('master.Prayer.edit',['data'=>$data]);
            }
     
            public function delete(Request $request){
                $id = $request->delete_id;
                $sss = Prayer::find($id)->delete();
                return redirect::to('prayer_view')->with('message', 'Prayer  Delete Successfully.');
            }
}
