<?php
namespace App\Helpers;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Carbon;
use DB;
use Mail;
use Session;
use File;
use Http;
use App\Models\Admin;
use App\Models\User;
use App\Models\SidebarSub;
use App\Models\FailedMessages;
use App\Models\Admission;
use App\Models\exam\digital\ChapterDigital;
use App\Models\exam\digital\ExamResultDigital;
use App\Models\exam\digital\ExamPatternDigital;
use App\Models\exam\digital\TopicDigital;
use App\Models\exam\digital\SukaDigital;
use App\Models\exam\digital\UploadByDigital;
use App\Models\exam\digital\QuestionTypeDigital;
use App\Models\exam\digital\LevelDigital;
use App\Models\TotalDays;
use App\Models\WhatsappApiResponse;
use App\Models\StudentAttendance;
use App\Models\Master\TeacherSubject; 
use App\Models\Month;
use App\Models\FeesReminder;
use App\Models\Master\SubjectStreams; 
use App\Models\Master\Weekendcalendar; 
use App\Models\exam\Exam;
use App\Models\Remark;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\fees\FeesAssignDetail;
use App\Models\Setting;
use App\Models\ClassType;
use App\Models\BloodGroup;
use App\Models\Enquiry;
use App\Models\exam\FillMarks;
use App\Models\exam\FillMinMaxMarks;
use App\Models\Sidebar;
use App\Models\Gender;
use App\Models\Subject;
use App\Models\FeesGroup;
use App\Models\FeesMaster;
use App\Models\FeesDetail;
use App\Models\FeesAssign;
use App\Models\FeesCollect;
use App\Models\OnlinePaymentTransaction;
use App\Models\AdmitCardNote;
use App\Models\UserPermission;
use App\Models\AttendanceStatus;
use App\Models\Master\Branch;
use App\Models\Master\EnquiryStatus;
use App\Models\Master\PaymentMode;
use App\Models\Master\Role;
use App\Models\Master\MessageContent;
use App\Models\Master\MessageType;
use App\Models\Expense;
use App\Models\Master\HomeworkReview;
use App\Models\Master\HomeworkDocuments;
use App\Models\exam\ExamResultDetail;
use App\Models\exam\ExamResult;
use App\Models\exam\AssignExam;
use App\Models\ExaminationScheduleDetail;
use App\Models\Sessions;
use App\Models\NotificationToken;
use App\Models\Notification;
use App\Models\MessageQueue;
use App\Jobs\SendMessageJob;
use App\Models\StudentField;
use App\Services\FcmDirectService;
use Illuminate\Support\Facades\Cache;
use DateTime;
use Response;
class Helper{

    private static $cachedSettings = [];
    private static $cachedMasterData = [];
    private static $cachedUserData = [];
    private static $cachedUserPermisn = [];
    private static $cachedSubPermisn = [];
    private static $cachedBranches = [];

    public static function clearSettingCache($branchId = null)
    {
        self::$cachedSettings = [];
        if ($branchId) {
            Cache::forget('app_setting_' . $branchId);
        } else {
            for ($i = 1; $i <= 50; $i++) {
                Cache::forget('app_setting_' . $i);
            }
        }
        Cache::forget('app_setting_1');
    }

    public static function clearStudentCache($branchId = null, $sessionId = null)
    {
        $branchId = $branchId ?: Session::get('branch_id');
        $sessionId = $sessionId ?: Session::get('session_id');
        $today = date('Y-m-d');
        if ($branchId && $sessionId) {
            Cache::forget('student_birthdays_' . $branchId . '_' . $sessionId . '_' . $today);
            Cache::forget('dashboard_admin_' . $branchId . '_' . $sessionId . '_' . $today);
            Cache::forget('dashboard_teacher_' . $branchId . '_' . $sessionId . '_' . $today);
            Cache::forget('active_students_count_' . $branchId . '_' . $sessionId);
        }
    }

    public static function clearAttendanceCache($branchId = null, $sessionId = null)
    {
        $branchId = $branchId ?: Session::get('branch_id');
        $sessionId = $sessionId ?: Session::get('session_id');
        $today = date('Y-m-d');
        if ($branchId && $sessionId) {
            Cache::forget('dashboard_admin_' . $branchId . '_' . $sessionId . '_' . $today);
            Cache::forget('dashboard_teacher_' . $branchId . '_' . $sessionId . '_' . $today);
            Cache::forget('attendance_stats_' . $branchId . '_' . $sessionId . '_' . $today);
        }
    }

    public static function clearFeesCache($branchId = null, $sessionId = null)
    {
        $branchId = $branchId ?: Session::get('branch_id');
        $sessionId = $sessionId ?: Session::get('session_id');
        $today = date('Y-m-d');
        if ($branchId && $sessionId) {
            Cache::forget('dashboard_admin_' . $branchId . '_' . $sessionId . '_' . $today);
            Cache::forget('fee_stats_' . $branchId . '_' . $sessionId);
            Cache::forget('fee_by_month_' . $branchId . '_' . $sessionId . '_' . date('Y'));
            Cache::forget('total_fee_collection_' . $branchId . '_' . $sessionId);
            Cache::forget('today_fee_collection_' . $branchId . '_' . $sessionId . '_' . $today);
        }
    }

    public static function clearExpenseCache($branchId = null, $sessionId = null)
    {
        $branchId = $branchId ?: Session::get('branch_id');
        $sessionId = $sessionId ?: Session::get('session_id');
        $today = date('Y-m-d');
        if ($branchId && $sessionId) {
            Cache::forget('dashboard_admin_' . $branchId . '_' . $sessionId . '_' . $today);
            Cache::forget('expense_stats_' . $branchId . '_' . $sessionId . '_' . $today);
            Cache::forget('expense_by_month_' . $branchId . '_' . $sessionId . '_' . date('Y'));
        }
    }

    public static function clearNoticeCache($branchId = null, $sessionId = null)
    {
        $branchId = $branchId ?: Session::get('branch_id');
        $sessionId = $sessionId ?: Session::get('session_id');
        $today = date('Y-m-d');
        if ($branchId && $sessionId) {
            Cache::forget('dashboard_admin_' . $branchId . '_' . $sessionId . '_' . $today);
            Cache::forget('notice_board_' . $branchId . '_' . $sessionId . '_' . $today);
        }
    }

