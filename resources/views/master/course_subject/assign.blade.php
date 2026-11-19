@extends('layout.app')

@section('styles')
<style>
/* Page Layout & Viewport Fitting - Exactly Matching admissionView & Dashboard */
.admission-page {
    background: #eef2f6;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
}
.admission-page * {
    box-sizing: border-box;
}

/* Top Hero Banner - Exact Arise ERP Style from admissionView / dash-hero */
.admission-hero {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #ffffff;
    border-radius: 2px;
    padding: 6px 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 6px;
    box-shadow: 0 1px 3px rgba(0,44,84,.12);
    margin-bottom: 6px;
}
.admission-kicker {
    font-size: 9.5px;
    text-transform: uppercase;
    letter-spacing: .05em;
    opacity: .85;
    display: block;
    margin-bottom: 1px;
    color: #ffffff;
}
.admission-title {
    font-size: 14px;
    font-weight: 700;
    margin: 0 0 1px 0;
    line-height: 1.2;
    color: #ffffff;
}
.admission-subtitle {
    font-size: 10.5px;
    opacity: .85;
    margin: 0;
    color: #ffffff;
}
.admission-hero-actions {
    display: flex;
    gap: 4px;
    align-items: center;
    flex-wrap: wrap;
}

/* Arise Unified Button System */
.dash-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 26px;
    padding: 0 10px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 2px;
    text-decoration: none !important;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all .15s ease;
    line-height: 1;
    gap: 5px;
    white-space: nowrap;
}
.dash-btn-light {
    background: #ffffff;
    color: #002C54;
    border-color: #ffffff;
    box-shadow: 0 1px 2px rgba(0,0,0,0.08);
}
.dash-btn-light:hover {
    background: #f1f5f9;
    color: #001f3d;
    border-color: #f1f5f9;
}
.dash-btn-outline {
    background: transparent;
    color: #ffffff;
    border-color: rgba(255,255,255,0.4);
}
.dash-btn-outline:hover {
    background: rgba(255,255,255,0.15);
    color: #ffffff;
    border-color: #ffffff;
}
.dash-btn-primary {
    background: #002C54;
    color: #ffffff;
    border-color: #002C54;
}
.dash-btn-primary:hover {
    background: #001f3d;
    color: #ffffff;
    border-color: #001f3d;
}

/* Sharp Compact Cards - Arise ERP Style */
.dash-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 2px;
    box-shadow: 0 1px 2px rgba(0,0,0,.02);
    display: flex;
    flex-direction: column;
    margin-bottom: 6px;
}
.dash-card-header {
    padding: 6px 10px;
    border-bottom: 1px solid rgba(255,255,255,.12);
    background: #002342;
    color: #ffffff;
    flex-shrink: 0;
    border-radius: 2px 2px 0 0;
}
.dash-card-title {
    font-size: 12px;
    font-weight: 700;
    margin: 0;
    color: #ffffff;
    line-height: 1.2;
}
.badge-total-records {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 22px;
    font-size: 10.5px;
    font-weight: 600;
    background: rgba(255,255,255,.14);
    color: #f1f5f9;
    padding: 0 8px;
    border-radius: 2px;
    border: 1px solid rgba(255,255,255,.18);
    white-space: nowrap;
    line-height: 1;
}

/* Course Switcher Bar */
.course-switcher-bar {
    background: #002342;
    color: #ffffff;
    padding: 6px 10px;
    border-radius: 2px;
    border: 1px solid #001f3d;
    margin-bottom: 6px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.08);
}

/* Assigned Subject Chips - Sharp Crisp ERP */
.assigned-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: #eff6ff;
    color: #1e40af;
    border: 1px solid #bfdbfe;
    padding: 3px 8px;
    border-radius: 2px;
    font-size: 11px;
    font-weight: 600;
    margin: 2px;
    line-height: 1.3;
    transition: all 0.15s ease;
}
.assigned-chip:hover {
    background: #dbeafe;
    border-color: #93c5fd;
}
.assigned-chip .remove-chip-btn {
    cursor: pointer;
    color: #2563eb;
    font-size: 14px;
    font-weight: bold;
    line-height: 1;
    margin-left: 2px;
}
.assigned-chip .remove-chip-btn:hover {
    color: #dc2626;
}

/* Subject Checkbox Items */
.subject-item-box {
    display: flex;
    align-items: center;
    padding: 6px 9px;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    margin-bottom: 4px;
    background: #ffffff;
    cursor: pointer;
    transition: all 0.15s ease;
    font-size: 11.5px;
    user-select: none;
}
.subject-item-box:hover {
    background: #f8fafc;
    border-color: #94a3b8;
}
.subject-item-box.selected {
    background: #f0fdf4;
    border-color: #86efac;
    color: #166534;
    font-weight: 600;
}
.subject-item-box input[type="checkbox"] {
    margin-right: 7px;
    cursor: pointer;
    accent-color: #002C54;
}

