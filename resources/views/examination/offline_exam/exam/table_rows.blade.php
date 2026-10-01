@php
    $date = date('Y-m-d');
    $permission = $permission ?? Helper::permissioncheck(8);
    $startIndex = $startIndex ?? 0;
@endphp

@forelse($data as $item)
    @php
        $classes = $item->assigned_classes ?? collect();
        $dateGroups = $classes->groupBy(function ($assignedClass) {
            return !empty($assignedClass->exam_date)
                ? \Carbon\Carbon::parse($assignedClass->exam_date)->format('d M Y')
                : 'No Date Set';
        });
        $publishedCount = $classes->where('is_published', true)->count();
        $totalClassCount = count($classes);
        $hasPublished = $publishedCount > 0;
        $isAllPublished = $totalClassCount > 0 && $publishedCount === $totalClassCount;
        
        $statusType = 'unassigned';
        if ($totalClassCount > 0) {
            if ($isAllPublished) {
                $statusType = 'published';
            } elseif ($hasPublished) {
                $statusType = 'published';
            } else {
                $statusType = 'pending';
            }
        }
        $assignedClassesText = $classes->pluck('class_name')->implode(', ') ?: 'None';
    @endphp
    <tr class="exam-row" 
        data-id="{{ $item->id }}"
        data-name="{{ strtolower($item->name ?? '') }}" 
        data-term="{{ strtolower($item->exam_term_name ?? '') }}"
        data-classes="{{ strtolower($classes->pluck('class_name')->implode(' ')) }}"
        data-status="{{ $statusType }}">
        
        {{-- 1. S.No. --}}
        <td class="text-center font-weight-bold text-muted row-index" style="vertical-align: middle;">
            {{ $startIndex + $loop->iteration }}
        </td>
        
        {{-- 2. Exam Name & Date --}}
        <td>
            <div class="exam-title-cell">
                <a href="{{ url('edit/exam/'.$item->id) }}" class="exam-name-link" title="Click to edit exam">
                    {{ $item->name ?? '' }}
                </a>
                @if(!empty($item->exam_date))
                    <div class="exam-date-meta">
                        <i class="fa fa-calendar-check-o mr-1"></i>{{ date('d M Y', strtotime($item->exam_date)) }}
                    </div>
                @endif
            </div>
        </td>

        {{-- 3. Exam Term --}}
        <td>
            @if(!empty($item->exam_term_name))
                <span class="badge-term">{{ $item->exam_term_name }}</span>
            @else
                <span class="text-muted" style="font-size: 11px;">-</span>
            @endif
        </td>

        {{-- 4. Assigned Classes & Schedules --}}
        <td>
            @if($classes->isNotEmpty())
                <div class="exam-classes-wrap">
                    @foreach($dateGroups as $examDateLabel => $dateGroup)
                        <div class="exam-schedule-group">
                            <span class="exam-schedule-date">
                                <i class="fa fa-calendar-check-o mr-1"></i>{{ $examDateLabel }}:
                            </span>
                            <div class="exam-class-chips">
                                @foreach($dateGroup as $dateClass)
                                    <span class="badge-class-chip {{ $dateClass->is_published ? 'chip-published' : 'chip-pending' }}" 
                                          title="{{ $dateClass->is_published ? 'Result published for ' . $dateClass->class_name : 'Result pending publication for ' . $dateClass->class_name }}">
                                        @if($dateClass->is_published)
                                            <i class="fa fa-check-circle mr-1 text-success"></i>
                                        @else
                                            <i class="fa fa-clock-o mr-1 text-muted"></i>
                                        @endif
                                        {{ $dateClass->class_name ?? '' }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="exam-no-classes">
                    <i class="fa fa-info-circle mr-1"></i> No classes assigned. 
                    <a href="{{ url('assign/exam/'.$item->id) }}" class="assign-link">Assign Classes &rarr;</a>
                </div>
            @endif
        </td>

        {{-- 5. Result Publication Status --}}
        <td class="text-center">
            @if($totalClassCount === 0)
                <span class="badge-pub-readonly" title="Assign classes before publishing results">
                    <i class="fa fa-minus-circle mr-1"></i> Not Assigned
                </span>
            @elseif($isAllPublished)
                <button type="button" 
                        class="btn-pub-action btn-pub-success" 
                        data-toggle="modal" 
                        data-target="#publishResultModal{{ $item->id }}"
                        title="All results published. Click to manage.">
                    <i class="fa fa-check-circle mr-1"></i> Published ({{ $publishedCount }}/{{ $totalClassCount }})
                </button>
            @elseif($hasPublished)
                <button type="button" 
                        class="btn-pub-action btn-pub-partial" 
                        data-toggle="modal" 
                        data-target="#publishResultModal{{ $item->id }}"
                        title="Partially published. Click to manage.">
                    <i class="fa fa-adjust mr-1"></i> Partial ({{ $publishedCount }}/{{ $totalClassCount }})
                </button>
            @else
                <button type="button" 
                        class="btn-pub-action btn-pub-pending" 
                        data-toggle="modal" 
                        data-target="#publishResultModal{{ $item->id }}"
                        title="Results not published yet. Click to publish.">
                    <i class="fa fa-clock-o mr-1"></i> Publish Result
                </button>
            @endif
        </td>

        {{-- 6. Actions (Sticky Fixed Column) --}}
        <td class="text-center fixed_action_col">
            <div class="table-actions">
                {{-- Assign Classes --}}
                <a href="{{ url('assign/exam/'.$item->id) }}" class="table-btn btn-action-assign" title="Assign Classes to Exam">
                    <i class="fa fa-th-large"></i>
                </a>

                {{-- Fill Marks via Excel --}}
                <a href="{{ url('fill-marks-by-excel?exam_id='.$item->id) }}" class="table-btn btn-action-excel" title="Fill Marks via Excel">
                    <i class="fa fa-file-excel-o"></i>
                </a>

                {{-- Manual Marks Entry --}}
                <a href="{{ url('fill_marks?exam_id='.$item->id) }}" class="table-btn btn-action-marks" title="Manual Marks Entry">
                    <i class="fa fa-pencil"></i>
                </a>

                {{-- Duplicate / Copy Exam --}}
                @if($permission->add ?? true)
                    <button type="button" class="table-btn btn-action-copy copyExamData" 
                            data-id="{{ $item->id }}" 
                            data-name="{{ $item->copy_name }}" 
                            data-date="{{ $item->exam_date ?? $date }}" 
                            title="Duplicate / Copy Exam">
                        <i class="fa fa-copy"></i>
                    </button>
                @endif

                {{-- Edit Exam --}}
                @if($permission->edit ?? true)
                    <a href="{{ url('edit/exam/'.$item->id) }}" class="table-btn btn-action-edit" title="Edit Exam Details">
                        <i class="fa fa-edit"></i>
                    </a>
                @endif

                {{-- Delete Exam (Themed Modal) --}}
                @if($permission->delete ?? true)
                    <button type="button" class="table-btn btn-action-delete deleteExamBtn" 
                            data-id="{{ $item->id }}" 
                            data-name="{{ $item->name }}" 
                            data-term="{{ $item->exam_term_name ?? 'General' }}"
                            data-classes="{{ $assignedClassesText }}"
                            data-date="{{ !empty($item->exam_date) ? date('d M Y', strtotime($item->exam_date)) : '-' }}"
                            title="Delete Exam">
                        <i class="fa fa-trash-o"></i>
                    </button>
                @endif
            </div>
        </td>
    </tr>
@empty
    <tr id="empty-state-row">
        <td colspan="6" class="p-0">
            <div class="dash-empty-state">
                <div class="empty-icon"><i class="fa fa-leanpub"></i></div>
                <div class="empty-title">No Examinations Found</div>
                <div class="empty-desc">No examination records match your current filter criteria.</div>
            </div>
        </td>
    </tr>
@endforelse