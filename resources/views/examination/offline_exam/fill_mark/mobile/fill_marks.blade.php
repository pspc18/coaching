@php
    $getSetting = $getSetting ?? Helper::getSetting();
    $classType = Helper::classType();
    $search = $search ?? [];
    $requestedClassId = (int) ($search['class_type_id'] ?? 0);
    $selectedExamId = (int) ($search['exam_id'] ?? 0);
    $selectedSubjectIds = collect($selectedSubjectIds ?? old('subject_name_id', $search['subject_name_id'] ?? []))
        ->map(function ($id) {
            return (int) $id;
        })
        ->filter()
        ->values()
        ->all();
    $showMarksSection = (bool) ($showMarksSection ?? false);
    $subjects = $subjects ?? collect();
    $Allsubjects = $Allsubjects ?? collect();
    $data2 = $data2 ?? collect();
    $examlist = $examlist ?? collect();
    $selectedClassName = $classType->firstWhere('id', $requestedClassId)->name ?? 'Class Not Selected';
    $selectedExamName = $examlist->firstWhere('exam_id', $selectedExamId)->exam_name ?? 'Exam Not Selected';
    $roleId = Session::get('role_id');
    $isPublished = $isPublished ?? false;
    $fillMinMaxMarksMap = $fillMinMaxMarksMap ?? collect();
    $existingMarksMap = $existingMarksMap ?? collect();
    $studentSubjectAssignments = $studentSubjectAssignments ?? [];
    $classOrderBy = $classOrderBy ?? 0;
@endphp

@extends('layout.mobile_app')

@section('styles')
<style>
/* Fill Marks - Mobile Layout */
.mob-marks-hero {
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    color: #ffffff;
    border-radius: 4px;
    padding: 10px 12px;
    margin-bottom: 8px;
    box-shadow: 0 4px 14px rgba(0, 44, 84, 0.25);
    border: 1px solid rgba(255, 255, 255, 0.12);
}
.mob-marks-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 4px;
}
.mob-marks-title {
    font-size: 13px;
    font-weight: 800;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.mob-badge-step {
    font-size: 9px;
    font-weight: 700;
    background: rgba(255, 255, 255, 0.2);
    color: #ffffff;
    padding: 2px 7px;
    border-radius: 3px;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

/* Steps Indicator */
.mob-steps-nav {
    display: flex;
    background: #ffffff;
    border-radius: 3px;
    border: 1px solid #e2e8f0;
    padding: 4px;
    margin-bottom: 8px;
    gap: 4px;
}
.mob-step-item {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    padding: 5px 4px;
    border-radius: 2px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    font-size: 10.5px;
    font-weight: 700;
    color: #64748b;
    text-decoration: none !important;
}
.mob-step-item.active {
    background: #002C54;
    border-color: #001f3f;
    color: #ffffff;
}
.mob-step-num {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: #cbd5e1;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 9px;
    font-weight: 800;
}
.mob-step-item.active .mob-step-num {
    background: #ffffff;
    color: #002C54;
}

/* Card Containers */
.mob-card {
    background: #ffffff;
    border-radius: 4px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    padding: 10px 12px;
    margin-bottom: 8px;
}
.mob-card-head {
    font-size: 11.5px;
    font-weight: 800;
    color: #002C54;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
    padding-bottom: 5px;
    border-bottom: 1px solid #f1f5f9;
}

.mob-form-group {
    margin-bottom: 8px;
}
.mob-label {
    display: block;
    font-size: 10px;
    font-weight: 700;
    color: #334155;
    margin-bottom: 3px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}
.mob-select, .mob-input {
    width: 100%;
    height: 32px;
    border-radius: 3px;
    border: 1px solid #cbd5e1;
    padding: 0 8px;
    font-size: 11.5px;
    background: #ffffff;
    color: #0f172a;
}
.mob-select:focus, .mob-input:focus {
    border-color: #002C54;
    outline: none;
    box-shadow: 0 0 0 2px rgba(0, 44, 84, 0.1);
}

/* Buttons */
.mob-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    height: 32px;
    border-radius: 3px;
    font-size: 11.5px;
    font-weight: 700;
    border: none;
    cursor: pointer;
    text-decoration: none !important;
    width: 100%;
}
.mob-btn-primary {
    background: #002C54;
    color: #ffffff;
}
.mob-btn-primary:hover {
    background: #001f3d;
    color: #ffffff;
}
.mob-btn-success {
    background: #10b981;
    color: #ffffff;
}
.mob-btn-secondary {
    background: #f1f5f9;
    color: #334155;
    border: 1px solid #cbd5e1;
}

/* Student Entry Card */
.mob-student-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    padding: 8px 10px;
    margin-bottom: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
}
.mob-student-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 6px;
    padding-bottom: 4px;
    border-bottom: 1px solid #f1f5f9;
}
.mob-student-name {
    font-size: 12px;
    font-weight: 800;
    color: #002C54;
}
.mob-student-adm {
    font-size: 10px;
    font-weight: 700;
    color: #475569;
    background: #f1f5f9;
    padding: 1px 6px;
    border-radius: 2px;
    border: 1px solid #cbd5e1;
}

