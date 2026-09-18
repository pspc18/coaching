<?php

namespace App\Http\Controllers\master;
use Illuminate\Validation\Validator; 
use App\Models\User;
use App\Models\Master\SidebarPermission;
use App\Models\Role;
use App\Models\Sidebar;
use Session;
use Hash;
use Str;
use Helper;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SidebarPermisController extends Controller

{
    public function view(Request $request){
        $permission =  SidebarPermission::with('roleName')->with('sidebarName')->get();
    
    return view('master.side_permis.view',['data'=>$permission]);
 
    }
    
    public function add(Request $request){
        
       if($request->isMethod('post')){
            $request->validate([
            
            'role_id' => 'required',
            'sidebar_id' => 'required',
            
         ]);

        if(!empty($request->sidebar_id)){
             $sidebar_id = implode(',', $request->sidebar_id);
            }
        
        $add_permis = new SidebarPermission;//model name

		$add_permis->user_id  = Session::get('id');
		$add_permis->session_id = Session::get('session_id');
        $add_permis->branch_id = Session::get('branch_id');
		$add_permis->role_id  = $request->role_id;
		$add_permis->sidebar_id  = $sidebar_id;
		$add_permis->save();
        
        return redirect::to('side_permis_view')->with('message', 'Sidebar Permission Successfully.');
        }
        
        return view('master.side_permis.add');
 
     }

    
     public function edit(Request $request,$id){

        $data = SidebarPermission::find($id);
            if($request->isMethod('post')){
                 $request->validate([
              
    
            ]);
            if(!empty($request->sidebar_id)){
                 $sidebar_id = implode(',', $request->sidebar_id);
                }
            
    		$data->user_id  = Session::get('id');
    		$data->role_id  = $request->role_id;
    		$data->sidebar_id  = $sidebar_id;
    		$data->save();

            return redirect::to('side_permis_view')->with('message', 'Sidebar Permission Successfully.');
        }
        
         return view('master.side_permis.edit',['data'=>$data]);
    }
    
    public function delete(Request $request){
       
        $id = $request->delete_id;
        
        $students = SidebarPermission::find($id)->delete();
         return redirect::to('side_permis_view')->with('message', 'Sidebar Permission Deleted Successfully.');
    }
    
 
    
}    