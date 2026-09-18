<?php

namespace App\Http\Controllers\student_login;
use App\Models\User;
use App\Models\Admission;
use App\Models\StudentDocument;
use Helper;
use Session;
use Hash;
use Str;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
class ProfileController extends Controller

{
   public function profileEdit(Request $request)
   {
    $data = Admission::findOrFail(Session::get('id'));

    if ($request->boolean('delete_photo')) {

        if ($data->image && file_exists(env('IMAGE_UPLOAD_PATH') . 'profile/' . $data->image)) {
            unlink(env('IMAGE_UPLOAD_PATH') . 'profile/' . $data->image);
        }

        $data->image = null;
        $data->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Photo Removed Successfully.'
        ]);
    }

    if ($request->hasFile('photo')) {
        $request->validate([
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
        ]);

        // delete old image if exists
        if ($data->image && file_exists(env('IMAGE_UPLOAD_PATH') . 'profile/' . $data->image)) {
            unlink(env('IMAGE_UPLOAD_PATH') . 'profile/' . $data->image);
        }

        $image = $request->file('photo');
        $student_image = time() . '_' . Str::random(12) . '.' . $image->getClientOriginalExtension();
        $destinationPath = env('IMAGE_UPLOAD_PATH') . 'profile/';
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }
        $image->move($destinationPath, $student_image);

        $data->image = $student_image;
        $data->save();

        return response()->json([
            'status'     => 'success',
            'message'    => 'Photo uploaded successfully.',
            'image_url'  => env('IMAGE_SHOW_PATH') . '/profile/' . $student_image
        ]);
    }

    if ($request->isMethod('post')) {
        $validated = $request->validate([
            'email' => ['nullable', 'email', 'max:150'],
            'mobile' => ['nullable', 'digits_between:8,15'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        $data->email = $validated['email'] ?? null;
        $data->mobile = $validated['mobile'] ?? null;
        $data->address = $validated['address'] ?? null;
        $data->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Contact details updated successfully.',
            'data' => $validated,
        ]);
    }

    return view('student_login.profile', ["data" => $data]);
   }

    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'different:current_password'],
        ], [
            'password.different' => 'New password must be different from your current password.',
        ]);

        $student = Admission::findOrFail(Session::get('id'));

        if (!Hash::check($validated['current_password'], $student->password)) {
            return response()->json([
                'message' => 'The current password is incorrect.',
                'errors' => ['current_password' => ['The current password is incorrect.']],
            ], 422);
        }

        $student->password = Hash::make($validated['password']);
        $student->confirm_password = $validated['password'];
        $student->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Password changed successfully.',
        ]);
    }
    
    public function document_upload(Request $request, $id)
        {
            $data = Admission::find($id);
        
            if (!$data) {
                return redirect()->back()->with('error', 'Student not found.');
            }
        
            // Validate request
            $request->validate([
              
            ]);
        
            // Handle file upload
            if ($request->hasFile('file')) {
                
                 $file = '';
                    if ($request->file('file')) {
                        $image = $request->file('file');
                        $file = time() . uniqid() . '.' . $image->getClientOriginalExtension();
                        $destinationPath = env('IMAGE_UPLOAD_PATH') . 'student_document/';
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0755, true);
                    }
                    if (isset($data->file) && File::exists($destinationPath . $data->file)) {
                        File::delete($destinationPath . $data->file);
                    }
                    $compressedImage = Image::make($image)
                        ->resize(600, null, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        })
                        ->encode('jpg', 80); // Adjust quality as needed
                        $compressedImage->save($destinationPath . $file);
                      
                    }
        
                // Create a new document record
                $document = new StudentDocument();
                $document->admission_id = $id;
                $document->title = $request->title;
                $document->file = $file;
                $document->remark = $request->remark;
                $document->save();
            }
        
            return redirect()->back()->with('message', 'Document uploaded successfully.');
        }



}