/* Form Styles */
.form-label-compact {
    font-size: 11px;
    font-weight: 600;
    color: #334155;
    margin-bottom: 2px;
}
.form-control-compact {
    height: 28px;
    font-size: 11.5px;
    border-radius: 2px;
    border: 1px solid #cbd5e1;
    padding: 3px 8px;
    color: #0f172a;
}
.form-control-compact:focus {
    border-color: #0284c7;
    box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.15);
}
</style>
@endsection

@section('content')
<div class="content-wrapper admission-page">
    <section class="content p-0">
        <div class="container-fluid p-0">
            <div class="px-2 pt-1 pb-2">

                {{-- Alert Messages --}}
                @if(session('message'))
                    <div class="alert alert-success alert-dismissible fade show m-0 mb-2 py-1 px-3" role="alert" style="font-size: 11.5px; border-radius: 2px;">
                        <i class="fa fa-check-circle mr-1"></i> {{ session('message') }}
                        <button type="button" class="close py-1" data-dismiss="alert">&times;</button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show m-0 mb-2 py-1 px-3" role="alert" style="font-size: 11.5px; border-radius: 2px;">
                        <i class="fa fa-exclamation-triangle mr-1"></i> {{ session('error') }}
                        <button type="button" class="close py-1" data-dismiss="alert">&times;</button>
                    </div>
                @endif

                {{-- Top Hero Welcome Banner --}}
                <div class="admission-hero">
                    <div class="admission-hero-text">
                        <span class="admission-kicker"><i class="fa fa-book mr-1"></i> MASTER &middot; CURRICULUM &amp; SYLLABUS MAPPING</span>
                        <h1 class="admission-title">Course Subjects Assignment Hub</h1>
                        <p class="admission-subtitle">Map and link examination &amp; syllabus subjects to coaching courses for student admissions &amp; exams</p>
                    </div>
                    <div class="admission-hero-actions">
                        <a href="{{ url('courses') }}" class="dash-btn dash-btn-outline">
                            <i class="fa fa-graduation-cap mr-1"></i> Courses Master
                        </a>
                        <a href="{{ url('batches') }}" class="dash-btn dash-btn-outline">
                            <i class="fa fa-users mr-1"></i> Batches Setup
                        </a>
                        <a href="{{ url('create_subject') }}" class="dash-btn dash-btn-light">
                            <i class="fa fa-plus mr-1"></i> Master Subjects
                        </a>
                    </div>
                </div>

                {{-- Top Course Switcher Bar --}}
                <div class="course-switcher-bar d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div class="d-flex align-items-center flex-grow-1" style="max-width: 520px;">
                        <label class="text-white font-weight-bold mr-2 mb-0 text-nowrap" style="font-size: 11.5px;">
                            <i class="fa fa-graduation-cap text-info mr-1"></i> Select Coaching Course:
                        </label>
                        <select id="course_switcher" class="form-control form-control-compact text-dark" style="background: #ffffff; font-weight: 600;" onchange="window.location.href = '{{ url('course_subjects') }}?course_id=' + this.value">
                            @foreach($courses as $c)
                                <option value="{{ $c->id }}" {{ ($selectedCourseId == $c->id) ? 'selected' : '' }}>
                                    {{ $c->name }} ({{ $c->code ?? 'N/A' }}) &mdash; {{ $c->courseSubjects->count() }} subjects
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @if($selectedCourse)
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge-total-records">
                                <i class="fa fa-book mr-1"></i> <span id="header_subject_count">{{ count($assignedSubjects) }}</span> Assigned Subjects
                            </span>
                        </div>
                    @endif
                </div>

                @if(!$selectedCourse)
                    <div class="dash-card p-4 text-center">
                        <div style="font-size: 32px; color: #cbd5e1; margin-bottom: 6px;"><i class="fa fa-folder-open-o"></i></div>
                        <div style="font-size: 13px; font-weight: 700; color: #334155;">No Coaching Course Selected</div>
                        <div style="font-size: 11px; color: #94a3b8;">Please select a course from the dropdown above or create one in Courses Master.</div>
                    </div>
                @else
                    <form action="{{ url('course_subjects') }}" method="POST" id="courseSubjectForm">
                        @csrf
                        <input type="hidden" name="course_id" value="{{ $selectedCourse->id }}">

                        <div class="row">
                            {{-- Left Column: Currently Assigned Subjects --}}
                            <div class="col-12 col-lg-5 mb-2">
                                <div class="dash-card h-100">
                                    <div class="dash-card-header d-flex align-items-center justify-content-between">
                                        <h3 class="dash-card-title">
                                            <i class="fa fa-check-circle text-success mr-1"></i> Assigned Subjects (<span id="assignedCount">{{ count($assignedSubjects) }}</span>)
                                        </h3>
                                        <span class="badge badge-info" style="font-size: 10px; border-radius: 2px;">{{ $selectedCourse->name }}</span>
                                    </div>
                                    <div class="p-2 d-flex flex-column justify-content-between" style="min-height: 480px;">
                                        <div>
                                            <p class="text-muted mb-2" style="font-size: 11px; line-height: 1.35;">
                                                Students admitted to <strong>{{ $selectedCourse->name }}</strong> will automatically study and take exams for these assigned subjects.
                                            </p>

                                            {{-- Chips Container --}}
                                            <div class="mb-2">
                                                <label class="form-label-compact d-flex justify-content-between">
                                                    <span>Active Course Syllabus</span>
                                                    <span class="text-muted" style="font-size: 10px;">Click &times; to remove</span>
                                                </label>
                                                <div id="assigned_chips_container" class="p-2 border bg-light" style="min-height: 140px; max-height: 240px; overflow-y: auto; border-radius: 2px; border-color: #cbd5e1 !important;">
                                                    @forelse($assignedSubjects as $subName)
                                                        <span class="assigned-chip" data-name="{{ $subName }}">
                                                            <i class="fa fa-book text-muted mr-1" style="font-size: 10px;"></i> {{ $subName }}
                                                            <span class="remove-chip-btn" onclick="removeSubjectChip('{{ $subName }}')">&times;</span>
                                                        </span>
                                                    @empty
                                                        <div class="text-center text-muted py-4" id="no_subjects_msg">
                                                            <div style="font-size: 24px; color: #cbd5e1; margin-bottom: 2px;"><i class="fa fa-book"></i></div>
                                                            <div style="font-size: 11.5px; font-weight: 600; color: #64748b;">No subjects assigned yet</div>
                                                            <div style="font-size: 10.5px; color: #94a3b8;">Select subjects from the right panel to assign them.</div>
                                                        </div>
                                                    @endforelse
                                                </div>
                                            </div>

                                            {{-- Quick Add Custom Subject --}}
                                            <div class="mt-3 p-2 border" style="background: #fafbfc; border-radius: 2px; border-color: #e2e8f0 !important;">
                                                <label class="form-label-compact">
                                                    <i class="fa fa-plus-circle text-info mr-1"></i> Add Custom Subject to this Course
                                                </label>
                                                <div class="input-group input-group-sm">
                                                    <input type="text" id="new_subject_input" name="new_subject_name" class="form-control form-control-compact" placeholder="e.g. Reasoning, Current Affairs, Technical...">
                                                    <div class="input-group-append">
                                                        <button type="button" class="dash-btn dash-btn-primary" id="btn_add_custom_subject" style="border-radius: 0 2px 2px 0;">
                                                            <i class="fa fa-plus mr-1"></i> Add
                                                        </button>
                                                    </div>
                                                </div>
                                                <small class="text-muted d-block mt-1" style="font-size: 10px;">Creates subject in master library and links it to this course automatically.</small>
                                            </div>
                                        </div>

                                        <button type="submit" class="dash-btn dash-btn-primary w-100 mt-3" style="height: 32px; font-size: 12px;">
                                            <i class="fa fa-save mr-1"></i> Save Subject Assignment
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Right Column: Master Subjects Library --}}
                            <div class="col-12 col-lg-7 mb-2">
                                <div class="dash-card h-100">
                                    <div class="dash-card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <h3 class="dash-card-title">
                                                <i class="fa fa-th-list text-info mr-1"></i> Master Subjects Library
                                            </h3>
                                            <span class="badge-total-records">{{ $masterSubjects->count() }} Available</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-1">
                                            <input type="text" id="search_master_sub" class="form-control" placeholder="Search subjects..." style="height: 24px; font-size: 11px; width: 140px; border-radius: 2px;">
                                            <button type="button" class="dash-btn dash-btn-outline" id="btn_select_all" style="height: 24px; font-size: 10px; padding: 0 6px;">Select All</button>
                                            <button type="button" class="dash-btn dash-btn-outline" id="btn_clear_all" style="height: 24px; font-size: 10px; padding: 0 6px;">Clear</button>
                                        </div>
                                    </div>
                                    <div class="p-2" style="max-height: 480px; overflow-y: auto;">
                                        <div class="row m-0" id="subjects_checkbox_grid">
                                            @foreach($masterSubjects as $mSub)
                                                @php
                                                    $isChecked = in_array($mSub->name, $assignedSubjects, true);
                                                @endphp
                                                <div class="col-6 col-md-4 p-1 subject-grid-item" data-name="{{ strtolower($mSub->name) }}">
                                                    <label class="subject-item-box {{ $isChecked ? 'selected' : '' }} m-0">
                                                        <input type="checkbox" name="subjects[]" value="{{ $mSub->name }}" class="subject-checkbox" {{ $isChecked ? 'checked' : '' }}>
                                                        <span class="subject-title text-truncate">{{ $mSub->name }}</span>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="p-2 d-flex align-items-center justify-content-between" style="background: #f8fafc; border-top: 1px solid #e2e8f0;">
                                        <span class="text-muted" style="font-size: 11px;">
                                            Checked subjects will be assigned on save.
                                        </span>
                                        <button type="submit" class="dash-btn dash-btn-primary" style="height: 28px; padding: 0 16px;">
                                            <i class="fa fa-save mr-1"></i> Save Changes
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                @endif

            </div>
        </div>
    </section>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Filter master subjects
    $('#search_master_sub').on('input', function() {
        var query = $(this).val().toLowerCase().trim();
        $('.subject-grid-item').each(function() {
            var name = $(this).data('name');
            if (name.indexOf(query) !== -1 || query === '') {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Checkbox toggle sync with chips
    $(document).on('change', '.subject-checkbox', function() {
        var subName = $(this).val();
        var isChecked = $(this).is(':checked');
        var itemBox = $(this).closest('.subject-item-box');

        if (isChecked) {
            itemBox.addClass('selected');
            addChip(subName);
        } else {
            itemBox.removeClass('selected');
            removeChipFromDom(subName);
        }
        updateCounts();
    });

    // Select All
    $('#btn_select_all').on('click', function() {
        $('.subject-grid-item:visible .subject-checkbox').each(function() {
            if (!$(this).is(':checked')) {
                $(this).prop('checked', true).closest('.subject-item-box').addClass('selected');
                addChip($(this).val());
            }
        });
        updateCounts();
    });

    // Clear All
    $('#btn_clear_all').on('click', function() {
        $('.subject-checkbox').each(function() {
            if ($(this).is(':checked')) {
                $(this).prop('checked', false).closest('.subject-item-box').removeClass('selected');
                removeChipFromDom($(this).val());
            }
        });
        updateCounts();
    });

    // Add Custom Subject Button
    $('#btn_add_custom_subject').on('click', function() {
        var inputVal = $('#new_subject_input').val().trim();
        if (!inputVal) return;

        // Check if exists in checkboxes
        var found = false;
        $('.subject-checkbox').each(function() {
            if ($(this).val().toLowerCase() === inputVal.toLowerCase()) {
                $(this).prop('checked', true).closest('.subject-item-box').addClass('selected');
                addChip($(this).val());
                found = true;
                return false;
            }
        });

        if (!found) {
            // Append new checkbox item to grid
            var newHtml = '<div class="col-6 col-md-4 p-1 subject-grid-item" data-name="' + inputVal.toLowerCase() + '">' +
                '<label class="subject-item-box selected m-0">' +
                '<input type="checkbox" name="subjects[]" value="' + inputVal + '" class="subject-checkbox" checked>' +
                '<span class="subject-title text-truncate">' + inputVal + '</span>' +
                '</label></div>';
            $('#subjects_checkbox_grid').append(newHtml);
            addChip(inputVal);
        }

        $('#new_subject_input').val('');
        updateCounts();
    });

    $('#new_subject_input').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            $('#btn_add_custom_subject').trigger('click');
        }
    });

    function updateCounts() {
        var count = $('#assigned_chips_container .assigned-chip').length;
        $('#assignedCount').text(count);
        $('#header_subject_count').text(count);
    }
});

