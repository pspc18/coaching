<?php

namespace App\Http\Controllers\master;
use Illuminate\Validation\Validator; 
use App\Models\User;
use App\Models\Master\Gallery;
use Session;
use Hash;
use Str;
use Redirect;
use File;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GalleryController extends Controller

{
            public function view(Request $request){
                $data= Gallery::groupBy('img_category')->where('branch_id',Session::get('branch_id'));
                $barnch =Session::all();
                if($barnch['role_id'] == 3){
                    $data = $data->where('type', 'gallery')->orderBy('id','DESC')->get();
                    return view('dashboard.student.gallery',['data'=>$data]);            
                }
                else{
                    $data = $data->orderBy('id','DESC')->get();
                    return view('master.gallery.view',['data'=>$data]);
                } 
            }   
    
            public function add(Request $request){
                if($request->isMethod('post')){
                    $request->validate([
                        'type' => 'required',
                        'img_category' => 'required',
                        'photo' => 'required',
                    ]);
                    for ($count = 0; $count <= count($request->photo); $count++) {
                        if (isset($request->photo[$count])) {   
                            $photo ='';
                            if($request->hasfile('photo')){
                                $image = $request->file('photo')[$count];
                                $path = $image->getRealPath();      
                                $photo =  time().uniqid().$image->getClientOriginalName();
                                $destinationPath = env('IMAGE_UPLOAD_PATH').'school_gallery';
                                $image->move($destinationPath, $photo);     
                            }
                            $add_user = new Gallery;//model name
                            $add_user->user_id = Session::get('id');
                            $add_user->session_id = Session::get('session_id');
                            $add_user->branch_id = Session::get('branch_id');      
                            $add_user->img_category =$request->img_category;
                            $add_user->type =$request->type;
                            $add_user->image = $photo;
                    	    $add_user->save();
                        }
                    }
                     return response()->json(['status' => 'success','message' => 'Gallery add Successfully.',]);
                }
                return view('master.gallery.add');
            } 
    
            public function edit(Request $request, $id){
                $data = Gallery::find($id);
                if($request->isMethod('post')){
                    $request->validate([
                        'img_category' => 'required',
                        'type' => 'required',
                    ]);
                    if($request->file('photo')){
                        $image = $request->file('photo');
                        $path = $image->getRealPath();      
                        $photo =  time().uniqid().$image->getClientOriginalName();
                        $destinationPath = env('IMAGE_UPLOAD_PATH').'school_gallery';
                        $image->move($destinationPath, $photo);   
                        if (File::exists(env('IMAGE_UPLOAD_PATH') . 'school_gallery/' . $data->image)) {
                            File::delete(env('IMAGE_UPLOAD_PATH') . 'school_gallery/' . $data->image);
                        }
                        $data->image = $photo;
                    }
                    $data->type =$request->type;
        	        $data->save();
                     return response()->json(['status' => 'success', 'message' => 'Gallery edit Successfully.','redirect' => url('gallery_view')]);
                }
                return view('master.gallery.edit',['data'=>$data]);
            } 
    
            // public function delete(Request $request){
            //     $data = Gallery::find($request->delete_id)->delete();
            //     return redirect::to('gallery_view')->with('message', 'Image Deleted Successfully.');
            // }
            
            
            public function delete(Request $request)
{
    $data = Gallery::find($request->delete_id);

    if (!$data) {
        // Record not found, redirect with error message
        return redirect()->back()->with('error', 'Image not found or already deleted.');
    }

    // Delete image from folder if exists
    if (File::exists(env('IMAGE_UPLOAD_PATH') . 'school_gallery/' . $data->image)) {
        File::delete(env('IMAGE_UPLOAD_PATH') . 'school_gallery/' . $data->image);
    }

    $data->delete();

    return redirect()->to('gallery_view')->with('message', 'Image Deleted Successfully.');
}

    

}
