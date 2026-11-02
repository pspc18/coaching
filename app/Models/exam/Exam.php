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
        'marks_per_correct' => 'decimal:2',
        'marks_per_incorrect' => 'decimal:2',
        'result_declaration_at' => 'datetime',
        'duration' => 'integer',
        'exam_creation_mode' => 'string',
    ];

    public function Subject()
    {
        return $this->belongsTo('App\Models\Subject', 'subject_id');
    }

    public function classType()
    {
        return $this->belongsTo(ClassType::class, 'class_type_id');
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
