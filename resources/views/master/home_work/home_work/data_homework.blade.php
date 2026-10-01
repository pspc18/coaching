@php
    $studentName = trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? ''));
    if (empty($studentName) && !empty($data[0]['Admission'])) {
        $studentName = trim(($data[0]['Admission']['first_name'] ?? '') . ' ' . ($data[0]['Admission']['last_name'] ?? ''));
    }
    if (empty($studentName)) {
        $studentName = 'Student';
    }
    $admNo = $student->admissionNo ?? ($data[0]['Admission']['admissionNo'] ?? 'N/A');
    $className = $student->ClassType->name ?? ($student->ClassTypes->name ?? ($data[0]['ClassType']['name'] ?? 'N/A'));
    $sectionName = $student->Section->name ?? ($data[0]['Section']['name'] ?? '');
    $fatherName = $student->father_name ?? ($data[0]['Admission']['father_name'] ?? 'N/A');
    $mobile = $student->mobile ?? ($data[0]['Admission']['mobile'] ?? 'N/A');
    $totalAttempts = count($data ?? []);
    $isStudentRole = (Session::get('role_id') == 3);
@endphp

<style>
/* Homework Evaluation Modal Scoped Styles */
.hw-eval-wrapper {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    color: #1e293b;
    font-size: 12px;
}
.hw-eval-wrapper * {
    box-sizing: border-box;
}

/* Flex Gap Fallbacks for Bootstrap 4 */
.gap-1 { gap: 3px; }
.gap-2 { gap: 6px; }
.gap-3 { gap: 10px; }
.gap-4 { gap: 14px; }

/* Icon Spacing Standard */
.hw-eval-wrapper i.fa,
.hw-eval-wrapper .fa {
    margin-right: 4px;
}

/* 1. Student Profile Top Banner (Compact) */
.eval-student-strip {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    padding: 6px 12px;
    margin-bottom: 8px;
    box-shadow: 0 1px 2px rgba(0, 44, 84, 0.05);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    border-left: 3px solid #002C54;
}
.eval-stu-avatar {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: linear-gradient(135deg, #002C54 0%, #0284c7 100%);
    color: #ffffff;
    font-weight: 700;
    font-size: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    margin-right: 10px;
    box-shadow: 0 1px 2px rgba(0, 44, 84, 0.2);
}
.eval-stu-name {
    font-size: 12.5px;
    font-weight: 700;
    color: #002C54;
    line-height: 1.2;
    margin-bottom: 1px;
}
.eval-stu-meta {
    font-size: 10.5px;
    color: #64748b;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
}
.eval-stu-meta span {
    display: inline-flex;
    align-items: center;
    margin-right: 12px;
}
.eval-stu-meta span:last-child {
    margin-right: 0;
}
.eval-stu-meta strong {
    color: #1e293b;
}

/* 2. Attempt Card Container (Compact) */
.eval-attempt-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    margin-bottom: 8px;
    overflow: hidden;
    box-shadow: 0 1px 2px rgba(0, 44, 84, 0.03);
    transition: all 0.15s ease;
}
.eval-attempt-card:hover {
    box-shadow: 0 2px 6px rgba(0, 44, 84, 0.06);
}
.eval-attempt-card:last-child {
    margin-bottom: 2px;
}

/* Card Header (Compact) */
.attempt-header {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    padding: 5px 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
}
.attempt-badge {
    background: #002C54;
    color: #ffffff;
    padding: 1px 6px;
    border-radius: 2px;
    font-weight: 700;
    font-size: 9.5px;
    letter-spacing: 0.03em;
    text-transform: uppercase;
    margin-right: 8px;
    display: inline-block;
    line-height: 1.4;
}
.attempt-date {
    font-size: 10.5px;
    color: #475569;
}

/* Student Note Card (Compact) */
.student-note-card {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 2px;
    padding: 4px 10px;
    margin: 6px 10px 0 10px;
    font-size: 11px;
    color: #166534;
    display: flex;
    align-items: center;
    line-height: 1.35;
}
.student-note-card i.fa {
    margin-right: 6px;
    font-size: 12px;
}
.student-note-card strong {
    color: #14532d;
    margin-right: 4px;
}

