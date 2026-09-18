<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\CcForm;
use App\Models\EventeCertificate;
use App\Models\SportCertificate;
use App\Models\TcCertificate;
use Illuminate\Support\Facades\Auth;
use Validator;
use Hash;
use Session;
use File;
use App;
use URL;
use Image;
use Carbon;
use Str;
use App\Helpers\helper;
use Mail;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\View;


class CertificateController extends BaseController
{
    
        
    public function getAllCertificates(Request $request){
       
       $id = $request->admission_id;
        if($request->isMethod('post')){
          try{
	        
	        
        $cc_certificate_pr =  CcForm::Select('c_certificates_form.*','class_types.name as class_name','admissions.mother_name','admissions.address','admissions.image')
         ->leftjoin('admissions','admissions.id','c_certificates_form.admission_id')
         ->leftjoin('class_types as class_types','class_types.id','c_certificates_form.class_type_id')->where('c_certificates_form.admission_id',$id)->get();
         
        
         $sport_certificate_pr =  SportCertificate::Select('sports_certificates.*','class_types.name as class_name', 'admissions.first_name as stu_name','admissions.mother_name','admissions.address','admissions.image')
         ->leftjoin('admissions','admissions.id','sports_certificates.admission_id')
         ->leftjoin('class_types as class_types','class_types.id','sports_certificates.class_type_id')->where('sports_certificates.admission_id',$id)->get(); 
          
          
          $evente_certificates = EventeCertificate::Select('evente_certificates.*','class_types.name as class_name','admissions.father_name as father_names','admissions.dob','admissions.mother_name','admissions.address','admissions.image','admissions.admissionNo')
      ->leftjoin('admissions','admissions.id','evente_certificates.admission_id')
      ->leftjoin('class_types as class_types','class_types.id','evente_certificates.class_type_id')->where('evente_certificates.admission_id',$id)->get();
                 
         
         
         $count =0;
         
         	         	      $data = array();
	         	if ($cc_certificate_pr) {
	         	    

            foreach ($cc_certificate_pr as $key => $item) {
                $data[] = array(
                    'key' => $count+1,
                    'name' => 'Character Certificate',
                    'event' => $item->character_type,
                    'url' => 'cc_print',
                    'id' => $item->id,
                    
                );
                
                $count++;
            }
	   
	         	} 	    
	         	if ($sport_certificate_pr) {
	         	    
	         	 
            foreach ($sport_certificate_pr as $key => $item) {
                $data[] = array(
                    'key' => $count+1,
                    'name' => 'Sport Certificate',
                    'event' => $item->event_type,
                    'url' => 'sport_print',
                     'id' => $item->id,
                    
                );
                
                $count++;
            }
	   
	         	} 	    
	         	if ($evente_certificates) {
	         	    
	         	 
            foreach ($evente_certificates as $key => $item) {
                $data[] = array(
                    'key' => $count+1,
                    'name' => 'Event Certificate',
                    'event' => $item->event_type,
                    'url' => 'evente_print',
                     'id' => $item->id,
                    
                );
                
                $count++;
            }
	   
	         	} 	    
	return response()->json(['status' => true, 'message' => 'Data Found','data'=>$data], 200);
	

	     } catch (Exception $e) {
            return $this->sendError('Validation Error.', 'Error');
        }
        
    }
    }
    
      public function characterCertificate(Request $request,$id){
          
         
          $certificate_data =  CcForm::Select('c_certificates_form.*','class_types.name as class_name','admissions.mother_name','admissions.address','admissions.image')
        ->leftjoin('admissions','admissions.id','c_certificates_form.admission_id')
        ->leftjoin('class_types as class_types','class_types.id','c_certificates_form.class_type_id')->where('c_certificates_form.id',$id)->get()->first();
        
        $printPreview = Helper::printPreview('Character certificate'); 
        $html = View::make($printPreview, ['data' => $certificate_data])->render();
        
         $dompdf = new Dompdf();
         $dompdf->loadHtml($html);
        
         $dompdf->setPaper('A4', 'landscape');
        
         $dompdf->render();
        
         return $dompdf->stream("Certificate.pdf");
       
      }
    
      public function eventCertificate(Request $request,$id){
          
         
          $certificate_data =  EventeCertificate::Select('evente_certificates.*','class_types.name as class_name','admissions.father_name as father_names','admissions.dob','admissions.mother_name','admissions.address','admissions.image','admissions.admissionNo')
                                                ->leftjoin('admissions','admissions.id','evente_certificates.admission_id')
                                                ->leftjoin('class_types as class_types','class_types.id','evente_certificates.class_type_id')
                                                ->where('evente_certificates.id',$id)->get()->first();
        
            $printPreview = Helper::printPreview('Event certificate'); 
            $html = View::make($printPreview, ['data' => $certificate_data])->render();
        
            $dompdf = new Dompdf();
            $dompdf->loadHtml($html);
            
            $dompdf->setPaper('A4', 'landscape');
            
            $dompdf->render();
        
            return $dompdf->stream("eventCertificate.pdf");
       
      }
    
      public function sportCertificate(Request $request,$id){
          
         
          $certificate_data = SportCertificate::Select('sports_certificates.*','class_types.name as class_name', 'admissions.first_name as stu_name','admissions.mother_name','admissions.address','admissions.image')
                               ->leftjoin('admissions','admissions.id','sports_certificates.admission_id')
                               ->leftjoin('class_types as class_types','class_types.id','sports_certificates.class_type_id')->where('sports_certificates.id',$id)->get()->first();
        
            $printPreview = Helper::printPreview('Sports certificate');

            $html = View::make($printPreview, ['data' => $certificate_data])->render();
        
            $dompdf = new Dompdf();
            $dompdf->loadHtml($html);
            
            $dompdf->setPaper('A4', 'landscape');
            
            $dompdf->render();
        
            return $dompdf->stream("sportCertificate.pdf");
       
      }
/*    
      public function tcCertificate(Request $request,$id){
          
         
          $certificate_data = TcCertificate::select('tc_certificates.*','class_types.name as ClassName', 'admissions.admissionNo','admissions.first_name as first_names','admissions.last_name','admissions.father_name as admissions_father_name','admissions.mother_name as admissions_mother_name')
                            ->leftjoin('admissions','admissions.id','tc_certificates.admission_id')                                                    
                            ->leftjoin('class_types','class_types.id','admissions.class_type_id')   
                            ->where('tc_certificates.id',$id)->first();
        
            $printPreview = Helper::printPreview('Tc certificate');

            $html = View::make($printPreview, ['data' => $certificate_data])->render();
        
            $dompdf = new Dompdf();
            $dompdf->loadHtml($html);
            
            $dompdf->setPaper('A4', 'landscape');
            
            $dompdf->render();
        
            return $dompdf->stream("sportCertificate.pdf");
       
      }*/


}