<?php

namespace App\Models\exam;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamChapter extends Model
{
    use SoftDeletes;

    protected $table = 'exam_chapters';
    protected $guarded = [];

    public function topics()
    {
        return $this->hasMany('App\Models\exam\ExamTopic', 'chapter_id');
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