/* Card Body (Compact) */
.attempt-body {
    padding: 8px 10px;
}
.doc-review-item {
    background: #fcfdfe;
    border: 1px solid #e2e8f0;
    border-radius: 3px;
    padding: 8px;
    margin-bottom: 8px;
}
.doc-review-item:last-child {
    margin-bottom: 0;
}

/* Document File Box (Compact) */
.doc-tile {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 2px;
    padding: 6px 8px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: space-between;
    text-align: center;
    height: 100%;
    min-height: 95px;
}
.doc-thumb-wrap {
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    margin-bottom: 3px;
}
.doc-thumb-img {
    max-height: 46px;
    max-width: 100%;
    object-fit: contain;
    border-radius: 2px;
    cursor: pointer;
    border: 1px solid #cbd5e1;
    transition: transform 0.15s ease;
}
.doc-thumb-img:hover {
    transform: scale(1.05);
}
.doc-icon-wrap {
    text-align: center;
}
.doc-icon-wrap i {
    font-size: 26px;
    line-height: 1;
    margin-right: 0 !important;
}
.doc-filename {
    font-size: 9.5px;
    color: #64748b;
    font-family: monospace;
    max-width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    margin-bottom: 4px;
    padding: 0 2px;
}
.doc-actions {
    display: flex;
    align-items: center;
    width: 100%;
    justify-content: center;
}
.btn-doc-act {
    display: inline-flex;
    align-items: center;
    padding: 1px 6px;
    font-size: 10px;
    font-weight: 600;
    border-radius: 2px;
    text-decoration: none !important;
    cursor: pointer;
    line-height: 1.3;
    transition: all 0.15s;
    margin: 0 2px;
}
.btn-doc-act i.fa {
    margin-right: 3px;
}
.btn-doc-view {
    background: #0284c7;
    color: #ffffff !important;
    border: 1px solid #0284c7;
}
.btn-doc-view:hover {
    background: #0369a1;
}
.btn-doc-download {
    background: #ffffff;
    color: #002C54 !important;
    border: 1px solid #cbd5e1;
}
.btn-doc-download:hover {
    background: #f1f5f9;
    border-color: #94a3b8;
}

/* Feedback Box (Compact) */
.feedback-container {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 100%;
}
.feedback-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 4px;
}
.feedback-title {
    font-size: 10.5px;
    font-weight: 700;
    color: #002C54;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    margin: 0;
}
.feedback-title i.fa {
    margin-right: 4px;
}
.review-textarea {
    font-size: 11px;
    color: #0f172a;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 5px 8px;
    resize: vertical;
    min-height: 44px;
    line-height: 1.35;
    margin-bottom: 4px;
    transition: border-color 0.15s, box-shadow 0.15s;
}
.review-textarea:focus {
    border-color: #0284c7;
    box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.15);
    outline: none;
}

/* Quick Suggestion Chips (Compact) */
.quick-chips-bar {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
}
.chips-label {
    font-size: 9px;
    text-transform: uppercase;
    font-weight: 700;
    color: #64748b;
    margin-right: 4px;
}
.quick-chip {
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    color: #334155;
    padding: 1px 6px;
    border-radius: 10px;
    font-size: 9.5px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s;
    user-select: none;
    line-height: 1.25;
    margin-right: 4px;
    margin-bottom: 2px;
}
.quick-chip:hover {
    background: #e2e8f0;
    color: #002C54;
    border-color: #94a3b8;
}

/* Attempt Footer (Compact) */
.attempt-card-footer {
    padding: 5px 10px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
}
.footer-note {
    font-size: 10.5px;
    color: #64748b;
}
.footer-note i.fa {
    margin-right: 4px;
}
.btn-navy-action {
    display: inline-flex;
    align-items: center;
    padding: 3px 12px;
    font-size: 11px;
    font-weight: 700;
    background: #002C54;
    color: #ffffff;
    border: 1px solid #001f3d;
    border-radius: 2px;
    cursor: pointer;
    transition: all 0.15s;
}
.btn-navy-action i.fa {
    margin-right: 5px;
}
.btn-navy-action:hover {
    background: #0284c7;
    border-color: #0284c7;
    color: #ffffff;
}

