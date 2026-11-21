@extends('layout.app')

@section('title', 'Green Sheet Report - Examination Management')

@php
    $classType = Helper::ClassType();
    $setting = Helper::getSetting();
    $search = $search ?? [];
    $selectedClassId = $search['class_type_id'] ?? request('class_type_id', '');
    $selectedExamId = $search['exam_id'] ?? request('exam_id', '');
@endphp

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - GREEN SHEET REPORT (ADMIN SIGNATURE THEME)
   ========================================================================== */
.report-page-wrapper {
    background-color: #f4f6f9;
    padding: 10px 14px 24px 14px;
    min-height: calc(100vh - var(--header-height, 56px) - 16px);
}

.report-hero {
    background: linear-gradient(135deg, #001833 0%, #002C54 60%, #0f3460 100%);
    border-radius: 4px;
    padding: 12px 18px;
    color: #ffffff;
    margin-bottom: 12px;
    box-shadow: 0 2px 8px rgba(0, 44, 84, 0.18);
    display: flex;
    justify-content: space-between;
    align-items: center;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.report-kicker {
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    font-weight: 700;
    color: #93c5fd;
    display: block;
    margin-bottom: 2px;
}

.report-title {
    font-size: 17px;
    font-weight: 800;
    margin: 0;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 8px;
}

.report-subtitle {
    font-size: 11.5px;
    margin: 3px 0 0 0;
    color: #e2e8f0;
}

.filter-card {
    background: #ffffff;
    border-radius: 4px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
    margin-bottom: 16px;
}

.filter-card-header {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    padding: 10px 16px;
    font-weight: 700;
    font-size: 12px;
    color: #002C54;
    display: flex;
    align-items: center;
    gap: 6px;
}

.filter-card-body {
    padding: 16px;
}

.filter-label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    color: #475569;
    margin-bottom: 4px;
    display: block;
}

.filter-label .req {
    color: #dc2626;
}

.btn-generate {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #ffffff;
    border: none;
    font-weight: 600;
    font-size: 12px;
    padding: 7px 18px;
    border-radius: 4px;
    transition: all 0.2s ease;
    height: 38px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

.btn-generate:hover {
    background: linear-gradient(135deg, #001f3d 0%, #0c2b50 100%);
    color: #ffffff;
    box-shadow: 0 2px 6px rgba(0, 44, 84, 0.25);
}

.info-note {
    background: #f0f9ff;
    border-left: 3px solid #0284c7;
    padding: 10px 14px;
    border-radius: 0 4px 4px 0;
    font-size: 11.5px;
    color: #0369a1;
    margin-top: 12px;
}
</style>
@endsection

@section('content')
<div class="content-wrapper report-page-wrapper">
    {{-- Hero Banner --}}
    <div class="report-hero">
        <div>
            <span class="report-kicker">Examination Analytics & Results</span>
            <h1 class="report-title">
                <i class="fa fa-table text-primary"></i> Green Sheet Report
            </h1>
            <p class="report-subtitle">Comprehensive master grade sheet with student-wise and subject-wise score evaluation.</p>
        </div>
        <div>
            <a href="{{ url('examination_dashboard') }}" class="btn btn-sm btn-outline-light" style="font-size: 11.5px; font-weight: 600;">
                <i class="fa fa-arrow-left mr-1"></i> Exam Dashboard
            </a>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="filter-card">
        <div class="filter-card-header">
            <i class="fa fa-filter text-primary"></i> Select Criteria to Generate Green Sheet
        </div>
        <div class="filter-card-body">
            <form id="greenSheetForm" action="{{ url('green_sheet_report') }}" method="post" target="_blank">
                @csrf
                <div class="row align-items-end">
                    {{-- Course / Class --}}
                    <div class="col-md-4 col-sm-6 mb-3">
                        <label class="filter-label">Course / Class <span class="req">*</span></label>
                        <select class="form-control select2 @error('class_type_id') is-invalid @enderror" 
                                id="class_type_id" 
                                name="class_type_id" 
                                required>
                            <option value="">-- Select Course / Class --</option>
                            @if(!empty($classType))
                                @foreach($classType as $type)
                                    <option value="{{ $type->id ?? '' }}" {{ ($type->id == $selectedClassId) ? 'selected' : '' }}>
                                        {{ $type->name ?? '' }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('class_type_id')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    {{-- Exam Selection --}}
                    <div class="col-md-4 col-sm-6 mb-3">
                        <label class="filter-label">Examination <span class="req">*</span></label>
                        <select class="form-control select2 @error('exam_id') is-invalid @enderror" 
                                id="exam_id" 
                                name="exam_id" 
                                required>
                            <option value="">-- Select Course First --</option>
                        </select>
                        @error('exam_id')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    {{-- Submit Button --}}
                    <div class="col-md-4 col-sm-12 mb-3">
                        <button type="submit" id="btnSubmit" class="btn btn-generate w-100">
                            <i class="fa fa-print"></i> Generate & View Green Sheet
                        </button>
                    </div>
                </div>
            </form>

            <div class="info-note">
                <i class="fa fa-info-circle mr-1"></i>
                <strong>Notice:</strong> The report opens in a printable, formatted master ledger sheet displaying student names, roll numbers, subject marks breakdown, totals, percentages, and performance remarks.
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    if ($.fn.select2) {
        $('.select2').select2({ width: '100%' });
    }

    const baseurl = "{{ url('/') }}";
    const initialClassId = $('#class_type_id').val();
    const preselectedExamId = "{{ $selectedExamId }}";

    function loadExamsForClass(classTypeId, selectedExam) {
        if (!classTypeId) {
            $('#exam_id').html('<option value="">-- Select Course First --</option>').trigger('change');
            return;
        }

        $('#exam_id').html('<option value="">Loading examinations...</option>').prop('disabled', true);

        $.ajax({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            url: baseurl + '/examData/' + classTypeId,
            type: 'GET',
            success: function(data) {
                $('#exam_id').prop('disabled', false).html(data);
                if (selectedExam) {
                    $('#exam_id').val(selectedExam).trigger('change');
                } else {
                    $('#exam_id').trigger('change');
                }
            },
            error: function() {
                $('#exam_id').prop('disabled', false).html('<option value="">Error loading examinations</option>');
            }
        });
    }

    $('#class_type_id').on('change', function() {
        const classTypeId = $(this).val();
        loadExamsForClass(classTypeId, null);
    });

    if (initialClassId) {
        loadExamsForClass(initialClassId, preselectedExamId);
    }

    $('#greenSheetForm').on('submit', function(e) {
        const classId = $('#class_type_id').val();
        const examId = $('#exam_id').val();
        if (!classId) {
            e.preventDefault();
            alert('Please select a Course / Class first.');
            $('#class_type_id').focus();
            return false;
        }
        if (!examId) {
            e.preventDefault();
            alert('Please select an Examination.');
            $('#exam_id').focus();
            return false;
        }
    });
});
</script>
@endsection
