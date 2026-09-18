<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Admission;
use Illuminate\Support\Facades\Auth;
use Validator;
use App;
use PDF;




class PdfController extends BaseController
{

	public function studentIdCardPDF(Request $request)
{
    
    try{
        
         $data = Admission::select("*")->find(3);
            
// $pdf = PDF::loadView('print_file.student_print.multipal_id_print', array($data));
//$pdf = PDF::loadFile('print_file.student_print.multipal_id_print')->save('you-file-name.pdf');

        // Set the file name
     //   $fileName = 'test';
 $res =  view('print_file.student_print.multipal_id_print', compact($data));
 
 $fileContents = file_get_contents($res);

// Encode the contents in Base64
$base64Code = base64_encode($fileContents);
        // Return the PDF as a response with the specified file name
        // return $pdf->stream($fileName.'.pdf');
        // return $pdf->download('invoice.pdf');
     return $this->sendResponseData($base64Code, 'success');
       
} catch (Exception $e) {
            return $this->sendError('Validation Error.', 'Error');
        }
}






}