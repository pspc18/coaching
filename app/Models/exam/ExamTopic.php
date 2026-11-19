<?php

namespace App\Models\exam;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamTopic extends Model
{
    use SoftDeletes;

    protected $table = 'exam_topics';
    protected $guarded = [];

    public function chapter()
    {
        return $this->belongsTo('App\Models\exam\ExamChapter', 'chapter_id');
    }

    public function subject()
    {
        return $this->belongsTo('App\Models\Subject', 'subject_id');
    }

    public function classType()
    {
        return $this->belongsTo('App\Models\ClassType', 'class_type_id');
    }
}
