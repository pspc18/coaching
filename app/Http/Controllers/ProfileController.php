<?php

namespace App\Http\Controllers;
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
    if ($request->isMethod('post') && (int) Session::get('role_id') === 3) {
        return response()->json([
            'status' => 'error',
            'message' => 'Students are not allowed to update their profile.',
        ], 403);
    }

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
        $roleId = (int) Session::get('role_id');
        $userId = (int) Session::get('id');

        if ($request->isMethod('get')) {
            $user = null;
            if ($roleId === 3) {
                $user = Admission::find($userId);
            } else {
                $user = User::find($userId);
            }
            return view('auth.changepassword', compact('user'));
        }

        // POST / AJAX Request
        $input = $request->all();
        if (!isset($input['old_password']) && isset($input['current_password'])) {
            $input['old_password'] = $input['current_password'];
        }
        if (!isset($input['password_confirmation']) && isset($input['confirm_password'])) {
            $input['password_confirmation'] = $input['confirm_password'];
        }

        $rules = [
            'old_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed', 'different:old_password'],
        ];

        $messages = [
            'old_password.required' => 'Current password is required.',
            'password.required' => 'New password is required.',
            'password.min' => 'New password must be at least 6 characters.',
            'password.confirmed' => 'New password and confirm password do not match.',
            'password.different' => 'New password must be different from current password.',
        ];

        $validator = Validator::make($input, $rules, $messages);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $account = null;
        if ($roleId === 3) {
            $account = Admission::find($userId);
        } else {
            $account = User::find($userId);
        }

        if (!$account) {
            $msg = 'User account not found.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $msg], 404);
            }
            return redirect()->back()->with('error', $msg);
        }

        // Verify current password (supports Hash check and legacy fallback)
        $currentPasswordMatches = Hash::check($input['old_password'], $account->password) ||
                                 ($input['old_password'] === ($account->confirm_password ?? '')) ||
                                 ($input['old_password'] === ($account->password ?? ''));

        if (!$currentPasswordMatches) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => ['old_password' => ['Current password is incorrect. Please try again.']]
                ], 422);
            }
            return redirect()->back()->withErrors(['old_password' => 'Current password is incorrect.'])->withInput();
        }

        // Update password
        $account->password = Hash::make($input['password']);
        $account->confirm_password = $input['password'];
        $account->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Password has been changed successfully!'
            ]);
        }

        return redirect()->back()->with('success', 'Password has been changed successfully!');
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















