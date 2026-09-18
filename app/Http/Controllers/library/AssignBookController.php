<?php

namespace App\Http\Controllers\library;
use Illuminate\Validation\Validator; 
use App\Models\library\AssignBook;
use App\Models\library\LibraryBook;
use App\Models\Admission;
use App\Models\Setting;
use App\Models\User;
use App\Models\Master\Branch;
use App\Models\library\LibraryAssign;
use App\Models\library\RetrunBook;
use App\Models\Master\MessageTemplate;
use App\Models\library\BookInvoice;
use Session;
use Hash;
use Str;
use Redirect;
use Helper;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AssignBookController extends Controller

{
    
    public function bookAssignView(Request $request){
        
        
         $search['name'] = $request->name;
        
         $data = AssignBook::select('assign_books.*','library_books.mrp as book_mrp','library_books.name as name','library_books.book_code','library_books.brand as brand','admissions.first_name','admissions.mobile')
                            ->leftjoin('library_assign', 'library_assign.id','assign_books.library_assign_id')
                            ->leftjoin('admissions', 'admissions.id','library_assign.admission_id')
                            ->leftjoin('library_books', 'library_books.id','assign_books.library_book_id');
                       
       if($request->isMethod('post')){
		     if(!empty($request->name)){
		          $value = $request->name;
    		        $data = $data->where(function($query) use ($value){
        	        	$query->where('admissions.first_name', 'LIKE', '%'.$value.'%');
        		        $query->orWhere('admissions.last_name', 'LIKE', '%'.$value.'%');
        		        $query->orWhere('admissions.mobile', 'LIKE', '%'.$value.'%');
        		        $query->orWhere('admissions.email', 'LIKE', '%'.$value.'%');
        		        $query->orWhere('library_books.name', 'LIKE', '%'.$value.'%');
        		        $query->orWhere('library_books.book_code', 'LIKE', '%'.$value.'%');
        	
        		}); 
		    }
		  
             if (!empty($request->from_date)) {
                $data = $data->where('assign_books.updated_at','>=',$request->from_date.'00:00:00')->where('assign_books.updated_at','<=', $request->to_date.'00:00:00');
            }
           
                }
		    $data = $data->orderBy('assign_books.id','DESC')->get();  
		    
      
                 
       return view('library.assign.view',['data'=>$data,'search'=>$search]);
    }
    
    public function RetrunBookInvoice(Request $request){

         $search['name'] = $request->name;
         
         $data = AssignBook::select('assign_books.*','library_books.mrp as book_mrp','library_books.name as name','library_books.book_code','library_books.brand as brand','admissions.first_name','admissions.father_name','admissions.mobile','admissions.email','book_invoices.amount')
                            ->leftjoin('library_assign', 'library_assign.id','assign_books.library_assign_id')
                             ->leftjoin('book_invoices', 'book_invoices.assign_book_id','assign_books.id')
                            ->leftjoin('admissions', 'admissions.id','library_assign.admission_id')
                            ->leftjoin('library_books', 'library_books.id','assign_books.library_book_id');
                
                if($request->isMethod('post')){
		     if(!empty($request->name)){
		          $value = $request->name;
    		        $data = $data->where(function($query) use ($value){
        	        	$query->where('admissions.first_name', 'LIKE', '%'.$value.'%');
        		        $query->orWhere('admissions.last_name', 'LIKE', '%'.$value.'%');
        		        $query->orWhere('admissions.mobile', 'LIKE', '%'.$value.'%');
        		        $query->orWhere('admissions.email', 'LIKE', '%'.$value.'%');
        		        $query->orWhere('library_books.name', 'LIKE', '%'.$value.'%');
        		        $query->orWhere('library_books.book_code', 'LIKE', '%'.$value.'%');
        	
        		}); 
		    }
		  
             if (!empty($request->from_date)) {
                $data = $data->where('assign_books.updated_at','>=',$request->from_date.'00:00:00')->where('assign_books.updated_at','<=', $request->to_date.'00:00:00');
            }
           
                }
		    $data = $data->where('assign_books.status',1)->orderBy('assign_books.id','DESC')->get();
		    
		    return  view('library.retrun.view',['data'=>$data,'search'=>$search]);
		}            
                            

    
    public function bookAssign(Request $request){
     
        $data = '';
        $search = $request->name;
        if($request->isMethod('post')){
            $request->validate([
            'name' => 'required', 
            ]);
            $search = $request->name;
            $data = LibraryBook::where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))
            ->where('book_code',$search)->get()->first();  
           // dd($data);
              if(!empty($data)){
               $data = $data;
           } else{
            return redirect('book_assign')->with("error",'The book is not available on this barcode');
        } 
        
        }       


            return view('library.assign.add',['data'=>$data,'search'=>$search]);
    
        
    }

    public function searchStudentAssignBook(Request $request){
       
         $search['name'] = $request->name;
        
		 if($request->isMethod('post')){
		     $data =  Admission::where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))->where('status','1');
		     if(!empty($request->name)){
		          $value = $request->name;
    		        $alladmission = $data->where(function($query) use ($value){
        	        	$query->where('first_name', 'LIKE', '%'.$value.'%');
        		        $query->orWhere('father_name', 'LIKE', '%'.$value.'%');
        		        $query->orWhere('mobile', 'LIKE', '%'.$value.'%');
        		        $query->orWhere('email', 'LIKE', '%'.$value.'%');
        	
        		}); 
		    }
            if(!empty($request->stu_qr_code)){
               $alladmission = $data ->where("id", $request->stu_qr_code);
            }
            if(!empty($request->class_type_id)){
               $alladmission = $data ->where("class_type_id", $request->class_type_id);
            }	    
		    $all = $alladmission->orderBy('id','DESC')->get();
		    
		    return  view('library.assign.all_students',['data'=>$all,'search'=>$search]);
		}
    }  
    
    public function searchStudentlibrary(Request $request){
  
		 if($request->isMethod('post')){
		     
		     $data =  LibraryAssign::select('library_assign.*','admissions.first_name','admissions.father_name','admissions.mobile','admissions.email')
                      ->leftjoin('admissions','admissions.id','library_assign.admission_id')->where('library_assign.session_id',Session::get('session_id'))->where('library_assign.branch_id',Session::get('branch_id'));

		     if(!empty($request->name)){
		        
		          $value = $request->name;
    		        $data = $data->where(function($query) use ($value){
        	        	$query->where('admissions.first_name', 'LIKE', '%'.$value.'%');
        		        $query->orWhere('admissions.mobile', 'LIKE', '%'.$value.'%');
        		        $query->orWhere('admissions.father_name', 'LIKE', '%'.$value.'%');
        		}); 
		    }
		
            if(!empty($request->li_stu_qr_code)){
               $data = $data ->where("library_assign.id", $request->li_stu_qr_code);
            }            	    
		    $all = $data->orderBy('library_assign.id','DESC')->get();
	
		    return  view('library.assign.library',['data'=>$all]);
		}
    }  
    public function assignBook(Request $request){
        $setting =Setting::find(1);
       $libraryAssign=LibraryAssign::find($request->library_assign_id);
        //dd($request->library_assign_id);
        
        if($request->isMethod('post')){
            if(!empty($request->admission_id)){
                   foreach($request->admission_id as $key=>$data){
                    $assign = new AssignBook;
                    $assign->user_id = Session::get('id');
                    $assign->branch_id = Session::get('branch_id');
                    $assign->session_id = Session::get('session_id');
                    $assign->library_book_id = $request->library_book_id;
                    $assign->admission_id = $request->admission_id[$key];
                    $assign->save();
                     LibraryBook::where('id',$request->library_book_id)->decrement('quantity',1);
                }
                
                
           
                return redirect('book_assign')->with("message",'Book Assigned Successfully !');
            
                
                }else if(!empty($request->library_assign_id)){
                     

                   foreach($request->library_assign_id as $key=>$data){
                                 
                     $libraryAss=LibraryAssign::find($request->library_assign_id[$key]);
                     $librarybook=LibraryBook::find($request->library_book_id);
  
                    $assign = new AssignBook;
                    $assign->user_id = Session::get('id');
                    $assign->branch_id = Session::get('branch_id');
                    $assign->session_id = Session::get('session_id');
                    $assign->library_book_id = $request->library_book_id;
                    $assign->library_assign_id = $request->library_assign_id[$key];
                    $assign->save();
                    LibraryBook::where('id',$request->library_book_id)->decrement('quantity',1);
                   }    
                
                return redirect('book_assign')->with("message",'Book Assigned Successfully !');
            }else{
                
            $library = new LibraryAssign;//model name
            $library->user_id = Session::get('id');
            $library->session_id = Session::get('session_id');
            $library->branch_id = Session::get('branch_id');
            $library->first_name = $request->first_name;
            $library->last_name = $request->last_name;
            $library->mobile = $request->mobile;
            $library->email = $request->email;
            $library->f_name = $request->father_name;
            $library->gender_id = $request->gender_id;
            $library->address = $request->address;
            $library->status = 1;
            $library->stu_type = 1;
            $library->cabin_status = 1;
            $library->save();
            $library_id= $library->id;
            
            $assign = new AssignBook;
            $assign->user_id = Session::get('id');
            $assign->branch_id = Session::get('branch_id');
            $assign->session_id = Session::get('session_id');
            $assign->library_book_id = $request->library_book_id;
            $assign->library_assign_id =$library_id;
            $assign->save();
            
            LibraryBook::where('id',$request->library_book_id)->decrement('quantity',1);
                    
            return redirect('book_assign')->with("message",'Book Assigned Successfully !');
            }
            
           /* $template =  MessageTemplate::Select('message_templates.*','message_types.slug')
                            ->leftjoin('message_types','message_types.id','message_templates.message_type_id')
                           ->where('message_types.status',1)->where('message_types.slug','bookassign')->first();
            
            if(!empty($template)){
                 $branch = Branch::find(Session::get('branch_id'));
                 $setting = Setting::find(1);
                
                 if($branch->email_srvc == 1 || $request->email){
                        $message = str_replace(
                               array('{#school_name#}', '{#first_name#}', '{#last_name#}', '{#mobile#}', '{#email#}', '{#father_name#}', '{#address#}', '{#school_name#}'),
                                 array($setting->name, $request->first_name, $request->last_name, $request->mobile, $request->email, $request->father_name, $request->address, $setting->name),
                                $template->email_content);
                               // dd($message);
                        $emailData = ['email' => $request->email, 'name' => $message, 'subject' => 'Book Assign'];
                        Helper::sendMail('email_print.bookassign', $emailData);
                                
                    }
                    if($branch->whatsapp_srvc == 1 || $request->mobile){
                              $whatsapp = str_replace(
                             array('{#school_name#}', '{#first_name#}', '{#last_name#}', '{#mobile#}', '{#email#}', '{#father_name#}', '{#address#}', '{#school_name#}'),
                                 array($setting->name, $request->first_name, $request->last_name, $request->mobile, $request->email, $request->father_name, $request->address, $setting->name),
                                $template->whatsapp_content);
                             //dd($template);
                              Helper::sendWhatsappMessage($request->mobile,$whatsapp);
                    }
                    if($branch->sms_srvc == 1 || $request->mobile){
                              $sms = str_replace(
                                array('{#school_name#}', '{#first_name#}', '{#last_name#}', '{#mobile#}', '{#email#}', '{#father_name#}', '{#address#}', '{#school_name#}'),
                                 array($setting->name, $request->first_name, $request->last_name, $request->mobile, $request->email, $request->father_name, $request->address, $setting->name),
                                $template->sms_content);
                              
                              Helper::SendMessage($request->mobile,$sms);
                                
                    }
            }*/
            
            
            
            
            
           
        }
    }
        
         public function returnInvoiceBook(Request $request){
     
            if(!empty($request->assign_book_id)){
                
               $assign_book = AssignBook::find($request->assign_book_id);
              LibraryBook::where('id',$assign_book->library_book_id)->increment('quantity',1);
              $book_name = LibraryBook::where('id',$assign_book->library_book_id)->first();
            
            $invoice_book = new BookInvoice;
            $invoice_book->user_id = Session::get('id');
            $invoice_book->branch_id = Session::get('branch_id');
            $invoice_book->session_id = Session::get('session_id');
            $invoice_book->library_book_id = $assign_book->library_book_id;
            $invoice_book->assign_book_id =$request->assign_book_id;
            $invoice_book->amount =$request->amount;
            $invoice_book->save();
            $assign =  AssignBook::where('id',$request->assign_book_id)->update(['status'=> 1]);
            
            $template =  MessageTemplate::Select('message_templates.*','message_types.slug')
                            ->leftjoin('message_types','message_types.id','message_templates.message_type_id')
                           ->where('message_types.status',1)->where('message_types.slug','return-book')->first();
                          
                    $setting = Setting::where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))->first();
        			$branch = Branch::find(Session::get('branch_id'));
                    $admin = User::where('id',Session::get('id'))->first();
                    
                    $arrey1 =   array(
                                    '{#admin_name#}',
                                    '{#book_name#}',
                                    '{#submit_date#}',
                                    '{#invoice#}',
                                    '{#school_name#}');
                           
                    $arrey2 =   array(
                                    $admin->first_name." ".$admin->last_name,
                                    $book_name->name,
                                    date('d-m-y'),
                                    $request->amount,
                                    $setting->name);
            
                            if($template->status != 1){
                                if($admin->email != ""){
                                    if($branch->email_srvc != 0){
                                        if($template->email_status != 0){
                                            $message = str_replace($arrey1,$arrey2,$template->email_content);
                                            $emailData = ['email' => $admin->email, 'data' => $message, 'subject' => $template->title];
                                            Helper::sendMail('email_print.template_print', $emailData);
                                        } 
                                    } 
                                }
                            
                                if($branch->whatsapp_srvc != 0){
                                    if ($admin->mobile != ""){
                                        if($template->whatsapp_status != 0){
                                            $whatsapp = str_replace($arrey1,$arrey2,$template->whatsapp_content);
                                            Helper::sendWhatsappMessage($admin->mobile, $whatsapp);
                                        }
                                    }
                                }
                                
                                if($branch->sms_srvc != 0){
                                    if($admin->mobile != ""){
                                        if($template->sms_status != 0){
                                            $sms = str_replace($arrey1,$arrey2,$template->sms_content);
                                            Helper::SendMessage($admin->mobile, $sms);
                                        }
                                    }
                                }    
                    } 

           echo 0 ;
        }else{
           echo 1 ;  
        }
    }
    
    
}