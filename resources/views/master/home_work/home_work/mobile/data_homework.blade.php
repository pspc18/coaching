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
    $mobile = $student->mobile ?? ($data[0]['Admission']['mobile'] ?? '');
    $totalAttempts = count($data ?? []);
    $isStudentRole = (Session::get('role_id') == 3);
@endphp

<style>
/* ==========================================================================
   ARISE ERP - NATIVE MOBILE EVALUATION & REVIEW MODAL WORKSPACE
   - Aligned with Arise ERP Mobile Design System (Navy theme, sharp 4px/6px radius)
   - Fixed Top Header: Student Switcher Bar (Prev / Next student navigation)
   - Student Glance Profile Card (Avatar, Name, Admission, Class, Father, Phone)
   - Structured Attempt Feed (Date, Attempt Pill, Evaluation Status)
   - Document Preview Tile (Clean thumbnail, full-screen view trigger, download)
   - Evaluation & Marks Card:
       - Marks Input with auto max-marks indicator
       - Auto-grow Feedback textarea
       - 1-Tap Quick Feedback Preset Chips (⭐ Excellent, 👍 Good, 📝 Incomplete, etc.)
   - Pinned Action Dock: "Save Review" & "Save & Next Student" buttons
   ========================================================================== */

.mob-eval-sheet-wrapper {
    display: flex;
    flex-direction: column;
    gap: 8px;
    font-family: inherit;
    color: #0f172a;
    font-size: 11.5px;
    padding-bottom: 70px; /* Space for sticky bottom action dock */
    position: relative;
}

/* 1. Top Student Switcher Navigation Bar */
.mob-eval-nav-bar {
    background: #001f3f;
    border-radius: 4px;
    padding: 6px 8px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    color: #ffffff;
    box-shadow: 0 2px 6px rgba(0, 31, 63, 0.25);
    border: 1px solid rgba(255, 255, 255, 0.1);
}
.mob-eval-nav-btn {
    height: 28px;
    padding: 0 9px;
    border-radius: 3px;
    font-size: 10.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    border: none;
    cursor: pointer;
    transition: all .12s ease;
    background: rgba(255, 255, 255, 0.15);
    color: #ffffff !important;
}
.mob-eval-nav-btn:disabled {
    opacity: 0.35;
    cursor: not-allowed;
}
.mob-eval-nav-btn:not(:disabled):active {
    transform: scale(0.95);
    background: rgba(255, 255, 255, 0.28);
}
.mob-eval-counter-pill {
    font-size: 10px;
    font-weight: 800;
    color: #38bdf8;
    background: rgba(56, 189, 248, 0.15);
    border: 1px solid rgba(56, 189, 248, 0.3);
    padding: 3px 8px;
    border-radius: 3px;
    white-space: nowrap;
}

