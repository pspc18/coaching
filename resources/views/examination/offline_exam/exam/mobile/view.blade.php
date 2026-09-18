@php
    $getSetting = $getSetting ?? Helper::getSetting();
    $classType = Helper::classType();
    $date = date('Y-m-d');
    $permission = Helper::permissioncheck(8);

    $totalExams = count($data ?? []);
    $totalAssignedClasses = 0;
    $totalPublished = 0;

    foreach ($data ?? [] as $examItem) {
        $classes = $examItem->assigned_classes ?? collect();
        $totalAssignedClasses += count($classes);
        foreach ($classes as $ac) {
            if ($ac->is_published ?? false) {
                $totalPublished++;
            }
        }
    }
@endphp

@extends('layout.mobile_app')

@section('styles')
<style>
/* 1. Mobile Hero Card */
.mob-exam-hero {
    background: linear-gradient(135deg, #001833 0%, #002C54 100%);
    color: #ffffff;
    border-radius: 4px;
    padding: 10px 12px;
    margin-bottom: 8px;
    box-shadow: 0 4px 14px rgba(0, 44, 84, 0.25);
    border: 1px solid rgba(255, 255, 255, 0.12);
}
.mob-exam-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
}
.mob-exam-title {
    font-size: 13px;
    font-weight: 800;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}

/* Quick Metrics in Hero */
.mob-kpi-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 5px;
    margin-bottom: 8px;
}
.mob-kpi-col {
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-radius: 3px;
    padding: 5px 6px;
    text-align: center;
}
.mob-kpi-tag {
    font-size: 8.5px;
    font-weight: 700;
    text-transform: uppercase;
    color: #94a3b8;
    line-height: 1.1;
    margin-bottom: 2px;
}
.mob-kpi-val {
    font-size: 13px;
    font-weight: 900;
    line-height: 1;
}

/* Top Quick Action Buttons */
.mob-action-row {
    display: flex;
    gap: 6px;
}
.mob-hero-btn {
    flex: 1;
    height: 28px;
    border-radius: 3px;
    font-size: 11px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    text-decoration: none !important;
}
.btn-hero-add {
    background: #ffffff;
    color: #002C54;
    border: 1px solid #ffffff;
}
.btn-hero-workflow {
    background: rgba(255, 255, 255, 0.16);
    color: #ffffff;
    border: 1px solid rgba(255, 255, 255, 0.35);
}

/* 2. Search Box */
.mob-search-wrap {
    position: relative;
    margin-bottom: 8px;
}
.mob-search-input {
    width: 100%;
    height: 34px;
    border-radius: 4px;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    padding-left: 32px;
    padding-right: 12px;
    font-size: 12px;
    color: #1e293b;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.mob-search-input:focus {
    outline: none;
    border-color: #002C54;
    box-shadow: 0 0 0 2px rgba(0, 44, 84, 0.12);
}
.mob-search-icon {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 12px;
    color: #64748b;
    pointer-events: none;
}

/* 3. Exam Cards List */
.mob-cards-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 12px;
}
.mob-exam-card {
    background: #ffffff;
    border-radius: 4px;
    border: 1px solid #e2e8f0;
    padding: 10px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.mob-card-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 6px;
}
.mob-exam-name {
    font-size: 13px;
    font-weight: 800;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 6px;
}
.mob-term-pill {
    font-size: 9.5px;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 2px;
    background: #e0f2fe;
    color: #0369a1;
    border: 1px solid #bae6fd;
}

/* Assigned classes inside card */
.mob-assigned-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 3px;
    padding: 6px 8px;
    margin-bottom: 8px;
}
.mob-class-chip {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    font-size: 10.5px;
    font-weight: 700;
    padding: 1px 6px;
    border-radius: 2px;
    margin-right: 3px;
    margin-bottom: 3px;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #334155;
}
.mob-class-chip.published {
    background: #dcfce7;
    border-color: #86efac;
    color: #15803d;
}

/* Card Action Bar */
.mob-card-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    align-items: center;
}
.mob-btn-action {
    height: 26px;
    padding: 0 8px;
    font-size: 10.5px;
    font-weight: 700;
    border-radius: 2px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    text-decoration: none !important;
}

