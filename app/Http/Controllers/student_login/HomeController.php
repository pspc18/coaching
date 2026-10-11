<?php

namespace App\Http\Controllers\student_login;
use Illuminate\Validation\Validator; 
use App\Models\User;
use App\Models\Admission;
use App\Models\Master\TeacherSubject;
use App\Models\Master\GatePass;
use App\Models\IdCardTemplate;
use Session;
use Hash;
use Str;
use Redirect;
use Auth;
use DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
            
            public function gatePassView(){
                $admissionNo = Admission::where('session_id', Session::get('session_id'))->where('id', Session::get('id'));
                $count = $admissionNo->count();
                $data = "";
                if ($count > 0) {
                    $admissionNo = $admissionNo->first();
                    $data = GatePass::where('admissionNo', $admissionNo->id)->get();
                }
                return view('student_login.gate_pass', ['data' => $data]);
            }
            
            
            
            
    public function my_id_card(Request $request)
    {
        $student = Admission::select('admissions.*', 'class_types.name as class_name')
            ->leftJoin('class_types', 'class_types.id', '=', 'admissions.class_type_id')
            ->where('admissions.id', Session::get('id'))
            ->firstOrFail();

        $template = IdCardTemplate::where('branch_id', Session::get('branch_id'))
            ->latest()
            ->first();

        if (!$template) {
            return back()->with('error', 'No ID Card template found.');
        }

        $design = is_string($template->design_content)
            ? json_decode($template->design_content, true)
            : $template->design_content;

        // Resolve local images to base64 data URIs so html2canvas captures background & photo without CORS issues
        $resolveImageBase64 = function ($subPath, $fileName, $fallbackUrl) {
            if (empty($fileName)) {
                return '';
            }
            $candidates = [
                base_path('schoolimage/' . $subPath . '/' . $fileName),
                base_path('schoolimage-1/' . $subPath . '/' . $fileName),
                public_path('schoolimage/' . $subPath . '/' . $fileName),
                public_path('schoolimage-1/' . $subPath . '/' . $fileName),
                env('IMAGE_UPLOAD_PATH') . $subPath . '/' . $fileName,
            ];
            foreach ($candidates as $cand) {
                if (!empty($cand) && file_exists($cand) && is_readable($cand)) {
                    $ext = strtolower(pathinfo($cand, PATHINFO_EXTENSION));
                    $mime = ($ext === 'png') ? 'image/png' : (($ext === 'webp') ? 'image/webp' : 'image/jpeg');
                    return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($cand));
                }
            }
            return $fallbackUrl;
        };

        $imageShowPath = rtrim(env('IMAGE_SHOW_PATH') ?: config('app.image_show_path') ?: '', '/');
        $bgImageUrl = $resolveImageBase64('id_template_bg', $template->bg_image, $imageShowPath . '/id_template_bg/' . $template->bg_image);
        $studentPhotoUrl = !empty($student->image)
            ? $resolveImageBase64('profile', $student->image, $imageShowPath . '/profile/' . $student->image)
            : '';
        $sealUrl = !empty($template->seal_sign)
            ? $resolveImageBase64('setting/seal_sign', $template->seal_sign, $imageShowPath . '/setting/seal_sign/' . $template->seal_sign)
            : '';

        return view('student_login.my_id_card', compact('student', 'template', 'design', 'bgImageUrl', 'studentPhotoUrl', 'sealUrl'));
    }

}
