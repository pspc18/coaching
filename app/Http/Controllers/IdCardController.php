<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Validator;
use App\Models\User;
use App\Models\Setting;
use App\Models\Enquiry;
use App\Models\Admission;
use App\Models\Classs;
use App\Models\BillCounter;
use App\Models\Sessions;
use App\Models\IdCardTemplate;
use App\Models\Master\Branch;
use Session;
use Hash;
use Helper;
use QrCode;
use Response;
use Str;
use PDF;
use Mail;
use DB;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IdCardController extends Controller

{
    
    public function sample_id_print(Request $request)
    {
                $templates = IdCardTemplate::where('branch_id', Session::get('branch_id'))->get();
                return view('print_file.student_print.id_print_template', compact('templates'));
          
    }
    
    
    public function save_template(Request $request)
        {
            
            $request->validate([
                'name' => 'required|string|max:255',
                'design_content' => 'required',
                'bg_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            ]);
        
            $bg_image = '';
            $design_content = json_decode($request->design_content, true);
        
            if ($request->hasFile('bg_image')) {
                $image = $request->file('bg_image');
                $bg_image = time() . uniqid() . '.' . $image->getClientOriginalExtension();
                $destinationPath = env('IMAGE_UPLOAD_PATH') . 'id_template_bg/';
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0777, true);
                }
                $image->move($destinationPath, $bg_image);
            }
        
            if ($request->has('templateId') && $request->templateId) {
             
                $template = IdCardTemplate::find($request->templateId);
                if ($template) {
                    $template->update([
                        'name' => $request->name,
                        'type' => "ID Card",
                        'design_content' => $design_content,
                        'bg_image' => $bg_image ?: $template->bg_image,
                    ]);
                    return response()->json(['message' => 'Template updated successfully']);
                }
            }
        
          
            IdCardTemplate::create([
                'name' => $request->name,
                'type' => "ID Card",
                'session_id' => session('session_id'),
                'user_id' => session('id'),
                'branch_id' => session('branch_id'),
                'design_content' => $design_content,
                'bg_image' => $bg_image,
            ]);
        
            return response()->json(['message' => 'Template saved successfully']);
        }
          
}