/* Modals */
.mob-sheet-modal {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.6);
    z-index: 1050;
    align-items: flex-end;
    backdrop-filter: blur(2px);
}
.mob-sheet-modal.show {
    display: flex;
}
.mob-sheet-content {
    background: #ffffff;
    width: 100%;
    max-height: 85vh;
    border-radius: 12px 12px 0 0;
    padding: 14px;
    display: flex;
    flex-direction: column;
    overflow-y: auto;
    box-shadow: 0 -4px 20px rgba(0,0,0,0.15);
}
.mob-sheet-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 12px;
    padding-bottom: 8px;
    border-bottom: 1px solid #e2e8f0;
}
.mob-sheet-title {
    font-size: 14px;
    font-weight: 800;
    color: #002C54;
}
.mob-sheet-close {
    background: transparent;
    border: none;
    font-size: 18px;
    color: #64748b;
    padding: 0 4px;
}
</style>
@endsection

@section('content')
<div class="px-2 pt-2 pb-5">

    {{-- 1. Mobile Hero Banner --}}
    <div class="mob-exam-hero">
        <div class="mob-exam-top">
            <div class="mob-exam-title">
                <i class="fa fa-leanpub"></i> Exams Management
            </div>
            <span class="badge badge-light text-dark" style="font-size:10px; border-radius:2px;">Offline Exams</span>
        </div>

        <div class="mob-kpi-grid">
            <div class="mob-kpi-col">
                <div class="mob-kpi-tag">Total Exams</div>
                <div class="mob-kpi-val" style="color:#ffffff;">{{ $totalExams }}</div>
            </div>
            <div class="mob-kpi-col">
                <div class="mob-kpi-tag">Assigned</div>
                <div class="mob-kpi-val" style="color:#38bdf8;">{{ $totalAssignedClasses }}</div>
            </div>
            <div class="mob-kpi-col">
                <div class="mob-kpi-tag">Published</div>
                <div class="mob-kpi-val" style="color:#4ade80;">{{ $totalPublished }}</div>
            </div>
        </div>

        <div class="mob-action-row">
            @if($permission->add ?? true)
                <a href="{{ url('add/exam') }}" class="mob-hero-btn btn-hero-add">
                    <i class="fa fa-plus-circle"></i> Add Exam
                </a>
            @endif
            <a href="{{ url('fill_marks') }}" class="mob-hero-btn btn-hero-workflow">
                <i class="fa fa-pencil"></i> Fill Marks
            </a>
            <a href="{{ url('exam_wise_report') }}" class="mob-hero-btn btn-hero-workflow">
                <i class="fa fa-bar-chart"></i> Reports
            </a>
        </div>
    </div>

    {{-- Session alerts --}}
    @if(session('success') || session('message'))
        <div class="alert alert-success py-2 px-3 mb-2" style="border-radius:3px; font-size:11.5px;">
            <i class="fa fa-check-circle mr-1"></i> {{ session('success') ?? session('message') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger py-2 px-3 mb-2" style="border-radius:3px; font-size:11.5px;">
            <i class="fa fa-exclamation-triangle mr-1"></i> {{ session('error') }}
        </div>
    @endif

    {{-- 2. Real-Time Search Bar --}}
    <div class="mob-search-wrap">
        <i class="fa fa-search mob-search-icon"></i>
        <input type="text" id="mobExamSearch" class="mob-search-input" placeholder="Search exam name, class, term..." autocomplete="off">
    </div>

    {{-- Result Counter --}}
    <div class="d-flex align-items-center justify-content-between mb-2 px-1">
        <span style="font-size:11px; font-weight:700; color:#64748b;">
            Exams (<span id="mobVisibleExamCount" class="text-dark">{{ $totalExams }}</span> / {{ $totalExams }})
        </span>
    </div>

    {{-- 3. Exam Cards List --}}
    <div class="mob-cards-list" id="mobExamsContainer">
        @forelse($data as $item)
            @php
                $classes = $item->assigned_classes ?? collect();
            @endphp
            <div class="mob-exam-card mob-exam-item" 
                 data-name="{{ strtolower($item->name ?? '') }}" 
                 data-term="{{ strtolower($item->exam_term_name ?? '') }}"
                 data-classes="{{ strtolower($classes->pluck('class_name')->implode(' ')) }}">
                
                <div class="mob-card-top">
                    <div class="mob-exam-name">
                        <div style="width:22px; height:22px; border-radius:2px; background:#002C54; color:#fff; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:700;">
                            <i class="fa fa-book"></i>
                        </div>
                        <span>{{ $item->name }}</span>
                    </div>
                    @if(!empty($item->exam_term_name))
                        <span class="mob-term-pill">{{ $item->exam_term_name }}</span>
                    @endif
                </div>

                {{-- Assigned Classes --}}
                <div class="mob-assigned-box">
                    <div style="font-size:10px; font-weight:700; color:#64748b; margin-bottom:3px;">
                        <i class="fa fa-tags mr-1"></i> Assigned Classes ({{ count($classes) }}):
                    </div>
                    @forelse($classes as $c)
                        <span class="mob-class-chip {{ $c->is_published ? 'published' : '' }}">
                            @if($c->is_published)
                                <i class="fa fa-check-circle text-success"></i>
                            @else
                                <i class="fa fa-clock-o text-muted"></i>
                            @endif
                            {{ $c->class_name }}
                            @if(!empty($c->exam_date))
                                <span style="font-size:9px; color:#64748b;">({{ date('d/m', strtotime($c->exam_date)) }})</span>
                            @endif
                        </span>
                    @empty
                        <span style="font-size:10.5px; color:#94a3b8;">No classes assigned yet</span>
                    @endforelse
                </div>

                {{-- Actions Toolbar --}}
                <div class="mob-card-actions">
                    <a href="{{ url('assign/exam/'.$item->id) }}" class="mob-btn-action btn-outline-primary border">
                        <i class="fa fa-tag"></i> Assign
                    </a>
                    <button type="button" class="mob-btn-action btn-outline-warning border" data-toggle="modal" data-target="#publishResultModal{{ $item->id }}">
                        <i class="fa fa-bullhorn"></i> Publish
                    </button>
                    <a href="{{ url('fill_marks?exam_id='.$item->id) }}" class="mob-btn-action btn-outline-success border">
                        <i class="fa fa-pencil"></i> Marks
                    </a>
                    @if($permission->add ?? true)
                        <button type="button" class="mob-btn-action btn-outline-secondary border copyExamData" 
                                data-id="{{ $item->id }}" 
                                data-name="{{ $item->copy_name }}" 
                                data-date="{{ $item->exam_date ?? $date }}" 
                                data-toggle="modal" 
                                data-target="#copyExamModal">
                            <i class="fa fa-copy"></i>
                        </button>
                    @endif
                    @if($permission->edit ?? true)
                        <a href="{{ url('edit/exam/'.$item->id) }}" class="mob-btn-action btn-outline-info border">
                            <i class="fa fa-edit"></i>
                        </a>
                    @endif
                    @if($permission->delete ?? true)
                        <button type="button" class="mob-btn-action btn-outline-danger border deleteData" 
                                data-id="{{ $item->id }}" 
                                data-name="{{ $item->name }}" 
                                data-toggle="modal" 
                                data-target="#deleteExamModal">
                            <i class="fa fa-trash-o"></i>
                        </button>
                    @endif
                </div>

            </div>
        @empty
            <div class="text-center text-muted py-4 bg-white rounded border">
                <i class="fa fa-leanpub mb-1" style="font-size:24px;"></i>
                <div style="font-size:12px; font-weight:700;">No examinations found</div>
            </div>
        @endforelse

        <div id="mobNoMatchesCard" class="text-center text-muted py-4 bg-white rounded border" style="display:none;">
            <i class="fa fa-search mb-1" style="font-size:20px;"></i>
            <div style="font-size:12px; font-weight:700;">No matching exams found</div>
        </div>
    </div>

</div>

{{-- Publish Modals --}}
@foreach($data as $examItem)
<div class="modal fade" id="publishResultModal{{ $examItem->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius:4px; overflow:hidden;">
            <div class="modal-header bg-primary text-white py-2 px-3" style="background:#002C54 !important;">
                <h6 class="modal-title font-weight-bold mb-0">
                    <i class="fa fa-bullhorn text-warning mr-1"></i> Publish Result — {{ $examItem->name }}
                </h6>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-2">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0" style="font-size:11.5px;">
                        <thead>
                            <tr class="bg-light">
                                <th>Class</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($examItem->assigned_classes as $assignedClass)
                                <tr>
                                    <td class="font-weight-bold">{{ $assignedClass->class_name }}</td>
                                    <td>
                                        @if($assignedClass->is_published)
                                            <span class="badge badge-success">Published</span>
                                        @else
                                            <span class="badge badge-secondary">Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($assignedClass->is_published)
                                            <form method="POST" action="{{ route('exam.result.reset-publication') }}" onsubmit="return confirm('Reset publication?');">
                                                @csrf
                                                <input type="hidden" name="exam_id" value="{{ $examItem->id }}">
                                                <input type="hidden" name="class_type_id" value="{{ $assignedClass->class_type_id }}">
                                                <button type="submit" class="btn btn-danger btn-xs py-0 px-2" style="font-size:10px;">Unpublish</button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('exam.result.publish') }}" onsubmit="return confirm('Publish result now?');">
                                                @csrf
                                                <input type="hidden" name="exam_id" value="{{ $examItem->id }}">
                                                <input type="hidden" name="class_type_id" value="{{ $assignedClass->class_type_id }}">
                                                <button type="submit" class="btn btn-primary btn-xs py-0 px-2" style="font-size:10px; background:#002C54;">Publish</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No classes assigned.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach

