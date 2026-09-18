<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Validator;
use App\Models\User;
use App\Models\Student;
use App\Models\Admission;
use App\Models\StudentAction;
use App\Models\Classs;
use App\Models\BillCounter;
use App\Models\SmsSetting;
use App\Models\WhatsappSetting;
use App\Models\Teacher;
use App\Models\State;
use App\Models\Remark;
use App\Models\City;
use Session;
use Hash;
use Helper;
use Str;
use PDF;
use Mail;
use DB;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PDFController extends Controller

{
    

  
    public function pdfRegistrationList(Request $request)
    {

        $search['state_id'] = $request->state_id;
        $search['city_id'] = $request->city_id;
        $search['class_type_id'] = $request->class_type_id;
        $search['section_id'] = $request->section_id;
        $search['name'] = $request->name;

        $data =  Student::Select('students.*', 'class_types.name as class_name', 'sections.name as section_name')
            ->leftjoin('class_types', 'class_types.id', 'students.class_type_id')
            ->leftjoin('sections', 'sections.id', 'students.section_id')->where('students.branch_id', Session::get('branch_id'))->where('students.session_id', Session::get('session_id'));
     
        if ($request->isMethod('post')) {
            $request->validate([]);
            if (!empty($request->name)) {
                $value = $request->name;
                $allstudents = $data = $data->where(function ($query) use ($value) {
                    $query->where('students.first_name', 'like', '%' . $value . '%');
                    $query->orWhere('students.last_name', 'like', '%' . $value . '%');
                    $query->orWhere('students.registration_no', 'like', '%' . $value . '%');
                    $query->orWhere('students.mobile', 'like', '%' . $value . '%');
                    $query->orWhere('students.aadhaar', 'like', '%' . $value . '%');
                    $query->orWhere('students.email', 'like', '%' . $value . '%');
                    $query->orWhere('students.father_name', 'like', '%' . $value . '%');
                    $query->orWhere('students.mother_name', 'like', '%' . $value . '%');
                    $query->orWhere('students.address', 'like', '%' . $value . '%');
                });
            }
            if (!empty($request->state_id)) {
                $data = $data->where("students.state_id", $request->state_id);
            }
            if (!empty($request->city_id)) {
                $data = $data->where("students.city_id", $request->city_id);
            }
            if (!empty($request->class_type_id)) {
                $data = $data->where("students.class_type_id", $request->class_type_id);
            }
            if (!empty($request->section_id)) {
                $data = $data->where("students.section_id", $request->section_id);
            }
        }

        $allstudents = $data->orderBy('id', 'DESC')->get();
$pdf = PDF::loadView('print_file.pdf.registration_list',['data'=>$allstudents]);
              return $pdf->download('pdf_file.pdf');

    }

 
    

  

   

   

 

    

   

  
}
