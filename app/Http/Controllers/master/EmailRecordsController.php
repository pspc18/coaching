<?php

namespace App\Http\Controllers\master;
use Illuminate\Validation\Validator; 
use App\Models\User;
use App\Models\Master\EmailRecords;
use Session;
use Hash;
use Helper;
use Str;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmailRecordsController extends Controller

{
    public function view(){
        
                return view('master.email_records.view');
    }    
} 
