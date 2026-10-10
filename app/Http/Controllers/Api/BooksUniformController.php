<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use Hash;
use Session;
use File;
use App;
use URL;
use Image;
use Carbon;
use Str;
use App\Helpers\helpers;
use Mail;

class BooksUniformController extends BaseController
{
 public function booksUniformShops(Request $request){
        return response()->json(['status' => true, 'message' => 'Success', 'shops' => [], 'category' => []], 200);
    }
}
 
 
 

    
    