    public static function noticeBoard()
    {
        $branchId = Session::get('branch_id');
        $sessionId = Session::get('session_id');
        $today = date('Y-m-d');
        $roleId = (int) Session::get('role_id');
        $userId = (int) Session::get('id');

        // If logged-in user is a student (role 3), provide student-specific notices matching student-notices
        if ($roleId === 3 && !empty($userId)) {
            return self::getStudentNotices($userId, $branchId, $sessionId, 6);
        }

        $cacheKey = 'notice_board_' . $branchId . '_' . $sessionId . '_' . $today;

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 600, function() use ($branchId, $sessionId, $today) {
            $query = \App\Models\ManagedNotice::where('status', 'approved')
                ->whereDate('to_date', '>=', $today);
            if (!empty($branchId)) {
                $query->where('branch_id', $branchId);
            }
            if (!empty($sessionId)) {
                $query->where('session_id', $sessionId);
            }
            return $query->orderBy('from_date', 'DESC')->orderBy('id', 'DESC')->get();
        });
    }

    public static function getStudentNotices($studentId = null, $branchId = null, $sessionId = null, $limit = 6)
    {
        $studentId = (int) ($studentId ?: Session::get('id'));
        $branchId = (int) ($branchId ?: Session::get('branch_id'));
        $sessionId = (int) ($sessionId ?: Session::get('session_id'));
        $today = date('Y-m-d');

        if (empty($studentId)) {
            return collect();
        }

        $cacheKey = 'student_notices_' . $studentId . '_' . $branchId . '_' . $sessionId . '_' . $today . '_' . $limit;

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 300, function() use ($studentId, $branchId, $sessionId, $today, $limit) {
            $baseQuery = \App\Models\ManagedNotice::query()
                ->select([
                    'id',
                    'title',
                    'message',
                    'attachment_path',
                    'attachment_name',
                    'attachment_size',
                    'from_date',
                    'to_date',
                    'created_by',
                    'published_at',
                    'status',
                ])
                ->with(['creator:id,first_name,last_name'])
                ->where('status', 'approved')
                ->where('branch_id', $branchId)
                ->where('session_id', $sessionId)
                ->whereHas('recipients', function ($q) use ($studentId) {
                    $q->where('recipient_type', 'student')
                      ->where('recipient_id', $studentId);
                });

            // Prioritize Upcoming first, then Active, then Recent
            return (clone $baseQuery)
                ->orderByRaw("
                    CASE 
                        WHEN from_date > ? THEN 1
                        WHEN from_date <= ? AND to_date >= ? THEN 2
                        ELSE 3
                    END ASC
                ", [$today, $today, $today])
                ->orderByDesc('from_date')
                ->orderByDesc('id')
                ->take($limit)
                ->get();
        });
    }

    public static function getStudentNoticeCounts($studentId = null, $branchId = null, $sessionId = null)
    {
        $studentId = (int) ($studentId ?: Session::get('id'));
        $branchId = (int) ($branchId ?: Session::get('branch_id'));
        $sessionId = (int) ($sessionId ?: Session::get('session_id'));
        $today = date('Y-m-d');

        if (empty($studentId)) {
            return (object) ['total_count' => 0, 'active_count' => 0, 'upcoming_count' => 0, 'expired_count' => 0];
        }

        $cacheKey = 'student_notice_counts_' . $studentId . '_' . $branchId . '_' . $sessionId . '_' . $today;

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 300, function() use ($studentId, $branchId, $sessionId, $today) {
            return \App\Models\ManagedNotice::query()
                ->where('status', 'approved')
                ->where('branch_id', $branchId)
                ->where('session_id', $sessionId)
                ->whereHas('recipients', function ($q) use ($studentId) {
                    $q->where('recipient_type', 'student')
                      ->where('recipient_id', $studentId);
                })
                ->selectRaw('COUNT(*) as total_count')
                ->selectRaw('SUM(CASE WHEN from_date <= ? AND to_date >= ? THEN 1 ELSE 0 END) as active_count', [$today, $today])
                ->selectRaw('SUM(CASE WHEN from_date > ? THEN 1 ELSE 0 END) as upcoming_count', [$today])
                ->selectRaw('SUM(CASE WHEN to_date < ? THEN 1 ELSE 0 END) as expired_count', [$today])
                ->first() ?: (object) ['total_count' => 0, 'active_count' => 0, 'upcoming_count' => 0, 'expired_count' => 0];
        });
    }

    public static function formatFileSize($bytes)
    {
        $bytes = (float) $bytes;
        if ($bytes <= 0) return '';
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1) . ' MB';
        }
        return number_format($bytes / 1024, 0) . ' KB';
    }

    public static function printPreview($subModule)
    {
        $map = [
            'Fees Collect' => 'print_file.student_print.print_fees',
            'Admission Print' => 'print_file.student_print.admissionStudentPrint',
            'Student Id Print' => 'print_file.student_print.admissionStudentIdPrint',
            'Enquiry Print' => 'print_file.student_print.registration_print',
            'Admit Card' => 'print_file.pdf.admit_card_all',
            'Teacher Drop' => 'print_file.staff_print.drop_teacher',
            'Teacher Joining Latter' => 'print_file.staff_print.joining_letter_print',
            'Teacher Id Card' => 'print_file.staff_print.id_print',
            'Time Table' => 'print_file.exam_time_table',
        ];

        return $map[$subModule] ?? 'print_file.view';
    }

    public static function clearComplaintCache($branchId = null, $sessionId = null)
    {
        $branchId = $branchId ?: Session::get('branch_id');
        $sessionId = $sessionId ?: Session::get('session_id');
        $today = date('Y-m-d');
        if ($branchId && $sessionId) {
            Cache::forget('dashboard_admin_' . $branchId . '_' . $sessionId . '_' . $today);
            Cache::forget('complaint_stats_' . $branchId . '_' . $sessionId);
        }
    }

    public static function clearDashboardCache($branchId = null, $sessionId = null)
    {
        $branchId = $branchId ?: Session::get('branch_id');
        $sessionId = $sessionId ?: Session::get('session_id');
        $today = date('Y-m-d');
        if ($branchId && $sessionId) {
            Cache::forget('dashboard_admin_' . $branchId . '_' . $sessionId . '_' . $today);
            Cache::forget('dashboard_teacher_' . $branchId . '_' . $sessionId . '_' . $today);
        }
    }

    public static function clearMasterCache()
    {
        self::$cachedMasterData = [];
        Cache::forget('master_counters');
        Cache::forget('master_exam_types');
        Cache::forget('master_food_categories');
        Cache::forget('master_enquiry_statuses');
        Cache::forget('master_blood_groups');
        Cache::forget('master_genders');
        Cache::forget('master_payment_modes');
        Cache::forget('master_sessions');
    }

    public static function getSetting(){
        $branchId = Session::get('branch_id') ?: 1;
        if (isset(self::$cachedSettings[$branchId])) {
            return self::$cachedSettings[$branchId];
        }

        $setting = Cache::remember('app_setting_' . $branchId, 86400, function () use ($branchId) {
            $data = Setting::where('branch_id', $branchId)
                ->with(['City', 'Country', 'State'])
                ->first();

            if (empty($data) && $branchId != 1) {
                $data = Setting::where('branch_id', 1)
                    ->with(['City', 'Country', 'State'])
                    ->first();
            }

            return $data;
        });

        self::$cachedSettings[$branchId] = $setting;
        return $setting;
    } 

    public static function getLogoUrl()
    {
        $setting = self::getSetting();
        $logoFile = $setting->left_logo ?? '';

        // Local public assets first: 100% reliable on both localhost and live domain
        if (file_exists(public_path('assets/school/img/logo.png'))) {
            return asset('assets/school/img/logo.png') . '?v=' . filemtime(public_path('assets/school/img/logo.png'));
        }

        if (!empty($logoFile)) {
            return rtrim((string) env('IMAGE_SHOW_PATH'), '/') . '/setting/left_logo/' . rawurlencode($logoFile);
        }

        return asset('assets/school/img/logo.png');
    }

    public static function getFaviconUrl()
    {
        if (file_exists(public_path('favicon.ico')) && filesize(public_path('favicon.ico')) > 0) {
            return asset('favicon.ico') . '?v=' . filemtime(public_path('favicon.ico'));
        }
        if (file_exists(public_path('favicon.png'))) {
            return asset('favicon.png') . '?v=' . filemtime(public_path('favicon.png'));
        }
        return self::getLogoUrl();
    }

    public static function getFirmAcronym($default = 'ACI')
    {
        $setting = self::getSetting();
        $name = trim($setting->name ?? '');
        if (empty($name)) {
            return $default;
        }

        $cleanName = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $name);
        $words = preg_split('/\s+/', trim($cleanName), -1, PREG_SPLIT_NO_EMPTY);
        if (empty($words)) {
            return $default;
        }

        if (count($words) === 1) {
            $word = $words[0];
            return mb_strlen($word, 'UTF-8') <= 5 ? mb_strtoupper($word, 'UTF-8') : mb_strtoupper(mb_substr($word, 0, 3, 'UTF-8'));
        }

        $ignoreWords = ['of', 'and', 'the', 'for', 'in', 'on', 'at', 'to', 'a', 'an', '&'];
        $acronym = '';
        foreach ($words as $w) {
            if (in_array(strtolower($w), $ignoreWords)) {
                continue;
            }
            $acronym .= mb_strtoupper(mb_substr($w, 0, 1, 'UTF-8'));
        }

        if (empty($acronym)) {
            foreach ($words as $w) {
                $acronym .= mb_strtoupper(mb_substr($w, 0, 1, 'UTF-8'));
            }
        }

        return !empty($acronym) ? $acronym : $default;
    }

    public static function getPageTitle($explicitTitle = null)
    {
        $explicitTitle = trim((string) $explicitTitle);
        if (!empty($explicitTitle)) {
            return $explicitTitle;
        }

        $request = request();
        if (!$request) {
            return 'Dashboard';
        }

        $path = trim($request->path(), '/');
        if ($path === '' || $path === '/' || $path === 'dashboard' || $path === 'home') {
            return 'Dashboard';
        }

        $routeMap = [
            'login' => 'Sign In',
            'change_password' => 'Change Password',
            'stream_update' => 'Stream Update',
            'studentParticularPerformance' => 'Student Performance',
            'student_performance' => 'Student Performance',
            'students/admission' => 'Student Admission',
            'admissionView' => 'Student Details',
            'students/enquiry' => 'Student Enquiry',
            'enquiry' => 'Student Enquiry',
            'fees/fees_collect' => 'Fees Collection',
            'fees_collect' => 'Fees Collection',
            'fees_history' => 'Fees History',
            'fees_receipt' => 'Fees Receipt',
            'offline_exam' => 'Offline Examination',
            'exam' => 'Exams',
            'attendance' => 'Attendance',
            'student_attendance' => 'Student Attendance',
            'staff_attendance' => 'Staff Attendance',
            'download_center' => 'Download Center',
            'student_download_center' => 'Download Center',
            'homework' => 'Homework',
            'notice' => 'Notices',
            'notification' => 'Notifications',
            'profile' => 'Profile',
            'setting' => 'Settings',
            'branch' => 'Branches',
            'expense' => 'Expenses',
            'users' => 'Users',
            'staff' => 'Staff Management',
            'classes' => 'Class Management',
            'subject' => 'Subject Management',
            'timetable' => 'Time Table',
            'certificate' => 'Certificates',
            'library' => 'Library Management',
            'transport' => 'Transport Management',
            'hostel' => 'Hostel Management',
            'inventory' => 'Inventory Management',
        ];

        if (isset($routeMap[$path])) {
            return $routeMap[$path];
        }

        $segments = explode('/', $path);
        $semanticSegments = array_values(array_filter($segments, function($seg) {
            return !is_numeric($seg) && !preg_match('/^[0-9a-fA-F-]{10,}$/', $seg);
        }));

        if (empty($semanticSegments)) {
            return 'Dashboard';
        }

        $lastSegment = end($semanticSegments);
        if (isset($routeMap[$lastSegment])) {
            return $routeMap[$lastSegment];
        }

        $spaced = preg_replace('/(?<!^)([A-Z])/', ' $1', $lastSegment);
        $spaced = str_replace(['_', '-'], ' ', $spaced);
        $formatted = ucwords(trim($spaced));

        return !empty($formatted) ? $formatted : 'Dashboard';
    }
     

    public static function uploadImageUniversal($file, $folder, $oldImage = null, $fileName = null, $branchWise = false)
    {
        if (empty($file)) {
            return $oldImage;
        }

        $branchId = Session::get('branch_id');
        $folder = trim($folder, '/\\');
        if ($branchWise && !empty($branchId)) {
            $folder .= '/' . $branchId;
        }

        $rootUploadPath = env('IMAGE_UPLOAD_PATH');
        if (empty($rootUploadPath)) {
            $rootUploadPath = base_path('schoolimage');
        }
        $baseUploadPath = rtrim($rootUploadPath, '/\\') . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $folder);
        $localMirrorPath = base_path('schoolimage' . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $folder));

        if (!File::isDirectory($baseUploadPath)) {
            File::makeDirectory($baseUploadPath, 0777, true, true);
        }
        if (!File::isDirectory($localMirrorPath)) {
            File::makeDirectory($localMirrorPath, 0777, true, true);
        }

        $extension = strtolower($file->getClientOriginalExtension());
        $newFileName = $fileName ?: (time() . uniqid() . ($extension ? '.' . $extension : ''));
        $file->move($baseUploadPath, $newFileName);

        $uploadedPath = $baseUploadPath . DIRECTORY_SEPARATOR . $newFileName;
        $localFilePath = $localMirrorPath . DIRECTORY_SEPARATOR . $newFileName;
        if (File::exists($uploadedPath)) {
            File::copy($uploadedPath, $localFilePath);
        }

        if (!empty($oldImage)) {
            $oldServerFile = $baseUploadPath . DIRECTORY_SEPARATOR . $oldImage;
            $oldLocalFile = $localMirrorPath . DIRECTORY_SEPARATOR . $oldImage;
            if (File::exists($oldServerFile)) {
                File::delete($oldServerFile);
            }
            if (File::exists($oldLocalFile)) {
                File::delete($oldLocalFile);
            }
        }

        return $newFileName;
    }
    

    public static function sendNotification($title, $body, $type = 'student', $ids = null, $image = null, $icon = null, $data = [], $priority = 'high')
    {
        if ($type === 'firebase') {
            return self::sendFirebaseNotification($title, $body, $ids, $data, $priority, $image);
        }

        $query = NotificationToken::where('platform', 'android');
        if ($type === 'student') {
            $column = 'admission_id';
        } elseif ($type === 'user') {
            $column = 'user_id';
        } else {
            return ['success' => false, 'message' => 'Invalid type specified.'];
        }

        if (is_array($ids) && !empty($ids)) {
            $query->whereIn($column, $ids);
        } elseif (is_numeric($ids)) {
            $query->where($column, $ids);
        }

        $tokens = $query->pluck('device_token')->filter()->unique()->values()->all();
        if (empty($tokens)) {
            return ['success' => true, 'skipped' => true, 'message' => 'No device tokens found.'];
        }

        $payloadData = is_array($data) ? $data : [];
        $payloadData = array_merge(['customKey' => 'customValue'], $payloadData);

        if (!empty($icon)) {
            $payloadData['icon'] = (string) $icon;
        }
        if (!empty($image)) {
            $payloadData['image'] = (string) $image;
        }

        $channelType = null;
        foreach (['notification_type', 'notificationType', 'channel_id', 'channelId', 'type'] as $key) {
            if (!isset($payloadData[$key]) || !is_string($payloadData[$key])) {
                continue;
            }
            $candidate = str_replace([' ', '-'], '_', strtolower(trim($payloadData[$key])));
            if ($candidate !== '') {
                $channelType = $candidate;
                break;
            }
        }

        if (!empty($channelType)) {
            $payloadData['notification_type'] = $channelType;
            $payloadData['channel_id'] = $payloadData['channel_id'] ?? $channelType;
            $payloadData['channelId'] = $payloadData['channelId'] ?? $channelType;
        }

        return self::sendFirebaseNotification($title, $body, $tokens, $payloadData, $priority, $image);
    }

    private static function sendFirebaseNotification($title, $body, $token, $data = [], $priority = 'high', $image = null)
    {
        $data = is_array($data) ? $data : [];
        $tokens = is_array($token) ? $token : [$token];
        $tokens = array_values(array_unique(array_filter(array_map(function ($item) {
            if (is_object($item)) {
                return trim((string) ($item->token ?? $item->device_token ?? ''));
            }
            return trim((string) $item);
        }, $tokens))));

        $data = array_map(function ($value) {
            if (is_bool($value)) {
                return $value ? 'true' : 'false';
            }
            if (is_scalar($value) || $value === null) {
                return (string) $value;
            }
            return json_encode($value);
        }, $data);

        if (empty($tokens)) {
            return ['success' => false, 'message' => 'No FCM token found.'];
        }

        $fcmService = new FcmDirectService();
        $responses = [];
        $failures = [];

        foreach ($tokens as $fcmToken) {
            $result = $fcmService->send($fcmToken, $data, $priority, $title, $body, $image);
            $responses[] = ['token' => $fcmToken, 'result' => $result];

            if (empty($result['success'])) {
                $firebaseDetails = $result['firebase_details'] ?? [];
                $firebaseErrorCode = $firebaseDetails['details'][0]['errorCode'] ?? null;
                if ($firebaseErrorCode === 'UNREGISTERED') {
                    NotificationToken::where('device_token', $fcmToken)->delete();
                }
                $failures[] = [
                    'token' => $fcmToken,
                    'error' => $result['error'] ?? 'Failed to send notification via Firebase.',
                    'firebase_details' => $firebaseDetails ?: null,
                ];
            }
        }

        if (!empty($failures)) {
            return [
                'success' => false,
                'message' => 'One or more notifications failed via Firebase.',
                'failed' => $failures,
                'data' => $responses,
            ];
        }

        return [
            'success' => true,
            'message' => 'Notification sent successfully via Firebase.',
            'data' => $responses,
            'message_id' => $responses[0]['result']['message_id'] ?? null,
        ];
    }
    
     public static function getUsers(){
          
        
      $users = User::select('users.*','role.name as role_name')
        ->leftjoin('role','role.id','users.role_id')
         ->orderBy('users.role_id')->get();
          return $users ;
      }

    public static function getAllUsers(){
        $users = User::select('users.*', 'role.name as role_name')
            ->leftjoin('role', 'role.id', 'users.role_id')
            ->whereNull('users.deleted_at');
        if (Session::has('branch_id')) {
            $users->where('users.branch_id', Session::get('branch_id'));
        }
        return $users->orderBy('users.first_name', 'ASC')->get();
    }
    public static function sendMail($tmplale,$data) {
                /*Mail::send($tmplale, $data, function($message) use ($data) {
                    $message->from(getenv('MAIL_FROM_ADDRESS'));
                    $message->to($data['email']);
                    $message->subject($data['subject']);
                    if(!empty($data['file'])){
                        $message->attach($data['file']); 
                    }
                 
               });*/
               
    }
    
    
    
     public static function getSiblings(){
          $studentId = Session::get('id');
          if (empty($studentId)) {
              return collect([]);
          }
          $current = Admission::find($studentId);
          if (!$current || empty($current->ledger_no)) {
              return collect([]);
          }
          $siblings = Admission::where('ledger_no', $current->ledger_no)
                    ->where('session_id', Session::get('session_id'))
                    ->where('branch_id', Session::get('branch_id'))
                    ->where('status', 1)
                    ->where('id', '!=', $studentId)
                    ->whereNotNull('ledger_no')
                    ->get();
          return $siblings;
     }
    
   








    public static function sendWhatsappMessage($toMobile, $text, $filepath = null, $filename = null) {
       
         //$toMobile = 8003486291; 
        if (empty($toMobile)) {
            return ['status' => 'error', 'message' => 'Mobile number is required.'];
        }
         
        $authKey = 'bTRtZXhLcTU1cmIrOHJTMTN3UnZHZz09';   
        $params = [
            'username' => 'ariseinstitute',
            'number'   => '91' . $toMobile,
            'message'  => ($text ?? ''),   
            'token'    => $authKey
        ];
         
        if (!empty($filepath)) {
            $params['file_url'] = $filepath;  
            $params['file_name'] = $filename ?? 'attachment';   
            $serverUrl = "https://int.chatway.in/api/send-file";
        } else {
            $serverUrl = "https://int.chatway.in/api/send-msg";
        }
         
        $url = $serverUrl . '?' . http_build_query($params);
         
        //echo "Final URL: " . $url . PHP_EOL;
         
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
        // Execute the request
        $response = curl_exec($ch);
        
        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            dd($error);
            return ['status' => 'error', 'message' => $error];
        }
    
        curl_close($ch);
        return ['status' => 'success', 'response' =>$response];
    }
    
     public static function classTypeExam()
        {
            $query = ClassType::where('session_id', Session::get('session_id'))->where('branch_id', Session::get('branch_id'))->orderBy('orderBy', 'ASC');
    
            // ðŸ”¹ Role = Teacher
            if (Session::get('role_id') == 2) {
    
                $classIds = TeacherSubject::where('user_id', Session::get('id'))
                    ->pluck('class_type_id')
                    ->unique()
                    ->toArray();
    
                // Agar teacher ke paas koi class nahi hai
                if (empty($classIds)) {
                    return collect();
                }
    
                $query->whereIn('id', $classIds);
            }
    
            return $query->get();
        }



    public static function sendWhatsappGroupMessage($group_id,  $text, $type=null,$filepath = null){
 if(empty($type)){
         $type ='text';
      }
        if (!empty($group_id)) {
            $serverUrl = "https://wapp.powerstext.in/api/send_group";
            $params = array(
                'group_id' => $group_id,
                'type' => $type,
                'message' => $text,
                'media_url' => $filepath,
                'instance_id' => '6613950872DC3',
                'access_token' => '661393ca7a4d7'
            );
           
            $url = $serverUrl . '?' . http_build_query($params);
             //dd($url);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $output = curl_exec($ch);
            curl_close($ch);


        $decode = json_decode($output);

        if($decode->status == 'error')
        {
            $data = new FailedMessages;
            $data->branch_id =Session::get('branch_id');
            $data->session_id =Session::get('session_id');
            $data->group_id =$group_id;
            $data->type = $type;
            $data->platform = 'whatsapp';
            $data->media_url = $filepath;
            $data->sender_message = $text;
            $data->status = $decode->status ?? '';
            $data->message = $decode->message ?? '';
            $data->save();
            
        }
        
      
            return $output;
        }
    }

     public static function SendMessage($mobile="",$msg="",$template_id="",$apiRequest=""){
        
     //  dd($mobile);
    // $mobile = 8619305581;
        $curl = curl_init();

        $data = array();

        $data['api_id'] = "API8rRTz3v6148315";

        $data['api_password'] = "Hb9KyI1A";
            
        $data['sms_type'] = "Transactional";
                
        $data['sms_encoding'] = $apiRequest;

        $data['sender'] = "ARISEJ";

        $data['number'] = $mobile;

        $data['message'] = $msg;

        $data['template_id'] = $template_id;

        $data_string = json_encode($data);
        $ch = curl_init('http://bulksmsplans.com/api/send_sms');

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");

        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(

            'Content-Type: application/json',

            'Content-Length: ' . strlen($data_string))

        );

        $result = curl_exec($ch);
        return $result;

      //  exit;

      
      
    }


   
    
    

    public static function getresult($table_name,$id=null){
         
            $result = DB::table($table_name);
            
            if($id >0){
              $result =$result->where('id',$id);
             
                 }
         $result = $result->orderBy('id','DESC')->get();
       
       return $result;
    }
   

  
   
   public static function getCounters(){
       return collect([]);
   }
   
     public static function getExamType(){
       return Cache::remember('master_exam_types', 86400, function() {
           return Exam::all();
       });
   }
   
     public static function getFoodCategory(){
       return Cache::remember('master_food_categories', 86400, function() {
           return MessFoodCategory::all();
       });
   }
   
     public static function getEnquiryStatus(){
       return Cache::remember('master_enquiry_statuses', 86400, function() {
           return EnquiryStatus::whereNull('deleted_at')->get();
       });
   }
   
       public static function getDocumentsIsNull($id){
        return '';
    }  
    
   public static function getCountry(){
       $getCountry = Country::where('id',101)->get();
       return $getCountry;
   }
   
   public static function getState(){
        $country_id = Setting::where('branch_id',Session::get('branch_id'))->get()->first();
        
        if(empty($country_id))
        {
            $getstate = State::where('country_id',101)->get();
        }
        else
        {
            $getstate = State::where('country_id',$country_id->country_id)->get();
        }
       
       return $getstate;
   
   }
   
    public static function getMessageType(){
        $getMessageType = MessageType::where('status',1)->get();
        return $getMessageType;
    }
   
   public static function getallStudent(){
       $getallStudent = Admission::get();
       return $getallStudent;
   
   }
   
     public static function getstudentbirthday(){
         $branchId = Session::get('branch_id');
         $sessionId = Session::get('session_id');
         $today = date('Y-m-d');
         $cacheKey = 'student_birthdays_' . $branchId . '_' . $sessionId . '_' . $today;

         return Cache::remember($cacheKey, 3600, function() use ($branchId, $sessionId) {
             return Admission::leftjoin('class_types', 'admissions.class_type_id', '=', 'class_types.id')
                 ->select('admissions.*', 'class_types.name as class_name')
                 ->whereMonth('dob', now()->month)
                 ->whereDay('dob', now()->day)
                 ->where('admissions.session_id', $sessionId)
                 ->where('admissions.branch_id', $branchId)
                 ->get();
         });
     }  
   
    public static function getRemark(){
        $branchId = Session::get('branch_id');
        $sessionId = Session::get('session_id');
        $today = date('Y-m-d');
        $cacheKey = 'remarks_' . $branchId . '_' . $sessionId . '_' . $today;

        return Cache::remember($cacheKey, 1800, function() use ($branchId, $sessionId, $today) {
            try {
                return Remark::where('date', $today)
                    ->where('session_id', $sessionId)
                    ->where('branch_id', $branchId)
                    ->orderBy('id', 'DESC')
                    ->get();
            } catch (\Exception $e) {
                return collect([]);
            }
        });
    }


    public static function getUsersBirthday(){
        $today = date('Y-m-d');
        return Cache::remember('users_birthdays_' . $today, 3600, function() {
            return User::whereMonth('dob', now()->month)
                ->whereDay('dob', now()->day)->orderBy('id', 'DESC')->get();
        });
    }  
   
   
    public static function getCity($state_ids=null){
        $state_id = Setting::where('branch_id',Session::get('branch_id'))->get()->first();
         if(empty($state_ids))
        {
            $state_ids = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41];
            $getcitie = City::whereIn('state_id',$state_ids)->get();
        }
        else
        {
            $getcitie = City::where('state_id',$state_ids)->get();
        }
       
       return $getcitie;
    }
    public static function getQRCode($id){
        return null;
    }
                        
   
    public static function classType()
    {
        $query = ClassType::where('session_id', Session::get('session_id'))
            ->where('branch_id', Session::get('branch_id'))
            ->orderBy('orderBy', 'ASC');

        if (Session::get('role_id') >= 2) {

            $user = User::find(Session::get('id'));

            if (!$user || empty($user->class_type_id)) {
                return collect();
            }

            $classIds = is_array($user->class_type_id)
                ? $user->class_type_id
                : explode(',', $user->class_type_id);

            $query->whereIn('id', $classIds);
        }

        return $query->get();
    }

    public static function studentexamview(){
        return \App\Models\exam\Exam::where('session_id', Session::get('session_id'))
            ->where('branch_id', Session::get('branch_id'))
            ->whereNull('deleted_at')
            ->get();
    }

    public static function examPanelClassType(){
          $getTypeclass = ClassType::orderBy('orderBy', 'ASC');
        if(Session::get('role_id') == 2)
        {
            
            
            $checkClassTeacher = null;
         
            $classes = TeacherSubject::where('teacher_id',Session::get('teacher_id'))->groupBy('class_type_id')->get();
             $att = array();
              
              if(!empty($classes))
              {
                  foreach($classes as $item)
                  {
                      $att[] = $item->class_type_id;
                  }
              }
              
              if(!empty($checkClassTeacher))
              {
                 
                      $att[] = $checkClassTeacher->class_type_id;
                
              }
              
            
           
            $getTypeclass = $getTypeclass->whereIn('id',$att);
            
        }
        
        $getTypeclass = $getTypeclass->get();
        return $getTypeclass;
 
    }

    public static function bloodGroupType(){
       return Cache::remember('master_blood_groups', 86400, function() {
           return BloodGroup::orderBy('id', 'ASC')->get();
       });
    }

           
           
    public static function getMarks($exam_id,$className ,$admissionNo){
       $marks = FillMarks::where('exam_id',$exam_id)->where('class_type_id',$className)->where('admission_id',$admissionNo)->where('session_id', Session::get('session_id'))->get(['id','subject_id','student_marks']);
     
       return $marks;
   
   }
    public static function getMaxMarks($exam_id,$className ){
       $marks = FillMinMaxMarks::where('exam_id',$exam_id)->where('class_type_id',$className)->where('session_id', Session::get('session_id'))->get(['subject_id','exam_minimum_marks','exam_maximum_marks']);
     
       return $marks;
   
   }
    public static function roleType(){
       $getRole = Role::orderBy('id', 'ASC')->get();
       return $getRole;
   
   }
   

    public static function getrole(){
       $role = Role::where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))->orderBy('id', 'DESC')->get();
       return $role;
   
   }

    public static function getPermisn() {
        $userId = Session::get('id');
        if (!$userId) {
            return [];
        }
        if (isset(self::$cachedUserPermisn[$userId])) {
            return self::$cachedUserPermisn[$userId];
        }

        // For Admin (role_id == 1), provide all active modules
        if ((int) Session::get('role_id') === 1) {
            $mainPermisn = DB::table('sidebars')
                ->pluck('id')
                ->map(fn($id) => (string) $id)
                ->toArray();
            self::$cachedUserPermisn[$userId] = $mainPermisn;
            return $mainPermisn;
        }

        $userPerm = DB::table('user_permission')
            ->where('user_id', $userId)
            ->whereNull('deleted_at')
            ->get(['sidebar_id']);

        $mainPermisn = [];
        if ($userPerm->isNotEmpty()) {
            foreach ($userPerm as $perm) {
                if ($perm->sidebar_id) {
                    $ids = explode(',', $perm->sidebar_id);
                    $mainPermisn = array_merge($mainPermisn, $ids);
                }
            }
            $mainPermisn = array_unique($mainPermisn);
        } else {
            $roleId = Session::get('role_id');
            if ($roleId) {
                $rolePerm = DB::table('role_permissions')->where('role_id', $roleId)->where('view', 1)->pluck('sidebar_id')->toArray();
                $mainPermisn = array_unique($rolePerm);
            }
        }

        self::$cachedUserPermisn[$userId] = $mainPermisn;
        return $mainPermisn;
    }

    // Get sub-sidebar IDs as array
    public static function getSubPermisn($sidebar_id) {
        $userId = Session::get('id');
        if (!$userId) {
            return [];
        }
        $cacheKey = $userId . '_' . $sidebar_id;
        if (isset(self::$cachedSubPermisn[$cacheKey])) {
            return self::$cachedSubPermisn[$cacheKey];
        }

        // For Admin (role_id == 1), provide all active submenus for this sidebar
        if ((int) Session::get('role_id') === 1) {
            $result = DB::table('sidebar_sub')
                ->where('sidebar_id', $sidebar_id)
                ->whereNull('deleted_at')
                ->pluck('id')
                ->map(fn($id) => (string) $id)
                ->toArray();
            self::$cachedSubPermisn[$cacheKey] = $result;
            return $result;
        }

        $userPermSub = DB::table('user_permission')
            ->where('user_id', $userId)
            ->where('sidebar_id', $sidebar_id)
            ->whereNull('deleted_at')
            ->first();

        if ($userPermSub && $userPermSub->sub_sidebar_id) {
            $result = explode(',', $userPermSub->sub_sidebar_id);
            self::$cachedSubPermisn[$cacheKey] = $result;
            return $result;
        }

        self::$cachedSubPermisn[$cacheKey] = [];
        return [];
    }



   
   public static function getPermisnByBranch(){
       $branchId = Session::get('branch_id');
       if (!$branchId) {
           return null;
       }
       return Cache::remember('master_branch_' . $branchId, 86400, function() use ($branchId) {
           return Branch::find($branchId);
       });
   }
     public static function getAllBranch() {
        $userId = Session::get('id');
        $roleId = Session::get('role_id');
        $branchId = Session::get('branch_id');
        $cacheKey = ($userId ?: '0') . '_' . ($roleId ?: '0') . '_' . ($branchId ?: '0');

        if (isset(self::$cachedBranches[$cacheKey])) {
            return self::$cachedBranches[$cacheKey];
        }

        $users = User::find($userId);
        $data = Branch::orderBy('id', 'ASC');

        if ($roleId > 1 && $roleId != 3) {
            $branchIds = !empty($users->access_branch_id) ? explode(',', $users->access_branch_id) : [];
            if(!empty($branchIds)){
                $data = $data->whereIn('id', $branchIds);
            }else{
                $data = $data->where('id', $branchId);
            }
        }

        $result = $data->get();
        self::$cachedBranches[$cacheKey] = $result;
        return $result;
    }

   
   public static function getStudents(){
       $getStudents = Enquiry::where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))->orderBy('id', 'DESC')->get();
       return $getStudents;
   }

   public static function getStudent(){
       $getStudent = Admission::where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))->orderBy('id', 'DESC')->get();
       return $getStudent;
   }
   
   public static function attendanceType(){
       $getAttendance= AttendanceStatus::where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))->orderBy('id', 'DESC')->get();
       return $getAttendance;
   }
   
  public static function getaccount(){
        return collect([]);
    }

  
   public static function getInvantory(){
       return collect([]);
   }


   
   public static function getgender(){
       return Cache::remember('master_genders', 86400, function() {
           return Gender::orderBy('id', 'ASC')->get();
       });
   }
   
   public static function getAttendanceStatus(){
       $getAttendanceStatus = AttendanceStatus::where('status',1)->orderBy('id', 'ASC')->get();
       return $getAttendanceStatus;
   }

   public static function getMarkableAttendanceStatus(){
       return AttendanceStatus::markable()
           ->where('status', 1)
           ->orderBy('id', 'ASC')
           ->get();
   }
 
   public static function getSubject(){
       $getsubject = Subject::orderBy('id', 'ASC')->get();
       return $getsubject;
   }
   
   public static function getStreamSubjects($class_type_id){
       $streamSubjects = Subject::where('class_type_id',$class_type_id)->orderBy('id', 'ASC')->get();
       return $streamSubjects;
   }
   


   public static function getFeesGroup(){
       $getFeesGroup = FeesGroup::where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))->orderBy('id', 'ASC')->get();
       return $getFeesGroup;
   }   




   
 
   public static function getPaymentMode(){
       return Cache::remember('master_payment_modes', 86400, function() {
           return PaymentMode::orderBy('id', 'ASC')->get();
       });
   } 

   public static function getStuFeesDetail($fees_group_id,$fees_type_id,$admission_id){
     
        $data = FeesCollect::where('fees_group_id',$fees_group_id)->where('fees_type_id',$fees_type_id)->where('admission_id',$admission_id)->get()->first();
        
       return $data;
   
   }

   public static function feesType(){
       $feesType = FeesMaster::with('FeesGroup')->where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))->get();
       return $feesType;
   } 

   public static function onlinePayDetail(){
       $onlinePayDetail = OnlinePaymentTransaction::with('Student')->where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))->get()->first();
       return $onlinePayDetail;
   } 
   
   public static function busRoute(){
       return collect([]);
   }
    
   public static function bus(){
       return collect([]);
   }
    
   public static function busRouteAssign($route_id = null){
       return collect([]);
   }

   public static function busAssign(){
       return null;
   }   
    
    public static function getUser(){
      $role=Session::get('role_id');
      $user_id=Session::get('id');
      $teacher_id=Session::get('teacher_id');
      $student_id=Session::get('id');
      $branch_id=Session::get('branch_id');
      $cacheKey = ($role ?? '') . '_' . ($user_id ?? '') . '_' . ($branch_id ?? '');

      if (isset(self::$cachedUserData[$cacheKey])) {
          return self::$cachedUserData[$cacheKey];
      }

        if($role==3){
           $studentData = Admission::with('ClassTypes')->where('id',$student_id)->where('branch_id',$branch_id)->first();
           self::$cachedUserData[$cacheKey] = $studentData;
           return $studentData;
        }else{
           $userData = User::where('id',$user_id)->first(); 
           self::$cachedUserData[$cacheKey] = $userData;
           return $userData;
        }
          
    }
      
   public static function getHostel(){
        return collect([]);
    }      
     
    public static function getSession(){
        return Cache::remember('master_sessions', 86400, function() {
            return Sessions::all();
        });
    }
   
  

    public static function task(){
        return collect([]);
    }
 

    public static function homeworkReview(){
        $homeworkReview = HomeworkReview::orderBy('id','ASC')->get();
        return $homeworkReview;
    }
     public static function count($id){
        $homeworkReview = HomeworkDocuments::where('upload_hw_id',$id)->where('status','0')->count(); 
        return $homeworkReview;
    
    
        }

    public static function getHwDocument($id){
       $hwDocument = HomeworkDocuments::where('upload_hw_id',$id)->get();
       return $hwDocument;
   }

    public static function examAtndStu($id){
       $exam = AssignExam::where('exam_id',$id)->get()->first();
       $examAtndStu['atnStu'] = ExamResult::where('exam_id',$id)->count();
       $examAtndStu['examRes'] = ExamResult::where('exam_id',$id)->sum('percentage');
       $examAtndStu['allStu'] = Admission::where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))->where('class_type_id',$exam->class_type_id)->where('status',1)->count();
       
       return $examAtndStu;
    } 


    
    public static function chartAttendanceStudentsClassWise($date = null)
{
    $sessionId = Session::get('session_id');
    $branchId  = Session::get('branch_id');
    $selectedDate = $date ?? date('Y-m-d');

    $records = DB::table('attendance_marks')
        ->join('admissions', 'admissions.attendance_unique_id', '=', 'attendance_marks.unique_id')
        ->select(
            'admissions.class_type_id',

            DB::raw("SUM(CASE WHEN LOWER(attendance_marks.status) IN ('present','in','out') THEN 1 ELSE 0 END) as present"),
            DB::raw("SUM(CASE WHEN LOWER(attendance_marks.status) = 'absent' THEN 1 ELSE 0 END) as absent"),
            DB::raw("SUM(CASE WHEN LOWER(attendance_marks.status) = 'holiday' THEN 1 ELSE 0 END) as holiday"),
            DB::raw("SUM(CASE WHEN LOWER(attendance_marks.status) = 'leave' THEN 1 ELSE 0 END) as leave_count"),
            DB::raw("SUM(CASE WHEN LOWER(attendance_marks.status) = 'event' THEN 1 ELSE 0 END) as event_count"),
            DB::raw("SUM(CASE WHEN LOWER(attendance_marks.status) = 'exam' THEN 1 ELSE 0 END) as exam_count"),
            DB::raw("SUM(CASE WHEN LOWER(attendance_marks.status) = 'earlyout' THEN 1 ELSE 0 END) as early_out"),
            DB::raw("SUM(CASE WHEN LOWER(attendance_marks.status) = 'halfday' THEN 1 ELSE 0 END) as halfday"),
            DB::raw("SUM(CASE WHEN LOWER(attendance_marks.status) = 'late' THEN 1 ELSE 0 END) as late_count")
        )
        ->whereDate('attendance_marks.date', $selectedDate)
        ->where('attendance_marks.session_id', $sessionId)
        ->where('attendance_marks.branch_id', $branchId)
        ->groupBy('admissions.class_type_id')
        ->get()
        ->keyBy('class_type_id');

    $classes = ClassType::where('branch_id', $branchId)
        ->where('session_id', $sessionId)
        ->pluck('name','id');

    $data = [];

    foreach ($classes as $classId => $className) {

        $row = $records[$classId] ?? null;

        $data[$className] = [
            'in'        => $row->present ?? 0,
            'Absent'    => $row->absent ?? 0,
            'Holiday'   => $row->holiday ?? 0,
            'Leave'     => $row->leave_count ?? 0,
            'Event'     => $row->event_count ?? 0,
            'Exam'      => $row->exam_count ?? 0,
            'EarlyOut'  => $row->early_out ?? 0,
            'Halfday'   => $row->halfday ?? 0,
            'Late'      => $row->late_count ?? 0,
        ];
    }

    return $data;
}

 protected static function resolveStudentAttendanceUniqueId($student): string
{
    $attendanceId = trim((string) ($student->attendance_unique_id ?? ''));
    if ($attendanceId !== '') {
        return $attendanceId;
    }

    $admissionNo = trim((string) ($student->admissionNo ?? ''));
    if ($admissionNo !== '') {
        return $admissionNo;
    }

    return 'STU-' . $student->id;
}

