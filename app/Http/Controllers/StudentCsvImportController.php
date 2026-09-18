<?php

namespace App\Http\Controllers;

use Log;
use Illuminate\Validation\Validator;
use App\Models\User;
use App\Models\Enquiry;
use App\Models\Admission;
use App\Models\RollNumber;
use App\Models\StudentId;
use App\Models\StudentAction;
use App\Models\exam\FillMarks;
use App\Models\Classs;
use App\Models\ClassType;
use App\Models\Subject;
use App\Models\Sessions;
use App\Models\Master\Branch;
use App\Models\TcCertificate;
use App\Models\BillCounter;
use App\Models\SmsSetting;
use App\Models\BloodGroup;
use App\Models\DatatableFields;
use App\Models\FeesMaster;
use App\Models\FeesCollect;
use App\Models\WhatsappSetting;
use App\Models\FeesStructure;
use App\Models\FeesDetail;
use App\Models\StudentDocument;
use App\Models\Setting;
use App\Models\State;
use App\Models\Gender;
use App\Models\Master\MessageTemplate;
use App\Models\Master\MessageType;
use App\Models\City;
use App\Models\fees\FeesAssign;
use App\Models\fees\FeesAssignDetail;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Models\fees\FeesDetailsInvoices;
use Session;
use Hash;
use PDF;
use Helper;
use Str;
use Mail;
use File;
use DB;
use Redirect;
use Auth;
use App\Imports\YourImportClassName;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Schema;


class StudentCsvImportController extends Controller
{
        
          
    
            
        public function studentCsvImport(){
                $getAdmissionDatatableFields = Helper::getAdmissionDatatableFields();
                $gender = DB::table('gender')->whereNull('deleted_at')->pluck('name')->implode(',');
                $villageList = DB::table('custom_villages_list')->whereNull('deleted_at')->pluck('name')->implode(',');
                $class = DB::table('class_types')->whereNull('deleted_at')->pluck('name')->implode(',');
                $stateList = DB::table('states')->where('id', 13)->pluck('name')->implode(',');
                $cityList = DB::table('citys')->whereNull('deleted_at')->where('state_id', 13)->take(25)->pluck('name')->implode(',');
                $bloodgroupList = DB::table('blood_groups')->whereNull('deleted_at')->pluck('name')->implode(',');

                return view('students.admission.studentCsvImport', compact(
                    'getAdmissionDatatableFields',
                    'gender',
                    'villageList',
                    'class',
                    'stateList',
                    'cityList',
                    'bloodgroupList'
                ));
            }

          
            
