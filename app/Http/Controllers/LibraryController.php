<?php

namespace App\Http\Controllers;
use Illuminate\Validation\Validator; 
use App\Models\User;
use App\Models\Neeraj;
use App\Models\library\LibraryBook;
use Session;
use Hash;
use Str;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LibraryController extends Controller

{
    
    public function View(Request $request){
        
        $data = LibraryBook::select('library_books.*','libry.name as catname')
            ->LeftJoin ('library_categarys as libry','libry.id','library_books.category_id')->OrderBy('id','DESC')->get();
            return view('book_library.index',['data'=>$data]);
        } 
        

       
        
           
}




