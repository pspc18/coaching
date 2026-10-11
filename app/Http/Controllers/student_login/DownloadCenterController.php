<?php

namespace App\Http\Controllers\student_login;

use App\Models\DownloadCenter;
use Session;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DownloadCenterController extends Controller
{
    /**
     * Mobile-optimized high performance student download center.
     * Column-projected query with eager loading and in-memory metrics.
     */
    public function student_download_center(Request $request)
    {
        $sessionId = Session::get('session_id');
        $branchId = Session::get('branch_id');
        $classTypeId = Session::get('class_type_id');

        $contents = DownloadCenter::select([
                'id', 'user_id', 'branch_id', 'session_id', 'class_type_id',
                'content_title', 'content_type', 'upload_date', 'content_file',
                'description', 'video_link', 'created_at'
            ])
            ->with([
                'ClassType:id,name',
                'User:id,first_name,last_name'
            ])
            ->where('session_id', $sessionId)
            ->where('branch_id', $branchId)
            ->where(function ($query) use ($classTypeId) {
                $query->whereNull('class_type_id')
                    ->orWhere('class_type_id', '')
                    ->orWhere('class_type_id', 0)
                    ->orWhere('class_type_id', $classTypeId);
            })
            ->orderBy('upload_date', 'DESC')
            ->orderBy('id', 'DESC')
            ->get();

        // Calculate single-pass category metrics in memory
        $totalCount = $contents->count();
        $assignmentsCount = 0;
        $studyMaterialCount = 0;
        $syllabusCount = 0;
        $otherDownloadsCount = 0;
        $hasFilesCount = 0;
        $hasVideosCount = 0;

        foreach ($contents as $item) {
            $type = trim($item->content_type ?? '');
            if (strcasecmp($type, 'Assignments') === 0) {
                $assignmentsCount++;
            } elseif (strcasecmp($type, 'Study Material') === 0) {
                $studyMaterialCount++;
            } elseif (strcasecmp($type, 'Syllabus') === 0) {
                $syllabusCount++;
            } else {
                $otherDownloadsCount++;
            }

            if (!empty($item->content_file)) {
                $hasFilesCount++;
            }
            if (!empty($item->video_link)) {
                $hasVideosCount++;
            }

            // File type classification
            $ext = !empty($item->content_file) ? strtolower(pathinfo($item->content_file, PATHINFO_EXTENSION)) : '';
            $item->file_ext = $ext;
            $item->is_image = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
            $item->is_pdf = ($ext === 'pdf');
            $item->is_video = in_array($ext, ['mp4', 'avi', 'mov', 'mkv', 'webm']) || !empty($item->video_link);
        }

        $metrics = [
            'total' => $totalCount,
            'assignments' => $assignmentsCount,
            'study_material' => $studyMaterialCount,
            'syllabus' => $syllabusCount,
            'other_downloads' => $otherDownloadsCount,
            'files_count' => $hasFilesCount,
            'videos_count' => $hasVideosCount,
        ];

        return view('student_login.download_center.student_download_center', [
            'dataview' => $contents,
            'metrics' => $metrics,
        ]);
    }

    public function studentDownloadCenter()
    {
        return $this->student_download_center(request());
    }

    public function assignments()
    {
        return $this->student_download_center(request());
    }

    public function studyMaterials()
    {
        return $this->student_download_center(request());
    }

    public function syllabus()
    {
        return $this->student_download_center(request());
    }

    public function otherDownloads()
    {
        return $this->student_download_center(request());
    }
}