<?php

namespace App\Models\library;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class LibraryAssign extends Model
{
        use SoftDeletes;
	protected $table = "library_assign"; //table name


    
	public function Library(){
        return $this->belongsTo('App\Models\library\Library','library_id');
    }
    public function LibraryCabin(){
        return $this->belongsTo('App\Models\library\LibraryCabin','cabin_id');
    }
    
    public static function countData(){
        $data = LibraryAssign::select('library_assign.*')
                   ->leftjoin('admissions','admissions.id','library_assign.admission_id')
                   ->leftjoin('library_plans','library_plans.admission_id','library_assign.admission_id')
                   ->where('library_assign.session_id',Session::get('session_id'))
		           ->where('library_assign.branch_id',Session::get('branch_id'))->groupBy('library_assign.id')->where('library_plans.status',0); 
            $data1 = clone  $data;   
            $data2 = clone $data;   
            $data3 = clone $data;   
            $data4 = clone $data;   
            $data5 = clone $data;   
            $data6 = clone $data;   
            $data7 = clone $data;   
            $data8 = clone $data;   
            $data9 = clone $data;   
            $data10 = clone $data;   
            $data11 = clone $data;   
            $count['active_user'] = $data1->where('library_assign.status',1)->get()->count();
            $count['expired_last_15_days'] = $data2->where('library_plans.renew_date', '>=', date('Y-m-d', strtotime("-15 day")))->where('library_plans.renew_date', '<=', date('Y-m-d'))->get()->count();
            $count['expired_yesterday'] = $data3->where('library_plans.renew_date','>=',date('Y-m-d', strtotime("-1 day")))->where('library_plans.renew_date','<=',date('Y-m-d'))->get()->count();
            $count['expiring_today'] = $data4->where('library_plans.renew_date',date('Y-m-d'))->get()->count();
            $count['expiring_3_days'] = $data5->where('library_plans.renew_date','<=',date('Y-m-d', strtotime("+3 day")))->where('library_plans.renew_date','>=',date('Y-m-d'))->get()->count();
            $count['expiring_7_days'] = $data6->where('library_plans.renew_date','<=',date('Y-m-d', strtotime("+7 day")))->where('library_plans.renew_date','>=',date('Y-m-d'))->get()->count();
            $count['expiring_15_days'] = $data7->where('library_plans.renew_date','<=',date('Y-m-d', strtotime("+15 day")))->where('library_plans.renew_date','>=',date('Y-m-d'))->get()->count();
            $count['new_student_today'] = $data8->whereDate('library_assign.created_at','=',date('Y-m-d'))->get()->count();
            $count['new_student_yesterday'] = $data9->whereDate('library_assign.created_at','=',date('Y-m-d', strtotime("-1 day")))->get()->count();
            $count['new_student_this_month'] = $data10->whereYear('library_assign.created_at', '=', date('Y-m-d'))->whereMonth('library_assign.created_at', '=', date('Y-m-d'))->get()->count();
            $count['new_student_last_month'] = $data11->whereYear('library_assign.created_at', '=', date('Y-m-d'))->whereMonth('library_assign.created_at', '=', date('Y-m-d', strtotime("-1 month")))->get()->count();

             // dd($count);     
      
        return $count;
    }    
    
}