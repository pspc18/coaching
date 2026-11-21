<?php

namespace App\Models\exam;

use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\OmrResult;
use App\Models\ClassType;

class Exam extends Model
{
    use SoftDeletes;

    protected $table = "exams";

    protected $guarded = ['id'];

    protected $casts = [
        'exam_date' => 'date',
        'examination_date' => 'date',
        'is_online' => 'boolean',
        'answer_key' => 'array',
        'online_questions' => 'array',
        'subject_ids' => 'array',
        'marks_per_correct' => 'decimal:2',
        'marks_per_incorrect' => 'decimal:2',
        'result_declaration_at' => 'datetime',
        'duration' => 'integer',
        'exam_creation_mode' => 'string',
    ];

    /**
     * Get the array of selected subject IDs for this exam.
     * Fallback to subject_id if subject_ids is not set.
     */
    public function getSelectedSubjectIdsAttribute(): array
    {
        $raw = $this->subject_ids;
        if (!empty($raw)) {
            $list = is_array($raw) ? $raw : (json_decode($raw, true) ?: []);
            $list = array_values(array_filter(array_map('intval', $list)));
            if (!empty($list)) {
                return $list;
            }
        }
        if (!empty($this->subject_id)) {
            return [(int) $this->subject_id];
        }
        return [];
    }

    public function Subject()
    {
        return $this->belongsTo('App\Models\Subject', 'subject_id');
    }

    public function classType()
    {
        return $this->belongsTo(ClassType::class, 'class_type_id');
    }

    public function batch()
    {
        return $this->belongsTo(\App\Models\Batch::class, 'batch_id');
    }

    public function results()
    {
        return $this->hasMany(OmrResult::class, 'exam_id');
    }

    public static function countExam()
    {
        $data = Exam::where('session_id', Session::get('session_id'));

        if (Session::get('role_id') > 1) {
            $data = $data->where('branch_id', Session::get('branch_id'));
        }
        return $data->count();
    }

    public static function thisMonthExam()
    {
        $data = Exam::where('session_id', Session::get('session_id'));

        if (Session::get('role_id') > 1) {
            $data = $data->where('branch_id', Session::get('branch_id'));
        }

        return $data->whereMonth('exam_date', date('m'))->count();
    }
}