.mob-sub-block {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 3px;
    padding: 6px 8px;
    margin-bottom: 6px;
}
.mob-sub-name {
    font-size: 10.5px;
    font-weight: 700;
    color: #002C54;
    margin-bottom: 4px;
    display: flex;
    justify-content: space-between;
}
.mob-sub-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 4px;
}
.mob-sub-input-wrap label {
    font-size: 8.5px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    margin-bottom: 1px;
    display: block;
    text-align: center;
}
.mob-sub-input {
    width: 100%;
    height: 26px;
    border-radius: 2px;
    border: 1px solid #cbd5e1;
    text-align: center;
    font-size: 11px;
    font-weight: 700;
    color: #0f172a;
    background: #ffffff;
}
.mob-sub-input:focus {
    border-color: #002C54;
    outline: none;
    box-shadow: 0 0 0 2px rgba(0,44,84,0.1);
}
.mob-sub-input.bg-danger {
    background-color: #fee2e2 !important;
    color: #991b1b !important;
    border-color: #f87171 !important;
}
.mob-sub-input.bg-warning {
    background-color: #fef3c7 !important;
    color: #92400e !important;
    border-color: #fcd34d !important;
}

/* Legend */
.mob-legend {
    font-size: 9.5px;
    color: #475569;
    line-height: 1.4;
    background: #f8fafc;
    padding: 6px 8px;
    border-radius: 3px;
    border: 1px solid #e2e8f0;
    margin-bottom: 8px;
}
</style>
@endsection

