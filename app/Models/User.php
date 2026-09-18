<?php

namespace App\Models;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class User extends Model
{
        use SoftDeletes;
	protected $table = "users"; //table name
	protected $fillable = [
    'branch_id',
    'session_id',
    'role_id',
    'access_branch_id',
    'userName',
    'first_name',
    'mobile',
    'email',
    'address',
    'status',
    'confirm_password',
    'password'
];

    protected static function booted()
    {
        static::created(function (self $user) {
            $user->syncAttendanceUniqueId();
        });

        static::saving(function (self $user) {
            if ($user->exists && self::shouldGenerateAttendanceUniqueId($user->attendance_unique_id)) {
                $user->attendance_unique_id = self::buildAttendanceUniqueId($user->id);
            }
        });
    }

    public static function shouldGenerateAttendanceUniqueId($attendanceUniqueId): bool
    {
        $attendanceUniqueId = trim((string) $attendanceUniqueId);

        return $attendanceUniqueId === '' || $attendanceUniqueId === 'ua-0';
    }

    public static function buildAttendanceUniqueId($id): string
    {
        return 'ua-' . (int) $id;
    }

    public function syncAttendanceUniqueId(): void
    {
        if (!$this->id || !self::shouldGenerateAttendanceUniqueId($this->attendance_unique_id)) {
            return;
        }

        $attendanceUniqueId = self::buildAttendanceUniqueId($this->id);

        static::withoutEvents(function () use ($attendanceUniqueId) {
            $this->newQuery()->whereKey($this->id)->update([
                'attendance_unique_id' => $attendanceUniqueId,
            ]);
        });

        $this->attendance_unique_id = $attendanceUniqueId;
    }

	
public function roleName()
    {
        return $this->belongsTo('App\Models\Role','role_id');
    }
    
    
    

 public static function countTodaysBirthday(){
        $data=User::where('session_id',Session::get('session_id'))->where('dob',date('Y-m-d'));
        
        if(Session::get('role_id') > 1){
            $data = $data->where('branch_id',Session::get('branch_id'));
        }
        
        $data = $data->count();
        return $data;
    }
    
    
}
