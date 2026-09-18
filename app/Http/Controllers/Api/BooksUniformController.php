<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Master\BooksUniformShop;
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
      
                $Category= BooksUniformShop::groupBy('category')->orderBy('id','DESC')->get();
        $Shops= BooksUniformShop::orderBy('id','DESC')->get();
          if(!empty($Category))
            {
              return response()->json(['status' => true, 'message' => 'Success','shops'=>$Shops,'category'=>$Category], 200);
            }
            else
            {
                 return response()->json(['status' => false, 'message' => 'Error','shops'=>[],'category'=>[]], 200);
            }
    }
}
 
 
 

    
    