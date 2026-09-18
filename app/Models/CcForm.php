<?php

namespace App\Models;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class CcForm extends Model
{
        use SoftDeletes;
	protected $table = "c_certificates_form"; //table name
	
	
	public static function countCCForm(){
        $data = CcForm::where('session_id',Session::get('session_id'));
        
        if(Session::get('role_id') > 1){
            $data = $data->where('branch_id',Session::get('branch_id'));
        }
        
        $data = $data->count();
        return $data;
    }
	
}