/* Empty State */
.eval-empty-state {
    padding: 30px 16px;
    text-align: center;
    background: #ffffff;
    border: 1px dashed #cbd5e1;
    border-radius: 4px;
    color: #64748b;
}
.eval-empty-icon {
    font-size: 32px;
    color: #94a3b8;
    margin-bottom: 6px;
}
</style>

<div class="hw-eval-wrapper" style="max-height: 520px; overflow-y: auto; padding-right: 2px;">

    {{-- Hidden element to preserve JS compatibility for student name --}}
    <span id="stuName" data-first_name="{{ $studentName }}" style="display:none;"></span>

    {{-- Top Seamless Student Switcher Bar --}}
    @if(!$isStudentRole && isset($totalStudents) && $totalStudents > 1)
        <div class="eval-nav-strip d-flex align-items-center justify-content-between mb-2 p-1 px-2" style="background: #002342; border-radius: 2px; color: #ffffff;">
            <button type="button" 
                    class="btn btn-xs btn-light btn-nav-student" 
                    data-homework_id="{{ $homeworkId ?? '' }}" 
                    data-admission_id="{{ $prevStudentId }}" 
                    {{ empty($prevStudentId) ? 'disabled style=opacity:0.4;cursor:not-allowed;' : '' }}
                    style="font-size: 10.5px; font-weight: 600; padding: 2px 8px;">
                <i class="fa fa-chevron-left mr-1"></i> Previous Student
            </button>

            <div class="d-flex align-items-center gap-2">
                <span class="badge" style="background: rgba(255,255,255,0.15); color: #ffffff; font-size: 10.5px; padding: 3px 8px;">
                    Student <strong>{{ $currentIndex ?? 1 }}</strong> of <strong>{{ $totalStudents }}</strong>
                </span>
                @if(!empty($homework->max_marks))
                    <span class="badge" style="background: #38bdf8; color: #002C54; font-size: 10.5px; padding: 3px 8px; font-weight: 700;">
                        Max Marks: {{ $homework->max_marks }}
                    </span>
                @endif
            </div>

            <button type="button" 
                    class="btn btn-xs btn-light btn-nav-student" 
                    data-homework_id="{{ $homeworkId ?? '' }}" 
                    data-admission_id="{{ $nextStudentId }}" 
                    {{ empty($nextStudentId) ? 'disabled style=opacity:0.4;cursor:not-allowed;' : '' }}
                    style="font-size: 10.5px; font-weight: 600; padding: 2px 8px;">
                Next Student <i class="fa fa-chevron-right ml-1"></i>
            </button>
        </div>
    @endif

    {{-- 1. Student Profile Strip --}}
    <div class="eval-student-strip">
        <div class="d-flex align-items-center" style="min-width: 0;">
            <div class="eval-stu-avatar">
                {{ strtoupper(substr($studentName, 0, 1)) }}
            </div>
            <div style="min-width: 0;">
                <div class="eval-stu-name text-truncate">
                    {{ $studentName }}
                    <span class="badge badge-light border text-navy ml-2 font-weight-normal" style="font-size: 10.5px;">
                        Adm: <strong>{{ $admNo }}</strong>
                    </span>
                </div>
                <div class="eval-stu-meta">
                    <span><i class="fa fa-th-large text-primary"></i> Class: <strong>{{ $className }} {{ !empty($sectionName) ? "($sectionName)" : "" }}</strong></span>
                    <span><i class="fa fa-user text-secondary"></i> Father: <strong>{{ $fatherName }}</strong></span>
                    <span><i class="fa fa-phone text-success"></i> <a href="tel:{{ $mobile }}" class="text-dark"><strong>{{ $mobile }}</strong></a></span>
                </div>
            </div>
        </div>

        <div>
            <span class="badge" style="background: #002C54; color: #ffffff; padding: 4px 8px; font-size: 11px; font-weight: 600; border-radius: 3px;">
                <i class="fa fa-history"></i> {{ $totalAttempts }} {{ Str::plural('Submission Attempt', $totalAttempts) }}
            </span>
        </div>
    </div>

    {{-- 2. Submissions Attempts List --}}
    @if(!empty($data) && count($data) > 0)
        @php
            $attemptCount = count($data);
        @endphp
        @foreach($data as $key1 => $type)
            @php
                $hwDocument = $type->HomeworkDocuments ?? \App\Helpers\Helper::getHwDocument($type['id']);
                $attemptNumber = $attemptCount - $key1;
                $allDocsChecked = true;
                $hasDocs = (!empty($hwDocument) && count($hwDocument) > 0);
                if ($hasDocs) {
                    foreach($hwDocument as $d) {
                        if (($d->status ?? 0) != 1) {
                            $allDocsChecked = false;
                            break;
                        }
                    }
                } else {
                    $allDocsChecked = false;
                }
            @endphp

            <div class="eval-attempt-card">
                {{-- Attempt Header Strip --}}
                <div class="attempt-header">
                    <div class="d-flex align-items-center">
                        <span class="attempt-badge">Attempt #{{ $attemptNumber }}</span>
                        <span class="attempt-date">
                            <i class="fa fa-calendar-check-o text-primary"></i> Submitted: 
                            <strong>{{ !empty($type['submission_date']) ? date('d M Y', strtotime($type['submission_date'])) : 'N/A' }}</strong>
                            @if(!empty($type['created_at']))
                                <span class="text-muted ml-1 font-italic">({{ date('h:i A', strtotime($type['created_at'])) }})</span>
                            @endif
                        </span>
                    </div>

                    <div>
                        @if($allDocsChecked && $hasDocs)
                            <span class="badge badge-success px-2 py-1" style="font-size: 10px;">
                                <i class="fa fa-check-circle"></i> All Checked
                            </span>
                        @else
                            <span class="badge badge-warning text-dark px-2 py-1" style="font-size: 10px; background: #fef08a; border: 1px solid #fde047;">
                                <i class="fa fa-clock-o"></i> Needs Review
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Student Message/Note if provided --}}
                @if(!empty($type['message']))
                    <div class="student-note-card">
                        <i class="fa fa-commenting-o text-success"></i>
                        <span><strong>Student Note:</strong> "{{ $type['message'] }}"</span>
                    </div>
                @endif

                {{-- Attempt Body: Documents & Teacher Evaluation --}}
                <div class="attempt-body">
                    @if($hasDocs)
                        @foreach($hwDocument as $key => $info)
                            @php
                                $fileName = $info->content_file ?? '';
                                $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                                $isPdf = ($ext === 'pdf');
                                $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                $filePath = env('IMAGE_SHOW_PATH') . 'uploadHomework/' . $fileName;
                                $isDocChecked = (($info->status ?? 0) == 1);
                            @endphp

                            <div class="doc-review-item">
                                <div class="row align-items-stretch">
                                    {{-- Left: Uploaded Assignment File --}}
                                    <div class="col-12 col-md-4 col-lg-3 mb-2 mb-md-0">
                                        <div class="doc-tile">
                                            <div class="doc-thumb-wrap">
                                                @if($isImage)
                                                    <img src="{{ $filePath }}" 
                                                         class="doc-thumb-img viewModal_{{ $info['upload_hw_id'] }} viewModal2" 
                                                         data-href="{{ $filePath }}" 
                                                         data-upload_id="{{ $info['upload_hw_id'] }}" 
                                                         alt="Assignment Image"
                                                         title="Click to preview image full-screen"
                                                         onerror="this.onerror=null; this.src='{{ env('IMAGE_SHOW_PATH') }}/default/user_image.jpg';">
                                                @elseif($isPdf)
                                                    <div class="doc-icon-wrap">
                                                        <i class="fa fa-file-pdf-o text-danger"></i>
                                                        <div class="badge badge-danger mt-1" style="font-size: 9.5px;">PDF File</div>
                                                    </div>
                                                @else
                                                    <div class="doc-icon-wrap">
                                                        <i class="fa fa-file-text-o text-primary"></i>
                                                        <div class="badge badge-secondary mt-1" style="font-size: 9.5px;">.{{ strtoupper($ext ?: 'DOC') }}</div>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="doc-filename" title="{{ $fileName }}">
                                                {{ $fileName }}
                                            </div>

                                            <div class="doc-actions">
                                                <button type="button" 
                                                        class="btn-doc-act btn-doc-view viewModal_{{ $info['upload_hw_id'] }} viewModal2" 
                                                        data-href="{{ $filePath }}" 
                                                        data-upload_id="{{ $info['upload_hw_id'] }}" 
                                                        title="View Document in Preview Modal">
                                                    <i class="fa fa-eye"></i> View
                                                </button>

                                                @if($isPdf || $isImage)
                                                    <button type="button" 
                                                            class="btn-doc-act btn-doc-split" 
                                                            data-target="#inline-doc-{{ $info->id }}" 
                                                            title="Toggle in-modal document view"
                                                            style="background: #3b82f6; color: #fff !important; border: 1px solid #2563eb;">
                                                        <i class="fa fa-columns"></i> Split
                                                    </button>
                                                @endif

                                                <a href="{{ url('download_assignment') }}/{{ $fileName }}" 
                                                   class="btn-doc-act btn-doc-download" 
                                                   title="Download original file" 
                                                   download>
                                                    <i class="fa fa-download"></i> Download
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Right: Teacher Evaluation & Feedback Box --}}
                                    <div class="col-12 col-md-8 col-lg-9">
                                        <div class="feedback-container">
                                            <div class="feedback-header">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="feedback-title">
                                                        <i class="fa fa-pencil-square-o text-primary"></i> Feedback &amp; Remarks:
                                                    </span>
                                                    @if(!$isStudentRole)
                                                        <div class="d-flex align-items-center gap-1 ml-2">
                                                            <label class="mb-0 font-weight-bold" style="font-size: 10px; color: #475569;">Marks:</label>
                                                            <input type="number" 
                                                                   step="0.5" 
                                                                   min="0" 
                                                                   max="{{ $homework->max_marks ?? 100 }}" 
                                                                   class="form-control marks-input marks_{{ $key1 }}" 
                                                                   data-id="{{ $info->id }}" 
                                                                   value="{{ $info->marks ?? '' }}" 
                                                                   placeholder="{{ !empty($homework->max_marks) ? '0-' . $homework->max_marks : 'Marks' }}" 
                                                                   style="width: 72px; height: 24px; padding: 2px 6px; font-size: 11px; font-weight: 700; text-align: center; border: 1px solid #cbd5e1; border-radius: 2px;">
                                                            @if(!empty($homework->max_marks))
                                                                <span class="text-muted font-weight-bold" style="font-size: 10px;">/ {{ $homework->max_marks }}</span>
                                                            @endif
                                                        </div>
                                                    @else
                                                        @if(isset($info->marks) && $info->marks !== null)
                                                            <span class="badge badge-success ml-2 px-2 py-1" style="font-size: 10px;">
                                                                <i class="fa fa-trophy mr-1"></i> Marks: <strong>{{ $info->marks }}</strong>@if(!empty($homework->max_marks))/{{ $homework->max_marks }}@endif
                                                            </span>
                                                        @endif
                                                    @endif
                                                </div>
                                                <div>
                                                    @if($isDocChecked)
                                                        <span class="badge badge-success px-2 py-1" style="font-size: 10px;">
                                                            <i class="fa fa-check"></i> Evaluated
                                                        </span>
                                                        @if(!empty($info->evaluate_date))
                                                            <small class="text-muted ml-1" style="font-size: 10px;">
                                                                ({{ date('d M Y', strtotime($info->evaluate_date)) }})
                                                            </small>
                                                        @endif
                                                    @else
                                                        <span class="badge badge-secondary px-2 py-1" style="font-size: 10px; background: #94a3b8;">
                                                            Awaiting Evaluation
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            @if($isStudentRole)
                                                <textarea class="form-control review-textarea" 
                                                          placeholder="No review submitted by teacher yet." 
                                                          readonly>{{ $info->hw_review ?? '' }}</textarea>
                                            @else
                                                <textarea class="form-control review-textarea submit_{{ $key1 }}" 
                                                          id="message_{{ $key }}" 
                                                          name="message" 
                                                          placeholder="Type remarks or feedback for this assignment..." 
                                                          data-id="{{ $info->id }}">{{ $info->hw_review ?? '' }}</textarea>

                                                {{-- Quick Feedback Template Chips --}}
                                                <div class="quick-chips-bar">
                                                    <span class="chips-label">Quick Suggestions:</span>
                                                    <button type="button" class="quick-chip btn-quick-chip" data-text="Excellent work, well done! ⭐">⭐ Excellent</button>
                                                    <button type="button" class="quick-chip btn-quick-chip" data-text="Good job, keep it up! 👍">👍 Good Job</button>
                                                    <button type="button" class="quick-chip btn-quick-chip" data-text="Fair attempt, but please check question 2.">📝 Check Q2</button>
                                                    <button type="button" class="quick-chip btn-quick-chip" data-text="Incomplete homework. Please complete and resubmit. ⚠️">⚠️ Incomplete</button>
                                                    <button type="button" class="quick-chip btn-quick-chip" data-text="Handwriting needs improvement.">✍️ Handwriting</button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- In-Modal Split-Screen Document Viewer Pane --}}
                                @if($isPdf || $isImage)
                                    <div id="inline-doc-{{ $info->id }}" class="inline-preview-pane mt-2" style="display: none; border: 1px solid #cbd5e1; border-radius: 3px; background: #0f172a; padding: 6px; text-align: center;">
                                        <div class="d-flex align-items-center justify-content-between mb-1 px-1">
                                            <span class="text-white-50" style="font-size: 10.5px;"><i class="fa fa-eye mr-1"></i> Document Preview: <strong>{{ $fileName }}</strong></span>
                                            <button type="button" class="btn btn-xs btn-outline-light py-0 px-1 btn-close-inline-preview" data-target="#inline-doc-{{ $info->id }}" style="font-size: 9.5px;">
                                                <i class="fa fa-times"></i> Close
                                            </button>
                                        </div>
                                        @if($isPdf)
                                            <iframe src="{{ $filePath }}" width="100%" height="420" frameborder="0" style="border-radius: 2px; background: #fff;"></iframe>
                                        @else
                                            <img src="{{ $filePath }}" class="img-fluid" style="max-height: 420px; border-radius: 2px; object-fit: contain;" alt="Assignment File">
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endforeach

                        {{-- Attempt Submission Footer Action --}}
                        @if(!$isStudentRole)
                            <div class="attempt-card-footer">
                                <span class="footer-note">
                                    <i class="fa fa-info-circle text-info"></i> Save remarks &amp; marks for this student's attempt.
                                </span>
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" class="btn-navy-action submitReview" data-submit="{{ $key1 }}" data-homework_id="{{ $homeworkId ?? '' }}">
                                        <i class="fa fa-save"></i> Save Review
                                    </button>
                                    @if(!empty($nextStudentId))
                                        <button type="button" class="btn-navy-action submitReviewAndNext" style="background: #0284c7; border-color: #0284c7;" data-submit="{{ $key1 }}" data-homework_id="{{ $homeworkId ?? '' }}" data-next_admission_id="{{ $nextStudentId }}">
                                            Save &amp; Next Student <i class="fa fa-arrow-right ml-1"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="alert alert-light text-muted border mb-0 text-center py-3">
                            <i class="fa fa-file-o"></i> No assignment files attached in this attempt.
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    @else
        <div class="eval-empty-state">
            <div class="eval-empty-icon"><i class="fa fa-folder-open-o"></i></div>
            <h6 class="font-weight-bold text-dark mb-1">No Submissions Found</h6>
            <p class="text-muted mb-0" style="font-size: 11px;">The student has not submitted any files for this homework yet.</p>
        </div>
    @endif
</div>

<script>
// Attach click listener for Quick Feedback Chips
$(document).off('click', '.btn-quick-chip').on('click', '.btn-quick-chip', function(e) {
    e.preventDefault();
    var text = $(this).data('text');
    var $textarea = $(this).closest('.feedback-container').find('.review-textarea');
    var current = $textarea.val().trim();
    if (!current) {
        $textarea.val(text);
    } else {
        $textarea.val(current + ' | ' + text);
    }
    $textarea.focus();
});

// Toggle inline split-screen preview
$(document).off('click', '.btn-doc-split').on('click', '.btn-doc-split', function(e) {
    e.preventDefault();
    var target = $(this).data('target');
    $(target).slideToggle(200);
});

$(document).off('click', '.btn-close-inline-preview').on('click', '.btn-close-inline-preview', function(e) {
    e.preventDefault();
    var target = $(this).data('target');
    $(target).slideUp(200);
});
</script>