/* 2. Student Glance Profile Card */
.mob-eval-profile-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 8px 10px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    border-left: 3.5px solid #002C54;
}
.mob-eval-profile-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 6px;
    padding-bottom: 5px;
    border-bottom: 1px solid #f1f5f9;
}
.mob-eval-profile-left {
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 0;
    flex: 1;
}
.mob-eval-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, #002C54 0%, #0284c7 100%);
    color: #ffffff;
    font-weight: 800;
    font-size: 13px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 2px 5px rgba(0, 44, 84, 0.25);
}
.mob-eval-name {
    font-size: 12.5px;
    font-weight: 800;
    color: #002C54;
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.mob-eval-subtext {
    font-size: 9.5px;
    color: #64748b;
    font-weight: 600;
    margin-top: 1px;
}
.mob-eval-profile-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4px 8px;
    font-size: 10px;
    color: #475569;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 3px;
    padding: 5px 8px;
}
.mob-eval-grid-col {
    display: flex;
    align-items: center;
    gap: 4px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.mob-eval-grid-col i {
    color: #64748b;
    font-size: 9.5px;
    width: 12px;
    text-align: center;
    flex-shrink: 0;
}
.mob-eval-grid-col strong {
    color: #0f172a;
    font-weight: 700;
}

/* 3. Attempt Card Container */
.mob-attempt-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    margin-bottom: 6px;
}
.mob-attempt-header {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    padding: 6px 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.mob-attempt-title {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 10.5px;
    font-weight: 700;
    color: #334155;
}
.mob-attempt-badge {
    background: #002C54;
    color: #ffffff;
    font-size: 9px;
    font-weight: 800;
    padding: 2px 6px;
    border-radius: 2px;
    text-transform: uppercase;
}
.mob-attempt-note {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 3px;
    padding: 5px 8px;
    margin: 6px 8px 0 8px;
    font-size: 10.5px;
    color: #166534;
    display: flex;
    align-items: flex-start;
    gap: 5px;
    line-height: 1.35;
}

/* 4. Document Item & Evaluation Workspace */
.mob-doc-eval-card {
    background: #fcfdfe;
    border: 1px solid #e2e8f0;
    border-radius: 3px;
    padding: 8px;
    margin: 6px 8px 8px 8px;
}

/* Document Thumbnail Box */
.mob-eval-doc-box {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    padding: 6px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    margin-bottom: 8px;
}
.mob-eval-doc-left {
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 0;
    flex: 1;
}
.mob-eval-doc-thumb {
    width: 38px;
    height: 38px;
    border-radius: 3px;
    border: 1px solid #cbd5e1;
    object-fit: contain;
    background: #f8fafc;
    flex-shrink: 0;
    cursor: pointer;
}
.mob-eval-doc-icon {
    width: 38px;
    height: 38px;
    border-radius: 3px;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: #0284c7;
    flex-shrink: 0;
}
.mob-eval-doc-details {
    min-width: 0;
    flex: 1;
}
.mob-eval-doc-name {
    font-size: 10.5px;
    font-weight: 700;
    color: #0f172a;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: block;
}
.mob-eval-doc-tag {
    font-size: 9px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
}
.mob-eval-doc-btns {
    display: flex;
    align-items: center;
    gap: 4px;
    flex-shrink: 0;
}
.mob-doc-btn {
    height: 26px;
    padding: 0 8px;
    border-radius: 3px;
    font-size: 10px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 3px;
    text-decoration: none !important;
    border: none;
    cursor: pointer;
}
.mob-doc-btn-view {
    background: #0284c7;
    color: #ffffff !important;
}
.mob-doc-btn-download {
    background: #f1f5f9;
    color: #334155 !important;
    border: 1px solid #cbd5e1;
}

/* 5. Teacher Grading & Feedback Inputs */
.mob-eval-form-box {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 8px 10px;
}
.mob-eval-form-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 6px;
}
.mob-eval-form-title {
    font-size: 10.5px;
    font-weight: 800;
    color: #002C54;
    text-transform: uppercase;
    display: flex;
    align-items: center;
    gap: 4px;
}
.mob-marks-input-wrap {
    display: flex;
    align-items: center;
    gap: 4px;
}
.mob-marks-input {
    width: 60px;
    height: 26px;
    padding: 2px 6px;
    font-size: 11px;
    font-weight: 800;
    text-align: center;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    background: #f8fafc;
    color: #0f172a;
    outline: none;
}
.mob-marks-input:focus {
    border-color: #0284c7;
    background: #ffffff;
    box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.15);
}

/* Textarea with auto styling */
.mob-review-textarea {
    width: 100%;
    min-height: 52px;
    padding: 6px 8px;
    font-size: 11px;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    background: #f8fafc;
    color: #0f172a;
    outline: none;
    font-family: inherit;
    line-height: 1.4;
    resize: vertical;
    margin-bottom: 6px;
}
.mob-review-textarea:focus {
    border-color: #0284c7;
    background: #ffffff;
    box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.15);
}

/* 6. Quick Feedback Preset Chips */
.mob-quick-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
}
.mob-quick-chip {
    font-size: 9.5px;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 3px;
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    color: #334155;
    cursor: pointer;
    transition: all .1s ease;
    user-select: none;
}
.mob-quick-chip:active {
    transform: scale(0.95);
    background: #e2e8f0;
}

