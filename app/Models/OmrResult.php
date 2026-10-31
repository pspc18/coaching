<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\exam\Exam;
use App\Models\Admission;

class OmrResult extends Model
{
    use HasFactory;

    protected $table = 'omr_results';
    protected $guarded = ['id'];

    protected $casts = [
        'extracted_answers' => 'array',
        'total_marks' => 'decimal:2',
        'obtained_marks' => 'decimal:2',
        'percentage' => 'decimal:2',
        'extraction_confidence' => 'decimal:4',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function student()
    {
        return $this->belongsTo(Admission::class, 'admission_id');
    }
}