function addChip(name) {
    $('#no_subjects_msg').hide();
    var existing = $('#assigned_chips_container').find('.assigned-chip[data-name="' + name + '"]');
    if (existing.length === 0) {
        var chip = '<span class="assigned-chip" data-name="' + name + '">' +
            '<i class="fa fa-book text-muted mr-1" style="font-size: 10px;"></i> ' + name +
            '<span class="remove-chip-btn" onclick="removeSubjectChip(\'' + name + '\')">&times;</span>' +
            '</span>';
        $('#assigned_chips_container').append(chip);
    }
}

function removeChipFromDom(name) {
    $('#assigned_chips_container').find('.assigned-chip[data-name="' + name + '"]').remove();
    var remaining = $('#assigned_chips_container').find('.assigned-chip').length;
    if (remaining === 0) {
        $('#no_subjects_msg').show();
    }
    $('#assignedCount').text(remaining);
    $('#header_subject_count').text(remaining);
}

function removeSubjectChip(name) {
    // Uncheck corresponding checkbox
    $('.subject-checkbox').each(function() {
        if ($(this).val().toLowerCase() === name.toLowerCase()) {
            $(this).prop('checked', false).closest('.subject-item-box').removeClass('selected');
        }
    });
    removeChipFromDom(name);
}
</script>
@endsection