/* 7. Pinned Sticky Action Dock inside Modal */
.mob-eval-bottom-dock {
    position: sticky;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(8px);
    border-top: 1px solid #cbd5e1;
    padding: 8px 10px;
    display: flex;
    align-items: center;
    gap: 6px;
    margin-top: 6px;
    z-index: 10;
    box-shadow: 0 -2px 10px rgba(0, 44, 84, 0.08);
}
.mob-eval-action-btn {
    height: 34px;
    border-radius: 4px;
    font-size: 11.5px;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    border: none;
    cursor: pointer;
    transition: all .12s ease;
    font-family: inherit;
    box-sizing: border-box;
}
.mob-eval-action-btn:active {
    transform: scale(0.97);
}
.mob-btn-save {
    flex: 1;
    background: #002C54;
    color: #ffffff !important;
    box-shadow: 0 2px 6px rgba(0, 44, 84, 0.25);
}
.mob-btn-save-next {
    flex: 1.2;
    background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
    color: #ffffff !important;
    box-shadow: 0 2px 6px rgba(2, 132, 199, 0.3);
}
.mob-eval-action-btn:disabled {
    background: #94a3b8 !important;
    cursor: not-allowed;
    transform: none !important;
}

/* Empty State */
.mob-eval-empty {
    background: #ffffff;
    border: 1px dashed #cbd5e1;
    border-radius: 4px;
    padding: 30px 16px;
    text-align: center;
    color: #64748b;
    margin-top: 8px;
}
</style>

