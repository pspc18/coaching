<?php

namespace App\Http\Controllers;
use Illuminate\Validation\Validator; 
use App\Models\DownloadCenter;
use App\Models\ExaminationAdmitCard;
use App\Models\Master\TeacherSubject;
use App\Models\FeesCollect;
use App\Models\fees\FeesAssign;
use Session;
use Hash;
use Str;
use Redirect;
use Response;
use Auth;
use File;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class DownloadController extends Controller

{
            public function downloadCenter(){
                $upload_count = DownloadCenter::where('session_id',Session::get('session_id'))
                ->where('branch_id',Session::get('branch_id'))->count();
                $assignments_count = DownloadCenter::where('session_id',Session::get('session_id'))
                ->where('branch_id',Session::get('branch_id'))->where('content_type','Assignments')->count();
                $syllabus_count =  DownloadCenter::where('session_id',Session::get('session_id'))
                ->where('branch_id',Session::get('branch_id'))->where('content_type','Syllabus')->count();
                $study_material_count =  DownloadCenter::where('session_id',Session::get('session_id'))
                ->where('branch_id',Session::get('branch_id'))->where('content_type','Study Material')->count();
                $other_doc_count =  DownloadCenter::where('session_id',Session::get('session_id'))
                ->where('branch_id',Session::get('branch_id'))->where('content_type','Other Downloads')->count();
                return view('download_center.download_center',['upload_count'=>$upload_count,'assignments_count'=>$assignments_count,
                'syllabus_count'=>$syllabus_count,'study_material_count'=>$study_material_count,'other_doc_count'=>$other_doc_count]);
            }
            public function upload(Request $request){
                if($request->isMethod('post')){
                    $request->validate([
                        'content_title' => 'required',    
                        'content_type' => 'required',    
                        'upload_date' => 'required',  
                        'content_file' => 'nullable|mimes:jpg,jpeg,png,gif,webp,pdf,mp4,avi,mov,mkv|max:51200', // 50MB  
                       
                    ]);
                    $download_center = '';

                    if ($request->hasFile('content_file')) {

                        $file = $request->file('content_file');

                        $extension = strtolower($file->getClientOriginalExtension());

                        $fileName = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());

                        $destinationPath = env('IMAGE_UPLOAD_PATH') . 'download_center/';

                        if (!file_exists($destinationPath)) {
                            mkdir($destinationPath, 0755, true);
                        }

                        // Compress only images
                        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {

                            Image::make($file)
                                ->resize(600, null, function ($constraint) {
                                    $constraint->aspectRatio();
                                    $constraint->upsize();
                                })
                                ->save($destinationPath . $fileName, 80);

                        } else {
                            // PDF & Video
                            $file->move($destinationPath, $fileName);
                        }

                        $download_center = $fileName;
                    }
                    $upload = new DownloadCenter;//model name
                    $upload->user_id = Session::get('id');
                    $upload->session_id = Session::get('session_id');
                    $upload->branch_id = Session::get('branch_id');
                    $upload->content_title = $request->content_title;
                    $upload->class_type_id = $request->class_search_id;
                    $upload->content_type = $request->content_type;
                    $upload->upload_date = $request->upload_date;
                    $upload->description = $request->description;
                    $upload->video_link = $request->video_link;
                    $upload->content_file = $download_center;
                    $upload->save();
                    return response()->json(['status' => 'success','message' => 'Content Uploaded Successfully !',]);   
                   
                }   
                $upload_list = DownloadCenter::select('download_center.*','types.name as class_name')
                ->leftjoin('class_types as types','types.id','download_center.class_type_id');
                if(Session::get('role_id') > 1){    
                    if(Session::get('role_id') == 2){
                        $classes = TeacherSubject::where('teacher_id',Session::get('teacher_id'))->where('branch_id',Session::get('branch_id'))->groupBy('class_type_id')->get();
                        if(!empty($classes)){
                            $att = array();
                            foreach($classes as $item){
                                $att[] = $item->class_type_id;
                            }
                            $upload_list = $upload_list->whereIn('class_type_id',$att);
                        }
                    }
                    
                }
                $upload_list = $upload_list->where('download_center.session_id',Session::get('session_id'))
                ->where('download_center.branch_id',Session::get('branch_id'))->orderBy('id', 'DESC')->get();
                return view('download_center.upload',['dataview'=>$upload_list]);
            }
    
        public function upload_edit(Request $request,$id){
            
        $upload = DownloadCenter::find($id);
        if($request->isMethod('post')){
        //dd($request);
            $request->validate([
            
            'content_title' => 'required',    
            'content_type' => 'required',    
            'upload_date' => 'required',    
            ]);

                    if ($request->file('content_file')) {

                        $oldFile = env('IMAGE_UPLOAD_PATH') . 'download_center/' . $upload->content_file;

                        $file = $request->file('content_file');
                        $originalName = $file->getClientOriginalName();
                        $extension = strtolower($file->getClientOriginalExtension());
                        $destinationPath = env('IMAGE_UPLOAD_PATH') . 'download_center/';
                        if (!file_exists($destinationPath)) {
                            mkdir($destinationPath, 0755, true);
                        }
                        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                            $compressedImage = Image::make($file)
                                ->resize(600, null, function ($constraint) {
                                    $constraint->aspectRatio();
                                    $constraint->upsize();
                                })
                                ->encode($extension, 80);
                    
                            $compressedImage->save($destinationPath . $originalName);
                    
                            $download_center = $originalName;
                        } else {
                            $file->move($destinationPath, $originalName);
                            $download_center =  $originalName;
                        }
                $upload->content_file = $download_center;

                if (!empty($oldFile) && File::exists($oldFile)) {
                    File::delete($oldFile);
                }
            }
                 
            $upload->user_id = Session::get('id');
            $upload->session_id = Session::get('session_id');
            $upload->branch_id = Session::get('branch_id');
            $upload->content_title = $request->content_title;
            $upload->content_type = $request->content_type;
            $upload->upload_date = $request->upload_date;
            $upload->class_type_id = $request->class_search_id;
             $upload->video_link = $request->video_link;
            $upload->description = $request->description;
            $upload->save();
            
            return response()->json(['status' => 'success', 'message' => 'Content Updated Successfully !','redirect' => url('upload/content')]);
        }   
        $upload_list = DownloadCenter::where('session_id',Session::get('session_id'))
                             ->where('branch_id',Session::get('branch_id'))->orderBy('id', 'DESC')->get();
        
        return view('download_center.upload_edit',['dataview'=>$upload_list,'upload_data'=>$upload]);
 
    }   

            public function uploadDelete(Request $request){
                $id = $request->delete_id;
                $upload_list = DownloadCenter::find($id);
                if(File::exists(env('IMAGE_UPLOAD_PATH').'download_center/'.$upload_list->content_file)){
                    File::delete(env('IMAGE_UPLOAD_PATH').'download_center/'.$upload_list->content_file);
                }        
                $upload_delete = DownloadCenter::find($id)->delete();
                return redirect::to('upload/content')->with('message', 'Content Deleted Successfully !');
            }
    
            public function assignments(){
                $assignments = DownloadCenter::select('download_center.*','types.name as class_name')
                ->leftjoin('class_types as types','types.id','download_center.class_type_id')
                ->where('download_center.session_id',Session::get('session_id'))
                ->where('download_center.branch_id',Session::get('branch_id'));
                if(Session::get('role_id') > 1){
                    if(Session::get('role_id') == 2){
                        $classes = TeacherSubject::where('teacher_id',Session::get('teacher_id'))->groupBy('class_type_id')->get();
                        if(!empty($classes)){
                            $att = array();
                            foreach($classes as $item){
                                $att[] = $item->class_type_id;
                            }
                            $assignments =$assignments->whereIn('class_type_id',$att);
                        }
                    }
                    elseif(Session::get('role_id') == 3){
                        $assignments = $assignments->where('download_center.class_type_id',Session::get('class_type_id'));
                    }
                }
                $assignments = $assignments->where('content_type','Assignments')->orderBy('id', 'DESC')->get();
                return view('download_center.assignments',['data'=>$assignments]);  
            }
    
            public function studyMaterials(){
                //$studyMaterials = DownloadCenter::where('session_id',Session::get('session_id'));
                $studyMaterials = DownloadCenter::select('download_center.*','types.name as class_name')
                ->leftjoin('class_types as types','types.id','download_center.class_type_id')
                ->where('download_center.session_id',Session::get('session_id'))
                ->where('download_center.branch_id',Session::get('branch_id'));
                if(Session::get('role_id') > 1){
                    if(Session::get('role_id') == 2){
                        $classes = TeacherSubject::where('teacher_id',Session::get('teacher_id'))->groupBy('class_type_id')->get();
                        if(!empty($classes)){
                            $att = array();
                            foreach($classes as $item){
                                $att[] = $item->class_type_id;
                            }
                            $studyMaterials =$studyMaterials->whereIn('class_type_id',$att);
                        }
                    }
                    elseif(Session::get('role_id') == 3){
                        $studyMaterials = $studyMaterials->where('download_center.class_type_id',Session::get('class_type_id'));
                    }
                }
                $studyMaterials = $studyMaterials->where('content_type','Study Material')->orderBy('id', 'DESC')->get();
                return view('download_center.study_material',['data'=>$studyMaterials]);  
            }
            
            public function syllabus(){
                //$syllabus = DownloadCenter::where('session_id',Session::get('session_id'));
                $syllabus = DownloadCenter::select('download_center.*','types.name as class_name')
                ->leftjoin('class_types as types','types.id','download_center.class_type_id')
                ->where('download_center.session_id',Session::get('session_id'))
                ->where('download_center.branch_id',Session::get('branch_id'));
                if(Session::get('role_id') > 1){
                    if(Session::get('role_id') == 2){
                        $classes = TeacherSubject::where('teacher_id',Session::get('teacher_id'))->groupBy('class_type_id')->get();
                        if(!empty($classes)){
                            $att = array();
                            foreach($classes as $item){
                                $att[] = $item->class_type_id;
                            }
                            $syllabus =$syllabus->whereIn('class_type_id',$att);
                        }
                    }
                    elseif(Session::get('role_id') == 3){
                        $syllabus = $syllabus->where('download_center.class_type_id',Session::get('class_type_id'));
                    }
                }
                $syllabus = $syllabus->where('content_type','Syllabus')->orderBy('id', 'DESC')->get();
                return view('download_center.syllabus',['data'=>$syllabus]);  
            }
            
            public function studentAdmitCard(Request $request){
                $data = '';
                $fees =[];
                if(Session::get('role_id') == 3){
                    $total_school_fees =  FeesAssign::where('admission_id',Session::get('id'))->first();
                    if(!empty($total_school_fees)){
                        $collected_school_fees = FeesCollect::where('admission_id',Session::get('id'))->first();
                        $fees['total_school_fees']  = $total_school_fees->net_amount ?? 0;
                        $fees['collected_school_fees'] =  $collected_school_fees->amount ?? 0;
                    }else
                    {
                        $fees['total_school_fees'] =0;
                        $fees['collected_school_fees'] =0;
                    }
                    $data = ExaminationAdmitCard::select('examination_admit_cards.*','exam.name as exam_name','admission.email','admission.mobile')
                    ->leftjoin('exams as exam','exam.id','examination_admit_cards.exam_id')
                    ->leftjoin('admissions as admission','admission.id','examination_admit_cards.admission_id')
                    ->where('examination_admit_cards.admission_id',Session::get('id'))
                    ->where('examination_admit_cards.branch_id',Session::get('branch_id'))->orderBy('created_at' ,'ASC' )->get();
                }
                else{
                    $data = [];
                }
                return view('download_center.studentAdmitCard',['data'=>$data,'fees'=>$fees]);  
            }
            
            public function otherDownloads(){
                //$otherDownloads = DownloadCenter::where('session_id',Session::get('session_id'));
                $otherDownloads = DownloadCenter::select('download_center.*','types.name as class_name')
                ->leftjoin('class_types as types','types.id','download_center.class_type_id')
                ->where('download_center.session_id',Session::get('session_id'))
                ->where('download_center.branch_id',Session::get('branch_id'));
                if(Session::get('role_id') > 1){
                    if(Session::get('role_id') == 2){
                        $classes = TeacherSubject::where('teacher_id',Session::get('teacher_id'))->groupBy('class_type_id')->get();
                        if(!empty($classes)){
                            $att = array();
                            foreach($classes as $item){
                                $att[] = $item->class_type_id;
                            }
                            $otherDownloads =$otherDownloads->whereIn('class_type_id',$att);
                        }
                    }
                    elseif(Session::get('role_id') == 3){
                        $otherDownloads = $otherDownloads->where('download_center.class_type_id',Session::get('class_type_id'));
                    }
                }
                $otherDownloads = $otherDownloads->orderBy('id', 'DESC')->get();
                return view('download_center.other_downloads',['data'=>$otherDownloads]);  
            }
            
            // public function download($id)
            //     {
            //         // ✅ id से record निकालो
            //         $file = DownloadCenter::find($id);
            
            //         if (!$file) {
            //             return redirect()->back()->with('error', 'File not found.');
            //         }
            
            //         // ✅ path बनाओ
            //         $filePath = env('IMAGE_UPLOAD_PATH') .'download_center/'. $file->content_file; 
            //         // मान लो DB में "download_center/abc.pdf" save है
            
            //         if (file_exists($filePath)) {
            //             return response()->download($filePath, basename($filePath));
            //         } else {
            //             return redirect()->back()->with('error', 'File not found on server.');
            //         }
            //     }


      public function download($id)
{
    $file = DownloadCenter::find($id);

    if (!$file) {
        return back()->with('error', 'File not found.');
    }

    $filePath = env('IMAGE_UPLOAD_PATH').'download_center/'.$file->content_file;

    if (!file_exists($filePath)) {
        return back()->with('error', 'File not found on server.');
    }

    return response()->download($filePath, basename($file->content_file));
}

}    