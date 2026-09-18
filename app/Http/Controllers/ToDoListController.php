<?php

namespace App\Http\Controllers;
use Illuminate\Validation\Validator; 
use App\Models\ToDoList;
use Session;
use Hash;
use Str;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ToDoListController extends Controller

{

    
    public function addTask(Request $request){
        if($request->isMethod('post')){
            $add = new ToDoList;//model name
            $add->user_id = Session::get('id');
            $add->session_id = Session::get('session_id');
            $add->branch_id = Session::get('branch_id'); 
            $add->name = $request->task;
            $add->status = '1';
    	    $add->save();
        }
    }


    public function toDoAssign(Request $request){
       
      
        if ($request->isMethod('post')) {
              if(!empty($request->user_id))
              {
               foreach($request->user_id as $user)
               {
                    $add = new ToDoList; 
                    $add->user_id = Session::get('id');
                    $add->session_id = Session::get('session_id');
                    $add->branch_id = Session::get('branch_id');
                    $add->name = $request->title ?? '';
                    $add->description = $request->description ?? '';
                    $add->deadline = $request->deadline ?? '';
                    $add->priority = $request->priority ?? '';
                    $add->assign_to = $user;
                    $add->save();
               }
                return redirect::to('to_do_assign_view')->with('message',"Task Created Successfully");
              }
        }
        return view('todo.toDoAssign');
  
    }

    public function toDoAssignEdit(Request $request,$id){
       
        $edit = ToDoList::find($id); 
        
        if($request->isMethod('post')) {
              if(!empty($request->user_id))
              {
                $edit->user_id = Session::get('id');
                $edit->session_id = Session::get('session_id');
                $edit->branch_id = Session::get('branch_id');
                $edit->name = $request->title ?? '';
                $edit->description = $request->description ?? '';
                $edit->deadline = $request->deadline ?? '';
                $edit->priority = $request->priority ?? '';
                $edit->status = $request->status ?? '';
                $edit->assign_to = $request->user_id;
                $edit->save();
              }
              
              return redirect::to('to_do_assign_view')->with('message',"Task Updated Successfully");
        }
        
        return view('todo.toDoAssignEdit',['data' => $edit]);
  
    }
    
    
    public function toDoAssignView(Request $request){
        
        $task = ToDoList::Select('to_do_list.*', 'users.first_name','users.last_name','role.name as role_name')
                ->leftjoin('users', 'to_do_list.assign_to', 'users.id')
                ->leftjoin('role', 'role.id', 'users.role_id')
                ->where('to_do_list.session_id',Session::get('session_id'))
                ->where('to_do_list.branch_id',Session::get('branch_id'));
                if($request->isMethod('post')) {
		     if(!empty($request->to_do_list_id)){
		        		     $task =$task->where('to_do_list.id',$request->to_do_list_id);
		     }
		 }
                if(Session::get('role_id') != 1)
                {
                    $task =$task->where('to_do_list.assign_to',Session::get('id'));
                }
                $task =$task->get();
         return view('todo.toDoAssignView',['data'=>$task]);
        
        
    }
    public function deleteTask(Request $request){
        $deleteTask =  ToDoList::find($request->task_id)->delete();
    }

    public function statusTask(Request $request){
        
        if($request->status == 1){
            $data = ToDoList::where('id',$request->id)->update(['status'=>'0']);
        }else{
            $data = ToDoList::where('id',$request->id)->update(['status'=>'1']);
        }
    }
    
    public function toDoAssignDelete(Request $request){
        ToDoList::where('id',$request->id)->delete();
        
        return redirect::to('to_do_assign_view')->with('message',"Task Deleted Successfully");
    }
    public function taskStatusChange(Request $request){
      $id = $request->id ?? "";
      $value = $request->value ?? "";
      
      $task = ToDoList::find($id);
      
      $task->status = $value;
      
      $task->save();
      
        
        return 'success';
    }


  public function taskList(Request $request){

          $task = ToDoList::select('to_do_list.*','users.first_name')
		 ->leftjoin('users','users.id','to_do_list.assign_to')
		 ->where('to_do_list.session_id',Session::get('session_id'))->where('to_do_list.branch_id',Session::get('branch_id'))->orderBy('id','DESC');
		 
		  if(Session::get('role_id') != 1)
                {
                    $task =$task->where('to_do_list.assign_to',Session::get('id'));
                }
		 
		$task = $task->get();
		//dd($task);
	
        return view('task_list',['task'=>$task]);
    }

} 
  





