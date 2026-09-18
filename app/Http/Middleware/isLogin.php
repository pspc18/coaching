<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Setting;
use Session;


class isLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next 
     * @return mixed
     */
   public function handle($request, Closure $next)
        { 
            
          
            $loginWithOtp = Setting::first();
            $ignoreRoutes = [
                'is-Login',
                'sendWhatsapp',
                'validateOtpWhatsapp'
            ];
        
            if (!session()->has('id') && $request->hasCookie('remember_token')) {
                $token = $request->cookie('remember_token');
                $isStudent = false;
                $userData = \App\Models\User::where('remember_token', $token)->first();
        
                if (!$userData) {
                    $userData = \App\Models\Admission::where('remember_token', $token)->orderBy('id','DESC')->first();
                    $isStudent = !empty($userData);
                }
        
                if ($userData && (int) ($userData->status ?? 0) === 1) {
                    $sessionId = $isStudent
                        ? (int) ($userData->session_id ?? 0)
                        : (int) (Setting::where('branch_id', $userData->branch_id)->value('current_active_session_id')
                            ?: ($userData->session_id ?? 0));
                    $attendanceUniqueId = trim((string) ($userData->attendance_unique_id ?? ''));

                    session()->put([
                        'id' => $userData->id,
                        'name' => $userData->name ?? trim((string) (($userData->first_name ?? '') . ' ' . ($userData->last_name ?? ''))),
                        'email' => $userData->email,
                        'teacher_id' => $userData->teacher_id ?? null,
                        'branch_id' => $userData->branch_id,
                        'session_id' => $sessionId,
                        'userName' => $userData->userName,
                        'first_name' => $userData->first_name,
                        'last_name' => $userData->last_name,
                        'role_id' => $userData->role_id,
                        'class_type_id' => $userData->class_type_id ?? null,
                        'attendance_unique_id' => $attendanceUniqueId,
                        'modal_name' => $isStudent ? 'Admission' : 'User',
                        'admin_branch_id' => null,
                        'otp_request' => 'accepted',
                    ]);
                }
            }
        
            // OTP Check
            if ($loginWithOtp->loginWithOtp == 'Yes' && session()->get('role_id') != 1 ) {
                if (session()->get('otp_request') != 'accepted' && session()->get('id') != '' ) {
                    if (!in_array($request->path(), $ignoreRoutes)) {
                        return redirect()->route('access.denied');
                    }
                }
            }
        
            if (!session()->has('id')) {
                return redirect()->intended('login');
            }
            
            // if (!session()->has('validated_on') || session('validated_on') !== date('Y-m-d'))
            // if (($r = app(\App\Services\RuntimeSync::class)->RuntimeSyncCheck($request)) instanceof \Illuminate\Http\Response) return $r;
        
        
            return $next($request);
        }

}
