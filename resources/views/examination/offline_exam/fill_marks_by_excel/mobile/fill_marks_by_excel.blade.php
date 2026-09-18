@php
    $getSetting = $getSetting ?? Helper::getSetting();
    $classType = $classType ?? Helper::classType();
    $search = $search ?? [];
    $examlist = $examlist ?? collect();
    $subjects = $subjects ?? collect();
    $classTypeId = (int) ($search['class_type_id'] ?? 0);
    $examId = (int) ($search['exam_id'] ?? 0);
    $selectedClassName = $classType->firstWhere('id', $classTypeId)->name ?? 'Class Not Selected';
    $selectedExamName = $examlist->firstWhere('exam_id', $examId)->exam_name ?? 'Exam Not Selected';
    $mappingMode = !empty($importToken) && !empty($mappingHeaders);
@endphp

@extends('layout.mobile_app')

@section('styles')
<style>
/* Mobile Fill Marks By Excel - Native App Style */
.mob-import-hero {
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    color: #ffffff;
    border-radius: 4px;
    padding: 10px 12px;
    margin-bottom: 8px;
    box-shadow: 0 4px 14px rgba(0, 44, 84, 0.25);
    border: 1px solid rgba(255, 255, 255, 0.12);
}
.mob-import-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 4px;
}
.mob-import-title {
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

/* Workflow Step Tabs */
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

/* Cards & Controls */
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

/* Upload Dropzone */
.mob-upload-box {
    border: 2px dashed #94a3b8;
    border-radius: 4px;
    padding: 16px 10px;
    text-align: center;
    background: #f8fafc;
    cursor: pointer;
    transition: all 0.2s;
    margin-bottom: 8px;
}
.mob-upload-box:hover {
    border-color: #002C54;
    background: #e6f0fa;
}
.mob-upload-icon {
    font-size: 28px;
    color: #002C54;
    margin-bottom: 4px;
}
.mob-upload-text {
    font-size: 11.5px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 2px;
}
.mob-upload-sub {
    font-size: 9.5px;
    color: #64748b;
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

/* Mapping Subject Card */
.mob-subject-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 3px;
    padding: 8px 10px;
    margin-bottom: 6px;
}
.mob-subject-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 11px;
    font-weight: 800;
    color: #002C54;
    margin-bottom: 6px;
    border-bottom: 1px solid #e2e8f0;
    padding-bottom: 3px;
}
.mob-subject-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 6px;
}
.mob-subject-grid .mob-form-group {
    margin-bottom: 4px;
}

/* Guidelines */
.mob-tips-item {
    display: flex;
    align-items: flex-start;
    gap: 6px;
    font-size: 10px;
    color: #475569;
    margin-bottom: 5px;
}
.mob-tips-item i {
    color: #002C54;
    margin-top: 2px;
    font-size: 10px;
}
</style>
@endsection

