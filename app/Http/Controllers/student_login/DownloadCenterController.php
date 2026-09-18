<?php

namespace App\Http\Controllers\student_login;
use Illuminate\Validation\Validator;
use App\Models\DownloadCenter;
use Session;
use Hash;
use Str;
use Redirect;
use Response;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DownloadCenterController extends Controller
{
            public function studentDownloadCenter(){
                $assignments = DownloadCenter::where('session_id', Session::get('session_id'))
                    ->where('branch_id', Session::get('branch_id'))->orderBy('id', 'DESC')->get();
                return view('student_login.download_center.download_center', ['data' => $assignments]);
            }
            
            public function assignments(){
                $assignments = DownloadCenter::where('session_id', Session::get('session_id'))
                    ->where('branch_id', Session::get('branch_id'))->orderBy('id', 'DESC')->get();
                return view('student_login.download_center.studentAssignments', ['data' => $assignments]);
            }
        
            public function studyMaterials(){
                $studyMaterials = DownloadCenter::where('session_id', Session::get('session_id'))
                    ->where('branch_id', Session::get('branch_id'))->orderBy('id', 'DESC')->get();
                return view('student_login.download_center.student_study_material', ['data' => $studyMaterials]);
            }
            
            public function syllabus(){
                $syllabus = DownloadCenter::where('session_id', Session::get('session_id'))
                    ->where('branch_id', Session::get('branch_id'))->orderBy('id', 'DESC')->get();
                return view('student_login.download_center.student_syllabus', ['data' => $syllabus]);
            }
            
            public function student_download_center()
            {
                $otherDownloads = DownloadCenter::where('session_id', Session::get('session_id'))
                    ->where('branch_id', Session::get('branch_id'))
                    ->where(function ($query) {
                        $query->whereNull('class_type_id')
                            ->orWhere('class_type_id', '')
                            ->orWhere('class_type_id', Session::get('class_type_id'));
                    })
                    ->orderBy('id', 'DESC')
                    ->get();

                return view('student_login.download_center.student_download_center', [
                    'dataview' => $otherDownloads
                ]);
            }

            // public function otherDownloads(){
            //     $otherDownloads = DownloadCenter::where('session_id', Session::get('session_id'))
            //         ->where('branch_id', Session::get('branch_id'))->orderBy('id', 'DESC')->get();
            //     return view('student_login.download_center.student_other_downloads', ['dataview' => $otherDownloads]);
            // }

}