<div class="mob-eval-sheet-wrapper">
    {{-- Hidden hook for JS student name synchronization --}}
    <span id="stuName" data-first_name="{{ $studentName }}" style="display:none;"></span>

    {{-- 1. Top Student Switcher Navigation Bar --}}
    @if(!$isStudentRole && isset($totalStudents) && $totalStudents > 1)
        <div class="mob-eval-nav-bar">
            <button type="button" 
                    class="mob-eval-nav-btn btn-nav-student" 
                    data-homework_id="{{ $homeworkId ?? '' }}" 
                    data-admission_id="{{ $prevStudentId }}" 
                    {{ empty($prevStudentId) ? 'disabled' : '' }}>
                <i class="fa fa-chevron-left"></i> Prev Student
            </button>

            <div class="mob-eval-counter-pill">
                <span>Student <strong>{{ $currentIndex ?? 1 }}</strong> / <strong>{{ $totalStudents }}</strong></span>
                @if(!empty($homework->max_marks))
                    <span class="ml-1 text-white">&bull; Max: <strong>{{ $homework->max_marks }}</strong></span>
                @endif
            </div>

            <button type="button" 
                    class="mob-eval-nav-btn btn-nav-student" 
                    data-homework_id="{{ $homeworkId ?? '' }}" 
                    data-admission_id="{{ $nextStudentId }}" 
                    {{ empty($nextStudentId) ? 'disabled' : '' }}>
                Next Student <i class="fa fa-chevron-right"></i>
            </button>
        </div>
    @endif

    {{-- 2. Student Glance Profile Card --}}
    <div class="mob-eval-profile-card">
        <div class="mob-eval-profile-top">
            <div class="mob-eval-profile-left">
                <div class="mob-eval-avatar">
                    {{ strtoupper(substr($studentName, 0, 1)) }}
                </div>
                <div style="min-width: 0; flex: 1;">
                    <div class="mob-eval-name">{{ $studentName }}</div>
                    <div class="mob-eval-subtext">
                        <span>Adm: <strong>{{ $admNo }}</strong></span>
                        <span class="mx-1">&bull;</span>
                        <span>Class: <strong>{{ $className }} {{ !empty($sectionName) ? "($sectionName)" : "" }}</strong></span>
                    </div>
                </div>
            </div>
            <div>
                <span class="badge badge-primary px-2 py-1" style="font-size: 9.5px; font-weight: 800;">
                    <i class="fa fa-history mr-1"></i> {{ $totalAttempts }} {{ Str::plural('Attempt', $totalAttempts) }}
                </span>
            </div>
        </div>

        <div class="mob-eval-profile-grid">
            <div class="mob-eval-grid-col">
                <i class="fa fa-user"></i> <span>Father: <strong>{{ $fatherName }}</strong></span>
            </div>
            <div class="mob-eval-grid-col">
                <i class="fa fa-phone text-success"></i> <span>Phone: <strong>{{ $mobile ?: 'N/A' }}</strong></span>
            </div>
        </div>
    </div>

    {{-- 3. Submissions Attempts List --}}
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

            <div class="mob-attempt-card">
                {{-- Attempt Header --}}
                <div class="mob-attempt-header">
                    <div class="mob-attempt-title">
                        <span class="mob-attempt-badge">Attempt #{{ $attemptNumber }}</span>
                        <span><i class="fa fa-calendar-check-o text-primary mr-1"></i> {{ !empty($type['submission_date']) ? date('d M Y, h:i A', strtotime($type['submission_date'])) : 'N/A' }}</span>
                    </div>
                    <div>
                        @if($allDocsChecked && $hasDocs)
                            <span class="badge badge-success px-2 py-1" style="font-size: 9px; font-weight: 800;">
                                <i class="fa fa-check-circle"></i> Checked
                            </span>
                        @else
                            <span class="badge badge-warning text-dark px-2 py-1" style="font-size: 9px; font-weight: 800;">
                                <i class="fa fa-clock-o"></i> Needs Review
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Student Message / Note --}}
                @if(!empty($type['message']))
                    <div class="mob-attempt-note">
                        <i class="fa fa-commenting-o text-success mt-1"></i>
                        <span><strong>Student Note:</strong> "{{ $type['message'] }}"</span>
                    </div>
                @endif

                {{-- Documents & Review Items --}}
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

                        <div class="mob-doc-eval-card">
                            {{-- Document File Box --}}
                            <div class="mob-eval-doc-box">
                                <div class="mob-eval-doc-left">
                                    @if($isImage)
                                        <img src="{{ $filePath }}" 
                                             class="mob-eval-doc-thumb viewModal2" 
                                             data-href="{{ $filePath }}" 
                                             alt="Doc Image" 
                                             onerror="this.onerror=null; this.src='{{ env('IMAGE_SHOW_PATH') }}/default/user_image.jpg';">
                                    @elseif($isPdf)
                                        <div class="mob-eval-doc-icon">
                                            <i class="fa fa-file-pdf-o text-danger"></i>
                                        </div>
                                    @else
                                        <div class="mob-eval-doc-icon">
                                            <i class="fa fa-file-text-o text-primary"></i>
                                        </div>
                                    @endif

                                    <div class="mob-eval-doc-details">
                                        <span class="mob-eval-doc-name" title="{{ $fileName }}">{{ $fileName }}</span>
                                        <span class="mob-eval-doc-tag">
                                            @if($isDocChecked)
                                                <span class="text-success"><i class="fa fa-check"></i> Evaluated</span>
                                            @else
                                                <span class="text-warning"><i class="fa fa-clock-o"></i> Pending Review</span>
                                            @endif
                                        </span>
                                    </div>
                                </div>

                                <div class="mob-eval-doc-btns">
                                    <button type="button" class="mob-doc-btn mob-doc-btn-view viewModal2" data-href="{{ $filePath }}">
                                        <i class="fa fa-eye"></i> View
                                    </button>
                                    <a href="{{ url('download_assignment') }}/{{ $fileName }}" class="mob-doc-btn mob-doc-btn-download" download>
                                        <i class="fa fa-download"></i>
                                    </a>
                                </div>
                            </div>

                            {{-- Teacher Feedback & Marks Form --}}
                            <div class="mob-eval-form-box">
                                <div class="mob-eval-form-header">
                                    <div class="mob-eval-form-title">
                                        <i class="fa fa-pencil-square-o text-primary"></i> Teacher Remarks:
                                    </div>
                                    @if(!$isStudentRole)
                                        <div class="mob-marks-input-wrap">
                                            <label class="mb-0 text-muted font-weight-bold" style="font-size: 9.5px;">Marks:</label>
                                            <input type="number" 
                                                   step="0.5" 
                                                   min="0" 
                                                   max="{{ $homework->max_marks ?? 100 }}" 
                                                   class="mob-marks-input marks_{{ $key1 }}" 
                                                   data-id="{{ $info->id }}" 
                                                   value="{{ $info->marks ?? '' }}" 
                                                   placeholder="{{ !empty($homework->max_marks) ? '0-' . $homework->max_marks : 'Marks' }}">
                                            @if(!empty($homework->max_marks))
                                                <span class="text-muted font-weight-bold" style="font-size: 9.5px;">/ {{ $homework->max_marks }}</span>
                                            @endif
                                        </div>
                                    @else
                                        @if(isset($info->marks) && $info->marks !== null)
                                            <span class="badge badge-success px-2 py-1" style="font-size: 9.5px;">
                                                Marks: <strong>{{ $info->marks }}</strong>@if(!empty($homework->max_marks))/{{ $homework->max_marks }}@endif
                                            </span>
                                        @endif
                                    @endif
                                </div>

                                @if($isStudentRole)
                                    <textarea class="mob-review-textarea" readonly>{{ $info->hw_review ?? 'No remarks submitted yet.' }}</textarea>
                                @else
                                    <textarea class="mob-review-textarea submit_{{ $key1 }}" 
                                              placeholder="Write feedback, praise, or corrections for student..." 
                                              data-id="{{ $info->id }}">{{ $info->hw_review ?? '' }}</textarea>

                                    {{-- Quick Preset Suggestions --}}
                                    <div class="mob-quick-chips">
                                        <button type="button" class="mob-quick-chip btn-quick-chip" data-text="Excellent work, well done! ⭐">⭐ Excellent</button>
                                        <button type="button" class="mob-quick-chip btn-quick-chip" data-text="Good job, keep it up! 👍">👍 Good Job</button>
                                        <button type="button" class="mob-quick-chip btn-quick-chip" data-text="Incomplete homework. Please resubmit. ⚠️">⚠️ Incomplete</button>
                                        <button type="button" class="mob-quick-chip btn-quick-chip" data-text="Handwriting needs improvement. ✍️">✍️ Handwriting</button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    {{-- Attempt Sticky Submit Footer Action --}}
                    @if(!$isStudentRole)
                        <div class="mob-eval-bottom-dock">
                            <button type="button" 
                                    class="mob-eval-action-btn mob-btn-save submitReview" 
                                    data-submit="{{ $key1 }}" 
                                    data-homework_id="{{ $homeworkId ?? '' }}">
                                <i class="fa fa-save"></i> Save Review
                            </button>
                            @if(!empty($nextStudentId))
                                <button type="button" 
                                        class="mob-eval-action-btn mob-btn-save-next submitReviewAndNext" 
                                        data-submit="{{ $key1 }}" 
                                        data-homework_id="{{ $homeworkId ?? '' }}" 
                                        data-next_admission_id="{{ $nextStudentId }}">
                                    Save &amp; Next Student <i class="fa fa-arrow-right"></i>
                                </button>
                            @endif
                        </div>
                    @endif
                @else
                    <div class="alert alert-light text-muted border m-2 text-center py-2" style="font-size: 10.5px;">
                        <i class="fa fa-file-o mr-1"></i> No attached documents in this attempt.
                    </div>
                @endif
            </div>
        @endforeach
    @else
        <div class="mob-eval-empty">
            <div style="font-size: 28px; color: #94a3b8; margin-bottom: 4px;"><i class="fa fa-folder-open-o"></i></div>
            <div style="font-size: 12px; font-weight: 800; color: #0f172a;">No Submissions Found</div>
            <div style="font-size: 10px; color: #64748b;">This student has not submitted any files for this homework yet.</div>
        </div>
    @endif
</div>

<script>
// Click listener for Quick Feedback Chips in mobile view
$(document).off('click', '.btn-quick-chip').on('click', '.btn-quick-chip', function(e) {
    e.preventDefault();
    var text = $(this).data('text');
    var $textarea = $(this).closest('.mob-eval-form-box').find('.mob-review-textarea');
    var current = $textarea.val().trim();
    if (!current) {
        $textarea.val(text);
    } else {
        $textarea.val(current + ' | ' + text);
    }
    $textarea.focus();
});
</script>
