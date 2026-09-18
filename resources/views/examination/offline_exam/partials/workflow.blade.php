@php
    $workflowClassId = $search['class_type_id'] ?? request('class_type_id') ?? request('class_name');
    $workflowExamId = $search['exam_id'] ?? request('exam_id');
    $workflowQuery = array_filter([
        'class_type_id' => $workflowClassId,
        'exam_id' => $workflowExamId,
    ]);
    $workflowSteps = [
        ['title' => '1. Exam Setup', 'url' => url('view/exam'), 'paths' => ['view/exam', 'add/exam']],
        ['title' => '2. Excel Import', 'url' => url('fill-marks-by-excel'), 'paths' => ['fill-marks-by-excel']],
        ['title' => '3. Manual Marks', 'url' => url('fill_marks').($workflowQuery ? '?'.http_build_query(array_merge($workflowQuery, ['show_all_subjects' => 1])) : '?show_all_subjects=1'), 'paths' => ['fill_marks']],
        ['title' => '4. Exam Report', 'url' => url('exam_wise_report').($workflowQuery ? '?'.http_build_query($workflowQuery) : ''), 'paths' => ['exam_wise_report']],
    ];
@endphp

<div class="card border-0 shadow-sm mb-3 examination-workflow">
    <div class="card-body py-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between">
            <div class="mb-2 mb-md-0">
                <strong><i class="fa fa-random text-primary mr-1"></i> Examination Workflow</strong>
                <div class="small text-muted">Create/assign the exam, enter marks by Excel or manually, then open the report.</div>
            </div>
            <div class="btn-group flex-wrap" role="group" aria-label="Examination workflow">
                @foreach($workflowSteps as $step)
                    @php
                        $active = in_array(request()->path(), $step['paths']);
                    @endphp
                    <a href="{{ $step['url'] }}" class="btn btn-sm {{ $active ? 'btn-primary' : 'btn-outline-primary' }}">
                        @if($active)<i class="fa fa-check-circle mr-1"></i>@endif{{ $step['title'] }}
                    </a>
                @endforeach
            </div>
        </div>
        @if($workflowClassId || $workflowExamId)
            <div class="mt-2 small">
                <span class="badge badge-light border">Selected class ID: {{ $workflowClassId ?: '—' }}</span>
                <span class="badge badge-light border">Selected exam ID: {{ $workflowExamId ?: '—' }}</span>
            </div>
        @endif
    </div>
</div>