@section('content')
<div class="content-wrapper" style="background: #f4f6f9; padding: 8px;">

    <!-- 1. Hero Header -->
    <div class="mob-import-hero">
        <div class="mob-import-top">
            <div class="mob-import-title">
                <i class="fa fa-file-excel-o text-success"></i>
                <span>Fill Marks by Excel</span>
            </div>
            <span class="mob-badge-step">
                @if(!$mappingMode) Step 1: Select &amp; Upload @else Step 2: Mapping @endif
            </span>
        </div>
        <div style="font-size: 10px; color: #cbd5e1; line-height: 1.2;">
            @if(!$mappingMode)
                Select class and exam, then upload spreadsheet to map marks.
            @else
                Map Excel columns for {{ $selectedClassName }} &bull; {{ $selectedExamName }}.
            @endif
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('error'))
        <div class="alert alert-danger py-1 px-2 mb-2" style="font-size: 11px; border-radius: 3px;">
            <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif
    @if(session('success'))
        <div class="alert alert-success py-1 px-2 mb-2" style="font-size: 11px; border-radius: 3px;">
            <i class="fa fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <!-- Steps Indicator -->
    <div class="mob-steps-nav">
        <div class="mob-step-item {{ !$mappingMode ? 'active' : '' }}">
            <div class="mob-step-num">1</div>
            <span>Select &amp; Upload</span>
        </div>
        <div class="mob-step-item {{ $mappingMode ? 'active' : '' }}">
            <div class="mob-step-num">2</div>
            <span>Map &amp; Import</span>
        </div>
    </div>

    @if(!$mappingMode)
        <!-- STEP 1: CLASS SELECTION & UPLOAD -->
        <form method="get" action="{{ url('fill-marks-by-excel') }}" id="mobSelectionForm">
            <div class="mob-card">
                <div class="mob-card-head">
                    <span><i class="fa fa-filter text-primary"></i> 1. Select Target Exam</span>
                </div>
                
                <div class="mob-form-group">
                    <label class="mob-label"><i class="fa fa-graduation-cap text-primary"></i> Class <span class="text-danger">*</span></label>
                    <select name="class_type_id" id="mob_class_type_id" class="mob-select" required>
                        <option value="">-- Select Class --</option>
                        @foreach($classType as $class)
                            <option value="{{ $class->id }}" {{ $classTypeId === (int)$class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mob-form-group">
                    <label class="mob-label"><i class="fa fa-file-text-o text-primary"></i> Examination <span class="text-danger">*</span></label>
                    <select name="exam_id" id="mob_exam_id" class="mob-select" required>
                        <option value="">-- Select Exam --</option>
                        @foreach($examlist as $exam)
                            <option value="{{ $exam->exam_id }}" {{ $examId === (int)$exam->exam_id ? 'selected' : '' }}>
                                {{ $exam->exam_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="mob-btn mob-btn-primary">
                    <i class="fa fa-arrow-down"></i> Load Exam &amp; Subjects
                </button>
            </div>
        </form>

        @if($classTypeId && $examId)
            @if($subjects->isEmpty())
                <div class="alert alert-warning py-2 px-2 mb-2" style="font-size: 11px; border-radius: 3px;">
                    <i class="fa fa-exclamation-triangle"></i> No subjects configured for this class/exam.
                </div>
            @else
                <!-- File Upload Card -->
                <form method="post" action="{{ route('marks.mapping.prepare') }}" enctype="multipart/form-data" id="mobUploadForm">
                    @csrf
                    <input type="hidden" name="class_type_id" value="{{ $classTypeId }}">
                    <input type="hidden" name="exam_id" value="{{ $examId }}">

                    <div class="mob-card">
                        <div class="mob-card-head">
                            <span><i class="fa fa-cloud-upload text-success"></i> 2. Upload Spreadsheet</span>
                            <span class="badge badge-primary" style="font-size: 9px;">{{ $subjects->count() }} Subjects</span>
                        </div>

                        <div class="mob-upload-box" id="mobUploadBox" onclick="document.getElementById('mob_excel_file').click()">
                            <input type="file" name="excel_file" id="mob_excel_file" accept=".xlsx,.xls,.csv" style="display: none;" required>
                            <div class="mob-upload-icon">
                                <i class="fa fa-file-excel-o"></i>
                            </div>
                            <div class="mob-upload-text" id="mobUploadText">Tap to select Excel / CSV file</div>
                            <div class="mob-upload-sub">Supports .xlsx, .xls, .csv (Max 10MB)</div>
                        </div>

                        <button type="submit" class="mob-btn mob-btn-primary" id="mobSubmitUpload">
                            <i class="fa fa-cogs"></i> Upload &amp; Prepare Column Mapping
                        </button>
                    </div>
                </form>

                <!-- Guidelines Card -->
                <div class="mob-card">
                    <div class="mob-card-head">
                        <span><i class="fa fa-info-circle text-info"></i> Guidelines</span>
                    </div>
                    <div class="mob-tips-item">
                        <i class="fa fa-check-circle"></i>
                        <span>First row of Excel must contain header column names.</span>
                    </div>
                    <div class="mob-tips-item">
                        <i class="fa fa-check-circle"></i>
                        <span>Ensure Admission Number or Student ID column exists.</span>
                    </div>
                    <div class="mob-tips-item">
                        <i class="fa fa-check-circle"></i>
                        <span>You can map Max, Min, Right, Wrong, Left or Obtained Marks.</span>
                    </div>
                </div>
            @endif
        @endif

    @else
        <!-- STEP 2: COLUMN MAPPING FORM -->
        <form action="{{ route('marks.mapping.save') }}" method="POST" id="mobSaveMappingForm">
            @csrf
            <input type="hidden" name="import_token" value="{{ $importToken }}">

            <!-- Summary Card -->
            <div class="mob-card">
                <div class="mob-card-head">
                    <span><i class="fa fa-info-circle"></i> Mapping Configuration</span>
                    <span class="badge badge-success" style="font-size: 9px;">{{ $uploadedRowCount ?? 0 }} Data Rows</span>
                </div>
                <div style="font-size: 10.5px; color: #334155; display: grid; grid-template-columns: 1fr 1fr; gap: 4px;">
                    <div><strong>Class:</strong> {{ $selectedClassName }}</div>
                    <div><strong>Exam:</strong> {{ $selectedExamName }}</div>
                    <div><strong>Excel Cols:</strong> {{ count($mappingHeaders ?? []) }}</div>
                    <div><strong>Subjects:</strong> {{ $subjects->count() }}</div>
                </div>
            </div>

            <!-- Candidate Identifier Mapping -->
            <div class="mob-card">
                <div class="mob-card-head">
                    <span><i class="fa fa-id-badge text-primary"></i> Candidate ID Column</span>
                    <span class="text-danger" style="font-size: 10px;">* Required</span>
                </div>
                <div class="mob-form-group">
                    <label class="mob-label">Select Student ID / Admission No Column</label>
                    <select name="candidate_column" class="mob-select" required>
                        <option value="">-- Select Column --</option>
                        @foreach($mappingHeaders as $columnIndex => $header)
                            <option value="{{ $columnIndex }}"
                                {{ (string)($candidateColumn ?? '') === (string)$columnIndex ? 'selected' : '' }}>
                                {{ $header }} (Col {{ $columnIndex + 1 }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Subject Marks Mapping -->
            <div class="mob-card">
                <div class="mob-card-head">
                    <span><i class="fa fa-book text-primary"></i> Subject Column Mappings</span>
                </div>

                @forelse($subjects as $subject)
                    <div class="mob-subject-card">
                        <div class="mob-subject-head">
                            <span><i class="fa fa-book text-primary"></i> {{ $subject->sub_name ?: $subject->name }}</span>
                            <span class="badge badge-secondary" style="font-size: 8.5px;">ID: {{ $subject->id }}</span>
                        </div>
                        <div class="mob-subject-grid">
                            @foreach(['marks' => 'Marks Scored', 'maximum' => 'Max Marks', 'minimum' => 'Min Marks', 'r' => 'Right (R)', 'w' => 'Wrong (W)', 'l' => 'Left (L)'] as $type => $label)
                                <div class="mob-form-group">
                                    <label class="mob-label" style="font-size: 9.5px;">{{ $label }}</label>
                                    <select name="mapping[{{ $subject->id }}][{{ $type }}]" class="mob-select" style="font-size: 10.5px; height: 28px;">
                                        <option value="">-- None --</option>
                                        @foreach($mappingHeaders as $columnIndex => $header)
                                            <option value="{{ $columnIndex }}"
                                                {{ (string)($mappingDefaults[$subject->id][$type] ?? '') === (string)$columnIndex ? 'selected' : '' }}>
                                                {{ $header }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-2" style="font-size: 11px;">
                        No subjects found for this class.
                    </div>
                @endforelse
            </div>

            <!-- Action buttons -->
            <div style="display: flex; gap: 6px; margin-bottom: 14px;">
                <a href="{{ url('fill-marks-by-excel?class_type_id='.$classTypeId.'&exam_id='.$examId) }}" class="mob-btn mob-btn-secondary" style="flex: 1;">
                    <i class="fa fa-arrow-left"></i> Re-upload
                </a>
                <button type="submit" class="mob-btn mob-btn-success" style="flex: 1.5;">
                    <i class="fa fa-save"></i> Save &amp; Import Marks
                </button>
            </div>
        </form>
    @endif

</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Dynamic Exam Loader on Class Change (Step 1)
    $('#mob_class_type_id').on('change', function() {
        var classId = $(this).val();
        var $examSelect = $('#mob_exam_id');
        $examSelect.html('<option value="">Loading examinations...</option>');

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
    });

    // File Input Name update
    $('#mob_excel_file').on('change', function() {
        if (this.files && this.files.length > 0) {
            var fileName = this.files[0].name;
            $('#mobUploadText').html('<i class="fa fa-check text-success"></i> ' + fileName);
        }
    });
});
</script>
@endsection