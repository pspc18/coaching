<?php

namespace App\Models\hostel;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Hostel extends Model
{
        use SoftDeletes;
	protected $table = "hostel"; //table name



    public static function countTotelHostel(){
        $data = Hostel:: where('session_id',Session::get('session_id'));
        
        if(Session::get('role_id') > 1){
            $data = $data->where('branch_id',Session::get('branch_id'));
        }
		 $data = $data->count();
        return $data;
    }
    
    public static function countData(){
        $data = HostelAssign::select('hostel_assign.*')
                   ->leftjoin('admissions','admissions.id','hostel_assign.admission_id')
                   ->where('hostel_assign.session_id',Session::get('session_id'))
		           ->where('hostel_assign.branch_id',Session::get('branch_id'))->groupBy('hostel_assign.id'); 
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
            $count['active_user'] = $data1->where('hostel_assign.status',1)->get()->count();
            $count['expired_last_15_days'] = $data2->where('hostel_assign.hostel_renewal_date', '>=', date('Y-m-d', strtotime("-15 day")))->where('hostel_assign.hostel_renewal_date', '<=', date('Y-m-d'))->get()->count();
            $count['expired_yesterday'] = $data3->where('hostel_assign.hostel_renewal_date','>=',date('Y-m-d', strtotime("-1 day")))->where('hostel_assign.hostel_renewal_date','<=',date('Y-m-d'))->get()->count();
            $count['expiring_today'] = $data4->where('hostel_assign.hostel_renewal_date',date('Y-m-d'))->get()->count();
            $count['expiring_3_days'] = $data5->where('hostel_assign.hostel_renewal_date','<=',date('Y-m-d', strtotime("+3 day")))->where('hostel_assign.hostel_renewal_date','>=',date('Y-m-d'))->get()->count();
            $count['expiring_7_days'] = $data6->where('hostel_assign.hostel_renewal_date','<=',date('Y-m-d', strtotime("+7 day")))->where('hostel_assign.hostel_renewal_date','>=',date('Y-m-d'))->get()->count();
            $count['expiring_15_days'] = $data7->where('hostel_assign.hostel_renewal_date','<=',date('Y-m-d', strtotime("+15 day")))->where('hostel_assign.hostel_renewal_date','>=',date('Y-m-d'))->get()->count();
            $count['new_student_today'] = $data8->whereDate('hostel_assign.created_at','=',date('Y-m-d'))->get()->count();
            $count['new_student_yesterday'] = $data9->whereDate('hostel_assign.created_at','=',date('Y-m-d', strtotime("-1 day")))->get()->count();
            $count['new_student_this_month'] = $data10->whereYear('hostel_assign.created_at', '=', date('Y-m-d'))->whereMonth('hostel_assign.created_at', '=', date('Y-m-d'))->get()->count();
            $count['new_student_last_month'] = $data11->whereYear('hostel_assign.created_at', '=', date('Y-m-d'))->whereMonth('hostel_assign.created_at', '=', date('Y-m-d', strtotime("-1 month")))->get()->count();

             // dd($count);     
      
        return $count;
    }  
    
}