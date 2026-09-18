<?php

namespace App\Models;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Notification extends Model
{
    use SoftDeletes;

    protected $table = 'notifications';

    protected $fillable = [
        'managed_notice_id',
        'source_key',
        'user_id',
        'branch_id',
        'session_id',
        'admission_id',
        'device_token',
        'title',
        'content',
        'attachment_path',
        'attachment_name',
        'attachment_mime',
        'type',
        'show_status',
        'message_seen',
        'created_at',
        'updated_at',
    ];

    public function managedNotice()
    {
        return $this->belongsTo(ManagedNotice::class, 'managed_notice_id');
    }
}