{{-- Copy Exam Modal --}}
<div class="modal fade" id="copyExamModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius:4px; overflow:hidden;">
            <form action="{{ url('copy/exam') }}" method="post">
                @csrf
                <div class="modal-header bg-primary text-white py-2 px-3" style="background:#002C54 !important;">
                    <h6 class="modal-title font-weight-bold mb-0">
                        <i class="fa fa-copy mr-1 text-info"></i> Duplicate Exam
                    </h6>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body p-3">
                    <input type="hidden" id="mob_copy_exam_id" name="exam_id">
                    <div class="form-group mb-2">
                        <label style="font-size:11.5px; font-weight:700;">New Exam Title</label>
                        <input type="text" class="form-control form-control-sm" id="mob_copy_exam_name" name="name" required>
                    </div>
                    <div class="form-group mb-2">
                        <label style="font-size:11.5px; font-weight:700;">Exam Date</label>
                        <input type="date" class="form-control form-control-sm" id="mob_copy_exam_date" name="exam_date" required>
                    </div>
                </div>
                <div class="modal-footer py-2 px-3 bg-light">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary" style="background:#002C54;">Copy Exam</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Delete Modal --}}
<div class="modal fade" id="deleteExamModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius:4px; overflow:hidden;">
            <form action="{{ url('delete/exam') }}" method="post"> 
                @csrf
                <div class="modal-header bg-danger text-white py-2 px-3">
                    <h6 class="modal-title font-weight-bold mb-0">
                        <i class="fa fa-trash mr-1"></i> Confirm Delete
                    </h6>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body p-3">
                    <input type="hidden" id="mob_delete_exam_id" name="delete_id">
                    <p class="mb-0" style="font-size:12px;">Are you sure you want to permanently delete this exam and its schedule?</p>
                </div>
                <div class="modal-footer py-2 px-3 bg-light">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
