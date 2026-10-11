<?php

namespace App\Http\Controllers\student_login;

use App\Models\User;
use App\Models\Master\Homework;
use App\Models\Master\UploadHomework;
use App\Models\Master\HomeworkDocuments;
use App\Models\Subject;
use App\Models\Admission;
use App\Helpers\helper;
use Session;
use File;
use Redirect;
use Response;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Image;
use DB;

class HomeworkController extends Controller
{
    /**
     * Display student's homework directory with high-performance optimized query
     * and in-memory glance metrics calculation.
     */
    public function view(Request $request)
    {
        $studentId = Session::get('id');
        $sessionId = Session::get('session_id');
        $branchId = Session::get('branch_id');
        $classTypeId = Session::get('class_type_id');
        $sectionId = Session::get('section_id');
        $today = date('Y-m-d');

        // Column-projected optimized query with scoped eager loading
        $homeworks = Homework::select([
                'id', 'user_id', 'branch_id', 'session_id', 'teacher_id',
                'title', 'max_marks', 'class_type_id', 'stream_id', 'section_id',
                'subject', 'homework_date', 'homework_issue_date', 'submission_date',
                'allow_late_submission', 'content_file', 'description', 'view_status', 'created_at'
            ])
            ->with([
                'Subject' => function ($q) {
                    $q->select('id', 'name');
                },
                'ClassType' => function ($q) {
                    $q->select('id', 'name');
                },
                'Teacher' => function ($q) {
                    $q->select('id', 'first_name', 'last_name', 'photo');
                },
                'User' => function ($q) {
                    $q->select('id', 'first_name', 'last_name');
                },
                'UploadHomework' => function ($q) use ($studentId) {
                    $q->where('admission_id', $studentId)
                      ->with(['HomeworkDocuments' => function ($dq) {
                          $dq->orderBy('id', 'ASC');
                      }])
                      ->orderBy('id', 'DESC');
                }
            ])
            ->where('session_id', $sessionId)
            ->where('branch_id', $branchId)
            ->where('class_type_id', $classTypeId)
            ->where(function ($q) use ($sectionId) {
                $q->whereNull('section_id');
                if (!empty($sectionId)) {
                    $q->orWhere('section_id', $sectionId);
                }
            })
            ->whereDate('homework_issue_date', '<=', $today)
            ->orderBy('submission_date', 'DESC')
            ->orderBy('id', 'DESC')
            ->get();

        // Glance metrics & status calculation in a single memory pass
        $totalCount = $homeworks->count();
        $submittedCount = 0;
        $evaluatedCount = 0;
        $pendingCount = 0;
        $dueTodayCount = 0;
        $overdueCount = 0;
        $subjects = [];

        foreach ($homeworks as $hw) {
            $subName = $hw->Subject->name ?? 'General';
            $subjects[$subName] = ($subjects[$subName] ?? 0) + 1;

            $mySubmission = $hw->UploadHomework->first();
            $subDate = !empty($hw->submission_date) ? date('Y-m-d', strtotime($hw->submission_date)) : '';

            $isSubmitted = !empty($mySubmission);
            $isEvaluated = false;
            $awardedMarks = null;
            $teacherReview = null;

            if ($isSubmitted) {
                $submittedCount++;
                $evalDocs = $mySubmission->HomeworkDocuments->where('status', '1');
                if ($evalDocs->count() > 0 || !empty($mySubmission->evaluate_date) || $mySubmission->marks !== null) {
                    $isEvaluated = true;
                    $evaluatedCount++;
                    $awardedMarks = $mySubmission->marks ?? $evalDocs->pluck('marks')->filter()->first();
                    $teacherReview = $evalDocs->pluck('hw_review')->filter()->first() ?? $mySubmission->message;
                }
                $hw->computed_status = $isEvaluated ? 'evaluated' : 'submitted';
            } else {
                $pendingCount++;
                if ($subDate === $today) {
                    $dueTodayCount++;
                    $hw->computed_status = 'due_today';
                } elseif (!empty($subDate) && $subDate < $today) {
                    $overdueCount++;
                    $hw->computed_status = 'overdue';
                } else {
                    $hw->computed_status = 'pending';
                }
            }

            $hw->my_submission = $mySubmission;
            $hw->is_submitted = $isSubmitted;
            $hw->is_evaluated = $isEvaluated;
            $hw->awarded_marks = $awardedMarks;
            $hw->teacher_review = $teacherReview;

            if (!empty($subDate)) {
                $diff = (int) round((strtotime($subDate) - strtotime($today)) / 86400);
                $hw->days_remaining = $diff;
            } else {
                $hw->days_remaining = null;
            }
        }

        $completionPct = $totalCount > 0 ? round(($submittedCount / $totalCount) * 100) : 0;

        $metrics = [
            'total' => $totalCount,
            'submitted' => $submittedCount,
            'evaluated' => $evaluatedCount,
            'pending' => $pendingCount,
            'due_today' => $dueTodayCount,
            'overdue' => $overdueCount,
            'completion_pct' => $completionPct,
        ];

        return view('student_login.homework.index', [
            'data' => $homeworks,
            'metrics' => $metrics,
            'subjects' => $subjects,
        ]);
    }

