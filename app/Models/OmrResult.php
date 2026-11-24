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
        'subject_breakdown' => 'array',
        'total_marks' => 'decimal:2',
        'obtained_marks' => 'decimal:2',
        'percentage' => 'decimal:2',
        'extraction_confidence' => 'decimal:4',
    ];

    protected $appends = ['image_url'];

    /**
     * Resolve public web URL to the scanned / annotated sheet image.
     */
    public function getImageUrlAttribute()
    {
        if (!empty($this->annotated_image_url)) {
            return $this->annotated_image_url;
        }
        if (!empty($this->stored_file) && file_exists(public_path($this->stored_file))) {
            return asset($this->stored_file);
        }
        if (!empty($this->source_filename)) {
            if (file_exists(public_path('uploads/omr_scans/' . $this->source_filename))) {
                return asset('uploads/omr_scans/' . $this->source_filename);
            }
            if (file_exists(public_path($this->source_filename))) {
                return asset($this->source_filename);
            }
            // Fallback: check uploads/omr_scans folder for any scan file
            $scans = glob(public_path('uploads/omr_scans/*'));
            if (!empty($scans)) {
                $latest = end($scans);
                return asset('uploads/omr_scans/' . basename($latest));
            }
        }
        return null;
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function student()
    {
        return $this->belongsTo(Admission::class, 'admission_id');
    }
}