@section('content')
<div class="content-wrapper" style="background: #f4f6f9; padding: 8px;">

    <!-- 1. Hero Header -->
    <div class="mob-marks-hero">
        <div class="mob-marks-top">
            <div class="mob-marks-title">
                <i class="fa fa-pencil-square-o text-warning"></i>
                <span>Fill Marks</span>
            </div>
            <span class="mob-badge-step">
                @if(!$showMarksSection) Step 1: Select @else Step 2: Scoring @endif
            </span>
        </div>
        <div style="font-size: 10px; color: #cbd5e1; line-height: 1.2;">
            @if(!$showMarksSection)
                Select class, exam and subjects to enter student marks.
            @else
                Recording marks for {{ $selectedClassName }} &bull; {{ $selectedExamName }}.
            @endif
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('error'))
        <div class="alert alert-danger py-1 px-2 mb-2" style="font-size: 11px; border-radius: 3px;">
            <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif
    @if(session('success') || session('message'))
        <div class="alert alert-success py-1 px-2 mb-2" style="font-size: 11px; border-radius: 3px;">
            <i class="fa fa-check-circle"></i> {{ session('success') ?? session('message') }}
        </div>
    @endif

    <!-- Steps Indicator -->
    <div class="mob-steps-nav">
        <div class="mob-step-item {{ !$showMarksSection ? 'active' : '' }}">
            <div class="mob-step-num">1</div>
            <span>Criteria</span>
        </div>
        <div class="mob-step-item {{ $showMarksSection ? 'active' : '' }}">
            <div class="mob-step-num">2</div>
            <span>Marks Entry</span>
        </div>
    </div>

    <!-- STEP 1: CRITERIA FORM -->
    <form method="post" action="{{ url('fill_marks') }}" id="mobSelectionForm">
        @csrf
        <div class="mob-card">
            <div class="mob-card-head">
                <span><i class="fa fa-filter text-primary"></i> Target Selection</span>
            </div>

            <div class="mob-form-group">
                <label class="mob-label"><i class="fa fa-graduation-cap text-primary"></i> Class <span class="text-danger">*</span></label>
                <select name="class_name" id="mob_class_type_id" class="mob-select" required>
                    <option value="">-- Select Class --</option>
                    @foreach($classType as $class)
                        <option value="{{ $class->id }}" {{ $requestedClassId === (int)$class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mob-form-group">
                <label class="mob-label"><i class="fa fa-leanpub text-primary"></i> Exam <span class="text-danger">*</span></label>
                <select name="exam_id" id="mob_exam_id" class="mob-select" required>
                    <option value="">-- Select Exam --</option>
                    @foreach($examlist as $exam)
                        <option value="{{ $exam->exam_id }}" {{ $selectedExamId === (int)$exam->exam_id ? 'selected' : '' }}>
                            {{ $exam->exam_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mob-form-group">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label class="mob-label mb-0"><i class="fa fa-book text-primary"></i> Subjects <span class="text-danger">*</span></label>
                    <input type="hidden" name="show_all_subjects" id="mob_show_all_subjects" value="0">
                </div>
                <select name="subject_name_id[]" id="mob_subject_id" class="mob-select" multiple="multiple" style="height:60px;">
                    @foreach($Allsubjects as $sub)
                        <option value="{{ $sub->id }}" {{ in_array((int)$sub->id, $selectedSubjectIds, true) ? 'selected' : '' }}>
                            {{ $sub->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; gap: 6px;">
                <button type="button" class="mob-btn mob-btn-secondary" id="mobBtnAllSubjects" style="flex: 1;">
                    <i class="fa fa-check-square-o"></i> All Subjects
                </button>
                <button type="submit" class="mob-btn mob-btn-primary" style="flex: 1.5;">
                    <i class="fa fa-search"></i> Load Students
                </button>
            </div>
        </div>
    </form>

    @if($showMarksSection)
        @if($subjects->isEmpty())
            <div class="alert alert-warning py-2 px-2 mb-2" style="font-size: 11px; border-radius: 3px;">
                <i class="fa fa-exclamation-triangle"></i> No subjects found for this class.
            </div>
        @else
            <!-- STEP 2: MARKS ENTRY FORM -->
            <form action="{{ url('fill_marks_submit') }}" method="post" id="mobFillMarksForm">
                @csrf
                <input type="hidden" name="class_type_id" value="{{ $requestedClassId }}">
                <input type="hidden" name="exam_id" value="{{ $selectedExamId }}">

                <!-- Min/Max Pass Configuration Card -->
                <div class="mob-card">
                    <div class="mob-card-head">
                        <span><i class="fa fa-sliders text-warning"></i> Subject Passing Marks</span>
                        <span class="badge badge-primary" style="font-size: 9px;">{{ $subjects->count() }} Subjects</span>
                    </div>

                    @foreach($subjects as $item)
                        @php
                            $old_value = $fillMinMaxMarksMap->get($item->id);
                            $maxMarks = $old_value->exam_maximum_marks ?? 100;
                            $minMarks = $old_value->exam_minimum_marks ?? 30;
                        @endphp
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 4px 0; border-bottom: 1px solid #f1f5f9; font-size: 11px;">
                            <div style="font-weight: 700; color: #002C54; flex: 1.2;">
                                {{ $item->sub_name ?: $item->name }}
                            </div>
                            <input type="hidden" name="fill_min_max_marks_id[]" class="max_min" data-max_min="{{ $maxMarks }}" value="{{ $old_value->id ?? '' }}"/>
                            <input type="hidden" name="subject_id[]" value="{{ $item->id }}" />
                            <div style="display: flex; gap: 4px; flex: 1.5;">
                                <input type="number" name="exam_maximum_marks[]" value="{{ $maxMarks }}" min="0" step="0.01" required
                                       class="mob-sub-input mob_max_input" id="mob_subject_{{ $item->id }}" placeholder="Max" title="Max Marks" />
                                <input type="number" name="exam_minimum_marks[]" value="{{ $minMarks }}" min="0" step="0.01" required
                                       class="mob-sub-input mob_min_input" id="mob_minimum_marks_{{ $item->id }}" placeholder="Min" title="Min Pass Marks" />
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Codes Legend -->
                <div class="mob-legend">
                    <strong>Status Codes:</strong> <code>AB</code>: Absent &bull; <code>M</code>: Medical &bull; <code>JL</code>: Join Late &bull; <code>F</code>: Fail
                </div>

                <!-- Students Marks Entry Cards -->
                <div class="mob-card" style="padding: 6px;">
                    <div class="mob-card-head" style="margin-bottom: 6px;">
                        <span><i class="fa fa-users text-primary"></i> Students ({{ $data2->count() }})</span>
                    </div>

                    @forelse($data2 as $key => $student)
                        <div class="mob-student-card">
                            <div class="mob-student-header">
                                <div>
                                    <span class="mob-student-name">{{ $key + 1 }}. {{ $student->first_name }} {{ $student->last_name }}</span>
                                    <div style="font-size: 9.5px; color: #64748b;">Father: {{ $student->father_name ?? '-' }}</div>
                                </div>
                                <span class="mob-student-adm">{{ $student->admissionNo }}</span>
                            </div>
                            <input type="hidden" name="admission_id[]" value="{{ $student->id }}">

                            @foreach($subjects as $item1)
                                @php
                                    $mapKey = $student->id . '_' . $item1->id;
                                    $old_marks = $existingMarksMap->get($mapKey);
                                    $minMaxConfig = $fillMinMaxMarksMap->get($item1->id);
                                    $minPassingMarks = $minMaxConfig->exam_minimum_marks ?? 30;

                                    $isEditable = ($classOrderBy <= 10) || !empty($studentSubjectAssignments[$student->id][$item1->id]);
                                    $notAssignedClass = $isEditable ? '' : 'bg-warning';

                                    $oldMarks = $old_marks->student_marks ?? '';
                                    $oldRMarks = $old_marks->r_marks ?? '';
                                    $oldWMarks = $old_marks->w_marks ?? '';
                                    $oldLMarks = $old_marks->l_marks ?? '';

                                    $displayMarks = $isEditable ? $oldMarks : '';
                                    $displayRMarks = $isEditable ? $oldRMarks : '';
                                    $displayWMarks = $isEditable ? $oldWMarks : '';
                                    $displayLMarks = $isEditable ? $oldLMarks : '';

                                    $isFailClass = '';
                                    if ($isEditable && is_numeric($oldMarks) && (float)$oldMarks < (float)$minPassingMarks) {
                                        $isFailClass = 'bg-danger';
                                    }
                                @endphp

                                <input type="hidden" name="fill_marks_id[]" value="{{ $old_marks->id ?? '' }}"/>

                                <div class="mob-sub-block">
                                    <div class="mob-sub-name">
                                        <span><i class="fa fa-book text-muted"></i> {{ $item1->sub_name ?: $item1->name }}</span>
                                        @if(!$isEditable)
                                            <span class="badge badge-warning" style="font-size: 8px;">Not Assigned</span>
                                        @endif
                                    </div>
                                    <div class="mob-sub-grid">
                                        <div class="mob-sub-input-wrap">
                                            <label>R</label>
                                            <input type="text" name="r_marks[]" class="mob-sub-input {{ $notAssignedClass }}"
                                                   maxlength="20" placeholder="R" value="{{ $displayRMarks }}"
                                                   oninput="this.value = this.value.toUpperCase()"
                                                   {{ $isEditable ? '' : 'readonly' }} />
                                        </div>
                                        <div class="mob-sub-input-wrap">
                                            <label>W</label>
                                            <input type="text" name="w_marks[]" class="mob-sub-input {{ $notAssignedClass }}"
                                                   maxlength="20" placeholder="W" value="{{ $displayWMarks }}"
                                                   oninput="this.value = this.value.toUpperCase()"
                                                   {{ $isEditable ? '' : 'readonly' }} />
                                        </div>
                                        <div class="mob-sub-input-wrap">
                                            <label>L</label>
                                            <input type="text" name="l_marks[]" class="mob-sub-input {{ $notAssignedClass }}"
                                                   maxlength="20" placeholder="L" value="{{ $displayLMarks }}"
                                                   oninput="this.value = this.value.toUpperCase()"
                                                   {{ $isEditable ? '' : 'readonly' }} />
                                        </div>
                                        <div class="mob-sub-input-wrap">
                                            <label style="color:#002C54; font-weight:800;">Marks</label>
                                            <input type="text" name="student_marks[]" 
                                                   class="mob-sub-input mob_student_marks {{ $isFailClass }} {{ $notAssignedClass }}"
                                                   data-subject_id="{{ $item1->id }}" 
                                                   data-old_marks="{{ $oldMarks }}" 
                                                   placeholder="Score" 
                                                   value="{{ $displayMarks }}" 
                                                   oninput="this.value = this.value.toUpperCase()" 
                                                   {{ $isEditable ? '' : 'readonly' }} />
                                        </div>
                                    </div>
                                    <input type="hidden" name="check_null[]" value="{{ $oldMarks }}"/>
                                    <input type="hidden" name="subject_id_fill[]" value="{{ $item1->id }}"/>
                                    <input type="hidden" name="other_subject[]" value="{{ $item1->other_subject ?? '' }}"/>
                                </div>
                            @endforeach
                        </div>
                    @empty
                        <div class="text-center py-3 text-muted" style="font-size: 11px;">
                            No students found for this class.
                        </div>
                    @endforelse
                </div>

                <!-- Bottom Save Actions -->
                @if($data2->isNotEmpty())
                    <div style="display: flex; gap: 6px; margin-bottom: 16px;">
                        <a href="{{ url('fill_marks') }}" class="mob-btn mob-btn-secondary" style="flex: 1;">
                            <i class="fa fa-refresh"></i> Reset
                        </a>
                        @if(!$isPublished || (int)$roleId === 1)
                            <button type="submit" class="mob-btn mob-btn-success" style="flex: 1.5;">
                                <i class="fa fa-save"></i> Save Marks
                            </button>
                        @endif
                    </div>
                @endif
            </form>
        @endif
    @endif

</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Dynamic Exam Loader
    $('#mob_class_type_id').on('change', function() {
        var classId = $(this).val();
        var $examSelect = $('#mob_exam_id');
        $examSelect.html('<option value="">Loading exams...</option>');

        if (!classId) {
            $examSelect.html('<option value="">-- Select Exam --</option>');
            return;
        }

        $.ajax({
            url: "{{ url('examData') }}/" + classId,
            type: "GET",
            success: function(res) {
                $examSelect.html(res);
            },
            error: function() {
                $examSelect.html('<option value="">-- Select Exam --</option>');
            }
        });

        $.ajax({
            url: "{{ url('subjectGetData') }}/" + classId,
            type: "GET",
            success: function(res) {
                $('#mob_subject_id').html(res);
            }
        });
    });

    // Select all subjects toggle
    $('#mobBtnAllSubjects').on('click', function() {
        $('#mob_show_all_subjects').val('1');
        $('#mob_subject_id option').prop('selected', true);
        $('#mobSelectionForm').submit();
    });

    // Real-time marks validation for mobile
    $('.mob_student_marks').on('input', function() {
        $(this).removeClass('bg-danger');
        var subject_id = $(this).data('subject_id');
        var maximum_marks = parseFloat($('#mob_subject_' + subject_id).val()) || 100;
        var minimum_marks = parseFloat($('#mob_minimum_marks_' + subject_id).val()) || 0;
        var val = $(this).val().trim().toUpperCase();

        if (val === '') return;

        var allowedCodes = ['AB', 'M', 'JL', 'T', 'F', 'A', 'B', 'C', 'D'];
        if (allowedCodes.indexOf(val) !== -1) {
            return;
        }

        var num = parseFloat(val);
        if (isNaN(num)) {
            $(this).val('');
            return;
        }

        if (num > maximum_marks) {
            $(this).val('');
            return;
        }

        if (num < minimum_marks) {
            $(this).addClass('bg-danger');
        }
    });
});
</script>
@endsection