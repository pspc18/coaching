<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\WebUser;
use App\Models\User;
use App\Models\ReactClassType;
use Illuminate\Support\Facades\Auth;
use Validator;
use Hash;
use File;
use Carbon;
use Str;
use App\Helpers\helpers;
use Mail;


class ClassController extends BaseController
{

	public function className(Request $request)
	{
	    
	     
	
	    try{
	    $className = ReactClassType::orderBy('class','ASC')->get();
	    
	     $data = array();
            foreach ($className as $item) {
                $data[] = array(
                    'label' => $item->name,
                    'value' => $item->id,
                );
            }
	     return $this->sendResponseData($data, 'success');
        } catch (Exception $e) {
            return $this->sendError('Validation Error.', 'Error');
        }
	}






}