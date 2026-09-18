<?php

namespace App\Http\Controllers\master;
use Illuminate\Validation\Validator; 
use App\Models\User;
use App\Models\Master\BooksUniformShop;
use Session;
use Hash;
use Str;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BooksUniformController extends Controller

{
    
            public function view(Request $request){
                $data = BooksUniformShop::where('branch_id',Session::get('branch_id'))->orderBy('id','DESC')->get();
                return view('master.books_uniform.view',['data'=>$data]);
            }
            public function add(Request $request){
                if($request->isMethod('post')){
                    $request->validate([
                     'category'  => 'required',
                     'shop_name'  => 'required',
                     'address'  => 'required',
                    ]);
                    $data = new BooksUniformShop;
                    $data->branch_id = Session::get('branch_id');
                    $data->session_id = Session::get('session_id');
                    $data->category = $request->category;
                    $data->shop_name = $request->shop_name;
                    $data->address = $request->address;
                    $data->shop_keeper_no = $request->shop_keeper_no;
                    $data->live_location = $request->live_location;
                    $data->save(); 
                   return response()->json(['status' => 'success','message' => 'Book Add Successfully.',]);
                }
                    return view('master.books_uniform.add');
            }
            public function edit(Request $request,$id){
                $data = BooksUniformShop::find($id);
                if($request->isMethod('post')){
                    $request->validate([
                        'category'  => 'required',
                        'shop_name'  => 'required',
                        'address'  => 'required',
                    ]);
                    $data->branch_id = Session::get('branch_id');
                    $data->session_id = Session::get('session_id');
                    $data->category = $request->category;
                    $data->shop_name = $request->shop_name;
                    $data->address = $request->address;
                    $data->shop_keeper_no = $request->shop_keeper_no;
                    $data->live_location = $request->live_location;
                    $data->save(); 
                   return response()->json(['status' => 'success', 'message' => 'Book Updated Successfully.','redirect' => url('books_uniform_view')]);
                }
                return view('master.books_uniform.edit',['data'=>$data]);
            }
    
            public function delete(Request $request){
       
                $id = $request->delete_id;
                $shops = BooksUniformShop::find($id)->delete();
                return redirect::to('books_uniform_view')->with('message', 'Shop Details  Delete Successfully.');
            }
 
}