protected static function activeStudentAttendanceIndex(): array
{
    $students = Admission::select('id', 'class_type_id', 'attendance_unique_id', 'admissionNo')
        ->where('session_id', Session::get('session_id'))
        ->where('branch_id', Session::get('branch_id'))
        ->where('status', 1)
        ->get();

    $lookup = [];
    $classTotals = [];
    $totalStudents = 0;

    foreach ($students as $student) {
        $uniqueId = self::resolveStudentAttendanceUniqueId($student);
        $classTypeId = (int) ($student->class_type_id ?? 0);

        $lookup[$uniqueId] = [
            'student_id' => (int) $student->id,
            'class_type_id' => $classTypeId,
        ];

        $classTotals[$classTypeId] = ($classTotals[$classTypeId] ?? 0) + 1;
        $totalStudents++;
    }

    return [$lookup, $classTotals, $totalStudents];
}

    
    public static function chartAttendanceStudents(){
        $att = array();
      
		 
        $att['in'] = $data=StudentAttendance::where('date',date('Y-m-d'))->where('session_id',Session::get('session_id'))
		 ->where('branch_id',Session::get('branch_id'))->whereIn('attendance_status_id',[1,2])->count();
        $att['Absent'] = $data=StudentAttendance::where('date',date('Y-m-d'))->where('session_id',Session::get('session_id'))
		 ->where('branch_id',Session::get('branch_id'))->where('attendance_status_id',3)->count();
        $att['Holiday'] = $data=StudentAttendance::where('date',date('Y-m-d'))->where('session_id',Session::get('session_id'))
		 ->where('branch_id',Session::get('branch_id'))->where('attendance_status_id',5)->count();
        $att['Event'] = $data=StudentAttendance::where('date',date('Y-m-d'))->where('session_id',Session::get('session_id'))
		 ->where('branch_id',Session::get('branch_id'))->where('attendance_status_id',6)->count();
        $att['Exam'] = $data=StudentAttendance::where('date',date('Y-m-d'))->where('session_id',Session::get('session_id'))
		 ->where('branch_id',Session::get('branch_id'))->where('attendance_status_id',7)->count();
        return $att;
   } 
    public static function chartAttendanceTeachers(){
        return [
            'Present' => 0,
            'Absent' => 0,
            'Work_From_Home' => 0,
            'Half_Day' => 0,
            'Holiday' => 0,
        ];
    } 

    public static function staffAtten($id,$monthId){
        return [
            'P' => 0,
            'A' => 0,
            'W' => 0,
            'HF' => 0,
            'H' => 0,
            'd' => 0,
            'TotalDay' => !empty($monthId) ? Carbon::now()->month($monthId)->daysInMonth : 0,
            'Sunday' => 0,
        ];
    } 


   public static function homework_check($id,$admission_id,$homework_id){
      // $status = HomeworkDocuments::where('upload_hw_id',$id)->where('admission_id',$admission_id)->get(); 
      $status[0] = HomeworkDocuments::select('homework_documents.*','upload.homework_id')
		 ->leftjoin('upload_homeworks as upload','upload.id','homework_documents.upload_hw_id')
		->where('upload.homework_id',$homework_id)->where('homework_documents.admission_id',$admission_id)->count();
      
       $status[1] = HomeworkDocuments::select('homework_documents.*','upload.homework_id')
		 ->leftjoin('upload_homeworks as upload','upload.id','homework_documents.upload_hw_id')
		->where('upload.homework_id',$homework_id)->where('homework_documents.admission_id',$admission_id)->where('homework_documents.status',1)->count();
      
    // dd($status);
       return $status;
   
   
       }

    public static function examData($id){
      $data = ExamResult::select('exam_results.*','Exam.name as exam_name','Admission.first_name','Admission.last_name','Admission.father_name','Admission.mobile')
		 ->leftjoin('admissions as Admission','Admission.id','exam_results.admission_id')
		 ->leftjoin('exams as Exam','Exam.id','exam_results.exam_id')
		 ->where('exam_results.exam_id',$id)->orderBy('id','DESC')->get();
       
       return $data;
   } 


   public static function getBookCategory(){
        return collect([]);
    }
      public static function monthlyConsumption($month_id,$hostel_room_id,$floor_id,$building_id,$hostel_id){
        return ['people' => 0, 'days' => 0];
    }
    
    public static function getTimePeriod(){
        return collect([]);
    }  
    
     public static function getAllTeachers(){
       return collect([]);
   
    }
    
    public static function getNote(){
        $note = AdmitCardNote::where('branch_id',Session::get('branch_id'))->first();
        return $note;
    }
    
   
    public static function oldScheduleDetails($subject_id,$class_type_id,$exam_id,$stream_id) {
          $old_data = ExaminationScheduleDetail::where('subject_id',$subject_id)
                                ->where('class_type_id',$class_type_id)
                                ->where('exam_id',$exam_id);
                                if($stream_id != '')
                                {
                               $old_data = $old_data->where('stream_id',$stream_id);
                                }
                                $old_data= $old_data->first();
                            // dd($old_data);  
                               return $old_data;
    }

     public static function getSubjectName($id){
       $getsubject = Subject::where('id',$id)->first();
      // dd($getsubject);
       return $getsubject->name ?? 'NA';
   }
   
      public static function getSubjectPercentage($id,$subject_id){
       $getSubjectPercentage = ExamResultDigital::where('id',$id)->first();
      
       $decode1 = json_decode($getSubjectPercentage->result);
      
      $data['correct']=0;
      $data['wrong']=0;
      $data['skip']=0;
      $data['total_ques']=0;
      
      foreach($decode1 as $item)
      {
          if($item->subject_id == $subject_id)
          {
              if($item->correct == 2)
              {
                  $data['correct']++ ;
              }
              
              elseif($item->correct == 1)
              {
                  $data['wrong']++;
              }
              elseif($item->correct == 0)
              {
                $data['skip']++;
              }
              
               $data['total_ques']++;
          }
          
      }
      
    
       return $data;
   }
    public static function allstudents() {
        return collect([]);
    }
    
 
    public static function SidebarSubPerm($sidebar_id) {
        
        $data =  SidebarSub::where('sidebar_id',$sidebar_id)->where('sub_sidebar','yes')->orderBy('orderBy','ASC')->get();
        $sidebar_sub_id = array();      
            foreach($data as $item){
               $per = UserPermission::where('user_id',Session::get('id'))->whereRaw("find_in_set({$item->id}, sub_sidebar_id)")->first();
              if(!empty($per)){
                 $sidebar_sub_id[] = $item->id; 
              }
            }        
        $data2 =  SidebarSub::wherein('id',$sidebar_sub_id);
        if($sidebar_id != 9)
        {
                $data2 = $data2->orderBy('orderBy','ASC');
        }
        else
        {
                        $data2=$data2->where('sub_sidebar','yes')->orderBy('orderBy','ASC');        
        }
    
    $data2 = $data2->get();
        return $data2;
    }
    

    public static function getSeatCounts($time_slot_id){
        return ['available_seats' => 0, 'booked_seats' => 0];
    }
    
    public static function getLibraryCabin($library_id){
        return collect([]);
    }
    
    
    public static function getAttendance($admission_id,$class_type_id){ 
         
         
        $first_date = StudentAttendance:: where('session_id',Session::get('session_id'))->where('class_type_id',$class_type_id)->orderBy('date', 'ASC')->first();
        $last_date = StudentAttendance:: where('session_id',Session::get('session_id'))->where('class_type_id',$class_type_id)->orderBy('date', 'DESC')->first();
        
        
        if(!empty($first_date))
        {
            
        
        
        $startDate = Carbon::createFromFormat('Y-m-d', date('Y-m-d' ,strtotime($first_date->date)));
        $endDate = Carbon::createFromFormat('Y-m-d', date('Y-m-d' ,strtotime($last_date->date)));

        $totalDays = 0;

        while ($startDate->lte($endDate)) {
            if ($startDate->dayOfWeek !== Carbon::SUNDAY) {
                $totalDays++;
            }

            $startDate->addDay();
        }
        
        
        
            $presentCount = StudentAttendance::where('session_id',Session::get('session_id'))->where('admission_id',$admission_id)->where('attendance_status_id', 1)->count();
            $absentCount = StudentAttendance::where('session_id',Session::get('session_id'))->where('admission_id',$admission_id)->where('attendance_status_id', 2)->count();
            $holidayCount = StudentAttendance::where('session_id',Session::get('session_id'))->where('admission_id',$admission_id)->where('attendance_status_id', 5)->count();
           
            return $presentCount . '/' .($totalDays-$holidayCount );
            
        }
        else
        {
             return 'N/A';
        }
         
    } 
    
    
    
       public static function getExamsForPerformance($class_type_id)
{
    $exams = AssignExam::where('session_id',Session::get('session_id'))->where('class_type_id',$class_type_id)->pluck('exam_id')->implode(',');
    
    return explode(',',$exams);
}
       public static function getPerformaceSubjects($admission_id,$class_type_id)
{
    
    $clsOrder = ClassType::where('id',$class_type_id)->first();
    $streams = [];
    if($clsOrder->orderBy > 10)
    {
        $student = Admission::where('id',$admission_id)->first();
        
        if(!empty($student))
        {
            $streams = explode(',',($student->stream_subject ?? ''));
            
        }
        
         $subject = FillMarks::where('session_id',Session::get('session_id'))->whereIn('subject_id',$streams)->where('admission_id',$admission_id)->groupBy('subject_id')->pluck('subject_id')->implode(',');
   
    }
    else
    {
       $subject = FillMarks::where('session_id',Session::get('session_id'))->where('admission_id',$admission_id)->groupBy('subject_id')->pluck('subject_id')->implode(',');
     
    }
    
    
    
    
    
    return explode(',',$subject);
}
       public static function getPerformaceOtherSubjets($admission_id,$class_type_id)
{
    return [];
}
       public static function getPerformaceSubjectsName($subject_ids)
{
   $name = Subject::whereIn('id',$subject_ids)->pluck('name')->implode(',');
    
    return explode(',',$name);
}
       public static function getPerformaceOtherSubjectsName($other_ids)
{
   $name = Subject::whereIn('id',$other_ids)->pluck('name')->implode(',');
    
    return explode(',',$name);
}
       public static function getParticularPerformaceData($admission_id,$exam_id,$subject_id,$class_type_id)
{
    
  
    $marks['mark'] = FillMarks::where('session_id',Session::get('session_id'))
    ->where('admission_id',$admission_id)
    ->where('exam_id',$exam_id)
    ->where('class_type_id',$class_type_id)
    ->where('subject_id',$subject_id)->first();
    $marks['max'] = FillMinMaxMarks::where('session_id',Session::get('session_id'))
    ->where('exam_id',$exam_id)
    ->where('class_type_id',$class_type_id)
    ->where('subject_id',$subject_id)->first();
    
    
    return $marks;
}
       public static function getParticularOtherPerformaceData($admission_id,$exam_id,$other_id,$class_type_id)
{
    $marks['mark'] = null;
    return $marks;
}
       public static function getExamMaximumForPerformance($exam_ids,$class_type_id)
{
    $max = FillMinMaxMarks::where('session_id',Session::get('session_id'))->whereIn('exam_id',$exam_ids)->where('class_type_id',$class_type_id)->sum('exam_maximum_marks');
    
    return $max;
    
}
       public static function getExamObtainedForPerformance($exam_ids,$class_type_id,$admission_id)
{
    $obtained = FillMarks::where('session_id',Session::get('session_id'))->whereIn('exam_id',$exam_ids)->where('admission_id',$admission_id)->where('class_type_id',$class_type_id)->sum('student_marks');
    
    return $obtained;
    
}
       public static function getAttendancePerformance($admission_id, $class_type_id)
{
    $first_date = StudentAttendance::where('session_id', Session::get('session_id'))
        ->where('class_type_id', $class_type_id)
        ->orderBy('date', 'ASC')
        ->first();
    $last_date = StudentAttendance::where('session_id', Session::get('session_id'))
        ->where('class_type_id', $class_type_id)
        ->orderBy('date', 'DESC')
        ->first();

    $holidayList = Weekendcalendar::where('session_id', Session::get('session_id'))
        ->where('attendance_status', 5)
        ->pluck('date')
        ->implode(',');

    $holidayList = explode(',', $holidayList);

    if (!empty($first_date)) {
        $startDate = Carbon::createFromFormat('Y-m-d', date('Y-m-d', strtotime($first_date->date)));
        $endDate = Carbon::createFromFormat('Y-m-d', date('Y-m-d', strtotime($last_date->date)));

        $totalDays = 0;

        while ($startDate->lte($endDate)) {
            // Convert the current date to a string format that matches the format of dates in $holidayList
            $currentDate = $startDate->format('Y-m-d');

            // Check if the current date is not a Sunday and not in the holidayList
            if ($startDate->dayOfWeek !== Carbon::SUNDAY && !in_array($currentDate, $holidayList)) {
                $totalDays++;
            }

            $startDate->addDay();
        }

        $presentCount = StudentAttendance::where('session_id', Session::get('session_id'))
            ->where('admission_id', $admission_id)
            ->where('attendance_status_id', 1)
            ->count();
        $absentCount = StudentAttendance::where('session_id', Session::get('session_id'))
            ->where('admission_id', $admission_id)
            ->where('attendance_status_id', 2)
            ->count();
        $holidayCount = StudentAttendance::where('session_id', Session::get('session_id'))
            ->where('admission_id', $admission_id)
            ->where('attendance_status_id', 5)
            ->count();
    $percentage = round(($presentCount / $totalDays) * 100) . '%';

    
        return $percentage;
    } else {
        return 'N/A';
    }
}
        public static function getAdmissionDatatableFields(){
    
            $array = [];
            $studentFields = StudentField::where('branch_id', Session::get('branch_id'))->where('status',0)->orderBy('field_order','ASC')->get();
            $array['SR.NO'] ='id';
            if(!empty($studentFields)){
                foreach ($studentFields as $field) {
                   
                    $array[$field->field_label] =$field->field_name;
                    }
                }
            $array['Fees Progress'] ='FeesProgress'; 
            return $array;
        }
        
        
       public static function getYearWiseFeeCollection(){ 
        
    // Initialize arrays to store year names and values
    $years = [];
    $values = [];

    // Define a start year and end year (you can adjust this based on your data range)
    $startYear = 2020; // Example starting year
    $endYear = Carbon::now()->year; // Current year

    // Loop through each year
    for ($year = $startYear; $year <= $endYear; $year++) {
        
        // Query to get the sum of amounts collected for the current year
        $totalAmount = FeesDetail::where('session_id', Session::get('session_id'))
                                  ->whereYear('date', $year)
                                  ->whereIn('status',[0,1])
                                  ->sum('total_amount');

        // Store year in $years array and value in $values array
        $years[] = $year; // Store the year
        $values[] = $totalAmount; // Store the total amount for the year
    }

    return [
        'val1' => $years,  // Years
        'val2' => $values  // Total amounts for each year
    ];
}
    public static function getMonthWiseFeeCollection(){ 
        
    // Initialize arrays to store month names and values
    $months = [];
    $values = [];

    // Loop through each month (from January to December)
    for ($month = 1; $month <= 12; $month++) {
        // Get the name of the month
        $monthName = Carbon::create(null, $month, 1)->format('F');
        
        // Query to get the sum of amounts collected for the current month
        $totalAmount = FeesDetail::where('session_id', Session::get('session_id'))
                                      ->where('branch_id',Session::get('branch_id'))
                                      ->whereIn('status',[0,1])
                                  ->whereMonth('date', $month)->whereYear('date', date('Y'))
                                  ->sum('total_amount');

        // Store month name in $months array and value in $values array
        $months[] = $monthName;
        $values[] = $totalAmount;
    }

    return [
        'val1' => $months,
        'val2' => $values
    ];
   }
    public static function getWeeklyWiseFeeCollection(){ 
  // Get today's date
    $today = Carbon::today();

    // Initialize arrays to store dates and values
    $dates = [];
    $values = [];

$startOfMonth = $today->copy()->startOfMonth();  // Start of the current month

$dates = [];
$values = [];

for ($date = $startOfMonth; $date->lte($today); $date->addDay()) {
    // Get the day number without leading zero (e.g., 1, 2, 3)
    $day = $date->format('d-m-Y');  // 'j' gives the day of the month without leading zeros

    // Query to get the sum of amounts collected on the current day
    $totalAmount = FeesDetail::where('session_id', Session::get('session_id'))
    ->where('branch_id',Session::get('branch_id'))->whereIn('status',[0,1])
                              ->whereDate('date', $date->format('Y-m-d'))
                              ->sum('total_amount');

    // Store the day number and value in respective arrays
    $dates[] = $day;
    $values[] = $totalAmount;
}

// Output the dates and values


   

    return [
        'val1' => $dates,
        'val2' => $values
    ];
   }
   
   
    public static function MessageQueue($toMobile,$text,$media_link = null,$filename = null)
        {
            //  $toMobile = 9376550276;
            // create message entry
            $message = MessageQueue::create([
                'receiver_number' => $toMobile,
                'content'         => $text,
                'media_link'      => $media_link,
                'file_name'       => $filename,
                'message_status'  => 0,
                'submitted_at'    => now(),
            ]);
        
            // dispatch job only if created successfully
            if ($message) {
                SendMessageJob::dispatch($message)->onQueue('messages'); // optional: custom queue
            }
        }
        
        public static function permissioncheck($sidebar_id)
            {
                $userId = Session::get('id');
                $perm = UserPermission::where('user_id', $userId)
                    ->where('sidebar_id', $sidebar_id)
                    ->whereNull('deleted_at')
                    ->first();

                if (!$perm) {
                    $roleId = Session::get('role_id');
                    if ($roleId) {
                        $perm = DB::table('role_permissions')
                            ->where('role_id', $roleId)
                            ->where('sidebar_id', $sidebar_id)
                            ->first();
                    }
                }
            
                return (object)[
                    'add' => $perm && $perm->add == 1,
                    'edit' => $perm && $perm->edit == 1,
                    'delete' => $perm && $perm->delete == 1,
                    'view' => $perm && $perm->view == 1,
                    'print' => $perm && $perm->print == 1,
                ];
            }
            
            public static function actionPermission($sidebar_id = null)
            {
                if (!empty($sidebar_id)) {
                    return self::permissioncheck($sidebar_id);
                }
                return (object)[
                    'add' => true,
                    'edit' => true,
                    'delete' => true,
                    'view' => true,
                    'print' => true,
                ];
            }
            
            
            
            public static function CarryForwardFees($admissionId)
{
    $totalPending = 0;

    $groups = FeesAssignDetail::select(
            'fees_assign_details.fees_group_id',
            'fees_group.name as group_name',
            'fees_assign_details.fees_group_amount'
        )
        ->join('fees_group', 'fees_group.id', '=', 'fees_assign_details.fees_group_id')
        ->where('fees_assign_details.admission_id', $admissionId)
        ->get();

    foreach ($groups as $g) {

        $paid = FeesDetail::where('fees_type', 0)
            ->whereIn('status', [0,1,2])
            ->where('admission_id', $admissionId)
            ->where('fees_group_id', $g->fees_group_id)
            ->sum('paid_amount');

        $pending = $g->fees_group_amount - $paid;

        if ($pending > 0) {
            $totalPending += $pending; // ✅ add all pending
        }
    }

    return $totalPending; // always safe
}

    /**
     * Determine if current client is mobile device / mobile webview / mobile view mode.
     */
    public static function isMobile(): bool
    {
        // 1. Explicit query param or session override
        if (request()->has('layout')) {
            if (request()->get('layout') === 'mobile') return true;
            if (request()->get('layout') === 'desktop') return false;
        }
        if (request()->has('view_mode')) {
            if (request()->get('view_mode') === 'mobile') return true;
            if (request()->get('view_mode') === 'desktop') return false;
        }
        if (Session::has('view_mode')) {
            if (Session::get('view_mode') === 'mobile') return true;
            if (Session::get('view_mode') === 'desktop') return false;
        }

        // 2. User Agent & MobileDetect
        $userAgent = request()->header('User-Agent', '');
        if (preg_match('/(android|iphone|ipod|ipad|windows phone|mobile|blackberry|iemobile|opera mini|ariseapp|wv)/i', $userAgent)) {
            try {
                $detect = new \Detection\MobileDetect;
                return $detect->isMobile() && !$detect->isTablet();
            } catch (\Throwable $e) {
                return true;
            }
        }

        return false;
    }

    /**
     * Intelligently render mobile view if available and client is mobile, else desktop view.
     * Pattern: 'dashboard.admin_dashboard' => looks for 'dashboard.mobile.admin_dashboard' or 'mobile.dashboard.admin_dashboard'
     */
    public static function view($view, $data = [], $mergeData = [])
    {
        if (self::isMobile()) {
            $parts = explode('.', $view);
            $last = array_pop($parts);
            $mobileViewVariant1 = implode('.', array_merge($parts, ['mobile', $last]));
            $mobileViewVariant2 = 'mobile.' . $view;

            if (view()->exists($mobileViewVariant1)) {
                return view($mobileViewVariant1, $data, $mergeData);
            }
            if (view()->exists($mobileViewVariant2)) {
                return view($mobileViewVariant2, $data, $mergeData);
            }
        }

        return view($view, $data, $mergeData);
    }


    /*
    |--------------------------------------------------------------------------
    | Student / Father / Mother Photo - single global save + show
    |--------------------------------------------------------------------------
    | Folder : public/assets/uploads/students/{student|father|mother}/
    | DB     : sirf file ka naam save hota hai (path nahi)
    | Use    : single aur bulk dono me same function call karo
    |          $name = Helper::saveStudentImage($request->file('student_img'), 'student');
    |          $name = Helper::saveStudentImage($file, 'father', $oldName); // purani photo delete ho jayegi
    */
    public const STUDENT_IMAGE_TYPES = ['student', 'father', 'mother'];

    public static function studentImageType($type)
    {
        return in_array($type, self::STUDENT_IMAGE_TYPES, true) ? $type : 'student';
    }

    // Save karne ka poora folder path (env ki zarurat nahi)
    public static function studentImagePath($type = 'student')
    {
        $type = self::studentImageType($type);
        return public_path('assets' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'students' . DIRECTORY_SEPARATOR . $type) . DIRECTORY_SEPARATOR;
    }

    // Photo dikhane ka URL, file na mile to $default return hoga
    public static function studentImageUrl($fileName, $type = 'student', $default = null)
    {
        $type = self::studentImageType($type);
        if (!empty($fileName) && File::exists(self::studentImagePath($type) . $fileName)) {
            return asset('public/assets/uploads/students/' . $type . '/' . $fileName);
        }
        return $default;
    }

    // Photo save (resize 600px + jpg 80%), unique naam return karta hai
    public static function saveStudentImage($file, $type = 'student', $oldFileName = null)
    {
        if (empty($file) || !($file instanceof \Illuminate\Http\UploadedFile) || !$file->isValid()) {
            return $oldFileName ?? '';
        }

        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, ['jpg', 'jpeg', 'png'], true)) {
            return $oldFileName ?? '';
        }

        $type = self::studentImageType($type);
        $directory = self::studentImagePath($type);
        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true, true);
        }

        $fileName = $type . '_' . time() . '_' . uniqid() . '.jpg';

        try {
            \Intervention\Image\Facades\Image::make($file)
                ->resize(600, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('jpg', 80)
                ->save($directory . $fileName);
        } catch (\Throwable $e) {
            \Log::error('saveStudentImage failed: ' . $e->getMessage());
            return $oldFileName ?? '';
        }

        if (!empty($oldFileName) && $oldFileName !== $fileName) {
            self::deleteStudentImage($oldFileName, $type);
        }

        return $fileName;
    }

    public static function deleteStudentImage($fileName, $type = 'student')
    {
        if (empty($fileName)) {
            return false;
        }
        $path = self::studentImagePath($type) . basename($fileName);
        return File::exists($path) ? File::delete($path) : false;
    }

    /**
     * Unified Student Fee Summary & Ledger
     * Synchronizes fee calculation across Dashboard, Fees History, and Payment Gateway.
     *
     * @param int|null $studentId
     * @param int|null $sessionId
     * @param int|null $branchId
     * @return array
     */
    public static function getStudentFeeSummary($studentId = null, $sessionId = null, $branchId = null)
    {
        $studentId = (int) ($studentId ?: Session::get('id'));
        $currentAdmission = $studentId ? \App\Models\Admission::find($studentId) : null;

        $defaultSummary = [
            'totalFees' => 0.0,
            'paidFees' => 0.0,
            'discount' => 0.0,
            'netPayable' => 0.0,
            'currentSessionDue' => 0.0,
            'dueAmount' => 0.0,
            'pendingFees' => 0.0,
            'finePaid' => 0.0,
            'paymentProgress' => 100,
            'getFees' => collect(),
            'feeHeadLedger' => collect(),
            'feeReceipts' => collect(),
            'filteredAdmission' => null,
            'student' => $currentAdmission,
        ];

        if (!$currentAdmission) {
            return $defaultSummary;
        }

        $activeSessionId = (int) ($sessionId ?: (Session::get('session_id') ?: $currentAdmission->session_id));
        $activeBranchId = (int) ($branchId ?: (Session::get('branch_id') ?: $currentAdmission->branch_id));

        $filteredAdmission = null;
        if (!empty($currentAdmission->unique_system_id)) {
            $filteredAdmission = \App\Models\Admission::where('unique_system_id', $currentAdmission->unique_system_id)
                ->where('session_id', $activeSessionId)
                ->where('branch_id', $activeBranchId)
                ->first(['id', 'session_id', 'branch_id']);
        } elseif ((int) $currentAdmission->session_id === $activeSessionId) {
            $filteredAdmission = $currentAdmission;
        }

        $getFees = collect();
        $feeHeadLedger = collect();
        $feeReceipts = collect();

        if ($filteredAdmission) {
            $getFees = \App\Models\fees\FeesAssignDetail::select(
                    'fees_assign_details.*',
                    'fees_group.name as group_name'
                )
                ->leftJoin('fees_group', 'fees_group.id', '=', 'fees_assign_details.fees_group_id')
                ->where('fees_assign_details.admission_id', $filteredAdmission->id)
                ->where('fees_assign_details.session_id', $activeSessionId)
                ->where('fees_assign_details.branch_id', $activeBranchId)
                ->get();

            $feePaymentDetails = \App\Models\FeesDetail::select(
                    'fees_detail.*',
                    'payment_modes.name as payment_mode',
                    'fees_group.name as fee_head_name'
                )
                ->leftJoin('payment_modes', 'payment_modes.id', '=', 'fees_detail.payment_mode_id')
                ->leftJoin('fees_group', 'fees_group.id', '=', 'fees_detail.fees_group_id')
                ->where('fees_detail.admission_id', $filteredAdmission->id)
                ->where('fees_detail.session_id', $activeSessionId)
                ->where('fees_detail.branch_id', $activeBranchId)
                ->where(function ($query) {
                    $query->where('fees_detail.fees_type', 0)
                        ->orWhereNull('fees_detail.fees_type');
                })
                ->whereIn('fees_detail.status', [0, 1])
                ->orderByDesc('fees_detail.date')
                ->orderByDesc('fees_detail.id')
                ->get();

            $paymentsByHead = $feePaymentDetails->groupBy('fees_group_id');

            $feeHeadLedger = $getFees
                ->groupBy('fees_group_id')
                ->map(function ($assignments, $feeGroupId) use ($paymentsByHead) {
                    $payments = $paymentsByHead->get($feeGroupId, collect())->values();
                    $validPayments = $payments->whereIn('status', [0, 1]);
                    $assignedAmount = round((float) $assignments->sum('fees_group_amount'), 2);
                    $assignmentDiscount = round((float) $assignments->sum('discount'), 2);
                    $paidAmount = round((float) $validPayments->sum('paid_amount'), 2);
                    $paymentDiscount = round((float) $validPayments->sum('discount'), 2);
                    $fineAmount = round((float) $validPayments->sum('installment_fine'), 2);
                    $dueAmount = round(max(
                        0,
                        $assignedAmount - $assignmentDiscount - $paidAmount - $paymentDiscount
                    ), 2);

                    return (object) [
                        'fees_group_id' => $feeGroupId,
                        'name' => optional($assignments->first())->group_name ?: 'Fee Installment',
                        'due_date' => optional($assignments->first())->installment_due_date,
                        'assigned_amount' => $assignedAmount,
                        'discount' => $assignmentDiscount + $paymentDiscount,
                        'paid_amount' => $paidAmount,
                        'fine_amount' => $fineAmount,
                        'due_amount' => $dueAmount,
                        'payments' => $payments,
                    ];
                })
                ->values();

            // Include any unassigned fee payments
            $unassignedGroupIds = $paymentsByHead->keys()->diff($getFees->pluck('fees_group_id'));
            foreach ($unassignedGroupIds as $uGroupId) {
                $uPayments = $paymentsByHead->get($uGroupId, collect())->values();
                $uValid = $uPayments->whereIn('status', [0, 1]);
                $uPaid = round((float) $uValid->sum('paid_amount'), 2);
                $uDisc = round((float) $uValid->sum('discount'), 2);
                $uFine = round((float) $uValid->sum('installment_fine'), 2);
                if ($uPaid > 0 || $uDisc > 0 || $uFine > 0) {
                    $feeHeadLedger->push((object) [
                        'fees_group_id' => $uGroupId,
                        'name' => optional($uPayments->first())->fee_head_name ?: 'Other Payment',
                        'due_date' => null,
                        'assigned_amount' => 0.0,
                        'discount' => $uDisc,
                        'paid_amount' => $uPaid,
                        'fine_amount' => $uFine,
                        'due_amount' => 0.0,
                        'payments' => $uPayments,
                    ]);
                }
            }

            $paymentDetailsById = $feePaymentDetails->keyBy('id');
            $feeReceipts = \App\Models\fees\FeesDetailsInvoices::select(
                    'fees_details_invoices.*',
                    'payment_modes.name as payment_mode'
                )
                ->leftJoin('payment_modes', 'payment_modes.id', '=', 'fees_details_invoices.payment_mode')
                ->where('fees_details_invoices.admission_id', $filteredAdmission->id)
                ->where('fees_details_invoices.session_id', $activeSessionId)
                ->where('fees_details_invoices.branch_id', $activeBranchId)
                ->whereIn('fees_details_invoices.status', [0, 1])
                ->orderByDesc('fees_details_invoices.payment_date')
                ->orderByDesc('fees_details_invoices.id')
                ->get()
                ->map(function ($receipt) use ($paymentDetailsById) {
                    $detailIds = collect(explode(',', (string) $receipt->fees_details_id))
                        ->filter()
                        ->map(function ($id) {
                            return (int) $id;
                        });
                    $receipt->fee_head_names = $detailIds
                        ->map(function ($id) use ($paymentDetailsById) {
                            return optional($paymentDetailsById->get($id))->fee_head_name;
                        })
                        ->filter()
                        ->unique()
                        ->implode(', ');
                    return $receipt;
                });
        }

        $totalFees = round((float) $feeHeadLedger->sum('assigned_amount'), 2);
        $totalDiscount = round((float) $feeHeadLedger->sum('discount'), 2);
        $netPayable = max(0, round($totalFees - $totalDiscount, 2));
        $paidFees = round((float) $feeHeadLedger->sum('paid_amount'), 2);
        $dueAmount = round((float) $feeHeadLedger->sum('due_amount'), 2);
        $finePaid = round((float) $feeHeadLedger->sum('fine_amount'), 2);
        $paymentProgress = $netPayable > 0 ? min(100, (int) round(($paidFees / $netPayable) * 100)) : 100;

        return [
            'totalFees' => $totalFees,
            'paidFees' => $paidFees,
            'discount' => $totalDiscount,
            'netPayable' => $netPayable,
            'currentSessionDue' => $dueAmount,
            'dueAmount' => $dueAmount,
            'pendingFees' => $dueAmount,
            'finePaid' => $finePaid,
            'paymentProgress' => $paymentProgress,
            'getFees' => $getFees,
            'feeHeadLedger' => $feeHeadLedger,
            'feeReceipts' => $feeReceipts,
            'filteredAdmission' => $filteredAdmission,
            'student' => $currentAdmission,
        ];
    }
}



if (!class_exists('App\Helpers\helper', false)) {
    class_alias(\App\Helpers\Helper::class, 'App\Helpers\helper');
}
if (!class_exists('helper', false)) {
    class_alias(\App\Helpers\Helper::class, 'helper');
}