    /**
     * Submit assignment files & message with high performance file handling
     * and automatic image compression.
     */
    public function uploadHomework(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'homework_id' => 'required',
                'content_file' => 'required|array|min:1',
            ], [
                'content_file.required' => 'Please select at least one document or photo to submit.',
            ]);

            $studentId = Session::get('id');
            $sessionId = Session::get('session_id');
            $branchId = Session::get('branch_id');
            $classTypeId = Session::get('class_type_id');
            $sectionId = Session::get('section_id');
            $today = date('Y-m-d');

            $homework = Homework::find($request->homework_id);
            if (!$homework) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['status' => false, 'message' => 'Homework assignment not found.'], 404);
                }
                return redirect()->back()->with('error', 'Homework assignment not found.');
            }

            // Check if submission is closed and late submissions are disallowed
            if (!empty($homework->submission_date) && $homework->submission_date < $today && (int)$homework->allow_late_submission === 0) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['status' => false, 'message' => 'Submission closed. Late submission is not allowed for this assignment.'], 422);
                }
                return redirect()->back()->with('error', 'Submission closed. Late submission is not allowed.');
            }

            $uploadHW = new UploadHomework;
            $uploadHW->user_id = $studentId;
            $uploadHW->session_id = $sessionId;
            $uploadHW->admission_id = $studentId;
            $uploadHW->branch_id = $branchId;
            $uploadHW->class_type_id = $classTypeId;
            $uploadHW->section_id = $sectionId;
            $uploadHW->submission_date = $today;
            $uploadHW->message = $request->message;
            $uploadHW->homework_id = $request->homework_id;
            $uploadHW->save();

            $uploadHwId = $uploadHW->id;

            $destinationPath = base_path('schoolimage/uploadHomework/');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0775, true);
            }

            if ($request->hasFile('content_file')) {
                $files = $request->file('content_file');
                if (!is_array($files)) {
                    $files = [$files];
                }

                foreach ($files as $file) {
                    if ($file && $file->isValid()) {
                        $extension = strtolower($file->getClientOriginalExtension());
                        $sanitizedName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                        $document = uniqid() . '_' . substr($sanitizedName, 0, 24) . '.' . $extension;

                        // Compress images; move non-image files directly (PDF, Doc, etc.)
                        if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                            try {
                                $compressedImage = Image::make($file)
                                    ->resize(1200, null, function ($constraint) {
                                        $constraint->aspectRatio();
                                        $constraint->upsize();
                                    })
                                    ->encode($extension === 'png' ? 'png' : 'jpg', 75);
                                $compressedImage->save($destinationPath . $document);
                            } catch (\Exception $e) {
                                $file->move($destinationPath, $document);
                            }
                        } else {
                            $file->move($destinationPath, $document);
                        }

                        $uploadDoc = new HomeworkDocuments;
                        $uploadDoc->user_id = $studentId;
                        $uploadDoc->session_id = $sessionId;
                        $uploadDoc->branch_id = $branchId;
                        $uploadDoc->admission_id = $studentId;
                        $uploadDoc->class_type_id = $classTypeId;
                        $uploadDoc->section_id = $sectionId;
                        $uploadDoc->upload_hw_id = $uploadHwId;
                        $uploadDoc->content_file = $document;
                        $uploadDoc->submission_date = $today;
                        $uploadDoc->status = '0';
                        $uploadDoc->save();
                    }
                }
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Assignment submitted successfully!'
                ]);
            }

            return redirect()->to('student_homework')->with('message', 'Assignment submitted successfully!');
        }
    }

    /**
     * Details view for a particular homework
     */
    public function homeworkDetails(Request $request, $id)
    {
        $studentId = Session::get('id');
        $homework = Homework::with(['Subject', 'ClassType', 'Teacher'])->find($id);

        $submissions = UploadHomework::with(['HomeworkDocuments', 'Admission'])
            ->where('session_id', Session::get('session_id'))
            ->where('branch_id', Session::get('branch_id'))
            ->where('admission_id', $studentId)
            ->where('homework_id', $id)
            ->orderBy('id', 'DESC')
            ->get();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => true,
                'homework' => $homework,
                'submissions' => $submissions
            ]);
        }

        return view('student_login.homework.details', [
            'students' => $submissions,
            'homework' => $homework,
            'id' => $id,
            'search' => []
        ]);
    }

    /**
     * AJAX endpoint to fetch submission data for modal view
     */
    public function particularHomeworkDetails(Request $request)
    {
        $studentId = Session::get('id');
        $homeworkId = $request->homework_id;

        $data = UploadHomework::with(['HomeworkDocuments', 'Admission'])
            ->where('session_id', Session::get('session_id'))
            ->where('branch_id', Session::get('branch_id'))
            ->where('admission_id', $studentId)
            ->where('homework_id', $homeworkId)
            ->orderBy('id', 'DESC')
            ->get();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => true,
                'data' => $data
            ]);
        }

        return view('student_login.homework.data_homework', ['data' => $data]);
    }
}