            public function studentExcelAdd(Request $request)
            {   
                $array = Helper::getAdmissionDatatableFields();
                $branch = Branch::find(Session::get('session_id'));
                $the_file = $request->file('excel');
            
                try {
                    $spreadsheet = IOFactory::load($the_file->getRealPath());
                    $sheet = $spreadsheet->getActiveSheet();
                    $row_limit = $sheet->getHighestDataRow();
                    $column_limit = $sheet->getHighestDataColumn();
                    $row_range = range(3, $row_limit);
                    $highestColumnNumber = $this->columnLetterToNumber($column_limit);
            
                    $data = array();
                    $val2 = [];
            
                    foreach ($row_range as $row) {
                        $val = [];
                        $columnBValue = '';
            
                       for ($i = 0; $i < $highestColumnNumber; $i++) {
                                $colLetter = $this->indexToColumnName($i);
                                $cell = $sheet->getCell($colLetter . $row);
                            
                                // ✅ Always take formatted value
                                $value = $cell->getFormattedValue();
                            
                                if ($value instanceof \PhpOffice\PhpSpreadsheet\RichText\RichText) {
                                    $value = $value->getPlainText();
                                }
                            
                                $index = trim((string) $sheet->getCell($colLetter . 2)->getValue());
                            
                                // Transform known columns
                                switch ($index) {
                                    case 'Class':
                                        $classType = ClassType::where('name', $value)
                                                    ->where('branch_id', Session::get('branch_id'))
                                                    ->first();
                                        $value = $classType->id ?? '';
                                        break;
                                    case 'Admission Type':
                                        $admissionTypeMapping = ["Yes" => 1, "No" => 2];
                                        $value = $admissionTypeMapping[$value] ?? '1';
                                        break;
                                    case 'Gender':
                                        $genderType = Gender::where('name', $value)->first();
                                        $value = $genderType->id ?? '';
                                        break;
                                    case 'State':
                                        $state = State::where('name', $value)->first();
                                        $value = $state->id ?? '';
                                        break;
                                    case 'City':
                                        $city = City::where('name', $value)->first();
                                        $value = $city->id ?? '';
                                        break;
                                    case 'D.O.B.':
                                    case 'Ad. Date':
                                        $value = $this->convertExcelDate($value);
                                        break;
                                }
                            
                                // Skip SR.NO and unknown fields
                                if ($index != 'SR.NO' && isset($array[$index]) && Schema::hasColumn('admissions', $array[$index])) {
                                    $val[$array[$index]] = $value;
                                }
                            
                                // Username setup
                                if ($colLetter === 'B') {
                                    $columnBValue = $value ?? '';
                                }
                            
                                // ✅ Mobile setup (col J)
                                if ($colLetter === 'J') {
                                    $columnKValue = preg_replace('/[^0-9]/', '', (string)$value);
                                }
                            }
            
                        // Add default values
                        $val['session_id'] = Session::get('session_id');
                        $val['user_id'] = Session::get('id');
                        $val['branch_id'] = Session::get('branch_id');
                        $val['status'] = 1;
                        $val['school'] = 1;
                        $val['unique_system_id'] = strtoupper(Str::random(10));
                        $val['userName'] = $columnBValue;
                        $val['password'] = Hash::make($columnBValue);
                        $val['confirm_password'] = $columnBValue;
                        $val['mobile'] = $columnKValue;
            
                        $val2[] = $val;
                    }
            //dd($val2);
                    DB::table('admissions')->insert($val2);
                } catch (Exception $e) {
                    Log::error("Student import error: " . $e->getMessage());
                    return redirect('admissionAdd')->with('error', 'Error: Student Not Added!');
                }
            
                return redirect('admissionView')->with('message', 'Student Add Successful!');
            }
       
      function indexToColumnName($index) {
                $columnName = '';
                    while ($index >= 0) {
                        $columnName = chr(($index % 26) + 65) . $columnName;
                        $index = intdiv($index, 26) - 1;
                    }
                return $columnName;
            }
          
      function columnLetterToNumber($columnLetter) {
                $columnNumber = 0;
                $length = strlen($columnLetter);
                    for ($i = 0; $i < $length; $i++) {
                        $columnNumber = $columnNumber * 26 + (ord($columnLetter[$i]) - ord('A') + 1);
                    }
                return $columnNumber;
            }

          protected function convertExcelDate($date)
                    {
                        if (empty($date)) {
                            return null;
                        }
                    
                        // ✅ Excel numeric date (serial number)
                        if (is_numeric($date)) {
                            try {
                                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date)
                                    ->format('Y-m-d');
                            } catch (\Exception $e) {
                                return null;
                            }
                        }
                    
                        // ✅ String date with multiple formats
                        $formats = ['Y-m-d', 'd-m-Y', 'm-d-Y', 'd/m/Y', 'm/d/Y', 'd.m.Y', 'm.d.Y'];
                    
                        foreach ($formats as $format) {
                            try {
                                return \Carbon\Carbon::createFromFormat($format, $date)->format('Y-m-d');
                            } catch (\Exception $e) {
                                continue;
                            }
                        }
                    
                        // ✅ Fallback: let Carbon try automatically
                        try {
                            return \Carbon\Carbon::parse($date)->format('Y-m-d');
                        } catch (\Exception $e) {
                            return null;
                        }
                    }




            }