$(document).ready(function(){
    $('.deleteData').on('click', function() {
        var id = $(this).data('id');
        $('#mob_delete_exam_id').val(id);
    });

    $('.copyExamData').on('click', function() {
        $('#mob_copy_exam_id').val($(this).data('id'));
        $('#mob_copy_exam_name').val($(this).data('name'));
        $('#mob_copy_exam_date').val($(this).data('date'));
    });

    $('#mobExamSearch').on('input keyup', function(){
        var query = $(this).val().toLowerCase().trim();
        var $cards = $('.mob-exam-item');
        var matchCount = 0;

        if (query === '') {
            $cards.show();
            matchCount = $cards.length;
            $('#mobNoMatchesCard').hide();
        } else {
            $cards.each(function(){
                var name = $(this).data('name') || '';
                var term = $(this).data('term') || '';
                var classes = $(this).data('classes') || '';
                if (name.indexOf(query) !== -1 || term.indexOf(query) !== -1 || classes.indexOf(query) !== -1) {
                    $(this).show();
                    matchCount++;
                } else {
                    $(this).hide();
                }
            });

            if (matchCount === 0 && $cards.length > 0) {
                $('#mobNoMatchesCard').show();
            } else {
                $('#mobNoMatchesCard').hide();
            }
        }

        $('#mobVisibleExamCount').text(matchCount);
    });
});
</script>
@endsection
@endsection